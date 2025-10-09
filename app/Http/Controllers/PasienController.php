<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasienRequest;
use App\Models\Pasien;
use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use App\Models\KondisiRumah;
use App\Models\PhbsRumahTangga;
use App\Models\PemeliharaanKesehatanKeluarga;
use App\Models\PengkajianIndividu;
use App\Models\SirkulasiCairan;
use App\Models\Perkemihan;
use App\Models\Pencernaan;
use App\Models\Muskuloskeletal;
use App\Models\Neurosensori;
use App\Models\Kunjungan;
use App\Models\Visiting;
use App\Imports\PasienImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SyncronisasiPasienCarik;
use App\Jobs\ExportPasienJob;
use App\Models\ExportProgress;
use App\Exports\PasienExport;
use Auth;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class PasienController extends Controller
{

    public function index(Request $request): \Illuminate\Contracts\View\View
    {
        $currentUser = \Auth::user();
        
        // Only load districts for administrators (superadmin)
        $districts = collect();
        if ($currentUser->role === 'superadmin') {
            $districtsQuery = DB::table('districts')
                ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
                ->join('provinces', 'regencies.province_id', '=', 'provinces.id')
                ->select('districts.id', 'districts.name')
                ->where('provinces.id', 31)
                ->orderBy('districts.name');
            
            $districts = $districtsQuery->get();
        }
        
        return view('pasiens.index', compact('districts'));
    }

    public function getData(Request $request)
    {
        $currentUser = \Auth::user();

        $query = DB::table('pasiens')
            ->select(
                'pasiens.id',
                'pasiens.name',
                'pasiens.nik',
                'pasiens.jenis_kelamin',
                'pasiens.alamat',
                'pasiens.rt',
                'pasiens.rw',
                'pasiens.flag_sicarik',
                'villages.name as village_name',
                'districts.name as district_name',
                'regencies.name as regency_name',
                'pustus.jenis_faskes'
            )
            ->leftJoin('pustus', 'pasiens.pustu_id', '=', 'pustus.id')
            ->leftjoin('villages', 'villages.id', '=', 'pasiens.village_id')
            ->leftjoin('districts', 'districts.id', '=', 'villages.district_id')
            ->leftjoin('regencies', 'regencies.id', '=', 'districts.regency_id')
            ->whereNull('pasiens.deleted_at');

        // Apply user role restrictions
        if ($currentUser->role === 'sudinkes') {
            $query->where('regencies.id', $currentUser->regency_id)->where('pasiens.user_id', '!=', '-');
        } elseif ($currentUser->role === 'perawat' || $currentUser->role === 'operator') {
            if ($currentUser->pustu) {
                $districtId = $currentUser->pustu->district_id;
                // Ambil semua pasien dari district ini (baik puskesmas maupun non-puskesmas)
                $pasienIds = DB::table('pasiens')
                    ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
                    ->where('villages.district_id', $districtId)
                    ->pluck('pasiens.id');
                $query->whereIn('pasiens.id', $pasienIds);
            } else {
                // Jika tidak ada pustu, hanya pasien milik dia sendiri
                $query->where('pasiens.user_id', $currentUser->id);
            }
        } elseif ($currentUser->role !== 'superadmin') {
            $query->where('pasiens.user_id', $currentUser->id);
        }

        // Apply district filter if provided (only for administrators)
        if ($request->filled('district_filter') && $currentUser->role === 'superadmin') {
            $query->where('districts.id', $request->district_filter);
        }

        // Apply search filter if provided
        if ($request->filled('search_input')) {
            $searchTerm = $request->search_input;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pasiens.name', 'like', "%{$searchTerm}%")
                  ->orWhere('pasiens.nik', 'like', "%{$searchTerm}%")
                  ->orWhere('pasiens.alamat', 'like', "%{$searchTerm}%")
                  ->orWhere('villages.name', 'like', "%{$searchTerm}%")
                  ->orWhere('districts.name', 'like', "%{$searchTerm}%")
                  ->orWhere('regencies.name', 'like', "%{$searchTerm}%");
            });
        }

        // Apply data source filter if provided
        if ($request->filled('flag_sicarik')) {
            $flagSicarik = $request->flag_sicarik;
            if ($flagSicarik == '1') {
                $query->where('pasiens.flag_sicarik', 1);
            } elseif ($flagSicarik == '0') {
                $query->where('pasiens.flag_sicarik', 0);
            }
        }

        return DataTables::of($query)
            ->addColumn('rt_rw', function ($pasien) {
                return $pasien->rt . '/' . $pasien->rw;
            })
            ->make(true);
    }


    public function create(): \Illuminate\Contracts\View\View
    {
        $provinces = Province::all();
        $parentId = auth()->user()->pustu_id;
        return view('pasiens.create', compact('provinces', 'parentId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|min:16|max:16|unique:pasiens,nik',
            'alamat' => 'nullable|string|max:255',
            'jenis_kelamin' => 'required|string|max:255',
            'jenis_ktp' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'village_id' => 'required|string|max:255',
            'district_id' => 'nullable|string|max:255',
            'regency_id' => 'nullable|string|max:255',
            'province_id' => 'nullable|string|max:255',
            'nomor_whatsapp' => 'nullable|string|max:20|regex:/^[0-9]{10,13}$/',
            'nama_pendamping' => 'nullable|string|max:255',
            'rt' => 'required|string|max:255',
            'rw' => 'required|string|max:255',
        ]);

        // Set additional fields
        $validated['pustu_id'] = auth()->user()->pustu_id;
        $validated['user_id'] = auth()->user()->id;
        $validated['village_id'] = $request->village_id;
        $validated['flag_sicarik'] = 0; // Default value for manual entry

        $pasien = Pasien::create($validated);
        return redirect()->route('pasiens.show', $pasien->id)->with('success', 'Data pasien berhasil ditambahkan');
    }

    public function show(Pasien $pasien): \Illuminate\Contracts\View\View
    {
        $kunjungan = Visiting::with(['pasien', 'user', 'healthForms'])->where('pasien_id', $pasien->id)->get();
        return view('pasiens.show', compact('pasien', 'kunjungan'));
    }

    public function edit(Pasien $pasien): \Illuminate\Contracts\View\View
    {
        $provinces = Province::all();
        $regencies = Regency::all();
        $districts = District::all();
        $villages = Village::all();

        $selectedVillage = DB::table('villages')
            ->join('districts', 'villages.district_id', '=', 'districts.id')
            ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
            ->join('provinces', 'regencies.province_id', '=', 'provinces.id')
            ->select(
                'villages.id as village_id', 'villages.name as village_name',
                'districts.name as district_name',
                'regencies.name as regency_name',
                'provinces.name as province_name'
            )
            ->where('villages.id', $pasien->village_id)
            ->first();

        return view('pasiens.edit', compact('pasien', 'provinces', 'regencies', 'districts', 'villages', 'selectedVillage'));
    }


    public function update(Request $request, Pasien $pasien)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|min:16|max:16|unique:pasiens,nik,' . $pasien->id,
            'alamat' => 'nullable|string|max:255',
            'jenis_kelamin' => 'required|string|max:255',
            'jenis_ktp' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'village_id' => 'required|string|max:255',
            'nomor_whatsapp' => 'nullable|string|max:20|regex:/^[0-9]{10,13}$/',
            'nama_pendamping' => 'nullable|string|max:255',
            'rt' => 'required|string|max:255',
            'rw' => 'required|string|max:255',
        ]);

        $pasien->update($validated);
        return redirect()->route('pasiens.show', $pasien->id)->with('success', 'Data pasien berhasil diperbarui');
    }

    public function destroy(Pasien $pasien): \Illuminate\Http\RedirectResponse
    {
        $pasien->delete();
        return redirect()->route('pasiens.index')->with('success', 'Deleted successfully');
    }

    public function autofill(Request $request)
    {
        try {
            $nik = $request->get('nik');
            
            // Basic validation
            if (empty($nik)) {
                return response()->json(['error' => 'NIK tidak boleh kosong.'], 400);
            }

            // Query with village relation
            $pasien = Pasien::with(['village.district.regency.province'])
                ->where('nik', $nik)
                ->first();
            
            if (!$pasien) {
                return response()->json([
                    'error' => 'Data pasien dengan NIK tersebut tidak ditemukan di database.'
                ], 404);
            }

            // Get village data safely
            $villageData = null;
            if ($pasien->village) {
                $villageData = [
                    'village_id' => $pasien->village->id,
                    'village_name' => $pasien->village->name,
                    'district_name' => $pasien->village->district->name ?? '',
                    'regency_name' => $pasien->village->district->regency->name ?? '',
                    'province_name' => $pasien->village->district->regency->province->name ?? '',
                ];
            }

            // Return data with village information
            return response()->json([
                'success' => true,
                'name' => $pasien->name ?? '',
                'alamat' => $pasien->alamat ?? '',
                'jenis_kelamin' => $pasien->jenis_kelamin ?? '',
                'jenis_ktp' => $pasien->jenis_ktp ?? '',
                'tanggal_lahir' => $pasien->tanggal_lahir ?? '',
                'nomor_whatsapp' => $pasien->nomor_whatsapp ?? '',
                'nama_pendamping' => $pasien->nama_pendamping ?? '',
                'rt' => $pasien->rt ?? '',
                'rw' => $pasien->rw ?? '',
                'village_id' => $pasien->village_id ?? '',
                'village_data' => $villageData,
                'message' => 'Data berhasil ditemukan di database'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPasienByNik(Request $request)
    {
        $search = $request->input('q');
        $currentUser = \Auth::user();

        $query = Pasien::with(['village', 'district', 'regency'])
            ->where(function ($query) use ($search) {
                $query->where('nik', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
            });

        // Apply user role restrictions based on wilayah
        if ($currentUser->role === 'sudinkes') {
            $query->whereHas('village.district.regency', function ($q) use ($currentUser) {
                $q->where('id', $currentUser->regency_id);
            })->where('user_id', '!=', '-');
        } elseif ($currentUser->role === 'perawat' || $currentUser->role === 'operator') {
            if ($currentUser->pustu) {
                $districtId = $currentUser->pustu->district_id;
                // Ambil semua pasien dari district ini (baik puskesmas maupun non-puskesmas)
                $query->whereHas('village.district', function ($q) use ($districtId) {
                    $q->where('id', $districtId);
                });
            } else {
                // Jika tidak ada pustu, hanya pasien milik dia sendiri
                $query->where('user_id', $currentUser->id);
            }
        } elseif ($currentUser->role !== 'superadmin') {
            $query->where('user_id', $currentUser->id);
        }

        $pasiens = $query->limit(10)->get();



        return response()->json(
            $pasiens->map(function ($pasien) {
                return [
                    'id' => $pasien->id,
                    'text' => "{$pasien->name} ({$pasien->nik}) - {$pasien->alamat}," . "{$pasien->village->name}" . ", {$pasien->village->district->name}" . ", {$pasien->village->district->regency->name}",
                    'fullData' => [
                        'id' => $pasien->id,
                        'name' => $pasien->name,
                        'nik' => $pasien->nik,
                        'alamat' => $pasien->alamat,
                        'rt' => $pasien->rt,
                        'rw' => $pasien->rw,
                        'village_id' => $pasien->village_id,
                    ]
                ];
            })
        );
    }


    public function createAsuhanKeluarga($id): \Illuminate\Contracts\View\View
    {
        $pasienId = $id;
        $kondisiRumah = KondisiRumah::where('pasien_id', $pasienId)->first();
        $PhbsRumahTangga = PhbsRumahTangga::where('pasien_id', $pasienId)->first();
        $pemeliharaanKesehatanKeluarga = PemeliharaanKesehatanKeluarga::where('pasien_id', $pasienId)->first();
        $pengkajianIndividu = PengkajianIndividu::where('pasien_id', $pasienId)->first();
        $sirkulasiCairan = SirkulasiCairan::where('pasien_id', $pasienId)->first();
        $perkemihan = Perkemihan::where('pasien_id', $pasienId)->first();
        $pencernaan = Pencernaan::where('pasien_id', $pasienId)->first();
        $muskuloskeletal = Muskuloskeletal::where('pasien_id', $pasienId)->first();
        $neurosensori = Neurosensori::where('pasien_id', $pasienId)->first();

        return view('kunjungans.form-pencatatan', compact(
            'pasienId',
            'kondisiRumah',
            'PhbsRumahTangga',
            'pemeliharaanKesehatanKeluarga',
            'pengkajianIndividu',
            'sirkulasiCairan',
            'perkemihan',
            'pencernaan',
            'muskuloskeletal',
            'neurosensori'
        ));
    }

    public function importPasien(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new PasienImport, $request->file('file'));

        return back()->with('success', 'Data pasien berhasil diimport!');
    }

    public function downloadTemplate()
    {
        $filePath = storage_path('app/public/template_pasiens.xlsx');

        if (file_exists($filePath)) {
            return Response::download($filePath);
        }

        // Jika file tidak ditemukan
        return redirect()->back()->with('error', 'Template tidak ditemukan.');
    }

    public function getDataPasienCarik(Request $request)
    {
        $nik = $request->input('nik');

        // Validate NIK
        if (!$nik || strlen($nik) !== 16 || !ctype_digit($nik)) {
            return response()->json(['error' => 'NIK harus berupa 16 digit angka.'], 400);
        }

        try {
            $response = Http::timeout(30)->withHeaders([
                'carik-api-key' => 'WydtKanwCc0dhbaclOLy2uUBl7WVICQA',
                'Cookie' => 'TS01f239ec=01b53461a6e068c46f652602c6a09733f49a58e0f31899b767a13a3358d6cac47368fe86ad7fb78a2034b98e8cb19c758b6dc2c1bf',
            ])->get('https://carik.jakarta.go.id/api/v1/dilan/activity-daily-living', [
                'nik' => $nik,
            ]);

            // Check if request was successful
            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['data']) && count($data['data']) > 0) {
                    $d = $data['data'][0];
                    
                    return response()->json([
                        'success' => true,
                        'nama' => $d['nama'] ?? '',
                        'alamat' => $d['alamat'] ?? '',
                        'jenis_kelamin' => isset($d['gender']) ? ($d['gender'] == '1' ? 'Laki-laki' : 'Perempuan') : '',
                        'kelurahan' => $d['kelurahan'] ?? '',
                        'kecamatan' => $d['kecamatan'] ?? '',
                        'kota' => $d['kota'] ?? '',
                        'nama_kota' => $d['nama_kota'] ?? '',
                        'nama_kelurahan' => $d['nama_kelurahan'] ?? '',
                        'nama_kecamatan' => $d['nama_kecamatan'] ?? '',
                        'nomor_whatsapp' => $d['nomor_whatsapp'] ?? $d['no_wa'] ?? $d['telepon'] ?? '',
                        'nama_pendamping' => $d['nama_pendamping'] ?? $d['pendamping'] ?? '',
                        'message' => 'Data berhasil ditemukan dari Carik Jakarta'
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Data tidak ditemukan di database Carik Jakarta. Silakan isi data secara manual.'
                    ], 404);
                }
            } else {
                \Log::warning('Carik API failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return response()->json([
                    'error' => 'Layanan Carik Jakarta sedang tidak tersedia. Silakan isi data secara manual.'
                ], $response->status());
            }
        } catch (\Exception $e) {
            \Log::error('Carik API Exception', [
                'message' => $e->getMessage(),
                'nik' => $nik
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data dari Carik Jakarta. Silakan isi data secara manual.'
            ], 500);
        }
    }

    public function searchVillage(Request $request)
    {
        $q = $request->input('q');

        if (strlen($q) < 3) {
            return response()->json([]);
        }

        try {
            $results = DB::table('villages')
                ->join('districts', 'villages.district_id', '=', 'districts.id')
                ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
                ->join('provinces', 'regencies.province_id', '=', 'provinces.id')
                ->select(
                    'villages.id as village_id', 
                    'villages.name as village_name',
                    'districts.id as district_id', 
                    'districts.name as district_name',
                    'regencies.id as regency_id', 
                    'regencies.name as regency_name',
                    'provinces.id as province_id', 
                    'provinces.name as province_name'
                )
                ->where(function ($query) use ($q) {
                    $query->where('villages.name', 'LIKE', '%' . $q . '%')
                        ->orWhere('districts.name', 'LIKE', '%' . $q . '%')
                        ->orWhere('regencies.name', 'LIKE', '%' . $q . '%');
                })
                ->where('provinces.id', 31) // DKI Jakarta
                ->orderBy('villages.name')
                ->limit(20)
                ->get();

            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Village search error', [
                'message' => $e->getMessage(),
                'query' => $q
            ]);
            
            return response()->json([]);
        }
    }

    public function startSyncCarik(Request $request)
    {
        try {
            $syncId = 'sync_carik_' . time();

            // Dispatch the job with the authenticated user's ID
            SyncronisasiPasienCarik::dispatch(auth()->user()->id, $syncId);

            \Log::info('Sinkronisasi Carik dimulai', ['sync_id' => $syncId, 'user_id' => auth()->user()->id]);

            return response()->json([
                'success' => true,
                'sync_id' => $syncId,
                'message' => 'Sinkronisasi telah dimulai. Gunakan sync_id untuk memantau progres.'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Gagal memulai sinkronisasi Carik: ' . $e->getMessage(), [
                'sync_id' => $syncId ?? null,
                'user_id' => auth()->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai sinkronisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check the progress of the synchronization job.
     *
     * @param Request $request
     * @param string $syncId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkSyncProgress(Request $request, $syncId)
    {
        $cacheKey = $syncId;
        $progress = Cache::get($cacheKey);

        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => 'Progres sinkronisasi tidak ditemukan atau telah kadaluarsa.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'message' => $progress['message']
        ], 200);
    }

    /**
     * Export patients data
     */
    public function exportPasien(Request $request)
    {
        try {
            $currentUser = auth()->user();
            $filters = [
                'district_filter' => $request->input('district_filter'),
                'search_input' => $request->input('search_input')
            ];

            // Generate export ID
            $exportId = 'export_pasien_' . time() . '_' . $currentUser->id;

            // Create progress record
            $exportProgress = ExportProgress::create([
                'export_id' => $exportId,
                'user_id' => $currentUser->id,
                'type' => 'pasien',
                'percentage' => 0,
                'message' => 'Memulai proses export...',
                'status' => 'processing',
                'started_at' => now()
            ]);

            $exportProgress->updateProgress(10, 'Menyiapkan data...');

            // Build query with filters
            $query = $this->buildExportQuery($currentUser, $filters);
            
            $exportProgress->updateProgress(20, 'Mengambil data pasien...');

            // Get total count for progress calculation
            $totalRecords = $query->count();
            
            if ($totalRecords === 0) {
                $exportProgress->updateProgress(100, 'Tidak ada data untuk diexport', 'warning');
                return response()->json([
                    'success' => true,
                    'export_id' => $exportId,
                    'message' => 'Tidak ada data untuk diexport'
                ]);
            }

            $exportProgress->updateProgress(30, "Memproses {$totalRecords} data pasien...");

            // Get all data
            $pasiens = $query->get();

            $exportProgress->updateProgress(60, 'Membuat file Excel...');

            // Create export file
            $fileName = 'export_pasien_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filePath = 'exports/' . $fileName;

            // Use Excel export
            Excel::store(new PasienExport($pasiens), $filePath, 'public');

            $exportProgress->updateProgress(90, 'Menyimpan file...');

            // Get file URL
            $fileUrl = asset('storage/' . $filePath);

            $exportProgress->markCompleted('Export selesai!', [
                'file_url' => $fileUrl,
                'file_name' => $fileName,
                'total_records' => $totalRecords
            ]);

            return response()->json([
                'success' => true,
                'export_id' => $exportId,
                'file_url' => $fileUrl,
                'file_name' => $fileName,
                'total_records' => $totalRecords,
                'message' => 'Export berhasil!'
            ]);

        } catch (\Exception $e) {
            Log::error('Export pasien failed: ' . $e->getMessage(), [
                'user_id' => auth()->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Export gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check export progress
     */
    public function checkExportProgress(Request $request, $exportId)
    {
        try {
            $progress = ExportProgress::where('export_id', $exportId)
                ->where('user_id', auth()->user()->id)
                ->first();

            if (!$progress) {
                return response()->json([
                    'success' => false,
                    'message' => 'Progres export tidak ditemukan atau telah kadaluarsa.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'progress' => [
                    'percentage' => $progress->percentage,
                    'message' => $progress->message,
                    'status' => $progress->status,
                    'data' => $progress->data,
                    'updated_at' => $progress->updated_at->toISOString()
                ],
                'message' => $progress->message
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error checking export progress', [
                'export_id' => $exportId,
                'user_id' => auth()->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memeriksa progres export.'
            ], 500);
        }
    }

    /**
     * Build query for export
     */
    private function buildExportQuery($user, $filters)
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
        } elseif ($user->role === 'perawat' || $user->role === 'operator') {
            if ($user->pustu) {
                $districtId = $user->pustu->district_id;
                // Ambil semua pasien dari district ini (baik puskesmas maupun non-puskesmas)
                $pasienIds = DB::table('pasiens')
                    ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
                    ->where('villages.district_id', $districtId)
                    ->pluck('pasiens.id');
                $query->whereIn('pasiens.id', $pasienIds);
            } else {
                // Jika tidak ada pustu, hanya pasien milik dia sendiri
                $query->where('pasiens.user_id', $user->id);
            }
        } elseif ($user->role !== 'superadmin') {
            $query->where('pasiens.user_id', $user->id);
        }

        // Apply filters
        if (isset($filters['district_filter']) && !empty($filters['district_filter'])) {
            $query->where('districts.id', $filters['district_filter']);
        }

        if (isset($filters['search_input']) && !empty($filters['search_input'])) {
            $searchTerm = $filters['search_input'];
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
