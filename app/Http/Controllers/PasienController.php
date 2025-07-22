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
use Auth;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class PasienController extends Controller
{

    public function index(Request $request): \Illuminate\Contracts\View\View
    {
        $currentUser = \Auth::user();

        $pasiens = DB::table('pasiens')
            ->select(
                'pasiens.*',
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

        if ($currentUser->role === 'sudinkes') {
            $pasiens->where('regencies.id', $currentUser->regency_id);
        } elseif ($currentUser->role === 'perawat') {
            if ($currentUser->pustu && $currentUser->pustu->jenis_faskes === 'puskesmas') {
                $districtId = $currentUser->pustu->district_id;
                $pasiens->where('districts.id', $districtId); 
            } else {
                $pasiens->where('pasiens.user_id', $currentUser->id);
            }
        } elseif ($currentUser->role !== 'superadmin') {
            $pasiens->where('pasiens.user_id', $currentUser->id);
        }

        $pasiens = $pasiens->orderBy('pasiens.created_at', 'desc')->get();

        return view('pasiens.index', compact('pasiens'));
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
            'name' => 'string|max:255',
            'nik' => 'string|min:16|max:16',
            'alamat' => 'string|max:255',
            'jenis_kelamin' => 'string|max:255',
            'jenis_ktp' => 'string|max:255',
            'tanggal_lahir' => 'date',
            'village_id' => 'string|max:255',
            'district_id' => 'string|max:255',
            'regency_id' => 'string|max:255',
            'province_id' => 'string|max:255',
            'no_wa' => 'string|max:255',
            'keterangan' => 'string|max:255',
            'rt' => 'string|max:255',
            'rw' => 'string|max:255',
        ]);

        $request['pustu_id'] = auth()->user()->pustu_id;
        $request['user_id'] = auth()->user()->id;
        $request['village_id'] = $request->village_search;
        $pasien = Pasien::create($request->all());
        return redirect()->route('pasiens.index')->with('success', 'Created successfully');
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
        // dd($request->all());
        $pasien->update($request->all());
        return redirect()->route('pasiens.index')->with('success', 'Updated successfully');
    }

    public function destroy(Pasien $pasien): \Illuminate\Http\RedirectResponse
    {
        $pasien->delete();
        return redirect()->route('pasiens.index')->with('success', 'Deleted successfully');
    }

    public function autofill(Request $request)
    {
        $search = $request->get('term');
        $field = $request->get('field');


        if (!in_array($field, ['name', 'nik'])) {
            return response()->json([]);
        }

        $pasiens = Pasien::where($field, 'LIKE', '%' . $search . '%')
                        ->limit(10)
                        ->get();

        return response()->json($pasiens);
    }

    public function getPasienByNik(Request $request)
    {
        $search = $request->input('q');

        $pasiens = Pasien::with(['village', 'district', 'regency'])
            ->where(function ($query) use ($search) {
                $query->where('nik', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();



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

}
