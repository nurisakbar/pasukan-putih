<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KunjunganExport;
use App\Exports\SasaranBulananExport;
use App\Exports\JumlahSasaranExport;
use App\Exports\KunjunganAwalExport;
use App\Exports\SummaryKunjunganAwalExport;

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

}
