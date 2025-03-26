<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Str;

class UserImport implements ToModel, WithHeadingRow, WithMapping
{
    /**
     * Clean up formula references in cell values
     * 
     * @param mixed $value
     * @return string|null
     */
    private function cleanCellValue($value)
    {
        // Remove formula references like '=SHEET!CELL'
        if (is_string($value) && strpos($value, '=') === 0) {
            // Extract value inside quotes if exists
            preg_match('/\'([^\']+)\'/', $value, $matches);
            return $matches[1] ?? null;
        }
        return $value;
    }

    public function map($row): array
    {
        return [
            'nama_puskesmas_pembantu' => $this->cleanCellValue($row['nama_puskesmas_pembantu'] ?? $row['NAMA PUSKESMAS PEMBANTU'] ?? null),
            'nama_perawat_koordinator' => $this->cleanCellValue($row['nama_perawat_koordinator'] ?? $row['NAMA PERAWAT KOORDINATOR'] ?? null),
            'email' => $this->cleanCellValue($row['email'] ?? $row['EMAIL'] ?? null),
            'nomor_hp' => $this->cleanCellValue($row['nomor_hp'] ?? $row['NOMOR HP'] ?? null),
            'status_pegawai' => $this->cleanCellValue($row['status_pegawai'] ?? $row['STATUS PEGAWAI'] ?? null),
            'keterangan' => $this->cleanCellValue($row['keterangan'] ?? $row['KETERANGAN'] ?? null),
            'kelurahan' => $this->cleanCellValue($row['kelurahan'] ?? $row['KELURAHAN'] ?? null),
            'kecamatan' => $this->cleanCellValue($row['kecamatan'] ?? $row['KECAMATAN'] ?? null),
            'kabupaten_kota' => $this->cleanCellValue($row['kabupaten/kota'] ?? $row['KABUPATEN/KOTA'] ?? null),
        ];
    }

    /**
     * Generate a unique email by appending a number if the email already exists
     * 
     * @param string $email
     * @return string
     */
    private function generateUniqueEmail($email)
    {
        if (empty($email) || !is_string($email) || strpos($email, '@') === false) {
            // Generate a random email if input is invalid
            $email = Str::random(10) . '@gmail.com';
        }
    
        $originalEmail = $email;
        $counter = 1;
    
        while (User::where('email', $email)->exists()) {
            // Safer email splitting
            $parts = explode('@', $originalEmail, 2);
            
            // Ensure we have both username and domain
            $username = $parts[0] ?? Str::random(10);
            $domain = $parts[1] ?? 'example.com';
    
            // Create new email with counter
            $email = $username . $counter . '@' . $domain;
            $counter++;
        }
    
        return $email;
    }

    /**
     * Generate a fallback name if name is null
     * 
     * @param array $row
     * @param string $role
     * @return string
     */
    private function generateFallbackName($row, $role)
    {
        // Try to use email username if available
        if (!empty($row['email'])) {
            $emailUsername = explode('@', $row['email'])[0];
            return ucwords(str_replace(['.', '_'], ' ', $emailUsername)) . ' - ' . ucfirst($role);
        }

        // Generate a random name if no email
        return 'User ' . Str::random(5) . ' - ' . ucfirst($role);
    }

    public function model(array $row)
    {
        \Log::info('Data yang akan dimasukkan:', $row);
        \Log::error('Problematic email generation', [
            'original_input' => $row['email'] ?? 'No email provided',
            'row_data' => $row
        ]);

        $superadminId = '69f6c283-c446-45dd-a552-a25c4110a44b';
        
        // Sanitize and prepare data
        $nomorHp = preg_replace('/\D/', '', $row['nomor_hp'] ?? '');
        
        // Puskesmas Name Handling
        $puskesmasName = !empty($row['nama_puskesmas_pembantu']) 
            ? str_replace('PEMBANTU', '', strtoupper($row['nama_puskesmas_pembantu']))
            : 'Puskesmas ' . Str::random(5);
        $puskesmasName = trim(str_replace(' ', '_', $puskesmasName)); 
        $keterangan = ucwords(strtolower(str_replace('_', ' ', $puskesmasName)));

        // Generate Puskesmas Email
        $puskesmasEmail = $this->generateUniqueEmail(strtolower($puskesmasName) . '@gmail.com');

        // Create Puskesmas User
        $puskesmas = User::firstOrCreate(
            ['email' => $puskesmasEmail],
            [
                'name' => $row['nama_puskesmas_pembantu'] ?? $puskesmasName,
                'role' => 'puskesmas',
                'parent_id' => $superadminId,
                'no_wa' => $nomorHp,
                'keterangan' => $keterangan,
                'password' => Hash::make($puskesmasEmail),
            ]
        );

        // Generate Pustu Email and Name
        $pustuName = $row['nama_puskesmas_pembantu'] 
            ? $row['nama_puskesmas_pembantu'] . ' - Pustu'
            : $this->generateFallbackName($row, 'pustu');
        $pustuEmail = $this->generateUniqueEmail(strtolower(str_replace(' ', '_', $pustuName)) . '@gmail.com');

        // Create Pustu User
        $pustu = User::firstOrCreate(
            ['email' => $pustuEmail],
            [
                'name' => $pustuName,
                'role' => 'pustu',
                'parent_id' => $puskesmas->id,
                'no_wa' => $nomorHp,
                'keterangan' => $keterangan,
                'password' => Hash::make($pustuEmail),
            ]
        );

        // Generate Perawat Email and Name
        $perawatName = !empty($row['nama_perawat_koordinator']) 
            ? $row['nama_perawat_koordinator'] 
            : $this->generateFallbackName($row, 'perawat');
        $perawatEmail = $this->generateUniqueEmail(
            !empty($row['email']) 
                ? $row['email'] 
                : strtolower(str_replace(' ', '_', $perawatName)) . '@gmail.com'
        );

        // Create Perawat User
        return new User([
            'name' => $perawatName,
            'email' => $perawatEmail,
            'no_wa' => $nomorHp,
            'status_pegawai' => $row['status_pegawai'] ?? 'Tidak Diketahui',
            'keterangan' => $row['keterangan'] ?? 'Tidak Ada Keterangan',
            'role' => 'perawat',
            'parent_id' => $pustu->id,
            'password' => Hash::make($perawatEmail),
            'village' => $row['kelurahan'] ?? 'Tidak Diketahui',
            'district' => $row['kecamatan'] ?? 'Tidak Diketahui',
            'regency' => $row['kabupaten_kota'] ?? 'Tidak Diketahui',
        ]);
    }
}