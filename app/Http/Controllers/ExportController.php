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

}
