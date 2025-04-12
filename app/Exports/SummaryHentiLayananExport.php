<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SummaryHentiLayananExport implements FromCollection, WithHeadings, ShouldAutoSize
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
                DB::raw('COUNT(DISTINCT k.pasien_id) as jumlah_sasaran'),
                DB::raw('SUM(CASE WHEN k.henti_layanan_kenaikan_aks = 1 THEN 1 ELSE 0 END) as total_henti_kenaikan_aks'),
                DB::raw('SUM(CASE WHEN k.henti_layanan_meninggal = 1 THEN 1 ELSE 0 END) as total_henti_meninggal'),
                DB::raw('SUM(CASE WHEN k.henti_layanan_menolak = 1 THEN 1 ELSE 0 END) as total_henti_menolak'),
                DB::raw('SUM(CASE WHEN k.henti_layanan_pindah_domisili = 1 THEN 1 ELSE 0 END) as total_henti_pindah_domisili')
            )
            ->leftJoin('pasiens as p', 'k.pasien_id', '=', 'p.id')
            ->leftJoin('villages', 'p.village_id', '=', 'villages.id')
            ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
            ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')

            ->when($this->bulan, function ($query) {
                return $query->whereMonth('k.tanggal', $this->bulan);
            })
            ->when($this->tanggalAwal && $this->tanggalAkhir, function ($query) {
                return $query->whereBetween('k.tanggal', [$this->tanggalAwal, $this->tanggalAkhir]);
            })
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
            'KABUPATEN/KOTA', 'KECAMATAN', 'KELURAHAN', 'JUMLAH SASARAN',
            'TOTAL HENTI LAYANAN - KENAIKAN AKS', 'TOTAL HENTI LAYANAN - MENINGGAL',
            'TOTAL HENTI LAYANAN - MENOLAK', 'TOTAL HENTI LAYANAN - PINDAH DOMISILI'
        ];
    }
}
