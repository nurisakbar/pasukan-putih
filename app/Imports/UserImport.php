<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Str;

class UserImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    // Cache of existing emails to reduce DB queries
    private $existingEmails = [];
    
    // Superadmin ID (constant to avoid repetition)
    private const SUPERADMIN_ID = '69f6c283-c446-45dd-a552-a25c4110a44b';
    
    /**
     * Process Excel file in chunks
     */
    public function chunkSize(): int
    {
        return 100; // Process 100 rows at a time
    }
    
    /**
     * Batch insert users for better performance
     */
    public function batchSize(): int
    {
        return 100;
    }
    
    /**
     * Clean up formula references in cell values
     * 
     * @param mixed $value
     * @return string|null
     */
    private function cleanCellValue($value)
    {
        if (is_null($value)) {
            return null;
        }
        
        if (!is_string($value)) {
            return (string)$value;
        }
        
        // Remove formula references like '=SHEET!CELL'
        if (strpos($value, '=') === 0) {
            // Extract value inside quotes if exists
            preg_match('/\'([^\']+)\'/', $value, $matches);
            return $matches[1] ?? null;
        }
        
        return trim($value);
    }
    
    /**
     * Normalize field names to handle different formats
     */
    private function getFieldValue($row, $fieldNames)
    {
        foreach ($fieldNames as $field) {
            if (isset($row[$field]) && !empty($row[$field])) {
                return $this->cleanCellValue($row[$field]);
            }
        }
        return null;
    }
    
    /**
     * Map row to normalized data structure
     */
    private function processRow($row): array
    {
        return [
            'nama_puskesmas_pembantu' => $this->getFieldValue($row, ['nama_puskesmas_pembantu', 'NAMA PUSKESMAS PEMBANTU']),
            'nama_perawat_koordinator' => $this->getFieldValue($row, ['nama_perawat_koordinator', 'NAMA PERAWAT KOORDINATOR']),
            'email' => $this->getFieldValue($row, ['email', 'EMAIL']),
            'nomor_hp' => $this->getFieldValue($row, ['nomor_hp', 'NOMOR HP']),
            'status_pegawai' => $this->getFieldValue($row, ['status_pegawai', 'STATUS PEGAWAI']),
            'keterangan' => $this->getFieldValue($row, ['keterangan', 'KETERANGAN']),
            'kelurahan' => $this->getFieldValue($row, ['kelurahan', 'KELURAHAN']),
            'kecamatan' => $this->getFieldValue($row, ['kecamatan', 'KECAMATAN']),
            'kabupaten_kota' => $this->getFieldValue($row, ['kabupaten/kota', 'KABUPATEN/KOTA']),
        ];
    }
    
    /**
     * Check if email exists in database or cache
     */
    private function emailExists($email)
    {
        // First check the cache
        if (isset($this->existingEmails[$email])) {
            return true;
        }
        
        // Then check the database
        $exists = DB::table('users')->where('email', $email)->exists();
        
        // Cache the result
        if ($exists) {
            $this->existingEmails[$email] = true;
        }
        
        return $exists;
    }
    
    /**
     * Generate a unique email that isn't already in use
     */
    private function generateUniqueEmail($input, $type = 'default')
    {
        // Handle empty input
        if (empty($input)) {
            $input = $type . '_' . substr(uniqid(), -6);
        }
        
        // For perawat with email
        if ($type === 'perawat' && !empty($input) && strpos($input, '@') !== false) {
            $originalEmail = strtolower($input);
            $parts = explode('@', $originalEmail);
            $username = $parts[0];
            $domain = $parts[1] ?? 'gmail.com';
            
            $counter = 1;
            $email = $originalEmail;
            
            while ($this->emailExists($email)) {
                $email = $username . '.' . $counter . '@' . $domain;
                $counter++;
            }
            
            $this->existingEmails[$email] = true;
            return $email;
        }
        
        // Sanitize the input for puskesmas and pustu
        $cleanInput = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($input));
        if (empty($cleanInput)) {
            $cleanInput = $type . substr(uniqid(), -6);
        }
        
        $username = $cleanInput . '.' . substr($type, 0, 1);
        $domain = 'gmail.com';
        $email = $username . '@' . $domain;
        $counter = 1;
        
        while ($this->emailExists($email)) {
            $email = $username . '.' . $counter . '@' . $domain;
            $counter++;
        }
        
        $this->existingEmails[$email] = true;
        return $email;
    }
    
    /**
     * Generate a proper name from available data
     */
    private function generateProperName($input, $type, $fallbackData = [])
    {
        if (!empty($input)) {
            return ucwords(strtolower($input));
        }
        
        // Try to use location information
        $locationParts = [];
        foreach (['kelurahan', 'kecamatan'] as $key) {
            if (!empty($fallbackData[$key])) {
                $locationParts[] = ucwords(strtolower($fallbackData[$key]));
            }
        }
        
        if (!empty($locationParts)) {
            return ucfirst($type) . ' ' . implode(' ', $locationParts);
        }
        
        // Use email username if available
        if (!empty($fallbackData['email'])) {
            $emailUsername = explode('@', $fallbackData['email'])[0];
            return ucwords(str_replace(['.', '_'], ' ', $emailUsername)) . ' - ' . ucfirst($type);
        }
        
        // Last resort
        return ucfirst($type) . ' ' . substr(uniqid(), -5);
    }
    
    /**
     * Process the collection in chunks
     */
    public function collection(Collection $rows)
    {
        // Pre-cache existing emails to reduce DB calls
        $existingUsers = DB::table('users')->select('email')->get();
        foreach ($existingUsers as $user) {
            $this->existingEmails[$user->email] = true;
        }
        
        // Prepare data for batch insertion
        $usersToInsert = [];
        
        foreach ($rows as $row) {
            $data = $this->processRow($row);
            
            // Sanitize and prepare common data
            $nomorHp = preg_replace('/\D/', '', $data['nomor_hp'] ?? '');
            
            // Create puskesmas user
            $puskesmasName = $this->generateProperName(
                $data['nama_puskesmas_pembantu'],
                'puskesmas',
                $data
            );
            $puskesmasEmail = $this->generateUniqueEmail($puskesmasName, 'puskesmas');
            $puskesmasId = Str::uuid()->toString();
            
            // Check if the puskesmas already exists
            $existingPuskesmas = DB::table('users')
                ->where('email', $puskesmasEmail)
                ->orWhere(function($query) use ($puskesmasName) {
                    $query->where('name', $puskesmasName)
                          ->where('role', 'puskesmas');
                })
                ->first();
            
            if (!$existingPuskesmas) {
                $usersToInsert[] = [
                    'id' => $puskesmasId,
                    'name' => $puskesmasName,
                    'email' => $puskesmasEmail,
                    'role' => 'puskesmas',
                    'parent_id' => self::SUPERADMIN_ID,
                    'no_wa' => $nomorHp,
                    'keterangan' => $data['keterangan'] ?? $puskesmasName,
                    'password' => Hash::make('puskesmas123'),
                    'status_pegawai' => $data['status_pegawai'],
                    'village' => $village ?? null,
                    'district' => $district ?? null,
                    'regency' => $regency ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                $puskesmasId = $existingPuskesmas->id;
            }
            
            // Create pustu user
            $pustuName = $this->generateProperName(
                $data['nama_puskesmas_pembantu'] ? $data['nama_puskesmas_pembantu'] . ' Pustu' : null,
                'pustu',
                $data
            );
            $pustuEmail = $this->generateUniqueEmail($pustuName, 'pustu');
            $pustuId = Str::uuid()->toString();
            
            // Check if the pustu already exists under this puskesmas
            $existingPustu = DB::table('users')
                ->where('parent_id', $puskesmasId)
                ->where(function($query) use ($pustuName, $pustuEmail) {
                    $query->where('email', $pustuEmail)
                          ->orWhere('name', $pustuName);
                })
                ->where('role', 'pustu')
                ->first();
            
            if (!$existingPustu) {
                $usersToInsert[] = [
                    'id' => $pustuId,
                    'name' => $pustuName,
                    'email' => $pustuEmail,
                    'role' => 'pustu',
                    'parent_id' => $puskesmasId,
                    'no_wa' => $nomorHp,
                    'keterangan' => $data['keterangan'] ?? $pustuName,
                    'password' => Hash::make('pustu123'),
                    'status_pegawai' => $data['status_pegawai'],
                    'village' => $village ?? null,
                    'district' => $district ?? null,
                    'regency' => $regency ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                $pustuId = $existingPustu->id;
            }
            
            // Create perawat user
            $perawatName = $this->generateProperName(
                $data['nama_perawat_koordinator'],
                'perawat',
                $data
            );
            $perawatEmail = $this->generateUniqueEmail(
                !empty($data['email']) ? $data['email'] : $perawatName,
                'perawat'
            );
            
            // Check if perawat already exists under this pustu
            $existingPerawat = DB::table('users')
                ->where('parent_id', $pustuId)
                ->where(function($query) use ($perawatName, $perawatEmail, $data) {
                    $query->where('email', $perawatEmail)
                          ->orWhere('name', $perawatName)
                          ->orWhere('email', $data['email']);
                })
                ->where('role', 'perawat')
                ->first();
            
            if (!$existingPerawat) {
                $usersToInsert[] = [
                    'id' => Str::uuid()->toString(),
                    'name' => $perawatName,
                    'email' => $perawatEmail,
                    'no_wa' => $nomorHp,
                    'status_pegawai' => $data['status_pegawai'] ?? null,
                    'keterangan' => $data['keterangan'] ?? null,
                    'role' => 'perawat',
                    'parent_id' => $pustuId,
                    'password' => Hash::make('perawat123'),
                    'village' => $data['kelurahan'] ?? null,
                    'district' => $data['kecamatan'] ?? null,
                    'regency' => $data['kabupaten_kota'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Insert users in chunks for better performance
        $chunks = array_chunk($usersToInsert, 50);
        foreach ($chunks as $chunk) {
            DB::table('users')->insert($chunk);
        }
    }
}