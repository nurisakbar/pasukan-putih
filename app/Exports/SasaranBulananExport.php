<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class SasaranBulananExport implements FromArray, WithHeadings, ShouldAutoSize
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
        $query = DB::table('visitings')
            ->select(
                'regencies.name as regency_name',
                'districts.name as district_name',
                'villages.name as village_name',
                'pasiens.nik as pasien_nik',
                'pasiens.name as pasien_name',
                'pasiens.jenis_ktp as pasien_jenis_ktp',
                'pasiens.tanggal_lahir as pasien_tanggal_lahir',
                'visitings.tanggal as tanggal_kunjungan',
                'health_forms.skor_aks as skor_aks',
            )
            ->join('pasiens', 'visitings.pasien_id', '=', 'pasiens.id')
            ->join('villages', 'pasiens.village_id', '=', 'villages.id')
            ->join('districts', 'villages.district_id', '=', 'districts.id')
            ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
            ->join('users', 'visitings.user_id', '=', 'users.id')
            ->join('health_forms', 'visitings.id', '=', 'health_forms.visiting_id');

        // Filter jika perawat
        if (Auth::user()->role === 'perawat') {
            $query->where('visitings.user_id', Auth::id());
        }

        // Filter berdasarkan bulan
        if ($this->bulan) {
            $query->whereMonth('visitings.tanggal', $this->bulan);
        }

        // Filter rentang tanggal
        if ($this->tanggalAwal && $this->tanggalAkhir) {
            $query->whereBetween('visitings.tanggal', [
                Carbon::parse($this->tanggalAwal)->startOfDay(),
                Carbon::parse($this->tanggalAkhir)->endOfDay(),
            ]);
        }

        // Filter search by name/nik
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('pasiens.name', 'LIKE', "%{$this->search}%")
                  ->orWhere('pasiens.nik', 'LIKE', "%{$this->search}%");
            });
        }

        // Group by Nama dan Alamat
        $query->groupBy(
            'regencies.name',
            'districts.name',
            'villages.name',
            'pasiens.nik',
            'pasiens.name',
            'pasiens.jenis_ktp',
            'pasiens.tanggal_lahir',
            'visitings.tanggal',
            'health_forms.skor_aks',
        )
        ->orderBy('pasiens.name');

        $data = $query->get();

        return $data->map(function ($row, $index) {
            $umur = $row->pasien_tanggal_lahir
                ? Carbon::parse($row->pasien_tanggal_lahir)->age
                : 'Belum Ada';
        
            $skorAksRaw = $row->skor_aks ?? 'Belum Ada';
            $skorAks = $skorAksRaw === 'Belum Ada' ? $skorAksRaw : strtoupper(str_replace('_', ' ', $skorAksRaw));
        
            $isSasaran = in_array(strtolower($skorAksRaw), ['ketergantungan_berat', 'ketergantungan_total']) ? 'YA' : 'TIDAK';
        
            return [
                'No' => $index + 1,
                'KABUPATEN/KOTA' => $row->regency_name ?? 'Belum Ada',
                'KECAMATAN' => $row->district_name ?? 'Belum Ada',
                'KELURAHAN' => $row->village_name ?? 'Belum Ada',
                'NIK' => '`' . $row->pasien_nik . '`' ?? 'Belum Ada',
                'NAMA' => $row->pasien_name ?? 'Belum Ada',
                'JENIS KTP' => $row->pasien_jenis_ktp ?? 'Belum Ada',
                'UMUR' => $umur,
                'TANGGAL KUNJUNGAN' => $row->tanggal_kunjungan ?? 'Belum Ada',
                'SKOR AKS' => $skorAks,
                'SASARAN' => $isSasaran,
            ];
        })->toArray();
        
    }

    public function headings(): array
    {
        return [
            'NO', 
            'KABUPATEN/KOTA', 
            'KECAMATAN', 
            'KELURAHAN', 
            'NIK', 
            'NAMA', 
            'JENIS KTP', 
            'UMUR', 
            'TANGGAL KUNJUNGAN',
            'SKOR AKS', 
            'SASARAN'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT, // Format kolom NIK (kolom C) sebagai teks
        ];
    }
}
