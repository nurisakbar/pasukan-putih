<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class KunjunganAwalExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    protected $bulan;
    protected $tanggalAwal;
    protected $tanggalAkhir;
    protected $search;

    public function __construct($bulan = null, $tanggalAwal = null, $tanggalAkhir = null, $search = null)
    {
        $this->bulan = $bulan;
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->search = $search;
    }

    public function collection()
    {
        return \DB::table('pasiens as p')
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

        // Tanggal kunjungan awal
        ->leftJoin(\DB::raw('(
            SELECT pasien_id, MIN(tanggal) as tanggal_awal
            FROM visitings
            GROUP BY pasien_id
        ) as va'), 'va.pasien_id', '=', 'p.id')

        // Join untuk mengambil data kunjungan awal saja - use quote escape
        ->join('visitings as vInit', function($join) {
            $join->on('vInit.pasien_id', '=', 'p.id')
                 ->where('vInit.status', '=', \DB::raw('"kunjungan awal"'));
        })

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
            \DB::raw('va.tanggal_awal as `TANGGAL KUNJUNGAN AWAL`'),
            \DB::raw('hf.skor_aks as `SKOR AKS-DATA SASARAN`'),
            \DB::raw('hf.skor_aks as `SKOR AKS TERAKHIR`'),
            \DB::raw("CASE WHEN hf.tanggal_kunjungan IS NOT NULL THEN 'Ya' ELSE 'Tidak' END as `LANJUT KUNJUNGAN`"),
            \DB::raw('hf.tanggal_kunjungan as `RENCANA KUNJUNGAN LANJUTAN`'),
            \DB::raw("CASE WHEN hf.henti_layanan = 'meninggal' THEN DATE(hf.updated_at) ELSE NULL END as `HENTI LAYANAN-MENINGGAL`"),
            \DB::raw("CASE WHEN hf.henti_layanan = 'menolak' THEN DATE(hf.updated_at) ELSE NULL END as `HENTI-LAYANAN-MENOLAK`"),
            \DB::raw("CASE WHEN hf.henti_layanan = 'pindah_domisili' THEN DATE(hf.updated_at) ELSE NULL END as `HENTI LAYANAN PINDAH-DOMISILI`"),
            \DB::raw('NULL as `RUJUKAN`'),
            \DB::raw('NULL as `KONVERSI DATA KE SASARAN KUNJUNGAN LANJUTAN`')
        )
        // Add the auth filter here
        ->when(\Auth::user()->role === 'perawat', function($query) {
            return $query->where('vInit.user_id', \Auth::id());
        })
        // Add the same filtering conditions from your original method
        ->when($this->bulan, function ($query) {
            return $query->whereMonth('vInit.tanggal', $this->bulan);
        })
        ->when($this->tanggalAwal && $this->tanggalAkhir, function ($query) {
            return $query->whereBetween('vInit.tanggal', [$this->tanggalAwal, $this->tanggalAkhir]);
        })
        ->when($this->search, function ($query) {
            return $query->where(function ($subquery) {
                $subquery->where('p.name', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('p.nik', 'LIKE', '%' . $this->search . '%');
            });
        })
        ->distinct() // Prevent duplicate records
        ->orderBy('r.name')
        ->orderBy('d.name')
        ->orderBy('vil.name')
        ->get();
    }

    public function headings(): array
    {
        return [
            'NO', 'KABUPATEN/KOTA', 'KECAMATAN', 'KELURAHAN', 'NIK', 'JENIS KTP', 'NAMA', 'ALAMAT', 'JENIS KELAMIN', 'UMUR',
            'TANGGAL KUNJUNGAN AWAL', 'SKOR AKS-DATA SASARAN', 'SKOR AKS', 'LANJUT KUNJUNGAN', 'RENCANA KUNJUNGAN LANJUTAN',
            'HENTI LAYANAN-KENAIKAN AKS', 'HENTI LAYANAN-MENINGGAL', 'HENTI LAYANAN-PINDAH DOMISILI', 'RUJUKAN',
            'KONVERSI DATA KE SASARAN KUNJUNGAN LANJUTAN'
        ];
    }
}
