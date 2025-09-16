<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PasienExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $pasiens;

    public function __construct($pasiens)
    {
        $this->pasiens = $pasiens;
    }

    public function collection()
    {
        return $this->pasiens;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NIK',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Alamat',
            'RT/RW',
            'Provinsi',
            'Kabupaten/Kota',
            'Kecamatan',
            'Kelurahan',
            'Tanggal Dibuat'
        ];
    }

    public function map($pasien): array
    {
        static $counter = 0;
        $counter++;

        return [
            $counter,
            $pasien->name,
            $pasien->nik,
            $pasien->jenis_kelamin,
            $pasien->tanggal_lahir ? \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d/m/Y') : '',
            $pasien->alamat,
            $pasien->rt . '/' . $pasien->rw,
            $pasien->province_name,
            $pasien->regency_name,
            $pasien->district_name,
            $pasien->village_name,
            \Carbon\Carbon::parse($pasien->created_at)->format('d/m/Y H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 25,  // Nama
            'C' => 20,  // NIK
            'D' => 15,  // Jenis Kelamin
            'E' => 15,  // Tanggal Lahir
            'F' => 30,  // Alamat
            'G' => 10,  // RT/RW
            'H' => 20,  // Provinsi
            'I' => 20,  // Kabupaten/Kota
            'J' => 20,  // Kecamatan
            'K' => 20,  // Kelurahan
            'L' => 20,  // Tanggal Dibuat
        ];
    }
}