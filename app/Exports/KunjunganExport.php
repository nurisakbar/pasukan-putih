<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class KunjunganExport implements FromArray, WithHeadings, WithColumnFormatting, ShouldAutoSize
{
    public function array(): array
    {
        $query = DB::table('visitings')
            ->select(
                'visitings.tanggal as tanggal',
                'pasiens.name as pasien_name',
                'pasiens.nik as pasien_nik',
                'pasiens.jenis_kelamin as pasien_jenis_kelamin',
                'visitings.status as jenis_kunjungan',
                'health_forms.kunjungan_lanjutan as status',
                'visitings.created_at as dibuat_pada',
            )
            ->join('pasiens', 'visitings.pasien_id', '=', 'pasiens.id')
            ->join('users', 'visitings.user_id', '=', 'users.id')
            ->join('health_forms', 'visitings.id', '=', 'health_forms.visiting_id');

        if (Auth::user()->role === 'perawat') {
            $query->where('visitings.user_id', Auth::id());
        }

        $visitings = $query->get();

        return $visitings->map(function ($v) {
            $status = 'Belum Ada';

            if (!is_null($v->status)) {
                $status = strtolower($v->status) === 'ya' ? 'Lanjut' : 'Berhenti';
            }
            return [
                'Tanggal' => $v->tanggal,
                'Nama Pasien' => $v->pasien_name ?? 'Tidak Ada',
                'NIK' => '`' . ($v->pasien_nik ?? 'Tidak Ada') . '`',
                'Jenis Kelamin' => $v->pasien_jenis_kelamin ?? 'Tidak Ada',
                'Jenis Kunjungan' => $v->jenis_kunjungan ?? 'Tidak Ada',
                'Status' => $status,
                'Dibuat Pada' => $v->dibuat_pada,
            ];
        })->toArray();
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
            'C' => NumberFormat::FORMAT_TEXT, // Format kolom NIK (kolom C) sebagai teks
        ];
    }
}
