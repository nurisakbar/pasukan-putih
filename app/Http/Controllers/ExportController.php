<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KunjunganExport;
use App\Exports\SasaranBulananExport;
use App\Exports\JumlahSasaranExport;
use App\Exports\KunjunganAwalExport;
use App\Exports\KunjunganLanjutanExport;
use App\Exports\SummaryKunjunganLanjutanExport;

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
    public function exportJumlahSasaran(Request $request) 
    {
        $bulan = $request->input('bulan');
        return Excel::download(new JumlahSasaranExport($bulan), 'Jumlah-Sasaran.xlsx');
    }

}
