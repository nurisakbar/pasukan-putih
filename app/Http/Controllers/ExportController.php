<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KunjunganExport;
use App\Exports\SasaranBulananExport;
use App\Exports\JumlahSasaranExport;
use App\Exports\KunjunganAwalExport;
use App\Exports\HentiLayananExport;
use App\Exports\SummaryHentiLayananExport;
use App\Exports\KunjunganLanjutanExport;
use App\Exports\SummaryKunjunganLanjutanExport;
use App\Exports\SummaryKunjunganAwalExport;
use App\Exports\KohortHsExport;

class ExportController extends Controller
{
    public function exportSasaranBulanan(Request $request)
    {
        return Excel::download(new SasaranBulananExport(
            $request->input('bulan'),
            $request->input('tanggal_awal'),
            $request->input('tanggal_akhir'),
            $request->input('search')
        ), 'sasaran_bulanan.xlsx');
    }
    public function exportKunjuganAwal(Request $request) 
    {
        return Excel::download(new KunjunganAwalExport(
            $request->input('bulan'),
            $request->input('tanggal_awal'),
            $request->input('tanggal_akhir'),
            $request->input('search')
        ), 'kunjugan_awal.xlsx');
    }

    public function exportKunjunganLanjutan(Request $request) 
    {
        return Excel::download(new KunjunganLanjutanExport(
            $request->input('bulan'),
            $request->input('tanggal_awal'),
            $request->input('tanggal_akhir'),
            $request->input('search')
        ), 'kunjungan_lanjutan.xlsx');
    }

    public function exportSummaryKunjunganLanjutan(Request $request) 
    {
        return Excel::download(new SummaryKunjunganLanjutanExport(
            $request->input('bulan'),
            $request->input('tanggal_awal'),
            $request->input('tanggal_akhir'),
            $request->input('search')
        ), 'summary_kunjungan_lanjutan.xlsx');
    }

    public function exportSummaryKunjunganAwal(Request $request) 
    {
        return Excel::download(new SummaryKunjunganAwalExport(
            $request->input('tanggal_mulai'),
            $request->input('tanggal_selesai'),
            $request->input('search'),
            $request->input('bulan')
        ), 'summary_kunjungan_awal.xlsx');
    }
    
    public function exportJumlahSasaran(Request $request) 
    {
        $bulan = $request->input('bulan');
        return Excel::download(new JumlahSasaranExport($bulan), 'Jumlah-Sasaran.xlsx');
    }

    public function exportHentiLayanan(Request $request)
    {
        $bulan = $request->bulan;
        $tanggalAwal = $request->tanggalAwal;
        $tanggalAkhir = $request->tanggalAkhir;
        $search = $request->search;

        return Excel::download(new HentiLayananExport($bulan, $tanggalAwal, $tanggalAkhir, $search), 'henti_layanan.xlsx');
    }

    public function exportSummaryHentiLayanan(Request $request)
    {
        $bulan = $request->bulan;
        $tanggalAwal = $request->tanggalAwal;
        $tanggalAkhir = $request->tanggalAkhir;
        $search = $request->search;

        return Excel::download(new SummaryHentiLayananExport($bulan, $tanggalAwal, $tanggalAkhir, $search), 'summary_henti_layanan.xlsx');
    }

    public function exportKohortHs(Request $request)
    {
        $bulan = $request->bulan;
        $tanggalAwal = $request->tanggalAwal;
        $tanggalAkhir = $request->tanggalAkhir;
        $search = $request->search;

        return Excel::download(new KohortHsExport($bulan, $tanggalAwal, $tanggalAkhir, $search), 'kohort_hs.xlsx');
    }

    function test()
    {
        // row excel
// 'NO', 
// 'KABUPATEN/KOTA', 
// 'KECAMATAN', 
// 'KELURAHAN', 
// 'NIK', 
// 'JENIS KTP', 
// 'NAMA', 
// 'ALAMAT', 
// 'JENIS KELAMIN', 
// 'UMUR',
// 'BB', 'TB', 'IMT', 
// 'TANGGAL KUNJUNGAN AWAL', 
// 'TANGGAL KUNJUNGAN TERAKHIR', 
// 'TOTAL BULAN KUNJUNGAN',   
// 'SKOR AKS-DATA SASARAN', 
// 'SKOR AKS TERAKHIR', 
// 'SKOR AKS LANJUTAN', 
// 'TANGGAL KONFIRMASI LANJUT KUNJUNGAN',        
// 'TANGGAL-HENTI LAYANAN-KENAIKAN AKS', 
// 'TANGGAL-HENTI LAYANAN-MENINGGAL', 
// 'TANGGAL-HENTI-LAYANAN-MENOLAK',
// 'TANGGAL-HENTI LAYANAN PINDAH-DOMISILI'
        $data = \DB::table('visitings')
            ->join('users', 'users.id', '=', 'visitings.user_id')
            ->join('pasiens', 'pasiens.id', '=', 'visitings.pasien_id')
            ->join('health_forms', 'health_forms.visiting_id', '=', 'visitings.id')
            ->join('villages', 'villages.id', '=', 'pasiens.village_id')
            ->join('districts', 'districts.id', '=', 'villages.district_id')
            ->join('regencies', 'regencies.id', '=', 'districts.regency_id')
            ->join('ttvs', 'ttvs.kunjungan_id', '=', 'visitings.id')
            ->select(
                'visitings.*',
                'villages.name as village_name',
                'districts.name as district_name',
                'regencies.name as regency_name',
                'pasiens.nik as pasien_nik',
                'pasiens.jenis_ktp as pasien_jenis_ktp',
                'pasiens.name as pasien_name',
                'pasiens.alamat as pasien_alamat',
                'pasiens.jenis_kelamin as pasien_jenis_kelamin',
                'pasiens.tanggal_lahir as pasien_tanggal_lahir',
                'ttvs.weight as ttv_weight',
                'ttvs.height as ttv_height',
                'ttvs.bmi as ttv_bmi',
                'health_forms.*'
            )
            ->get();

        return response()->json([
            'data' => $data,
        ]);
            
    }

}
