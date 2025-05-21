<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Models\Visiting;
use App\Models\HealthForm;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'superadmin') {
            $data['jumlah_data_sasaran'] = Pasien::count();

            $data['jumlah_kunjungan'] = Visiting::count();

            $latestVisitIds = Visiting::selectRaw('MAX(id) as id')
                ->groupBy('pasien_id')
                ->pluck('id');

            $data['jumlah_kunjungan_belum_selesai'] = HealthForm::whereIn('visiting_id', $latestVisitIds)
                ->where('kunjungan_lanjutan', 'ya')
                ->count();

            $data['jumlah_kunjungan_selesai'] = HealthForm::whereIn('visiting_id', $latestVisitIds)
                ->where('kunjungan_lanjutan', 'tidak')
                ->count();
                
        } elseif ($user->role === 'perawat') {
            if ($user->pustu && $user->pustu->jenis_faskes === 'puskesmas') {
                $districtId = $user->pustu->district_id;

                $data['jumlah_data_sasaran'] = Pasien::whereHas('pustu', function($q) use ($districtId) {
                    $q->where('district_id', $districtId);
                })->count();

                $data['jumlah_kunjungan'] = Visiting::whereHas('pasien.pustu', function($q) use ($districtId) {
                    $q->where('district_id', $districtId);
                })->count();

                $data['jumlah_kunjungan_belum_selesai'] = HealthForm::where('kunjungan_lanjutan', 'ya')
                    ->whereHas('visiting.pasien.pustu', function($q) use ($districtId) {
                        $q->where('district_id', $districtId);
                    })->count();

                $data['jumlah_kunjungan_selesai'] = HealthForm::where('kunjungan_lanjutan', 'tidak')
                    ->whereHas('visiting.pasien.pustu', function($q) use ($districtId) {
                        $q->where('district_id', $districtId);
                    })->count();

            } else {
                $data['jumlah_data_sasaran'] = \DB::table('pasiens')
                    ->where('pasiens.user_id', $user->id)
                    ->whereNull('pasiens.deleted_at')
                    ->count();

                $data['jumlah_kunjungan'] = Visiting::where('user_id', $user->id)->count();

                $data['jumlah_kunjungan_belum_selesai'] = HealthForm::where('kunjungan_lanjutan', 'ya')
                    ->whereHas('visiting', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->count();

                $data['jumlah_kunjungan_selesai'] = HealthForm::where('kunjungan_lanjutan', 'tidak')
                    ->whereHas('visiting', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->count();
            }

        } else {
           $data['jumlah_data_sasaran'] = \DB::table('pasiens')
                ->leftJoin('villages', 'villages.id', '=', 'pasiens.village_id')
                ->leftJoin('districts', 'districts.id', '=', 'villages.district_id')
                ->leftJoin('regencies', 'regencies.id', '=', 'districts.regency_id')
                ->where('regencies.id', $user->regency_id)
                ->whereNull('pasiens.deleted_at')
                ->count();

            // Ambil ID pasien yang berada di regency ini
            $pasienIds = \DB::table('pasiens')
                ->leftJoin('villages', 'villages.id', '=', 'pasiens.village_id')
                ->leftJoin('districts', 'districts.id', '=', 'villages.district_id')
                ->leftJoin('regencies', 'regencies.id', '=', 'districts.regency_id')
                ->where('regencies.id', $user->regency_id)
                ->whereNull('pasiens.deleted_at')
                ->pluck('pasiens.id');

            // Ambil ID kunjungan terakhir untuk setiap pasien
            $latestVisitIds = Visiting::whereIn('pasien_id', $pasienIds)
                ->selectRaw('MAX(id) as id')
                ->groupBy('pasien_id')
                ->pluck('id');

            // Jumlah semua kunjungan di regency ini (bisa disesuaikan juga)
            $data['jumlah_kunjungan'] = Visiting::whereIn('pasien_id', $pasienIds)->count();

            // Hitung kunjungan terakhir berdasarkan status kunjungan_lanjutan
            $data['jumlah_kunjungan_belum_selesai'] = HealthForm::whereIn('visiting_id', $latestVisitIds)
                ->where('kunjungan_lanjutan', 'ya')
                ->count();

            $data['jumlah_kunjungan_selesai'] = HealthForm::whereIn('visiting_id', $latestVisitIds)
                ->where('kunjungan_lanjutan', 'tidak')
                ->count();
        }

        return view('home', $data);
    }

}
