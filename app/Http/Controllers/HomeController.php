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
        
        // Get base queries based on user role
        $queries = $this->getBaseQueries($user, $filters);
        
        // Calculate all metrics using the base queries
        $data = $this->calculateMetrics($queries, $filters);
        
        if ($user->role === 'superadmin') {
            // Superadmin sees all districts in the province
            $data['districts'] = District::whereHas('regency', function ($query) {
                $query->where('province_id', 31);
            })->orderBy('name')->pluck('name', 'id')->toArray();
        } else {
            $data['districts'] = District::where('regency_id', $user->regency_id)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
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
                        'total_pasien' => Pasien::where('user_id', $user->id)->count()
                    ];
                }
                
            default: // regency role
                $regencyId = $user->regency_id;
                return [
                    'pasien' => $this->buildPasienQueryWithRegency($regencyId, $filters),
                    'visiting' => $this->buildVisitingQueryWithRegency($regencyId, $filters),
                    'total_pasien' => Pasien::whereHas('pustu.districts.regency', fn($q) => $q->where('id', $regencyId))->count()
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
        
        // Calculate scheduled patients
        $scheduledQuery = Visiting::whereIn('pasien_id', $pasienIds);
        if (!empty($filters['start_date'])) {
            $scheduledQuery->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $scheduledQuery->whereDate('tanggal', '<=', $filters['end_date']);
        }
        $sudahDijadwalkan = $scheduledQuery->distinct('pasien_id')->count();

        // Calculate visited patients (those with temperature recorded)
        $visitedQuery = Visiting::whereIn('pasien_id', $pasienIds)
            ->whereHas('ttvs', fn($q) => $q->whereNotNull('temperature'));
        if (!empty($filters['start_date'])) {
            $visitedQuery->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $visitedQuery->whereDate('tanggal', '<=', $filters['end_date']);
        }
        $sudahDikunjungi = $visitedQuery->distinct('pasien_id')->count();

        // Get latest visit IDs for completion status
        $latestVisitIds = $this->getLatestVisitIds($visitingQuery, $filters);
        
        // Calculate first visits
        $firstVisitIds = $visitingQuery->clone()
            ->selectRaw('MIN(id) as id')
            ->groupBy('pasien_id')
            ->pluck('id');

        $currentPasienCount = $pasienQuery->count();

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
                ->whereHas('healthForms', fn($q) => $q->where('henti_layanan', 'ya'))->count()
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
        
        return $query;
    }

    // Role-specific query builders
    private function buildPasienQueryWithDistrict($districtId, $filters)
    {
        $query = Pasien::whereHas('pustu', fn($q) => $q->where('district_id', $districtId));
        
        if (!empty($filters['village_id'])) {
            $query->where('village_id', $filters['village_id']);
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
        
        return $query;
    }

    private function buildPasienQueryWithUser($userId, $filters)
    {
        $query = Pasien::where('user_id', $userId);
        
        if (!empty($filters['village_id'])) {
            $query->where('village_id', $filters['village_id']);
        }
        
        return $query;
    }

    private function buildVisitingQueryWithUser($userId, $filters)
    {
        $query = Visiting::whereHas('pasien', fn($q) => $q->where('user_id', $userId));
        
        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal', '<=', $filters['end_date']);
        }
        if (!empty($filters['village_id'])) {
            $query->whereHas('pasien', fn($q) => $q->where('village_id', $filters['village_id']));
        }
        
        return $query;
    }

    private function buildPasienQueryWithRegency($regencyId, $filters)
    {
        $query = Pasien::whereHas('pustu.districts.regency', fn($q) => $q->where('id', $regencyId));
        
        if (!empty($filters['district_id'])) {
            $query->whereHas('pustu', fn($q) => $q->where('district_id', $filters['district_id']));
        }
        if (!empty($filters['village_id'])) {
            $query->where('village_id', $filters['village_id']);
        }
        
        return $query;
    }

    private function buildVisitingQueryWithRegency($regencyId, $filters)
    {
        $query = Visiting::whereHas('pasien.pustu.districts.regency', fn($q) => $q->where('id', $regencyId));
        
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
}