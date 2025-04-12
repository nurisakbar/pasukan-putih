<?php

namespace App\Exports;

use App\Models\Kunjungan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KunjunganExport implements FromArray, WithHeadings, WithColumnFormatting, ShouldAutoSize
{
    public function array(): array
    {
        return Kunjungan::with('pasien')
            ->get()
            ->map(function ($kunjungan) {
                return [
                    'Tanggal' => $kunjungan->tanggal,
                    'Nama Pasien' => $kunjungan->pasien->name ?? 'Tidak Ada',
                    'NIK' => '`' . $kunjungan->pasien->nik . '`'  ?? 'Tidak Ada',
                    'Jenis Kelamin' => $kunjungan->pasien->jenis_kelamin ?? 'Tidak Ada',
                    'Jenis Kunjungan' => $kunjungan->jenis,
                    'Status' => $kunjungan->status,
                    'Dibuat Pada' => $kunjungan->created_at,
                ];
            })
            ->toArray();
    }

    public function headings(): array
    {
        return [
            'Tanggal', 'Nama Pasien', 'NIK', 'Jenis Kelamin', 'Jenis Kunjungan', 'Status', 'Dibuat Pada'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
