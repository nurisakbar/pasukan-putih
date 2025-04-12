<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KunjunganAwalExport implements FromCollection, ShouldAutoSize
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
        $bulan = $this->bulan;
        $tanggalAwal = $this->tanggalAwal;
        $tanggalAkhir = $this->tanggalAkhir;
        $search = $this->search;

        $query = \DB::table('kunjungans as kunjungan')
            ->select(
                'pasien.id as pasien_id',
                'pasien.name as pasien_nama',
                'pasien.alamat as pasien_alamat',
                'pasien.nik as pasien_nik',
                'pasien.jenis_kelamin as pasien_jenis_kelamin',
                'pasien.tanggal_lahir as pasien_tanggal_lahir',
                'villages.name as village_name',
                'districts.name as district_name',
                'regencies.name as regency_name',
                'kunjungan.tanggal as kunjungan_tanggal',
                'kunjungan.skor_aks_data_sasaran as kunjungan_skor_aks_data_sasaran',
                'skrining_adl.total_score as skrining_adl_skor_aks',
                'kunjungan.lanjut_kunjungan as kunjungan_lanjut_kunjungan',
                'kunjungan.rencana_kunjungan_lanjutan as kunjungan_rencana_lanjut_kunjungan',
                'kunjungan.henti_layanan_kenaikan_aks as kunjungan_henti_layanan_kenaikan_aks',
                'kunjungan.henti_layanan_meninggal as kunjungan_henti_layanan_meninggal',
                'kunjungan.henti_layanan_pindah_domisili as kunjungan_henti_layanan_pindah_domisili',
                'kunjungan.rujukan as kunjungan_rujukan',
                'kunjungan.konversi_data_ke_sasaran_kunjungan_lanjutan as kunjungan_konversi_data_ke_sasaran_kunjungan_lanjutan',
            )
            ->leftJoin('pasiens as pasien', 'kunjungan.pasien_id', '=', 'pasien.id')
            ->leftJoin('villages', 'pasien.village_id', '=', 'villages.id')
            ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
            ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')
            ->leftJoin('skrining_adl', 'skrining_adl.kunjungan_id', '=', 'kunjungan.id')
            ->whereNotNull('skrining_adl.total_score')
            ->whereNotNull('kunjungan.skor_aks_data_sasaran')
            ->where('kunjungan.jenis', 'awal');

        // **Filter berdasarkan tanggal jika diberikan**
        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('kunjungan.tanggal', [$tanggalAwal, $tanggalAkhir]);
        }

        // **Filter berdasarkan bulan kunjungan jika diberikan**
        if ($bulan) {
            $query->whereMonth('kunjungan.tanggal', $bulan);
        }

        // **Filter pencarian berdasarkan nama atau NIK pasien jika diberikan**
        if ($search) {
            $query->whereHas('pasien', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        $kunjunganAwal = $query->orderBy('kunjungan.tanggal', 'desc')->get();

        // Tambahkan nomor urut & format data
        return $kunjunganAwal->map(function ($kunjungan, $index) {
            return [
                'NO' => $index + 1,
                'KABUPATEN/KOTA' => $kunjungan->regency_name,
                'KECAMATAN' => $kunjungan->district_name,
                'KELURAHAN' => $kunjungan->village_name,
                'NIK' => $kunjungan->pasien_nik,
                'JENIS KTP' => 'KTP',
                'NAMA' => $kunjungan->pasien_nama,
                'ALAMAT' => $kunjungan->pasien_alamat,
                'JENIS KELAMIN' => $kunjungan->pasien_jenis_kelamin,
                'UMUR' => \Carbon\Carbon::parse($kunjungan->pasien_tanggal_lahir)->age,
                'TANGGAL KUNJUNGAN AWAL' => $kunjungan->kunjungan_tanggal,
                'SKOR AKS-DATA SASARAN' => $kunjungan->kunjungan_skor_aks_data_sasaran,
                'SKOR AKS' => $kunjungan->skrining_adl_skor_aks,
                'LANJUT KUNJUNGAN' => $kunjungan->kunjungan_lanjut_kunjungan ? 'Iya' : 'Tidak',
                'RENCANA KUNJUNGAN LANJUTAN' => $kunjungan->kunjungan_rencana_lanjut_kunjungan,
                'HENTI LAYANAN-KENAIKAN AKS' => $kunjungan->kunjungan_henti_layanan_kenaikan_aks ? 'Iya' : 'Tidak',
                'HENTI LAYANAN-MENINGGAL' => $kunjungan->kunjungan_henti_layanan_meninggal ? 'Iya' : 'Tidak',
                'HENTI LAYANAN-PINDAH DOMISILI' => $kunjungan->kunjungan_henti_layanan_pindah_domisili ? 'Iya' : 'Tidak',
                'RUJUKAN' => $kunjungan->kunjungan_rujukan ? 'Iya' : 'Tidak',
                'KONVERSI DATA KE SASARAN KUNJUNGAN LANJUTAN' => $kunjungan->kunjungan_konversi_data_ke_sasaran_kunjungan_lanjutan ? 'Iya' : 'Tidak',
            ];
        });
    }
}
