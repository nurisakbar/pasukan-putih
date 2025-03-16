<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\SkriningAdl;
use Carbon\Carbon;

class JumlahSasaranExport implements FromArray, WithHeadings
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

        // Kelompokkan data berdasarkan lokasi
        $groupedData = $data->groupBy(function ($skrining) {
            return $skrining->kunjungan->pasien->regency_id . '|' .
                   $skrining->kunjungan->pasien->district_id . '|' .
                   $skrining->kunjungan->pasien->village_id;
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
