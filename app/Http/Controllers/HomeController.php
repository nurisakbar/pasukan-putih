<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Models\Visiting;
use App\Models\HealthForm;
use App\Models\User;
use App\Models\District;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
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

        return view('home', $data);
    }

    private function getFilters(Request $request, $user)
    {
        return [
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'district_id' => $request->get('district_id'),
            'village_id' => $request->get('village_id'),
            'data_source' => $request->get('data_source'),
            'user' => $user
        ];
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
                if ($user->pustu && $user->pustu->jenis_faskes === 'puskesmas') {
                    $districtId = $user->pustu->district_id;
                    return [
                        'pasien' => $this->buildPasienQueryWithDistrict($districtId, $filters),
                        'visiting' => $this->buildVisitingQueryWithDistrict($districtId, $filters),
                        'total_pasien' => Pasien::whereHas('pustu', fn($q) => $q->where('district_id', $districtId))->count()
                    ];
                } else {
                    return [
                        'pasien' => $this->buildPasienQueryWithUser($user->id, $filters),
                        'visiting' => $this->buildVisitingQueryWithUser($user->id, $filters),
                        'total_pasien' => Pasien::where(function($q) use ($user) {
                            $q->where('user_id', $user->id)->orWhere('user_id', '-');
                        })->count()
                    ];
                }
                
            default: // regency role
                $regencyId = $user->regency_id;
                return [
                    'pasien' => $this->buildPasienQueryWithRegency($regencyId, $filters),
                    'visiting' => $this->buildVisitingQueryWithRegency($regencyId, $filters),
                    'total_pasien' => Pasien::where(function($q) use ($regencyId) {
                        $q->whereHas('pustu.districts.regency', fn($subQ) => $subQ->where('id', $regencyId))
                          ->orWhere('user_id', '-');
                    })->count()
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

        // Calculate Si Carik and Manual Input data metrics
        $carikData = $this->calculateCarikData($pasienQuery, $visitingQuery, $filters);
        $manualData = $this->calculateManualData($pasienQuery, $visitingQuery, $filters);

        return [
            // Basic counts
            'jumlah_data_sasaran' => $currentPasienCount,
            'jumlah_kunjungan' => $visitingQuery->count(),
            
            // Visit completion status
            'jumlah_kunjungan_belum_selesai' => HealthForm::whereIn('visiting_id', $latestVisitIds)
                ->where('kunjungan_lanjutan', 'ya')->count(),
            'jumlah_kunjungan_selesai' => HealthForm::whereIn('visiting_id', $latestVisitIds)
                ->where('kunjungan_lanjutan', 'tidak')->count(),
            
            // Target data metrics
            'data_sasaran_keseluruhan' => $totalPasien,
            'data_sasaran_saat_ini' => $currentPasienCount,
            'data_sasaran_sudah_dijadwalkan' => $sudahDijadwalkan,
            'data_sasaran_belum_dijadwalkan' => $currentPasienCount - $sudahDijadwalkan,
            'data_sasaran_sudah_dikunjungi' => $sudahDikunjungi,
            'data_sasaran_belum_dikunjungi' => $currentPasienCount - $sudahDikunjungi,
            'data_sasaran_henti_layanan' => $this->getHentiLayananCount($pasienIds),
            
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
        
        if (!empty($filters['district_id'])) {
            $query->whereHas('pustu', fn($q) => $q->where('district_id', $filters['district_id']));
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
        
        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $filters['end_date']);
        }
        if (!empty($filters['district_id'])) {
            $query->whereHas('pasien.pustu', fn($q) => $q->where('district_id', $filters['district_id']));
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
        $query = Pasien::whereHas('pustu', fn($q) => $q->where('district_id', $districtId));
        
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
        $query = Visiting::whereHas('pasien.pustu', fn($q) => $q->where('district_id', $districtId));
        
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
        $query = Visiting::whereHas('pasien', function($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('user_id', '-');
        });
        
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
        $query = Pasien::where(function($q) use ($regencyId) {
            $q->whereHas('pustu.districts.regency', fn($subQ) => $subQ->where('id', $regencyId))
              ->orWhere('user_id', '-');
        });
        
        if (!empty($filters['district_id'])) {
            $query->whereHas('pustu', fn($q) => $q->where('district_id', $filters['district_id']));
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
        $query = Visiting::where(function($q) use ($regencyId) {
            $q->whereHas('pasien.pustu.districts.regency', fn($subQ) => $subQ->where('id', $regencyId))
              ->orWhereHas('pasien', fn($subQ) => $subQ->where('user_id', '-'));
        });
        
        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $filters['end_date']);
        }
        if (!empty($filters['district_id'])) {
            $query->whereHas('pasien.pustu', fn($q) => $q->where('district_id', $filters['district_id']));
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

    private function calculateCarikData($pasienQuery, $visitingQuery, $filters)
    {
        $user = $filters['user'];
        
        // Build SiCarik query based on user role, similar to getBaseQueries logic
        $carikQuery = $this->buildCarikQueryByRole($user, $filters);
        $carikTotalPasien = $carikQuery->count();

        // Get patient IDs for current filter with SiCarik flag
        $carikPasienIds = $carikQuery->pluck('id');
        
        // Calculate scheduled patients for SiCarik data
        $carikScheduledQuery = Visiting::whereIn('pasien_id', $carikPasienIds);
        if (!empty($filters['start_date'])) {
            $carikScheduledQuery->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $carikScheduledQuery->whereDate('tanggal', '<=', $filters['end_date']);
        }
        $carikSudahDijadwalkan = $carikScheduledQuery->distinct('pasien_id')->count();

        // Calculate visited patients for SiCarik data
        $carikVisitedQuery = Visiting::whereIn('pasien_id', $carikPasienIds)
            ->whereHas('ttvs', fn($q) => $q->whereNotNull('temperature'));
        if (!empty($filters['start_date'])) {
            $carikVisitedQuery->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $carikVisitedQuery->whereDate('tanggal', '<=', $filters['end_date']);
        }
        $carikSudahDikunjungi = $carikVisitedQuery->distinct('pasien_id')->count();

        return [
            'total_pasien' => $carikTotalPasien,
            'sudah_dijadwalkan' => $carikSudahDijadwalkan,
            'belum_dijadwalkan' => $carikTotalPasien - $carikSudahDijadwalkan,
            'sudah_dikunjungi' => $carikSudahDikunjungi,
            'belum_dikunjungi' => $carikTotalPasien - $carikSudahDikunjungi,
            'henti_layanan' => $this->getHentiLayananCount($carikPasienIds)
        ];
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
        switch ($user->role) {
            case 'superadmin':
                $query = Pasien::where('flag_sicarik', 1);
                if (!empty($filters['district_id'])) {
                    $query->whereHas('pustu', fn($q) => $q->where('district_id', $filters['district_id']));
                }
                if (!empty($filters['village_id'])) {
                    $query->where('village_id', $filters['village_id']);
                }
                return $query;
                
            case 'perawat':
                if ($user->pustu && $user->pustu->jenis_faskes === 'puskesmas') {
                    $districtId = $user->pustu->district_id;
                    // For SiCarik data, check village->district relationship since SiCarik data doesn't have pustu_id
                    $query = Pasien::where('flag_sicarik', 1)
                        ->whereHas('village.district', fn($q) => $q->where('id', $districtId));
                    if (!empty($filters['village_id'])) {
                        $query->where('village_id', $filters['village_id']);
                    }
                    return $query;
                } else {
                    // For non-puskesmas perawat, include SiCarik data from their district
                    $query = Pasien::where('flag_sicarik', 1);
                    if (!empty($filters['village_id'])) {
                        $query->where('village_id', $filters['village_id']);
                    }
                    return $query;
                }
                
            default: // regency role (sudinkes)
                $regencyId = $user->regency_id;
                // For SiCarik data, we need to check village->district->regency relationship since SiCarik data doesn't have pustu_id
                $query = Pasien::where('flag_sicarik', 1)
                    ->whereHas('village.district.regency', fn($q) => $q->where('id', $regencyId));
                if (!empty($filters['district_id'])) {
                    $query->whereHas('village.district', fn($q) => $q->where('id', $filters['district_id']));
                }
                if (!empty($filters['village_id'])) {
                    $query->where('village_id', $filters['village_id']);
                }
                return $query;
        }
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
                if ($user->pustu && $user->pustu->jenis_faskes === 'puskesmas') {
                    $districtId = $user->pustu->district_id;
                    $query = Pasien::whereHas('pustu', fn($q) => $q->where('district_id', $districtId))
                        ->where(function($q) {
                            $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                        });
                    if (!empty($filters['village_id'])) {
                        $query->where('village_id', $filters['village_id']);
                    }
                    return $query;
                } else {
                    $query = Pasien::where('user_id', $user->id)
                        ->where(function($q) {
                            $q->where('flag_sicarik', 0)->orWhereNull('flag_sicarik');
                        });
                    if (!empty($filters['village_id'])) {
                        $query->where('village_id', $filters['village_id']);
                    }
                    return $query;
                }
                
            default: // regency role (sudinkes)
                $regencyId = $user->regency_id;
                $query = Pasien::whereHas('pustu.districts.regency', fn($q) => $q->where('id', $regencyId))
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
}