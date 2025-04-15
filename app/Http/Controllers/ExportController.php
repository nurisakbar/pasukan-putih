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
        $query = \DB::table('visitings')
            ->select(
                'regencies.name as regency_name',
                'districts.name as district_name',
                'villages.name as village_name',
                \DB::raw('COUNT(DISTINCT visitings.id) as jumlah_sasaran'),
                \DB::raw("COUNT(DISTINCT CASE WHEN visitings.status = 'Kunjungan Awal' AND health_forms.skor_aks IN ('ketergantungan_berat', 'ketergantungan_total') THEN visitings.id END) as jumlah_total_warga_sasaran_setelah_kunjungan_awal"),
                \DB::raw("COUNT(DISTINCT CASE WHEN visitings.status = 'Kunjungan Lanjutan' THEN visitings.id END) as jumlah_kunjungan_lanjutan"),
                \DB::raw("COUNT(DISTINCT CASE WHEN visitings.status = 'Kunjungan Lanjutan' AND health_forms.skor_aks NOT IN ('ketergantungan_berat', 'ketergantungan_total') THEN visitings.id END) as jumlah_bukan_sasaran"),
                \DB::raw("COUNT(DISTINCT CASE WHEN visitings.status = 'Kunjungan Lanjutan' AND health_forms.skor_aks IN ('ketergantungan_berat', 'ketergantungan_total') THEN visitings.id END) as jumlah_sasaran_setelah_kunjungan")
            )
            ->join('pasiens', 'visitings.pasien_id', '=', 'pasiens.id')
            ->join('villages', 'pasiens.village_id', '=', 'villages.id')
            ->join('districts', 'villages.district_id', '=', 'districts.id')
            ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
            ->join('users', 'visitings.user_id', '=', 'users.id')
            ->leftJoin('health_forms', 'visitings.id', '=', 'health_forms.visiting_id');

        // Filter jika user perawat
        if (\Auth::user()->role === 'perawat') {
            $query->where('visitings.user_id', \Auth::id());
        }

        // Group dan order by wilayah
        $query->groupBy(
                'regencies.name',
                'districts.name',
                'villages.name'
            )
            ->orderBy('regencies.name')
            ->orderBy('districts.name')
            ->orderBy('villages.name');

        return $query->get();
    }


}
