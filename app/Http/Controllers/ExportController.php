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
        $data = \DB::table('pasiens as p')
        ->join('villages as vil', 'vil.id', '=', 'p.village_id')
        ->join('districts as d', 'd.id', '=', 'vil.district_id')
        ->join('regencies as r', 'r.id', '=', 'd.regency_id')

        // Join untuk visiting terakhir per pasien
        ->leftJoin(\DB::raw('(
            SELECT *
            FROM visitings
            WHERE (pasien_id, tanggal) IN (
                SELECT pasien_id, MIN(tanggal)
                FROM visitings
                GROUP BY pasien_id
            )
        ) as v'), 'v.pasien_id', '=', 'p.id')

        // Join health_form berdasarkan visiting terakhir
        ->leftJoin('health_forms as hf', 'hf.visiting_id', '=', 'v.id')

        // Join ttv berdasarkan kunjungan terakhir
        ->leftJoin('ttvs as t', 't.kunjungan_id', '=', 'v.id')

        // Join user (optional, dari visiting)
        ->leftJoin('users as u', 'u.id', '=', 'v.user_id')

        // Tanggal kunjungan lanjutan
        ->leftJoin(\DB::raw('(
            SELECT pasien_id, MIN(tanggal) as tanggal_lanjutan
            FROM visitings
            WHERE status = "kunjungan lanjutan"
            GROUP BY pasien_id
        ) as vl'), 'vl.pasien_id', '=', 'p.id')

        ->select(
            \DB::raw('ROW_NUMBER() OVER () as NO'),
            \DB::raw('r.name as `KABUPATEN/KOTA`'),
            'd.name as KECAMATAN',
            'vil.name as KELURAHAN',
            \DB::raw("CONCAT('\'', p.nik, '\'') as NIK"), 
            \DB::raw('p.jenis_ktp as `JENIS KTP`'),
            'p.name as NAMA',
            'p.alamat as ALAMAT',
            \DB::raw('p.jenis_kelamin as `JENIS KELAMIN`'),
            \DB::raw('TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) as UMUR'),
            \DB::raw('v.tanggal as `TANGGAL KUNJUNGAN AWAL`'),
            \DB::raw('vl.tanggal_lanjutan as `TANGGAL KUNJUNGAN LANJUTAN`'),
            \DB::raw('hf.skor_aks as `SKOR AKS-DATA SASARAN`'),
            \DB::raw('hf.skor_aks as `SKOR AKS TERAKHIR`'),
            \DB::raw('hf.skor_aks as `SKOR AKS LANJUTAN`'),
            \DB::raw("CASE WHEN hf.tanggal_kunjungan IS NOT NULL THEN 'Ya' ELSE 'Tidak' END as `LANJUT KUNJUNGAN`"),
            \DB::raw("CASE WHEN hf.henti_layanan = 'meninggal' THEN DATE(hf.updated_at) ELSE NULL END as `HENTI LAYANAN-MENINGGAL`"),
            \DB::raw("CASE WHEN hf.henti_layanan = 'menolak' THEN DATE(hf.updated_at) ELSE NULL END as `HENTI-LAYANAN-MENOLAK`"),
            \DB::raw("CASE WHEN hf.henti_layanan = 'pindah_domisili' THEN DATE(hf.updated_at) ELSE NULL END as `HENTI LAYANAN PINDAH-DOMISILI`"),
            \DB::raw('NULL as `RUJUKAN`'),
            \DB::raw('NULL as `KONVERSI DATA KE SASARAN KUNJUNGAN LANJUTAN`')
        )
        ->distinct() // Prevent duplicate records
        ->orderBy('r.name')
        ->orderBy('d.name')
        ->orderBy('vil.name')
        ->get();
    return response()->json([
        'data' => $data,
    ]);
     
    }

}
