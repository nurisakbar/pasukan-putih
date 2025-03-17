<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kunjungan;
use App\Models\Ttv;
use App\Models\Pasien;
use App\Models\User;

class TestController extends Controller
{
    public function getDetailKunjungan(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
    
        $kunjungan = \DB::table('kunjungans')
            ->select(
                'regencies.name', // Kabupaten/Kota
                'districts.name', // Kecamatan
                'villages.name', // Kelurahan
                'pasiens.nik',
                'pasiens.jenis_ktp',
                'pasiens.name',
                'pasiens.alamat',
                'pasiens.jenis_kelamin',
                \DB::raw("TIMESTAMPDIFF(YEAR, pasiens.tanggal_lahir, CURDATE())"),
    
                // Tanggal kunjungan awal (kunjungan pertama pasien)
                \DB::raw("(SELECT MIN(tanggal) FROM kunjungans WHERE pasien_id = kunjungans.pasien_id)"),
    
                // Tanggal kunjungan terakhir (jika NULL, ambil dari kunjungan awal)
                \DB::raw("
                    COALESCE(
                        (SELECT MAX(tanggal) FROM kunjungans 
                         WHERE pasien_id = kunjungans.pasien_id),
                        (SELECT MIN(tanggal) FROM kunjungans 
                         WHERE pasien_id = kunjungans.pasien_id)
                    )"),
    
                // Tanggal kunjungan lanjutan (kunjungan terbaru)
                \DB::raw("(SELECT tanggal FROM kunjungans WHERE pasien_id = kunjungans.pasien_id ORDER BY tanggal DESC LIMIT 1)"),
    
                // Skor AKS-DATA SASARAN (dari skrining ADL di kunjungan pertama)
                \DB::raw("(SELECT total_score FROM skrining_adl 
                           WHERE kunjungan_id = (SELECT id FROM kunjungans 
                                                 WHERE pasien_id = kunjungans.pasien_id 
                                                 ORDER BY tanggal ASC LIMIT 1))"),
    
                // Skor AKS TERAKHIR (jika NULL, ambil dari skor pertama)
                \DB::raw("
                    COALESCE(
                        (SELECT total_score FROM skrining_adl 
                         WHERE kunjungan_id = (SELECT id FROM kunjungans 
                                               WHERE pasien_id = kunjungans.pasien_id 
                                               ORDER BY tanggal DESC LIMIT 1)),
                        (SELECT total_score FROM skrining_adl 
                         WHERE kunjungan_id = (SELECT id FROM kunjungans 
                                               WHERE pasien_id = kunjungans.pasien_id 
                                               ORDER BY tanggal ASC LIMIT 1))
                    )"),
    
                // Skor AKS LANJUTAN (dari skrining ADL di kunjungan kedua terakhir, jika ada)
                \DB::raw("(SELECT total_score FROM skrining_adl 
                           WHERE kunjungan_id = (SELECT id FROM kunjungans 
                                                 WHERE pasien_id = kunjungans.pasien_id 
                                                 ORDER BY tanggal DESC LIMIT 1 OFFSET 1))"),
    
                'kunjungans.lanjut_kunjungan',
                'kunjungans.henti_layanan_kenaikan_aks',
                'kunjungans.henti_layanan_meninggal',
                'kunjungans.henti_layanan_menolak',
                'kunjungans.henti_layanan_pindah_domisili',
                'kunjungans.rujukan',
                'kunjungans.konversi_data_ke_sasaran_kunjungan_lanjutan'
            )
            ->leftJoin('pasiens', 'kunjungans.pasien_id', '=', 'pasiens.id')
            ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
            ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
            ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')
    
            // Filter berdasarkan tanggal kunjungan
            ->when($tanggalMulai && $tanggalSelesai, function ($query) use ($tanggalMulai, $tanggalSelesai) {
                return $query->whereBetween('kunjungans.tanggal', [$tanggalMulai, $tanggalSelesai]);
            })
    
            ->orderBy('regencies.name')
            ->orderBy('districts.name')
            ->orderBy('villages.name')
            ->get();
    
        return response()->json($kunjungan);
    }
    
}
