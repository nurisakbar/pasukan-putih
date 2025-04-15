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
        } else {
            $data['jumlah_data_sasaran'] = Pasien::where('parent_id', $user->id)->count();
            $data['jumlah_kunjungan'] = Visiting::where('user_id', $user->id)->count();
            $data['jumlah_kunjungan_belum_selesai'] = HealthForm::where('kunjungan_lanjutan', 'ya')->where('user_id', $user->id)->count();
            $data['jumlah_kunjungan_selesai'] = HealthForm::where('kunjungan_lanjutan', 'tidak')->where('user_id', $user->id)->count();
        }
        return view('home', $data);
    }
}
