<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SummaryKunjunganLanjutanExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        return DB::table('kunjungans as k')
            ->select(
                'regencies.name as kabupaten_kota',
                'districts.name as kecamatan',
                'villages.name as kelurahan',
                DB::raw('COUNT(k.id) as jumlah_sasaran'),
                DB::raw("SUM(CASE WHEN k.jenis = 'awal' THEN 1 ELSE 0 END) as jumlah_total_warga_sasaran_setelah_kunjungan_awal"),
                DB::raw("SUM(CASE WHEN k.jenis = 'lanjutan' THEN 1 ELSE 0 END) as jumlah_warga_yang_mendapat_kunjungan_lanjutan"),
                DB::raw("SUM(CASE WHEN k.jenis = 'lanjutan' AND s.total_score > 8 THEN 1 ELSE 0 END) as jumlah_bukan_warga_sasaran_setelah_kunjungan_lanjutan"),
                DB::raw("SUM(CASE WHEN k.jenis = 'lanjutan' AND s.total_score <= 8 THEN 1 ELSE 0 END) as jumlah_total_warga_sasaran_setelah_kunjungan_lanjutan")
            )
            ->leftJoin('pasiens as p', 'k.pasien_id', '=', 'p.id')
            ->leftJoin('villages', 'p.village_id', '=', 'villages.id')
            ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
            ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')
            ->leftJoin('skrining_adl as s', 's.kunjungan_id', '=', 'k.id')

            // Filter berdasarkan bulan
            ->when($this->bulan, function ($query) {
                return $query->whereMonth('k.tanggal', $this->bulan);
            })

            // Filter berdasarkan rentang tanggal
            ->when($this->tanggalAwal && $this->tanggalAkhir, function ($query) {
                return $query->whereBetween('k.tanggal', [$this->tanggalAwal, $this->tanggalAkhir]);
            })

            // Filter berdasarkan pencarian (misalnya nama atau NIK)
            ->when($this->search, function ($query) {
                return $query->where(function ($subquery) {
                    $subquery->where('p.name', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('p.nik', 'LIKE', '%' . $this->search . '%');
                });
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
            'KABUPATEN/KOTA',
            'KECAMATAN',
            'KELURAHAN',
            'JUMLAH SASARAN',
            'JUMLAH TOTAL WARGA SASARAN SETELAH KUNJUNGAN AWAL',
            'JUMLAH WARGA YANG MENDAPAT KUNJUNGAN LANJUTAN',
            'JUMLAH BUKAN WARGA SASARAN SETELAH KUNJUNGAN LANJUTAN',
            'JUMLAH TOTAL WARGA SASARAN SETELAH KUNJUNGAN LANJUTAN',
        ];
    }
}
