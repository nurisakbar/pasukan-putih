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
            $data['jumlah_kunjungan_belum_selesai'] = HealthForm::where('kunjungan_lanjutan', 'ya')->count();
            $data['jumlah_kunjungan_selesai'] = HealthForm::where('kunjungan_lanjutan', 'tidak')->count();
        } elseif($user->role=='perawat') {
            $data['jumlah_data_sasaran'] = \DB::table('pasiens')
                ->where('pasiens.user_id', $user->id)
                ->where('pasiens.deleted_at', null)
                ->count();
            $data['jumlah_kunjungan'] = Visiting::where('user_id', $user->id)->count();
            $data['jumlah_kunjungan_belum_selesai'] = Visiting::where('selesai', 0)->where('user_id', $user->id)->count();
            $data['jumlah_kunjungan_selesai'] = Visiting::where('selesai', 1)->where('user_id', $user->id)->count();
        }else{
            $data['jumlah_data_sasaran'] = \DB::table('pasiens')
            ->select(
                'pasiens.*',
                'villages.name as village_name',
                'districts.name as district_name',
                'regencies.name as regency_name'
            )
            ->leftjoin('villages', 'villages.id', '=', 'pasiens.village_id')
            ->leftjoin('districts', 'districts.id', '=', 'villages.district_id')
            ->leftjoin('regencies', 'regencies.id', '=', 'districts.regency_id')
            ->where('districts.id', \Auth::user()->district_id)
            ->count();

            $data['jumlah_kunjungan'] = Visiting::where('user_id', $user->id)->count();
            $data['jumlah_kunjungan_belum_selesai'] = Visiting::where('selesai', 0)->where('user_id', $user->id)->count();
            $data['jumlah_kunjungan_selesai'] = Visiting::where('selesai', 1)->where('user_id', $user->id)->count();
        }
        return view('home', $data);
    }
}
