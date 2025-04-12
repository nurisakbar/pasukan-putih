<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Pasien;

class PasienImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (isset($row[0]) && $row[0] == 'NO') {
            return null; // Mengabaikan baris pertama yang berisi header
        }

        // Pastikan bahwa nilai yang akan dioperasikan dengan floor() adalah numerik
        $nik = isset($row[2]) && is_numeric($row[2]) ? (int)$row[2] : 0; // Misalnya, mengonversi NIK ke int

        return new Pasien([
            'name'          => $row[1] ?? null, // Nama pada kolom 1 (dimulai dari baris kedua)
            'nik'           => $nik, // Pastikan NIK adalah angka
            'alamat'        => $row[3] ?? null, // Alamat pada kolom 3
            'jenis_kelamin' => $row[4] ?? null, // Jenis Kelamin pada kolom 4
            'jenis_ktp'     => $row[5] ?? null, // Jenis KTP pada kolom 5
            'tanggal_lahir' => $row[6] ?? null, // Tanggal Lahir pada kolom 6
        ]);
    }

    public function startRow(): int
    {
        return 2; // Mulai membaca data dari baris ke-2
    }
}
