<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Models\Visiting;
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
        $data['jumlah_data_sasaran'] = Pasien::where('parent_id', auth()->user()->id)->count();
        $data['jumlah_kunjungan'] = Visiting::where('user_id', auth()->user()->id)->count();

        return view('home', $data);
    }
}
