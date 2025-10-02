<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class JumlahSasaranExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $bulan;

    public function __construct($bulan = null)
    {
        $this->bulan = $bulan;
    }

    public function array(): array
    {
        $query = DB::table('visitings')
            ->select(
                'regencies.name as regency_name',
                'districts.name as district_name',
                'villages.name as village_name',
                DB::raw('COUNT(DISTINCT pasiens.id) as jumlah_sasaran')
            )
            ->join('pasiens', 'visitings.pasien_id', '=', 'pasiens.id')
            ->join('villages', 'pasiens.village_id', '=', 'villages.id')
            ->join('districts', 'villages.district_id', '=', 'districts.id')
            ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
            ->join('users', 'visitings.user_id', '=', 'users.id')
            ->join('health_forms', 'visitings.id', '=', 'health_forms.visiting_id')
            ->whereIn('health_forms.skor_aks', ['ketergantungan_berat', 'ketergantungan_total']);

        // Filter berdasarkan bulan jika diberikan
        if ($this->bulan) {
            $query->whereMonth('visitings.tanggal', Carbon::parse($this->bulan)->month)
                  ->whereYear('visitings.tanggal', Carbon::parse($this->bulan)->year);
        }

        // Filter jika user adalah perawat
        if (Auth::user()->role === 'perawat' || Auth::user()->role === 'operator') {
            $query->where('visitings.user_id', Auth::id());
        }

        // Grouping dan Sorting berdasarkan wilayah
        $result = $query->groupBy(
                'regencies.name',
                'districts.name',
                'villages.name'
            )
            ->orderBy('regencies.name')
            ->orderBy('districts.name')
            ->orderBy('villages.name')
            ->get();

        // Format array untuk export
        return $result->map(function ($row) {
            return [
                'KABUPATEN/KOTA' => $row->regency_name,
                'KECAMATAN' => $row->district_name,
                'KELURAHAN' => $row->village_name,
                'JUMLAH SASARAN' => $row->jumlah_sasaran,
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'KABUPATEN/KOTA', 
            'KECAMATAN', 
            'KELURAHAN', 
            'JUMLAH SASARAN'
        ];
    }
}
