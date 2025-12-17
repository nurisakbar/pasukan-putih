<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Visiting;
use App\Models\Kunjungan;
use App\Models\Ttv;
use App\Models\SkriningAdl;
use App\Models\HealthForm;
use App\Models\Village;
use App\Models\District;
use App\Models\Regency;
use App\Models\Province;
use App\Models\User;
use App\Models\ExportProgress;
use App\Jobs\ExportPasienWilayahJob;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasienWilayahReportController extends Controller
{
    /**
     * Get data kunjungan dan pemeriksaan terakhir per pasien, dikelompokkan berdasarkan wilayah
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $groupBy = $request->get('group_by', 'district'); // village, district, regency, province
        $wilayahId = $request->get('wilayah_id'); // ID wilayah yang dipilih untuk filter
        
        // Validasi group_by
        if (!in_array($groupBy, ['village', 'district', 'regency', 'province'])) {
            $groupBy = 'district';
        }

        // Base query untuk mendapatkan kunjungan terakhir per pasien
        $latestVisits = $this->getLatestVisits($user, $wilayahId, $groupBy);
        
        // Get pemeriksaan terakhir
        $pemeriksaanTerakhir = $this->getPemeriksaanTerakhir($latestVisits);
        
        // Group by wilayah
        $groupedData = $this->groupByWilayah($latestVisits, $pemeriksaanTerakhir, $groupBy, $wilayahId);

        // Jika request AJAX, return JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $groupedData,
                'group_by' => $groupBy
            ]);
        }

        // Return view dengan data
        return view('reports.pasien-wilayah', [
            'groupedData' => $groupedData,
            'groupBy' => $groupBy,
            'wilayahOptions' => $this->fetchWilayahOptions($user, $groupBy)
        ]);
    }

    /**
     * Get data for DataTable (AJAX)
     */
    public function getData(Request $request)
    {
        $user = auth()->user();
        $groupBy = $request->get('group_by', 'district');
        $wilayahId = $request->get('wilayah_id');

        // Get latest visits
        $latestVisits = $this->getLatestVisits($user, $wilayahId, $groupBy);
        
        // Get pemeriksaan terakhir
        $pemeriksaanTerakhir = $this->getPemeriksaanTerakhir($latestVisits);
        
        // Group by wilayah
        $groupedData = $this->groupByWilayah($latestVisits, $pemeriksaanTerakhir, $groupBy, $wilayahId);

        // Flatten grouped data untuk DataTable
        $flatData = collect();
        foreach ($groupedData as $wilayahKey => $wilayahData) {
            foreach ($wilayahData['pasien'] as $pasien) {
                $flatData->push([
                    'wilayah_name' => $wilayahData['wilayah_name'],
                    'pasien_id' => $pasien['pasien_id'],
                    'nama_pasien' => $pasien['nama_pasien'],
                    'nik' => $pasien['nik'],
                    'alamat' => $pasien['alamat'] ?? '-',
                    'rt' => $pasien['rt'] ?? '-',
                    'rw' => $pasien['rw'] ?? '-',
                    'village_name' => $pasien['village_name'] ?? '-',
                    'district_name' => $pasien['district_name'] ?? '-',
                    'regency_name' => $pasien['regency_name'] ?? '-',
                    'province_name' => $pasien['province_name'] ?? '-',
                    'tanggal_kunjungan' => $pasien['kunjungan']['tanggal'] ?? null,
                    'status_kunjungan' => $pasien['kunjungan']['status'] ?? '-',
                    'ttv' => $pasien['pemeriksaan']['ttv'],
                    'skrining_adl' => $pasien['pemeriksaan']['skrining_adl'],
                    'health_form' => $pasien['pemeriksaan']['health_form'],
                ]);
            }
        }

        // Sort data: by wilayah_name, then nama_pasien, then alamat
        $sortedData = $flatData->sort(function ($a, $b) {
            // First sort by wilayah_name
            $wilayahCompare = strcmp($a['wilayah_name'] ?? '', $b['wilayah_name'] ?? '');
            if ($wilayahCompare !== 0) {
                return $wilayahCompare;
            }
            
            // Then sort by nama_pasien
            $namaCompare = strcmp($a['nama_pasien'] ?? '', $b['nama_pasien'] ?? '');
            if ($namaCompare !== 0) {
                return $namaCompare;
            }
            
            // Finally sort by alamat
            return strcmp($a['alamat'] ?? '', $b['alamat'] ?? '');
        })->values();

        return DataTables::of($sortedData)
            ->addIndexColumn()
            ->addColumn('rt_rw', function ($row) {
                return ($row['rt'] ?? '-') . '/' . ($row['rw'] ?? '-');
            })
            ->addColumn('tanggal_kunjungan_formatted', function ($row) {
                return $row['tanggal_kunjungan'] 
                    ? Carbon::parse($row['tanggal_kunjungan'])->format('d/m/Y')
                    : '-';
            })
            ->addColumn('pemeriksaan_ttv', function ($row) {
                $ttv = $row['ttv'];
                if (!$ttv) return '-';
                
                $parts = [];
                if (isset($ttv['blood_pressure'])) $parts[] = 'TD: ' . $ttv['blood_pressure'];
                if (isset($ttv['pulse'])) $parts[] = 'Nadi: ' . $ttv['pulse'];
                if (isset($ttv['temperature'])) $parts[] = 'Suhu: ' . $ttv['temperature'] . '°C';
                if (isset($ttv['bmi'])) {
                    $bmi = number_format($ttv['bmi'], 1);
                    $category = $ttv['bmi_category'] ?? '';
                    $parts[] = 'BMI: ' . $bmi . ($category ? ' (' . $category . ')' : '');
                }
                return implode(' | ', $parts);
            })
            ->addColumn('pemeriksaan_adl', function ($row) {
                $adl = $row['skrining_adl'];
                if (!$adl) return '-';
                
                $score = $adl['total_score'] ?? '-';
                $parts = ['Skor: ' . $score];
                if (isset($adl['butuh_orang']) && $adl['butuh_orang']) {
                    $parts[] = 'Butuh Orang';
                }
                return implode(' | ', $parts);
            })
            ->addColumn('pemeriksaan_health_form', function ($row) {
                $hf = $row['health_form'];
                if (!$hf) return '-';
                
                $parts = [];
                if (isset($hf['tingkat_kemandirian'])) {
                    $parts[] = $hf['tingkat_kemandirian'];
                }
                return implode(' | ', $parts);
            })
            ->make(true);
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
            // Role lain, filter berdasarkan hirarki user
            $childUserIds = $this->getAllChildUserIds($user->id, $user->role);
            if (!empty($childUserIds)) {
                $query->whereIn('v.user_id', $childUserIds);
            } else {
                // Jika tidak ada child users, hanya tampilkan data user sendiri
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
     * Get pemeriksaan terakhir untuk setiap visiting
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

        // Get TTV terakhir per visiting (jika ada multiple TTV per visiting, ambil yang terakhir)
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
     * Group data berdasarkan wilayah
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
     * Get wilayah options untuk filter dropdown (hanya DKI Jakarta - province_id = 31)
     */
    private function fetchWilayahOptions($user, $groupBy)
    {
        $provinceIdDKI = 31; // DKI Jakarta
        
        switch ($groupBy) {
            case 'village':
                $query = Village::query()
                    ->whereHas('district.regency.province', function($q) use ($provinceIdDKI) {
                        $q->where('id', $provinceIdDKI);
                    });
                    
                if ($user->role !== 'superadmin' && $user->regency_id) {
                    $query->whereHas('district.regency', function($q) use ($user) {
                        $q->where('id', $user->regency_id);
                    });
                }
                return $query->orderBy('name')->get()->map(function($v) {
                    return ['id' => $v->id, 'name' => $v->name];
                });
                
            case 'district':
                $query = District::query()
                    ->whereHas('regency.province', function($q) use ($provinceIdDKI) {
                        $q->where('id', $provinceIdDKI);
                    });
                    
                if ($user->role !== 'superadmin' && $user->regency_id) {
                    $query->where('regency_id', $user->regency_id);
                }
                return $query->orderBy('name')->get()->map(function($d) {
                    return ['id' => $d->id, 'name' => $d->name];
                });
                
            case 'regency':
                $query = Regency::query()
                    ->where('province_id', $provinceIdDKI);
                    
                if ($user->role !== 'superadmin' && $user->regency_id) {
                    $query->where('id', $user->regency_id);
                }
                return $query->orderBy('name')->get()->map(function($r) {
                    return ['id' => $r->id, 'name' => $r->name];
                });
                
            case 'province':
                // Hanya return DKI Jakarta
                return Province::query()
                    ->where('id', $provinceIdDKI)
                    ->orderBy('name')
                    ->get()
                    ->map(function($p) {
                        return ['id' => $p->id, 'name' => $p->name];
                    });
                
            default:
                return collect();
        }
    }

    /**
     * Get all child user IDs based on role hierarchy
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

    /**
     * Get wilayah options via AJAX
     */
    public function getWilayahOptions(Request $request)
    {
        $user = auth()->user();
        $groupBy = $request->get('group_by', 'district');
        
        $options = $this->fetchWilayahOptions($user, $groupBy);
        
        return response()->json([
            'success' => true,
            'options' => $options
        ]);
    }

    /**
     * Export data to Excel menggunakan Job
     */
    public function export(Request $request)
    {
        try {
            $user = auth()->user();
            $groupBy = $request->get('group_by', 'district');
            $wilayahId = $request->get('wilayah_id');
            
            // Generate export ID
            $exportId = 'export_pasien_wilayah_' . time() . '_' . $user->id;

            // Dispatch job
            ExportPasienWilayahJob::dispatch($user->id, $groupBy, $wilayahId, $exportId);

            return response()->json([
                'success' => true,
                'export_id' => $exportId,
                'message' => 'Proses export sedang diproses. Silakan tunggu...'
            ]);

        } catch (\Exception $e) {
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
                    'started_at' => $progress->started_at,
                    'completed_at' => $progress->completed_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
