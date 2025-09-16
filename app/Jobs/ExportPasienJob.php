<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PasienExport;
use App\Models\ExportProgress;

class ExportPasienJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $filters;
    protected $exportId;
    protected $exportProgress;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $filters = [], $exportId = null)
    {
        $this->userId = $userId;
        $this->filters = $filters;
        $this->exportId = $exportId ?: 'export_pasien_' . time() . '_' . $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Create progress record
            $this->exportProgress = ExportProgress::create([
                'export_id' => $this->exportId,
                'user_id' => $this->userId,
                'type' => 'pasien',
                'percentage' => 0,
                'message' => 'Memulai proses export...',
                'status' => 'processing',
                'started_at' => now()
            ]);

            Log::info('Export progress record created', [
                'export_id' => $this->exportId,
                'user_id' => $this->userId
            ]);

            // Get user for role-based filtering
            $user = \App\Models\User::find($this->userId);
            if (!$user) {
                $this->exportProgress->markFailed('User tidak ditemukan');
                return;
            }

            $this->exportProgress->updateProgress(10, 'Menyiapkan data...');

            // Build query with filters
            $query = $this->buildQuery($user);
            
            $this->exportProgress->updateProgress(20, 'Mengambil data pasien...');

            // Get total count for progress calculation
            $totalRecords = $query->count();
            
            if ($totalRecords === 0) {
                $this->exportProgress->updateProgress(100, 'Tidak ada data untuk diexport', 'warning');
                return;
            }

            $this->exportProgress->updateProgress(30, "Memproses {$totalRecords} data pasien...");

            // Get all data
            $pasiens = $query->get();

            $this->exportProgress->updateProgress(60, 'Membuat file Excel...');

            // Create export file
            $fileName = 'export_pasien_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filePath = 'exports/' . $fileName;

            // Use Excel export
            Excel::store(new PasienExport($pasiens), $filePath, 'public');

            $this->exportProgress->updateProgress(90, 'Menyimpan file...');

            // Get file URL
            $fileUrl = Storage::disk('public')->url($filePath);

            $this->exportProgress->markCompleted('Export selesai!', [
                'file_url' => $fileUrl,
                'file_name' => $fileName,
                'total_records' => $totalRecords
            ]);

            Log::info('Export pasien completed', [
                'user_id' => $this->userId,
                'export_id' => $this->exportId,
                'total_records' => $totalRecords,
                'file_name' => $fileName
            ]);

        } catch (\Exception $e) {
            Log::error('Export pasien failed', [
                'user_id' => $this->userId,
                'export_id' => $this->exportId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (isset($this->exportProgress)) {
                $this->exportProgress->markFailed('Export gagal: ' . $e->getMessage());
            } else {
                // Try to create a failed progress record if none exists
                try {
                    ExportProgress::create([
                        'export_id' => $this->exportId,
                        'user_id' => $this->userId,
                        'type' => 'pasien',
                        'percentage' => 0,
                        'message' => 'Export gagal: ' . $e->getMessage(),
                        'status' => 'error',
                        'started_at' => now(),
                        'completed_at' => now()
                    ]);
                } catch (\Exception $createError) {
                    Log::error('Failed to create error progress record', [
                        'export_id' => $this->exportId,
                        'user_id' => $this->userId,
                        'error' => $createError->getMessage()
                    ]);
                }
            }
        }
    }

    /**
     * Build query with filters and user role restrictions
     */
    private function buildQuery($user)
    {
        $query = DB::table('pasiens')
            ->select(
                'pasiens.id',
                'pasiens.name',
                'pasiens.nik',
                'pasiens.jenis_kelamin',
                'pasiens.alamat',
                'pasiens.rt',
                'pasiens.rw',
                'pasiens.tanggal_lahir',
                'pasiens.keterangan',
                'villages.name as village_name',
                'districts.name as district_name',
                'regencies.name as regency_name',
                'provinces.name as province_name',
                'pustus.jenis_faskes',
                'pasiens.created_at'
            )
            ->leftJoin('pustus', 'pasiens.pustu_id', '=', 'pustus.id')
            ->leftjoin('villages', 'villages.id', '=', 'pasiens.village_id')
            ->leftjoin('districts', 'districts.id', '=', 'villages.district_id')
            ->leftjoin('regencies', 'regencies.id', '=', 'districts.regency_id')
            ->leftjoin('provinces', 'provinces.id', '=', 'regencies.province_id')
            ->whereNull('pasiens.deleted_at');

        // Apply user role restrictions
        if ($user->role === 'sudinkes') {
            $query->where('regencies.id', $user->regency_id)->where('pasiens.user_id', '!=', '-');
        } elseif ($user->role === 'perawat') {
            if ($user->pustu && $user->pustu->jenis_faskes === 'puskesmas') {
                $districtId = $user->pustu->district_id;
                $query->where('districts.id', $districtId)->where('pasiens.user_id', '!=', '-'); 
            } else {
                $query->where('pasiens.user_id', $user->id);
            }
        } elseif ($user->role !== 'superadmin') {
            $query->where('pasiens.user_id', $user->id);
        }

        // Apply filters
        if (isset($this->filters['district_filter']) && !empty($this->filters['district_filter'])) {
            $query->where('districts.id', $this->filters['district_filter']);
        }

        if (isset($this->filters['search_input']) && !empty($this->filters['search_input'])) {
            $searchTerm = $this->filters['search_input'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pasiens.name', 'like', "%{$searchTerm}%")
                  ->orWhere('pasiens.nik', 'like', "%{$searchTerm}%")
                  ->orWhere('pasiens.alamat', 'like', "%{$searchTerm}%")
                  ->orWhere('villages.name', 'like', "%{$searchTerm}%")
                  ->orWhere('districts.name', 'like', "%{$searchTerm}%")
                  ->orWhere('regencies.name', 'like', "%{$searchTerm}%");
            });
        }

        return $query->orderBy('pasiens.created_at', 'desc');
    }
}