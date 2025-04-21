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

        $data = \DB::table('pasiens as p')
        ->join('villages as vil', 'vil.id', '=', 'p.village_id')
        ->join('districts as d', 'd.id', '=', 'vil.district_id')
        ->join('regencies as r', 'r.id', '=', 'd.regency_id')
    
        // Join untuk visiting terakhir per pasien
        ->leftJoin(\DB::raw('(
            SELECT *
            FROM visitings
            WHERE (pasien_id, tanggal) IN (
                SELECT pasien_id, MAX(tanggal)
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
    
        // Tanggal kunjungan awal
        ->leftJoin(\DB::raw('(
            SELECT pasien_id, MIN(tanggal) as tanggal_awal
            FROM visitings
            GROUP BY pasien_id
        ) as va'), 'va.pasien_id', '=', 'p.id')
    
        // Tanggal kunjungan terakhir
        ->leftJoin(\DB::raw('(
            SELECT pasien_id, MAX(tanggal) as tanggal_akhir
            FROM visitings
            GROUP BY pasien_id
        ) as vt'), 'vt.pasien_id', '=', 'p.id')
    
        // Skor awal
        ->leftJoin(\DB::raw('(
            SELECT hf1.skor_aks, v1.pasien_id
            FROM health_forms hf1
            JOIN visitings v1 ON v1.id = hf1.visiting_id
            WHERE v1.tanggal = (
                SELECT MIN(v2.tanggal)
                FROM visitings v2
                WHERE v2.pasien_id = v1.pasien_id
            )
        ) as skor_awal'), 'skor_awal.pasien_id', '=', 'p.id')
    
        // Skor akhir
        ->leftJoin(\DB::raw('(
            SELECT hf1.skor_aks, v1.pasien_id
            FROM health_forms hf1
            JOIN visitings v1 ON v1.id = hf1.visiting_id
            WHERE v1.tanggal = (
                SELECT MAX(v2.tanggal)
                FROM visitings v2
                WHERE v2.pasien_id = v1.pasien_id
            )
        ) as skor_akhir'), 'skor_akhir.pasien_id', '=', 'p.id')
    
        ->select(
            \DB::raw('NULL as NO'),
            'r.name as KABUPATEN_KOTA',
            'd.name as KECAMATAN',
            'vil.name as KELURAHAN',
            'p.nik as NIK',
            'p.jenis_ktp as JENIS_KTP',
            'p.name as NAMA',
            'p.alamat as ALAMAT',
            'p.jenis_kelamin as JENIS_KELAMIN',
            \DB::raw('TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) as UMUR'),
            \DB::raw('COALESCE(t.weight, (SELECT t2.weight FROM ttvs t2 WHERE t2.kunjungan_id < t.kunjungan_id AND t2.weight IS NOT NULL ORDER BY t2.kunjungan_id DESC LIMIT 1)) as BB'),
        \DB::raw('COALESCE(t.height, (SELECT t2.height FROM ttvs t2 WHERE t2.kunjungan_id < t.kunjungan_id AND t2.height IS NOT NULL ORDER BY t2.kunjungan_id DESC LIMIT 1)) as TB'),
        \DB::raw('COALESCE(t.bmi, (SELECT t2.bmi FROM ttvs t2 WHERE t2.kunjungan_id < t.kunjungan_id AND t2.bmi IS NOT NULL ORDER BY t2.kunjungan_id DESC LIMIT 1)) as IMT'),
            'va.tanggal_awal as TANGGAL_KUNJUNGAN_AWAL',
            'vt.tanggal_akhir as TANGGAL_KUNJUNGAN_TERAKHIR',
            \DB::raw('PERIOD_DIFF(DATE_FORMAT(vt.tanggal_akhir, "%Y%m"), DATE_FORMAT(va.tanggal_awal, "%Y%m")) + 1 as TOTAL_BULAN_KUNJUNGAN'),
            'skor_awal.skor_aks as SKOR_AKS_DATA_SASARAN',
            'skor_akhir.skor_aks as SKOR_AKS_TERAKHIR',
            'hf.skor_aks as SKOR_AKS_LANJUTAN',
            \DB::raw('(
                SELECT hf1.tanggal_kunjungan
                FROM health_forms hf1
                JOIN visitings v1 ON v1.id = hf1.visiting_id
                WHERE v1.pasien_id = p.id
                AND hf1.tanggal_kunjungan IS NOT NULL
                ORDER BY hf1.tanggal_kunjungan DESC
                LIMIT 1
            ) as TANGGAL_KONFIRMASI_LANJUT_KUNJUNGAN'),        
            \DB::raw("CASE WHEN hf.henti_layanan = 'kenaikan_aks' THEN hf.updated_at ELSE NULL END as TANGGAL_HENTI_LAYANAN_KENAIKAN_AKS"),
            \DB::raw("CASE WHEN hf.henti_layanan = 'meninggal' THEN hf.updated_at ELSE NULL END as TANGGAL_HENTI_LAYANAN_MENINGGAL"),
            \DB::raw("CASE WHEN hf.henti_layanan = 'menolak' THEN hf.updated_at ELSE NULL END as TANGGAL_HENTI_LAYANAN_MENOLAK"),
            \DB::raw("CASE WHEN hf.henti_layanan = 'pindah_domisili' THEN hf.updated_at ELSE NULL END as TANGGAL_HENTI_LAYANAN_PINDAH_DOMISILI")
        )
        ->orderBy('p.nik')
        ->get();
    
    return response()->json([
        'data' => $data,
    ]);
     
    }

}
