<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PasienWilayahReportExport;
use App\Models\ExportProgress;
use App\Models\User;

class ExportPasienWilayahJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $groupBy;
    protected $wilayahId;
    protected $exportId;
    protected $exportProgress;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $groupBy = 'district', $wilayahId = null, $exportId = null)
    {
        $this->userId = $userId;
        $this->groupBy = $groupBy;
        $this->wilayahId = $wilayahId;
        $this->exportId = $exportId ?: 'export_pasien_wilayah_' . time() . '_' . $userId;
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
                'type' => 'pasien_wilayah',
                'percentage' => 0,
                'message' => 'Memulai proses export...',
                'status' => 'processing',
                'started_at' => now()
            ]);

            Log::info('Export pasien wilayah progress record created', [
                'export_id' => $this->exportId,
                'user_id' => $this->userId,
                'group_by' => $this->groupBy
            ]);

            // Get user
            $user = \App\Models\User::find($this->userId);
            if (!$user) {
                $this->exportProgress->markFailed('User tidak ditemukan');
                return;
            }

            $this->exportProgress->updateProgress(10, 'Menyiapkan data...');

            // Get latest visits
            $this->exportProgress->updateProgress(20, 'Mengambil data kunjungan terakhir...');
            $latestVisits = $this->getLatestVisits($user, $this->wilayahId, $this->groupBy);

            $this->exportProgress->updateProgress(40, 'Mengambil data pemeriksaan...');

            // Get pemeriksaan terakhir
            $pemeriksaanTerakhir = $this->getPemeriksaanTerakhir($latestVisits);

            $this->exportProgress->updateProgress(50, 'Mengelompokkan data berdasarkan wilayah...');

            // Group by wilayah
            $groupedData = $this->groupByWilayah($latestVisits, $pemeriksaanTerakhir, $this->groupBy, $this->wilayahId);

            $totalRecords = 0;
            foreach ($groupedData as $wilayahData) {
                $totalRecords += count($wilayahData['pasien']);
            }

            if ($totalRecords === 0) {
                $this->exportProgress->updateProgress(100, 'Tidak ada data untuk diexport', 'warning');
                return;
            }

            $this->exportProgress->updateProgress(70, "Memproses {$totalRecords} data pasien...");

            // Create export file
            $groupLabels = [
                'village' => 'Kelurahan',
                'district' => 'Kecamatan',
                'regency' => 'Kabupaten',
                'province' => 'Provinsi'
            ];
            $groupLabel = $groupLabels[$this->groupBy] ?? 'Wilayah';
            $fileName = 'export_pasien_' . strtolower($groupLabel) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filePath = 'exports/' . $fileName;

            $this->exportProgress->updateProgress(80, 'Membuat file Excel...');

            // Use Excel export
            Excel::store(new PasienWilayahReportExport($groupedData, $this->groupBy), $filePath, 'public');

            $this->exportProgress->updateProgress(95, 'Menyimpan file...');

            // Get file URL - ensure proper URL generation
            $fileUrl = url('storage/' . $filePath);

            $this->exportProgress->markCompleted('Export selesai!', [
                'file_url' => $fileUrl,
                'file_name' => $fileName,
                'total_records' => $totalRecords,
                'total_wilayah' => count($groupedData)
            ]);

            Log::info('Export pasien wilayah completed', [
                'user_id' => $this->userId,
                'export_id' => $this->exportId,
                'group_by' => $this->groupBy,
                'total_records' => $totalRecords,
                'file_name' => $fileName
            ]);

        } catch (\Exception $e) {
            Log::error('Export pasien wilayah failed', [
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
                        'type' => 'pasien_wilayah',
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
     * Get latest visit untuk setiap pasien (hanya DKI Jakarta - province_id = 31)
     */
    private function getLatestVisits($user, $wilayahId = null, $groupBy = 'district')
    {
        $provinceIdDKI = 31; // DKI Jakarta
        
        // Subquery untuk mendapatkan visiting terakhir per pasien
        $latestVisitingSubquery = DB::table('visitings')
            ->select('pasien_id', DB::raw('MAX(id) as latest_id'))
            ->groupBy('pasien_id');

        // Main query - hanya ambil data dari DKI Jakarta
        $query = DB::table('visitings as v')
            ->joinSub($latestVisitingSubquery, 'latest', function($join) {
                $join->on('v.id', '=', 'latest.latest_id');
            })
            ->join('pasiens as p', 'p.id', '=', 'v.pasien_id')
            ->join('villages as vil', 'vil.id', '=', 'p.village_id')
            ->join('districts as d', 'd.id', '=', 'vil.district_id')
            ->join('regencies as r', 'r.id', '=', 'd.regency_id')
            ->join('provinces as pr', 'pr.id', '=', 'r.province_id')
            ->where('pr.id', $provinceIdDKI) // Hanya DKI Jakarta
            ->whereNull('p.deleted_at')
            ->select(
                'v.id as visiting_id',
                'v.pasien_id',
                'v.tanggal as tanggal_kunjungan',
                'v.status',
                'v.selesai',
                'p.name as nama_pasien',
                'p.nik',
                'p.alamat',
                'p.rt',
                'p.rw',
                'vil.id as village_id',
                'vil.name as village_name',
                'd.id as district_id',
                'd.name as district_name',
                'r.id as regency_id',
                'r.name as regency_name',
                'pr.id as province_id',
                'pr.name as province_name'
            );

        // Filter berdasarkan role user
        if ($user->role === 'perawat' || $user->role === 'operator') {
            $query->where('v.user_id', $user->id);
        } elseif ($user->role === 'superadmin') {
            // Superadmin bisa lihat semua
        } else {
            $childUserIds = $this->getAllChildUserIds($user->id, $user->role);
            if (!empty($childUserIds)) {
                $query->whereIn('v.user_id', $childUserIds);
            } else {
                $query->where('v.user_id', $user->id);
            }
        }

        // Filter berdasarkan wilayah jika dipilih
        if ($wilayahId) {
            switch ($groupBy) {
                case 'village':
                    $query->where('vil.id', $wilayahId);
                    break;
                case 'district':
                    $query->where('d.id', $wilayahId);
                    break;
                case 'regency':
                    $query->where('r.id', $wilayahId);
                    break;
                case 'province':
                    $query->where('pr.id', $wilayahId);
                    break;
            }
        }

        return $query->get();
    }

    /**
     * Get pemeriksaan terakhir (replicate from controller)
     */
    private function getPemeriksaanTerakhir($visits)
    {
        $visitingIds = $visits->pluck('visiting_id')->toArray();
        
        if (empty($visitingIds)) {
            return [
                'ttvs' => collect(),
                'skrining_adls' => collect(),
                'health_forms' => collect()
            ];
        }

        // Get TTV terakhir per visiting
        $latestTtvSubquery = DB::table('ttvs')
            ->select('kunjungan_id', DB::raw('MAX(id) as latest_id'))
            ->whereIn('kunjungan_id', $visitingIds)
            ->groupBy('kunjungan_id');
            
        $ttvs = DB::table('ttvs as t')
            ->joinSub($latestTtvSubquery, 'latest', function($join) {
                $join->on('t.id', '=', 'latest.latest_id');
            })
            ->whereIn('t.kunjungan_id', $visitingIds)
            ->get()
            ->keyBy('kunjungan_id');

        // Get Skrining ADL
        $skriningAdls = DB::table('skrining_adl')
            ->whereIn('visiting_id', $visitingIds)
            ->get()
            ->keyBy('visiting_id');

        // Get Health Form
        $healthForms = DB::table('health_forms')
            ->whereIn('visiting_id', $visitingIds)
            ->get()
            ->keyBy('visiting_id');

        return [
            'ttvs' => $ttvs,
            'skrining_adls' => $skriningAdls,
            'health_forms' => $healthForms
        ];
    }

    /**
     * Group by wilayah (replicate from controller)
     */
    private function groupByWilayah($visits, $pemeriksaan, $groupBy, $wilayahId = null)
    {
        $grouped = [];

        foreach ($visits as $visit) {
            $key = null;
            $wilayahName = null;
            $wilayahIdValue = null;

            switch ($groupBy) {
                case 'village':
                    $key = $visit->village_id;
                    $wilayahName = $visit->village_name;
                    $wilayahIdValue = $visit->village_id;
                    break;
                case 'district':
                    $key = $visit->district_id;
                    $wilayahName = $visit->district_name;
                    $wilayahIdValue = $visit->district_id;
                    break;
                case 'regency':
                    $key = $visit->regency_id;
                    $wilayahName = $visit->regency_name;
                    $wilayahIdValue = $visit->regency_id;
                    break;
                case 'province':
                    $key = $visit->province_id;
                    $wilayahName = $visit->province_name;
                    $wilayahIdValue = $visit->province_id;
                    break;
            }

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'wilayah_id' => $wilayahIdValue,
                    'wilayah_name' => $wilayahName,
                    'pasien' => []
                ];
            }

            // Get pemeriksaan untuk visiting ini
            $ttv = $pemeriksaan['ttvs'][$visit->visiting_id] ?? null;
            $skriningAdl = $pemeriksaan['skrining_adls'][$visit->visiting_id] ?? null;
            $healthForm = $pemeriksaan['health_forms'][$visit->visiting_id] ?? null;

            $grouped[$key]['pasien'][] = [
                'pasien_id' => $visit->pasien_id,
                'nama_pasien' => $visit->nama_pasien,
                'nik' => $visit->nik,
                'alamat' => $visit->alamat,
                'rt' => $visit->rt,
                'rw' => $visit->rw,
                'village_name' => $visit->village_name,
                'district_name' => $visit->district_name,
                'regency_name' => $visit->regency_name,
                'province_name' => $visit->province_name,
                'kunjungan' => [
                    'visiting_id' => $visit->visiting_id,
                    'tanggal' => $visit->tanggal_kunjungan,
                    'status' => $visit->status,
                    'selesai' => $visit->selesai,
                ],
                'pemeriksaan' => [
                    'ttv' => $ttv ? [
                        'temperature' => $ttv->temperature,
                        'blood_pressure' => $ttv->blood_pressure,
                        'pulse' => $ttv->pulse,
                        'respiration' => $ttv->respiration,
                        'oxygen_saturation' => $ttv->oxygen_saturation,
                        'weight' => $ttv->weight,
                        'height' => $ttv->height,
                        'bmi' => $ttv->bmi,
                        'bmi_category' => $ttv->bmi_category,
                    ] : null,
                    'skrining_adl' => $skriningAdl ? [
                        'total_score' => $skriningAdl->total_score,
                        'butuh_orang' => $skriningAdl->butuh_orang,
                        'pendamping_tetap' => $skriningAdl->pendamping_tetap,
                        'sasaran_home_service' => $skriningAdl->sasaran_home_service,
                    ] : null,
                    'health_form' => $healthForm ? [
                        'skor_aks' => $healthForm->skor_aks,
                        'tingkat_kemandirian' => $healthForm->tingkat_kemandirian,
                        'henti_layanan' => $healthForm->henti_layanan,
                        'kunjungan_lanjutan' => $healthForm->kunjungan_lanjutan,
                    ] : null,
                ]
            ];
        }

        // Sort by wilayah name
        uasort($grouped, function($a, $b) {
            return strcmp($a['wilayah_name'], $b['wilayah_name']);
        });

        return $grouped;
    }

    /**
     * Get all child user IDs (replicate from controller)
     */
    private function getAllChildUserIds($userId, $role)
    {
        if ($role === 'superadmin') {
            return User::pluck('id')->toArray();
        }

        $allChildren = collect([$userId]);
        
        $directChildren = User::where('pustu_id', $userId)->pluck('id');
        
        foreach ($directChildren as $childId) {
            $allChildren->push($childId);
            $allChildren = $allChildren->merge($this->getAllChildUserIds($childId, $role));
        }

        return $allChildren->unique()->toArray();
    }
}
