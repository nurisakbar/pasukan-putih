<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SummaryKunjunganAwalExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $bulan;
    protected $tanggalAwal;
    protected $tanggalAkhir;
    protected $search;

    public function __construct($tanggalMulai, $tanggalSelesai, $search = null, $bulan = null)
    {
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalSelesai = $tanggalSelesai;
        $this->search = $search;
        $this->bulan = $bulan;
    }

    public function collection()
    {
        return DB::table('kunjungans as kunjungan')
            ->select(
                'regencies.name as regency_name', // Kabupaten
                'districts.name as district_name', // Kecamatan
                'villages.name as village_name', // Kelurahan
                // JUMLAH SASARAN (Total semua kunjungan per wilayah)
                DB::raw('COUNT(kunjungan.id) as jumlah_sasaran'),
                // JUMLAH WARGA YANG MENDAPAT KUNJUNGAN AWAL
                DB::raw("SUM(CASE WHEN kunjungan.jenis = 'awal' THEN 1 ELSE 0 END) as jumlah_kunjungan_awal"),
                // JUMLAH BUKAN WARGA SASARAN SETELAH KUNJUNGAN AWAL
                DB::raw("SUM(CASE WHEN kunjungan.jenis = 'awal' AND skrining_adl.total_score > 8 THEN 1 ELSE 0 END) as jumlah_bukan_warga_sasaran"),
                // JUMLAH TOTAL WARGA SASARAN SETELAH KUNJUNGAN AWAL
                DB::raw("SUM(CASE WHEN kunjungan.jenis = 'awal' AND skrining_adl.total_score <= 8 THEN 1 ELSE 0 END) as jumlah_total_warga_sasaran")
            )
            ->leftJoin('pasiens as pasien', 'kunjungan.pasien_id', '=', 'pasien.id')
            ->leftJoin('villages', 'pasien.village_id', '=', 'villages.id')
            ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
            ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')
            ->leftJoin('skrining_adl', 'skrining_adl.kunjungan_id', '=', 'kunjungan.id')

            ->when($this->tanggalMulai && $this->tanggalSelesai, function ($query) {
                return $query->whereBetween('kunjungan.tanggal', [$this->tanggalMulai, $this->tanggalSelesai]);
            })

            ->groupBy('regencies.name', 'districts.name', 'villages.name')
            ->orderBy('regencies.name')
            ->orderBy('districts.name')
            ->orderBy('villages.name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Kabupaten/Kota',
            'Kecamatan',
            'Kelurahan',
            'Jumlah Sasaran',
            'Jumlah Warga yang Mendapat Kunjungan Awal',
            'Jumlah Bukan Warga Sasaran Setelah Kunjungan Awal',
            'Jumlah Total Warga Sasaran Setelah Kunjungan Awal',
        ];
    }
}
