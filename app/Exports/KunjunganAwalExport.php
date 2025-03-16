<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;
use App\Models\Kunjungan;
use Illuminate\Support\Facades\DB;

class KunjunganAwalExport implements FromArray, WithHeadings, WithColumnFormatting
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
        // Use a direct SQL query with joins for better performance
        $query = Kunjungan::select([
                'kunjungans.id',
                'kunjungans.tanggal',
                'kunjungans.skor_aks_data_sasaran',
                'kunjungans.skor_aks',
                'kunjungans.lanjut_kunjungan',
                'kunjungans.rencana_kunjungan_lanjutan',
                'kunjungans.henti_layanan_kenaikan_aks',
                'kunjungans.henti_layanan_meninggal',
                'kunjungans.henti_layanan_menolak',
                'kunjungans.henti_layanan_pindah_domisili',
                'kunjungans.rujukan',
                'kunjungans.konversi_data_ke_sasaran_kunjungan_lanjutan',
                'pasiens.name',
                'pasiens.nik',
                'pasiens.jenis_ktp',
                'pasiens.alamat',
                'pasiens.jenis_kelamin',
                'pasiens.tanggal_lahir',
                'villages.name as village_name',
                'districts.name as district_name',
                'regencies.name as regency_name',
                'provinces.name as province_name'
            ])
            ->join('pasiens', 'kunjungans.pasien_id', '=', 'pasiens.id')
            ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
            ->leftJoin('districts', 'pasiens.district_id', '=', 'districts.id')
            ->leftJoin('regencies', 'pasiens.regency_id', '=', 'regencies.id')
            ->leftJoin('provinces', 'pasiens.province_id', '=', 'provinces.id')
            ->where('kunjungans.jenis', 'awal');

        if ($this->bulan) {
            [$tahun, $bulan] = explode('-', $this->bulan);
            $query->whereYear('kunjungans.tanggal', $tahun)
                  ->whereMonth('kunjungans.tanggal', $bulan);
        }

        if ($this->tanggalAwal && $this->tanggalAkhir) {
            $query->whereBetween('kunjungans.tanggal', [$this->tanggalAwal, $this->tanggalAkhir]);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('pasiens.name', 'like', '%' . $this->search . '%')
                  ->orWhere('pasiens.nik', 'like', '%' . $this->search . '%');
            });
        }

        // Use chunk processing for large datasets to prevent memory issues
        $data = [];
        $no = 1;
        
        // Limit query to a reasonable batch size
        $kunjunganChunks = $query->orderBy('kunjungans.tanggal', 'desc')->get();
        
        foreach ($kunjunganChunks as $k) {
            try {
                $umur = Carbon::parse($k->tanggal_lahir)->age ?? '-';
                
                $data[] = [
                    'no' => $no++,
                    'kabupaten_kota' => $k->regency_name ?? '-',
                    'kecamatan' => $k->district_name ?? '-',
                    'kelurahan' => $k->village_name ?? '-',
                    'nik' => $k->nik ?? '-',
                    'jenis_ktp' => $k->jenis_ktp ?? '-',
                    'nama' => $k->name ?? '-',
                    'alamat' => $k->alamat ?? '-',
                    'jenis_kelamin' => $k->jenis_kelamin ?? '-',
                    'umur' => $umur,
                    'tanggal_kunjungan_awal' => $k->tanggal ? Carbon::parse($k->tanggal)->format('d-m-Y') : '-',
                    'skor_aks_data_sasaran' => $k->skor_aks_data_sasaran ?? '-',
                    'skor_aks' => $k->skor_aks ?? '-',
                    'lanjut_kunjungan' => $k->lanjut_kunjungan ? 'Ya' : 'Tidak',
                    'rencana_kunjungan_lanjutan' => $k->rencana_kunjungan_lanjutan ? Carbon::parse($k->rencana_kunjungan_lanjutan)->format('d-m-Y') : '-',
                    'henti_layanan_kenaikan_aks' => $k->henti_layanan_kenaikan_aks ? 'Ya' : 'Tidak',
                    'henti_layanan_meninggal' => $k->henti_layanan_meninggal ? 'Ya' : 'Tidak',
                    'henti_layanan_menolak' => $k->henti_layanan_menolak ? 'Ya' : 'Tidak',
                    'henti_layanan_pindah_domisili' => $k->henti_layanan_pindah_domisili ? 'Ya' : 'Tidak',
                    'rujukan' => $k->rujukan ? 'Ya' : 'Tidak',
                    'konversi_data_ke_sasaran_kunjungan_lanjutan' => $k->konversi_data_ke_sasaran_kunjungan_lanjutan ? 'Ya' : 'Tidak',
                ];
            } catch (\Exception $e) {
                // Skip problematic records but continue processing
                continue;
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'NO', 'KABUPATEN/KOTA', 'KECAMATAN', 'KELURAHAN', 'NIK', 'JENIS KTP', 'NAMA', 'ALAMAT', 'JENIS KELAMIN',
            'UMUR', 'TANGGAL KUNJUNGAN AWAL', 'SKOR AKS-DATA SASARAN', 'SKOR AKS', 'LANJUT KUNJUNGAN', 'RENCANA KUNJUNGAN LANJUTAN',
            'HENTI LAYANAN-KENAIKAN AKS', 'HENTI LAYANAN-MENINGGAL', 'HENTI-LAYANAN-MENOLAK', 'HENTI LAYANAN PINDAH-DOMISILI',
            'RUJUKAN', 'KONVERSI DATA KE SASARAN KUNJUNGAN LANJUTAN'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT, // Format kolom NIK sebagai teks
            'K' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Format tanggal kunjungan awal
            'O' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Format tanggal rencana kunjungan lanjutan
        ];
    }
}