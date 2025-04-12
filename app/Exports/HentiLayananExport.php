<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HentiLayananExport implements FromCollection, WithHeadings, ShouldAutoSize
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
                DB::raw('ROW_NUMBER() OVER () AS no'),
                'regencies.name as kabupaten_kota',
                'districts.name as kecamatan',
                'villages.name as kelurahan',
                'p.nik',
                'p.jenis_ktp',
                'p.name as nama',
                'p.alamat',
                'p.jenis_kelamin',
                DB::raw('TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) as umur'),
                DB::raw('ANY_VALUE((SELECT MIN(tanggal) FROM kunjungans WHERE pasien_id = k.pasien_id)) as tanggal_kunjungan_awal'),
                DB::raw('ANY_VALUE((SELECT MAX(tanggal) FROM kunjungans WHERE pasien_id = k.pasien_id)) as tanggal_kunjungan_terakhir'),
                DB::raw('ANY_VALUE((SELECT tanggal FROM kunjungans WHERE pasien_id = k.pasien_id ORDER BY tanggal DESC LIMIT 1)) as tanggal_kunjungan_lanjutan'),
                DB::raw("(SELECT total_score FROM skrining_adl WHERE kunjungan_id = (SELECT id FROM kunjungans WHERE pasien_id = k.pasien_id ORDER BY tanggal ASC LIMIT 1)) as skor_aks_data_sasaran"),
                DB::raw("(SELECT total_score FROM skrining_adl WHERE kunjungan_id = (SELECT id FROM kunjungans WHERE pasien_id = k.pasien_id ORDER BY tanggal DESC LIMIT 1)) as skor_aks_terakhir"),
                DB::raw("(SELECT total_score FROM skrining_adl WHERE kunjungan_id = (SELECT id FROM kunjungans WHERE pasien_id = k.pasien_id ORDER BY tanggal DESC LIMIT 1 OFFSET 1)) as skor_aks_lanjutan"),
                'k.lanjut_kunjungan',
                DB::raw("(SELECT tanggal FROM kunjungans WHERE pasien_id = k.pasien_id AND henti_layanan_kenaikan_aks = 1 LIMIT 1) as tanggal_henti_layanan_kenaikan_aks"),
                DB::raw("(SELECT tanggal FROM kunjungans WHERE pasien_id = k.pasien_id AND henti_layanan_meninggal = 1 LIMIT 1) as tanggal_henti_layanan_meninggal"),
                DB::raw("(SELECT tanggal FROM kunjungans WHERE pasien_id = k.pasien_id AND henti_layanan_menolak = 1 LIMIT 1) as tanggal_henti_layanan_menolak"),
                DB::raw("(SELECT tanggal FROM kunjungans WHERE pasien_id = k.pasien_id AND henti_layanan_pindah_domisili = 1 LIMIT 1) as tanggal_henti_layanan_pindah_domisili")
            )
            ->leftJoin('pasiens as p', 'k.pasien_id', '=', 'p.id')
            ->leftJoin('villages', 'p.village_id', '=', 'villages.id')
            ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
            ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')

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
            ->orderBy('regencies.name')
            ->orderBy('districts.name')
            ->orderBy('villages.name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'NO', 'KABUPATEN/KOTA', 'KECAMATAN', 'KELURAHAN', 'NIK', 'JENIS KTP', 'NAMA', 'ALAMAT', 'JENIS KELAMIN',
            'UMUR', 'TANGGAL KUNJUNGAN AWAL', 'TANGGAL KUNJUNGAN TERAKHIR', 'TANGGAL KUNJUNGAN LANJUTAN',
            'SKOR AKS-DATA SASARAN', 'SKOR AKS TERAKHIR', 'SKOR AKS LANJUTAN', 'LANJUT KUNJUNGAN',
            'TANGGAL-HENTI LAYANAN-KENAIKAN AKS', 'TANGGAL-HENTI LAYANAN-MENINGGAL', 'TANGGAL-HENTI-LAYANAN-MENOLAK',
            'TANGGAL-HENTI LAYANAN PINDAH-DOMISILI'
        ];
    }
}
