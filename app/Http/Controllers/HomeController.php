<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Models\Visiting;
use App\Models\HealthForm;
use App\Models\User;
use App\Models\District;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Optimize database connection for this request
        DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        DB::connection()->getPdo()->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        
        $user = auth()->user();
        $filters = $this->getFilters($request, $user);
        
        $queries = $this->getBaseQueries($user, $filters);
        
        $data = $this->calculateMetrics($queries, $filters);
        
        if ($user->role === 'superadmin') {
            $data['districts'] = District::whereHas('regency', function ($query) {
                $query->where('province_id', 31);
            })->orderBy('name')->pluck('name', 'id')->toArray();
        } else {
            $data['districts'] = District::where('regency_id', $user->regency_id)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        }

        // If AJAX request, return only the dashboard content
        if ($request->ajax()) {
            return view('home', $data)->render();
        }

        // Clear memory after processing
        unset($queries, $filters);
        gc_collect_cycles();

        return view('home', $data);
    }

    private function getFilters(Request $request, $user)
    {
        $filters = [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'district_id' => $request->get('district_id'),
            'village_id' => $request->get('village_id'),
            'data_source' => $request->get('data_source'),
            'user' => $user
        ];

        // Auto-set district filter for perawat and operator roles
        if (in_array($user->role, ['perawat', 'operator'])) {
            $districtId = $this->getUserDistrictId($user);
            if ($districtId) {
                $filters['district_id'] = $districtId;
            }
        }

        return $filters;
    }

    private function getBaseQueries($user, $filters)
    {
        switch ($user->role) {
            case 'superadmin':
                return [
                    'pasien' => $this->buildPasienQuery($filters),
                    'visiting' => $this->buildVisitingQuery($filters),
                    'total_pasien' => Pasien::count()
                ];
                
            case 'perawat':
            case 'operator':
                // Gunakan buildPasienQuery() dan buildVisitingQuery() dengan filter district otomatis
                return [
                    'pasien' => $this->buildPasienQuery($filters),
                    'visiting' => $this->buildVisitingQuery($filters),
                    'total_pasien' => $this->getTotalPasienForUser($user, $filters)
                ];
                
            case 'sudinkes':
                $regencyId = $user->regency_id;
                return [
                    'pasien' => $this->buildPasienQueryWithRegency($regencyId, $filters),
                    'visiting' => $this->buildVisitingQueryWithRegency($regencyId, $filters),
                    'total_pasien' => Pasien::whereHas('village.district.regency', fn($q) => $q->where('id', $regencyId))->count()
                ];
                
            default: // other roles
                return [
                    'pasien' => $this->buildPasienQuery($filters),
                    'visiting' => $this->buildVisitingQuery($filters),
                    'total_pasien' => Pasien::count()
                ];
        }
    }

    private function calculateMetrics($queries, $filters)
    {
        $pasienQuery = $queries['pasien'];
        $visitingQuery = $queries['visiting'];
        $totalPasien = $queries['total_pasien'];

        // Get patient IDs for current filter
        $pasienIds = $pasienQuery->pluck('id');
        
        // Calculate scheduled patients - use the same visiting query that's already filtered
        $scheduledQuery = $visitingQuery->clone();
        $sudahDijadwalkan = $scheduledQuery->distinct('pasien_id')->count();

        // Calculate visited patients (those with temperature recorded) - use the same visiting query that's already filtered
        $visitedQuery = $visitingQuery->clone()
            ->whereHas('ttvs', fn($q) => $q->whereNotNull('temperature'));
        $sudahDikunjungi = $visitedQuery->distinct('pasien_id')->count();

        // Get latest visit IDs for completion status
        $latestVisitIds = $this->getLatestVisitIds($visitingQuery, $filters);
        
        // Calculate first visits
        $firstVisitIds = $visitingQuery->clone()
            ->selectRaw('MIN(id) as id')
            ->groupBy('pasien_id')
            ->pluck('id');

        $currentPasienCount = $pasienQuery->count();

        // Calculate Si Carik and Manual Input data metrics using Query Builder
        $carikData = $this->calculateCarikData($pasienQuery, $visitingQuery, $filters);
        $manualData = $this->calculateManualData($pasienQuery, $visitingQuery, $filters);

        return [
            // Basic counts
            'jumlah_data_sasaran' => $carikData['total_pasien'] + $manualData['total_pasien'],
            'jumlah_kunjungan' => $visitingQuery->count(),
            
            // Visit completion status using Query Builder for better performance
            'jumlah_kunjungan_belum_selesai' => $this->getKunjunganStatusCount($latestVisitIds, 'ya'),
            'jumlah_kunjungan_selesai' => $this->getKunjunganStatusCount($latestVisitIds, 'tidak'),
            
            // Target data metrics
            'data_sasaran_keseluruhan' => $carikData['total_pasien'] + $manualData['total_pasien'],
            'data_sasaran_saat_ini' => $currentPasienCount,
            'data_sasaran_sudah_dijadwalkan' => $sudahDijadwalkan,
            'data_sasaran_belum_dijadwalkan' => $carikData['total_pasien'] + $manualData['total_pasien'] - $sudahDijadwalkan,
            'data_sasaran_sudah_dikunjungi' => $sudahDikunjungi,
            'data_sasaran_belum_dikunjungi' => $carikData['total_pasien'] + $manualData['total_pasien'] - $sudahDikunjungi,
            'data_sasaran_henti_layanan' => $this->getHentiLayananCountQueryBuilder($pasienIds),
            
            // Visit type metrics
            'jumlah_kunjungan_awal' => $visitingQuery->clone()->whereIn('id', $firstVisitIds)->count(),
            'jumlah_kunjungan_lanjutan' => $visitingQuery->clone()->whereNotIn('id', $firstVisitIds)->count(),
            'jumlah_henti_layanan' => $visitingQuery->clone()
                ->whereHas('healthForms', fn($q) => $q->where('henti_layanan', 'ya'))->count(),
            
            // Si Carik data metrics
            'carik_data' => $carikData,
            
            // Manual input data metrics
            'manual_data' => $manualData
        ];
    }

    // Base query builders
    private function buildPasienQuery($filters)
    {
        $query = Pasien::query();
        $query->whereNotNull('village_id');
        
        if (!empty($filters['district_id'])) {
            $query->whereHas('village.district', fn($q) => $q->where('id', $filters['district_id']));
        }
        if (!empty($filters['village_id'])) {
            $query->where('village_id', $filters['village_id']);
        }
        if (!empty($filters['data_source'])) {
            if ($filters['data_source'] === 'carik') {
                $query->where('flag_sicarik', 1);
            } elseif ($filters['data_source'] === 'manual') {
                $query->where(function($q) {
                    $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                });
            }
        }
        
        return $query;
    }

    private function buildVisitingQuery($filters)
    {
        $query = Visiting::query();
        $query->whereHas('pasien', function($q) { $q->whereNotNull('village_id'); });
        
        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $filters['end_date']);
        }
        if (!empty($filters['district_id'])) {
            $query->whereHas('pasien.village.district', fn($q) => $q->where('id', $filters['district_id']));
        }
        if (!empty($filters['village_id'])) {
            $query->whereHas('pasien', fn($q) => $q->where('village_id', $filters['village_id']));
        }
        if (!empty($filters['data_source'])) {
            if ($filters['data_source'] === 'carik') {
                $query->whereHas('pasien', fn($q) => $q->where('flag_sicarik', 1));
            } elseif ($filters['data_source'] === 'manual') {
                $query->whereHas('pasien', function($q) {
                    $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                });
            }
        }
        
        return $query;
    }

    // Role-specific query builders
    private function buildPasienQueryWithDistrict($districtId, $filters)
    {
        // Ambil semua pasien dari district ini, baik yang memiliki pustu maupun tidak
        $query = Pasien::whereHas('village.district', fn($q) => $q->where('id', $districtId));
        $query->whereNotNull('village_id');
        
        if (!empty($filters['village_id'])) {
            $query->where('village_id', $filters['village_id']);
        }
        if (!empty($filters['data_source'])) {
            if ($filters['data_source'] === 'carik') {
                $query->where('flag_sicarik', 1);
            } elseif ($filters['data_source'] === 'manual') {
                $query->where(function($q) {
                    $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                });
            }
        }
        
        return $query;
    }

    private function buildVisitingQueryWithDistrict($districtId, $filters)
    {
        // Ambil semua kunjungan dari pasien di district ini, baik yang memiliki pustu maupun tidak
        $query = Visiting::whereHas('pasien.village.district', fn($q) => $q->where('id', $districtId));
        $query->whereHas('pasien', function($q) { $q->whereNotNull('village_id'); });
        
        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $filters['end_date']);
        }
        if (!empty($filters['village_id'])) {
            $query->whereHas('pasien', fn($q) => $q->where('village_id', $filters['village_id']));
        }
        if (!empty($filters['data_source'])) {
            if ($filters['data_source'] === 'carik') {
                $query->whereHas('pasien', fn($q) => $q->where('flag_sicarik', 1));
            } elseif ($filters['data_source'] === 'manual') {
                $query->whereHas('pasien', function($q) {
                    $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                });
            }
        }
        
        return $query;
    }

    private function buildPasienQueryWithUser($userId, $filters)
    {
        $query = Pasien::where(function($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('user_id', '-');
        });
        $query->whereNotNull('village_id');
        
        if (!empty($filters['village_id'])) {
            $query->where('village_id', $filters['village_id']);
        }
        if (!empty($filters['data_source'])) {
            if ($filters['data_source'] === 'carik') {
                $query->where('flag_sicarik', 1);
            } elseif ($filters['data_source'] === 'manual') {
                $query->where(function($q) {
                    $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                });
            }
        }
        
        return $query;
    }

    private function buildVisitingQueryWithUser($userId, $filters)
    {
        $query = Visiting::where(function($q) use ($userId) {
            // Visiting yang dibuat oleh user
            $q->whereHas('pasien', function($pasienQuery) use ($userId) {
                $pasienQuery->where('user_id', $userId)->orWhere('user_id', '-');
            })
            // ATAU visiting yang ditugaskan ke user sebagai operator
            ->orWhere('operator_id', $userId);
        });
        $query->whereHas('pasien', function($q) { $q->whereNotNull('village_id'); });
        
        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $filters['end_date']);
        }
        if (!empty($filters['village_id'])) {
            $query->whereHas('pasien', fn($q) => $q->where('village_id', $filters['village_id']));
        }
        if (!empty($filters['data_source'])) {
            if ($filters['data_source'] === 'carik') {
                $query->whereHas('pasien', fn($q) => $q->where('flag_sicarik', 1));
            } elseif ($filters['data_source'] === 'manual') {
                $query->whereHas('pasien', function($q) {
                    $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                });
            }
        }
        
        return $query;
    }

    private function buildPasienQueryWithRegency($regencyId, $filters)
    {
        // Ambil semua pasien dari regency ini, baik yang memiliki pustu maupun tidak
        $query = Pasien::whereHas('village.district.regency', fn($q) => $q->where('id', $regencyId));
        $query->whereNotNull('village_id');
        
        if (!empty($filters['district_id'])) {
            $query->whereHas('village.district', fn($q) => $q->where('id', $filters['district_id']));
        }
        if (!empty($filters['village_id'])) {
            $query->where('village_id', $filters['village_id']);
        }
        if (!empty($filters['data_source'])) {
            if ($filters['data_source'] === 'carik') {
                $query->where('flag_sicarik', 1);
            } elseif ($filters['data_source'] === 'manual') {
                $query->where(function($q) {
                    $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                });
            }
        }
        
        return $query;
    }

    private function buildVisitingQueryWithRegency($regencyId, $filters)
    {
        // Ambil semua kunjungan dari pasien di regency ini, baik yang memiliki pustu maupun tidak
        $query = Visiting::whereHas('pasien.village.district.regency', fn($q) => $q->where('id', $regencyId));
        $query->whereHas('pasien', function($q) { $q->whereNotNull('village_id'); });
        
        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $filters['end_date']);
        }
        if (!empty($filters['district_id'])) {
            $query->whereHas('pasien.village.district', fn($q) => $q->where('id', $filters['district_id']));
        }
        if (!empty($filters['village_id'])) {
            $query->whereHas('pasien', fn($q) => $q->where('village_id', $filters['village_id']));
        }
        if (!empty($filters['data_source'])) {
            if ($filters['data_source'] === 'carik') {
                $query->whereHas('pasien', fn($q) => $q->where('flag_sicarik', 1));
            } elseif ($filters['data_source'] === 'manual') {
                $query->whereHas('pasien', function($q) {
                    $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                });
            }
        }
        
        return $query;
    }

    // Helper methods
    private function getLatestVisitIds($visitingQuery, $filters)
    {
        $query = $visitingQuery->clone()
            ->selectRaw('MAX(id) as id')
            ->groupBy('pasien_id');
        
        return $query->pluck('id');
    }

    private function getHentiLayananCount($pasienIds)
    {
        $latestVisitIds = Visiting::whereIn('pasien_id', $pasienIds)
            ->selectRaw('MAX(id) as id')
            ->groupBy('pasien_id')
            ->pluck('id');
        
        return HealthForm::whereIn('visiting_id', $latestVisitIds)
            ->where('henti_layanan', '!=', null)
            ->count();
    }

    private function getHentiLayananCountQueryBuilder($pasienIds)
    {
        // Get latest visit IDs using Query Builder
        $latestVisitIds = DB::table('visitings')
            ->select(DB::raw('MAX(id) as id'))
            ->whereIn('pasien_id', $pasienIds)
            ->groupBy('pasien_id')
            ->pluck('id');
        
        if (empty($latestVisitIds)) {
            return 0;
        }
        
        // Count henti layanan using Query Builder
        return DB::table('health_forms')
            ->whereIn('visiting_id', $latestVisitIds)
            ->whereNotNull('henti_layanan')
            ->count();
    }

    private function getKunjunganStatusCount($latestVisitIds, $status)
    {
        if (empty($latestVisitIds)) {
            return 0;
        }
        
        return DB::table('health_forms')
            ->whereIn('visiting_id', $latestVisitIds)
            ->where('kunjungan_lanjutan', $status)
            ->count();
    }

    private function getCarikMetricsOptimized($pasienIds, $filters)
    {
        if (empty($pasienIds)) {
            return [
                'sudah_dijadwalkan' => 0,
                'sudah_dikunjungi' => 0,
                'henti_layanan' => 0
            ];
        }

        $placeholders = str_repeat('?,', count($pasienIds) - 1) . '?';
        $bindings = $pasienIds;
        
        // Build date filters
        $dateFilter = '';
        if (!empty($filters['start_date'])) {
            $dateFilter .= " AND DATE(v.tanggal) >= ?";
            $bindings[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $dateFilter .= " AND DATE(v.tanggal) <= ?";
            $bindings[] = $filters['end_date'];
        }

        // Single optimized query to get all metrics
        $sql = "
            SELECT 
                COUNT(DISTINCT v.pasien_id) as sudah_dijadwalkan,
                COUNT(DISTINCT CASE WHEN t.temperature IS NOT NULL THEN v.pasien_id END) as sudah_dikunjungi,
                COUNT(DISTINCT CASE WHEN hf.henti_layanan IS NOT NULL THEN v.pasien_id END) as henti_layanan
            FROM visitings v
            LEFT JOIN ttvs t ON v.id = t.kunjungan_id
            LEFT JOIN health_forms hf ON v.id = hf.visiting_id
            WHERE v.pasien_id IN ($placeholders)
            $dateFilter
        ";

        $result = DB::selectOne($sql, $bindings);
        
        return [
            'sudah_dijadwalkan' => $result->sudah_dijadwalkan ?? 0,
            'sudah_dikunjungi' => $result->sudah_dikunjungi ?? 0,
            'henti_layanan' => $result->henti_layanan ?? 0
        ];
    }

    private function calculateCarikData($pasienQuery, $visitingQuery, $filters)
    {
        $user = $filters['user'];
        
        // Use cache key based on user and filters for better performance
        $cacheKey = 'carik_data_' . $user->id . '_' . md5(serialize($filters));
        
        return \Cache::remember($cacheKey, 300, function() use ($user, $filters) { // Cache for 5 minutes
            // Get patient IDs using optimized raw SQL
            $carikPasienIds = $this->buildCarikQueryByRole($user, $filters);
            $carikTotalPasien = count($carikPasienIds);
            
            if (empty($carikPasienIds)) {
                return [
                    'total_pasien' => 0,
                    'sudah_dijadwalkan' => 0,
                    'belum_dijadwalkan' => 0,
                    'sudah_dikunjungi' => 0,
                    'belum_dikunjungi' => 0,
                    'henti_layanan' => 0
                ];
            }

            // Extract IDs for IN clause
            $pasienIds = array_column($carikPasienIds, 'id');
            $placeholders = str_repeat('?,', count($pasienIds) - 1) . '?';
            
            // Calculate all metrics in single optimized query
            $metrics = $this->getCarikMetricsOptimized($pasienIds, $filters);

            return [
                'total_pasien' => $carikTotalPasien,
                'sudah_dijadwalkan' => $metrics['sudah_dijadwalkan'],
                'belum_dijadwalkan' => $carikTotalPasien - $metrics['sudah_dijadwalkan'],
                'sudah_dikunjungi' => $metrics['sudah_dikunjungi'],
                'belum_dikunjungi' => $carikTotalPasien - $metrics['sudah_dikunjungi'],
                'henti_layanan' => $metrics['henti_layanan']
            ];
        });
    }

    private function calculateManualData($pasienQuery, $visitingQuery, $filters)
    {
        $user = $filters['user'];
        
        // Build manual query based on user role, similar to getBaseQueries logic
        $manualQuery = $this->buildManualQueryByRole($user, $filters);
        $manualTotalPasien = $manualQuery->count();

        // Get patient IDs for current filter with manual input flag
        $manualPasienIds = $manualQuery->pluck('id');
        
        // Calculate scheduled patients for manual data
        $manualScheduledQuery = Visiting::whereIn('pasien_id', $manualPasienIds);
        if (!empty($filters['start_date'])) {
            $manualScheduledQuery->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $manualScheduledQuery->whereDate('tanggal', '<=', $filters['end_date']);
        }
        $manualSudahDijadwalkan = $manualScheduledQuery->distinct('pasien_id')->count();

        // Calculate visited patients for manual data
        $manualVisitedQuery = Visiting::whereIn('pasien_id', $manualPasienIds)
            ->whereHas('ttvs', fn($q) => $q->whereNotNull('temperature'));
        if (!empty($filters['start_date'])) {
            $manualVisitedQuery->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $manualVisitedQuery->whereDate('tanggal', '<=', $filters['end_date']);
        }
        $manualSudahDikunjungi = $manualVisitedQuery->distinct('pasien_id')->count();

        return [
            'total_pasien' => $manualTotalPasien,
            'sudah_dijadwalkan' => $manualSudahDijadwalkan,
            'belum_dijadwalkan' => $manualTotalPasien - $manualSudahDijadwalkan,
            'sudah_dikunjungi' => $manualSudahDikunjungi,
            'belum_dikunjungi' => $manualTotalPasien - $manualSudahDikunjungi,
            'henti_layanan' => $this->getHentiLayananCount($manualPasienIds)
        ];
    }

    private function buildCarikQueryByRole($user, $filters)
    {
        // Use optimized raw SQL for maximum performance
        $sql = "
            SELECT p.id 
            FROM pasiens p
            INNER JOIN villages v ON p.village_id = v.id
            INNER JOIN districts d ON v.district_id = d.id
            INNER JOIN regencies r ON d.regency_id = r.id
            WHERE p.flag_sicarik = 1 
            AND p.deleted_at IS NULL
            AND p.village_id IS NOT NULL
        ";
        
        $bindings = [];
        
        switch ($user->role) {
            case 'superadmin':
                // No additional filtering for superadmin
                break;
                
            case 'perawat':
            case 'operator':
                $districtId = $this->getUserDistrictId($user);
                if ($districtId) {
                    $sql .= " AND d.id = ?";
                    $bindings[] = $districtId;
                }
                break;
                
            default: // regency role (sudinkes)
                $regencyId = $user->regency_id;
                $sql .= " AND r.id = ?";
                $bindings[] = $regencyId;
                break;
        }

        // Apply additional filters
        if (!empty($filters['district_id'])) {
            $sql .= " AND d.id = ?";
            $bindings[] = $filters['district_id'];
        }
        if (!empty($filters['village_id'])) {
            $sql .= " AND p.village_id = ?";
            $bindings[] = $filters['village_id'];
        }

        return DB::select($sql, $bindings);
    }

    private function buildManualQueryByRole($user, $filters)
    {
        switch ($user->role) {
            case 'superadmin':
                $query = Pasien::where(function($q) {
                    $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                });
                if (!empty($filters['district_id'])) {
                    $query->whereHas('pustu', fn($q) => $q->where('district_id', $filters['district_id']));
                }
                if (!empty($filters['village_id'])) {
                    $query->where('village_id', $filters['village_id']);
                }
                return $query;
                
            case 'perawat':
            case 'operator':
                $districtId = $this->getUserDistrictId($user);
                if ($districtId) {
                    $query = Pasien::whereHas('village.district', fn($q) => $q->where('id', $districtId))
                        ->where(function($q) {
                            $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                        });
                } else {
                    // Fallback to user-based query if no district found
                    $query = Pasien::where('user_id', $user->id)
                        ->where(function($q) {
                            $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                        });
                }
                
                if (!empty($filters['village_id'])) {
                    $query->where('village_id', $filters['village_id']);
                }
                return $query;
                
            default: // regency role (sudinkes)
                $regencyId = $user->regency_id;
                $query = Pasien::whereHas('pustu.districts', fn($q) => $q->where('regency_id', $regencyId))
                    ->where('user_id', '!=', '-') // Exclude SiCarik data
                    ->where(function($q) {
                        $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                    });
                if (!empty($filters['district_id'])) {
                    $query->whereHas('pustu', fn($q) => $q->where('district_id', $filters['district_id']));
                }
                if (!empty($filters['village_id'])) {
                    $query->where('village_id', $filters['village_id']);
                }
                return $query;
        }
    }

    /**
     * Get district ID for user based on their role and pustu
     */
    private function getUserDistrictId($user)
    {
        if ($user->pustu) {
            return $user->pustu->district_id;
        }
        
        // If user has village_id, get district from village
        if ($user->village_id) {
            $village = DB::selectOne("SELECT district_id FROM villages WHERE id = ?", [$user->village_id]);
            return $village ? $village->district_id : null;
        }
        
        return null;
    }

    /**
     * Get total pasien count for user based on their role and district
     */
    private function getTotalPasienForUser($user, $filters)
    {
        $districtId = $this->getUserDistrictId($user);
        
        if ($districtId) {
            return Pasien::whereHas('village.district', fn($q) => $q->where('id', $districtId))->count();
        }
        
        // Fallback to user-based query if no district found
        return Pasien::where(function($q) use ($user) {
            $q->where('user_id', $user->id)->orWhere('user_id', '-');
        })->count();
    }

    /**
     * Clear cache for dashboard data
     * Call this method when data is updated
     */
    public function clearDashboardCache($userId = null)
    {
        if ($userId) {
            // Clear cache for specific user
            $pattern = 'carik_data_' . $userId . '_*';
            $keys = \Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                \Cache::getRedis()->del($keys);
            }
        } else {
            // Clear all dashboard cache
            $pattern = 'carik_data_*';
            $keys = \Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                \Cache::getRedis()->del($keys);
            }
        }
    }
}