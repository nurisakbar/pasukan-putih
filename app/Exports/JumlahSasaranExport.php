<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\SkriningAdl;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class JumlahSasaranExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $bulan;

    public function __construct($bulan = null)
    {
        $this->bulan = $bulan;
    }

    public function array(): array
    {
        $query = SkriningAdl::with('kunjungan.pasien');

        // Filter berdasarkan bulan jika diberikan
        if ($this->bulan) {
            $query->whereHas('kunjungan', function ($q) {
                $q->whereMonth('tanggal', Carbon::parse($this->bulan)->month)
                  ->whereYear('tanggal', Carbon::parse($this->bulan)->year);
            });
        }

        // Ambil data yang menjadi sasaran home service (sasaran_home_service = 1)
        $data = $query->where('sasaran_home_service', 1)->get();

        $groupedData = $data->groupBy(function ($skrining) {
            $pasien = $skrining->kunjungan?->pasien;

            $regencyName = $pasien?->regency?->name ?? 'Tidak Diketahui';
            $districtName = $pasien?->district?->name ?? 'Tidak Diketahui';
            $villageName = $pasien?->village?->name ?? 'Tidak Diketahui';

            return "$regencyName|$districtName|$villageName";
        });


        // Format data untuk export
        $formattedData = $groupedData->map(function ($items, $key) {
            $first = $items->first();
            list($regency, $district, $village) = explode('|', $key);

            return [
                'KABUPATEN/KOTA' => $regency,
                'KECAMATAN' => $district,
                'KELURAHAN' => $village,
                'JUMLAH SASARAN' => $items->count(),
            ];
        })->values()->toArray();

        return $formattedData;
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
