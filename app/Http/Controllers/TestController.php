<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kunjungan;
use App\Models\Ttv;
use App\Models\Pasien;
use App\Models\User;

class TestController extends Controller
{
    public function getKunjunganAwal()
    {
        $kunjunganAwal = \DB::table('kunjungans as kunjungan')
        ->select(
                    'pasien.id as pasien_id',
                    'pasien.name as pasien_nama',
                    'pasien.alamat as pasien_alamat',
                    'pasien.nik as pasien_nik',
                    'pasien.jenis_kelamin as pasien_jenis_kelamin',
                    'pasien.tanggal_lahir as pasien_tanggal_lahir',
                    'villages.name as village_name',
                    'districts.name as district_name',
                    'regencies.name as regency_name',
                    'kunjungan.tanggal as kunjungan_tanggal',
                    'kunjungan.skor_aks_data_sasaran as kunjungan_skor_aks_data_sasaran',
                    'skrining_adl.total_score as skrining_adl_skor_aks',
                    'kunjungan.lanjut_kunjungan as kunjungan_lanjut_kunjungan',
                    'kunjungan.rencana_kunjungan_lanjutan as kunjungan_rencana_lanjut_kunjungan',
                    'kunjungan.henti_layanan_kenaikan_aks as kunjungan_henti_layanan_kenaikan_aks',
                    'kunjungan.henti_layanan_meninggal as kunjungan_henti_layanan_meninggal',
                    'kunjungan.henti_layanan_pindah_domisili as kunjungan_henti_layanan_pindah_domisili',
                    'kunjungan.rujukan as kunjungan_rujukan',
                    'kunjungan.konversi_data_ke_sasaran_kunjungan_lanjutan as kunjungan_konversi_data_ke_sasaran_kunjungan_lanjutan',
                )
                ->leftJoin('pasiens as pasien', 'kunjungan.pasien_id', '=', 'pasien.id')
                ->leftJoin('villages', 'pasien.village_id', '=', 'villages.id')
                ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
                ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')
                ->leftJoin('skrining_adl', 'skrining_adl.kunjungan_id', '=', 'kunjungan.id')
                ->where('skrining_adl.total_score', '!=', null)     
                ->where('kunjungan.skor_aks_data_sasaran', '!=', null)
                ->where('kunjungan.jenis', 'awal')           
                ->orderBy('kunjungan.tanggal', 'desc')
                ->get();

        return response()->json($kunjunganAwal);
    }
}
