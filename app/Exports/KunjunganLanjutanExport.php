<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KunjunganLanjutanExport implements FromArray, WithHeadings, ShouldAutoSize
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

    public function array(): array
    {
        $kunjungan = DB::table('kunjungans')
            ->select(
                'regencies.name as kabupaten_kota',
                'districts.name as kecamatan',
                'villages.name as kelurahan',
                'pasiens.nik',
                'pasiens.jenis_ktp',
                'pasiens.name',
                'pasiens.alamat',
                'pasiens.jenis_kelamin',
                DB::raw("TIMESTAMPDIFF(YEAR, pasiens.tanggal_lahir, CURDATE()) AS umur"),
                DB::raw("(SELECT MIN(tanggal) FROM kunjungans WHERE pasien_id = kunjungans.pasien_id) AS tanggal_kunjungan_awal"),
                DB::raw("
                    COALESCE(
                        (SELECT MAX(tanggal) FROM kunjungans 
                         WHERE pasien_id = kunjungans.pasien_id),
                        (SELECT MIN(tanggal) FROM kunjungans 
                         WHERE pasien_id = kunjungans.pasien_id)
                    ) AS tanggal_kunjungan_terakhir"),
                DB::raw("(SELECT tanggal FROM kunjungans WHERE pasien_id = kunjungans.pasien_id ORDER BY tanggal DESC LIMIT 1) AS tanggal_kunjungan_lanjutan"),
                DB::raw("(SELECT total_score FROM skrining_adl 
                           WHERE kunjungan_id = (SELECT id FROM kunjungans 
                                                 WHERE pasien_id = kunjungans.pasien_id 
                                                 ORDER BY tanggal ASC LIMIT 1)) AS skor_aks_data_sasaran"),
                DB::raw("
                    COALESCE(
                        (SELECT total_score FROM skrining_adl 
                         WHERE kunjungan_id = (SELECT id FROM kunjungans 
                                               WHERE pasien_id = kunjungans.pasien_id 
                                               ORDER BY tanggal DESC LIMIT 1)),
                        (SELECT total_score FROM skrining_adl 
                         WHERE kunjungan_id = (SELECT id FROM kunjungans 
                                               WHERE pasien_id = kunjungans.pasien_id 
                                               ORDER BY tanggal ASC LIMIT 1))
                    ) AS skor_aks_terakhir"),
                DB::raw("(SELECT total_score FROM skrining_adl 
                           WHERE kunjungan_id = (SELECT id FROM kunjungans 
                                                 WHERE pasien_id = kunjungans.pasien_id 
                                                 ORDER BY tanggal DESC LIMIT 1 OFFSET 1)) AS skor_aks_lanjutan"),
                DB::raw("IF(kunjungans.lanjut_kunjungan = 1, 'IYA', 'TIDAK') AS lanjut_kunjungan"),
                DB::raw("IF(kunjungans.henti_layanan_kenaikan_aks = 1, 'IYA', 'TIDAK') AS henti_layanan_kenaikan_aks"),
                DB::raw("IF(kunjungans.henti_layanan_meninggal = 1, 'IYA', 'TIDAK') AS henti_layanan_meninggal"),
                DB::raw("IF(kunjungans.henti_layanan_menolak = 1, 'IYA', 'TIDAK') AS henti_layanan_menolak"),
                DB::raw("IF(kunjungans.henti_layanan_pindah_domisili = 1, 'IYA', 'TIDAK') AS henti_layanan_pindah_domisili"),
                DB::raw("IF(kunjungans.rujukan = 1, 'IYA', 'TIDAK') AS rujukan"),
                DB::raw("IF(kunjungans.konversi_data_ke_sasaran_kunjungan_lanjutan = 1, 'IYA', 'TIDAK') AS konversi_data_ke_sasaran_kunjungan_lanjutan")
            )
            ->leftJoin('pasiens', 'kunjungans.pasien_id', '=', 'pasiens.id')
            ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
            ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
            ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')

            ->when($this->bulan, function ($query) {
                return $query->whereMonth('kunjungans.tanggal', $this->bulan);
            })
            ->when($this->tanggalAwal, function ($query) {
                return $query->whereDate('kunjungans.tanggal', '>=', $this->tanggalAwal);
            })
            ->when($this->tanggalAkhir, function ($query) {
                return $query->whereDate('kunjungans.tanggal', '<=', $this->tanggalAkhir);
            })
            ->when($this->search, function ($query) {
                return $query->where('pasiens.nik', 'like', '%' . $this->search . '%')
                             ->orWhere('pasiens.name', 'like', '%' . $this->search . '%')
                             ->orWhere('pasiens.alamat', 'like', '%' . $this->search . '%')
                             ->orWhere('pasiens.jenis_ktp', 'like', '%' . $this->search . '%');
            })
            ->orderBy('regencies.name')
            ->orderBy('districts.name')
            ->orderBy('villages.name')
            ->get()
            ->toArray();

        return array_map(function ($data, $index) {
            return array_merge(['no' => $index + 1], (array) $data);
        }, $kunjungan, array_keys($kunjungan));
    }

    public function headings(): array
    {
        return [
            'NO', 'KABUPATEN/KOTA', 'KECAMATAN', 'KELURAHAN', 'NIK', 'JENIS KTP', 'NAMA', 'ALAMAT', 'JENIS KELAMIN',
            'UMUR', 'TANGGAL KUNJUNGAN AWAL', 'TANGGAL KUNJUNGAN TERAKHIR', 'TANGGAL KUNJUNGAN LANJUTAN',
            'SKOR AKS-DATA SASARAN', 'SKOR AKS TERAKHIR', 'SKOR AKS LANJUTAN', 'LANJUT KUNJUNGAN',
            'HENTI LAYANAN-KENAIKAN AKS', 'HENTI LAYANAN-MENINGGAL', 'HENTI-LAYANAN-MENOLAK',
            'HENTI LAYANAN PINDAH-DOMISILI', 'RUJUKAN', 'KONVERSI DATA KE SASARAN KUNJUNGAN LANJUTAN'
        ];
    }
}
