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
use Illuminate\Support\Facades\Log;

class UserImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    // Cache of existing emails to reduce DB queries
    private $existingEmails = [];
    
    // Import log data
    private $importLog = [
        'puskesmas' => 0,
        'pustu' => 0,
        'perawat' => 0,
        'skipped' => 0,
        'total' => 0
    ];
    
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
        return 50;
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
        
        // Remove any numeric prefixes (e.g., "4. KOTA ADM...")
        $value = preg_replace('/^\d+\.\s*/', '', $value);
        
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
            'nomor_hp' => $this->cleanCellValue($this->getFieldValue($row, ['nomor_hp', 'NOMOR HP'])),
            'status_pegawai' => $this->getFieldValue($row, ['status_pegawai', 'STATUS PEGAWAI']),
            'keterangan' => $this->getFieldValue($row, ['keterangan', 'KETERANGAN']),
            'kelurahan' => $this->getFieldValue($row, ['kelurahan', 'KELURAHAN']),
            'kecamatan' => $this->getFieldValue($row, ['kecamatan', 'KECAMATAN']),
            'kabupaten_kota' => $this->getFieldValue($row, ['kabupaten/kota', 'kabupaten_kota', 'KABUPATEN/KOTA']),
        ];
    }
    
    /**
     * Sanitize phone number
     */
    private function sanitizePhoneNumber($number)
    {
        if (empty($number)) {
            return null;
        }
        
        // Remove any non-digit characters like '*'
        $cleaned = preg_replace('/\D/', '', $number);
        
        // Ensure it starts with proper country code if it doesn't have one
        if (strlen($cleaned) > 0 && $cleaned[0] !== '+') {
            if (strpos($cleaned, '62') === 0) {
                return $cleaned;
            } else if (strpos($cleaned, '0') === 0) {
                return '62' . substr($cleaned, 1);
            } else {
                return '62' . $cleaned;
            }
        }
        
        return $cleaned;
    }
    
    /**
     * Check if email exists in database or cache
     */
    private function emailExists($email)
    {
        if (empty($email)) {
            return false;
        }
        
        $email = strtolower(trim($email));
        
        // Check in cache first
        if (isset($this->existingEmails[$email])) {
            return true;
        }
        
        // Then check database
        $exists = DB::table('users')->where('email', $email)->exists();
        
        // Cache the result
        if ($exists) {
            $this->existingEmails[$email] = true;
        }
        
        return $exists;
    }
    
    /**
     * Find user by criteria
     */
    private function findUser($criteria)
    {
        $query = DB::table('users');
        
        foreach ($criteria as $field => $value) {
            if ($field === 'name_like') {
                $query->where('name', 'LIKE', '%' . $value . '%');
            } else {
                $query->where($field, $value);
            }
        }
        
        return $query->first();
    }
    
    /**
     * Generate a unique slug for email generation
     */
    private function generateSlug($input)
    {
        if (empty($input)) {
            return substr(uniqid(), -8);
        }
        
        // Transliterate to ASCII and remove all non-alphanumeric characters
        $slug = preg_replace('/[^a-z0-9]/', '', 
               strtolower(
                   str_replace(' ', '', 
                       preg_replace('/\s+/', ' ', trim($input))
                   )
               )
           );
        
        return $slug ?: substr(uniqid(), -8);
    }
    
    /**
     * Generate a unique email that isn't already in use
     */
    private function generateUniqueEmail($name, $role, $originalEmail = null)
    {
        // If a valid email is provided, try to use it first
        if (!empty($originalEmail) && filter_var($originalEmail, FILTER_VALIDATE_EMAIL)) {
            $email = strtolower($originalEmail);
            
            // Check if this exact email exists
            if (!$this->emailExists($email)) {
                return $email;
            }
            
            // If it exists, we'll generate a new one based on the original
            $parts = explode('@', $email);
            $username = $parts[0];
            $domain = $parts[1] ?? 'gmail.com';
            
            $counter = 1;
            while ($counter < 10) { // Limit attempts to prevent infinite loops
                $newEmail = $username . '.' . $counter . '@' . $domain;
                if (!$this->emailExists($newEmail)) {
                    return $newEmail;
                }
                $counter++;
            }
        }
        
        // Generate a new email based on the name and role
        $slug = $this->generateSlug($name);
        $rolePrefix = substr($role, 0, 1);
        $emailBase = $slug . '.' . $rolePrefix . '@gmail.com';
        
        if (!$this->emailExists($emailBase)) {
            return $emailBase;
        }
        
        // Add counter if already exists
        $counter = 1;
        while ($counter < 10) { // Limit attempts
            $email = $slug . '.' . $rolePrefix . '.' . $counter . '@gmail.com';
            if (!$this->emailExists($email)) {
                return $email;
            }
            $counter++;
        }
        
        // Last resort - use random string
        return $slug . '.' . $rolePrefix . '.' . substr(uniqid(), -8) . '@gmail.com';
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
     * Safely insert a single record with duplicate checking
     */
    private function safeInsert($userData)
    {
        try {
            // Check for duplicate email
            if ($this->emailExists($userData['email'])) {
                Log::warning('Skipping duplicate email: ' . $userData['email']);
                $this->importLog['skipped']++;
                return null;
            }
            
            // Insert the record
            DB::table('users')->insert($userData);
            
            // Cache the email
            $this->existingEmails[$userData['email']] = true;
            
            return $userData['id'];
        } catch (\Exception $e) {
            Log::warning('Error inserting user: ' . $e->getMessage(), [
                'email' => $userData['email'],
                'name' => $userData['name']
            ]);
            $this->importLog['skipped']++;
            return null;
        }
    }
    
    /**
     * Process the collection in chunks
     */
    public function collection(Collection $rows)
    {
        // Pre-cache existing emails to reduce DB calls
        $existingEmails = DB::table('users')->select('email')->get();
        foreach ($existingEmails as $user) {
            $this->existingEmails[strtolower($user->email)] = true;
        }
        
        // Process each row individually for better error handling
        foreach ($rows as $index => $row) {
            try {
                $data = $this->processRow($row);
                
                // Skip empty rows
                if (empty($data['nama_puskesmas_pembantu']) && empty($data['nama_perawat_koordinator'])) {
                    continue;
                }
                
                $this->importLog['total']++;
                
                // Sanitize phone number
                $data['nomor_hp'] = $this->sanitizePhoneNumber($data['nomor_hp'] ?? null);
                
                // STEP 1: Check if puskesmas exists or create it
                $puskesmasName = $this->generateProperName(
                    $data['nama_puskesmas_pembantu'],
                    'puskesmas',
                    $data
                );
                
                $existingPuskesmas = $this->findUser([
                    'role' => 'puskesmas',
                    'parent_id' => self::SUPERADMIN_ID,
                    'name_like' => $data['nama_puskesmas_pembantu']
                ]);
                
                if ($existingPuskesmas) {
                    $puskesmasId = $existingPuskesmas->id;
                } else {
                    $puskesmasEmail = $this->generateUniqueEmail($puskesmasName, 'puskesmas');
                    $puskesmasId = Str::uuid()->toString();
                    
                    $puskesmasId = $this->safeInsert([
                        'id' => $puskesmasId,
                        'name' => $puskesmasName,
                        'email' => $puskesmasEmail,
                        'role' => 'puskesmas',
                        'parent_id' => self::SUPERADMIN_ID,
                        'no_wa' => $data['nomor_hp'] ?? null,
                        'keterangan' => $data['keterangan'] ?? $puskesmasName,
                        'password' => Hash::make('puskesmas123'),
                        'status_pegawai' => $data['status_pegawai'] ?? null,
                        'village' => $data['kelurahan'] ?? null,
                        'district' => $data['kecamatan'] ?? null,
                        'regency' => $data['kabupaten_kota'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    if ($puskesmasId) {
                        $this->importLog['puskesmas']++;
                    } else {
                        // If puskesmas insertion failed, skip this row
                        continue;
                    }
                }
                
                // STEP 2: Check if pustu exists or create it
                $pustuName = $this->generateProperName(
                    !empty($data['nama_puskesmas_pembantu']) ? $data['nama_puskesmas_pembantu'] . ' Pustu' : null,
                    'pustu',
                    $data
                );
                
                $existingPustu = $this->findUser([
                    'role' => 'pustu',
                    'parent_id' => $puskesmasId,
                    'name_like' => $data['nama_puskesmas_pembantu']
                ]);
                
                if ($existingPustu) {
                    $pustuId = $existingPustu->id;
                } else {
                    $pustuEmail = $this->generateUniqueEmail($pustuName, 'pustu');
                    $pustuId = Str::uuid()->toString();
                    
                    $pustuId = $this->safeInsert([
                        'id' => $pustuId,
                        'name' => $pustuName,
                        'email' => $pustuEmail,
                        'role' => 'pustu',
                        'parent_id' => $puskesmasId,
                        'no_wa' => $data['nomor_hp'] ?? null,
                        'keterangan' => $data['keterangan'] ?? $pustuName,
                        'password' => Hash::make('pustu123'),
                        'status_pegawai' => $data['status_pegawai'] ?? null,
                        'village' => $data['kelurahan'] ?? null,
                        'district' => $data['kecamatan'] ?? null,
                        'regency' => $data['kabupaten_kota'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    if ($pustuId) {
                        $this->importLog['pustu']++;
                    } else {
                        // If pustu insertion failed, skip perawat creation
                        continue;
                    }
                }
                
                // STEP 3: Check if perawat exists or create it
                // Only create perawat if the name exists
                if (!empty($data['nama_perawat_koordinator'])) {
                    $perawatName = $this->generateProperName(
                        $data['nama_perawat_koordinator'],
                        'perawat',
                        $data
                    );
                    
                    // Check if perawat exists using exact name or using original email
                    $existingPerawat = null;
                    if (!empty($data['email'])) {
                        $existingPerawat = $this->findUser([
                            'email' => $data['email']
                        ]);
                    }
                    
                    if (!$existingPerawat) {
                        $existingPerawat = $this->findUser([
                            'role' => 'perawat',
                            'parent_id' => $pustuId,
                            'name' => $perawatName
                        ]);
                    }
                    
                    if (!$existingPerawat) {
                        // Generate unique email
                        $perawatEmail = $this->generateUniqueEmail(
                            $perawatName,
                            'perawat',
                            $data['email'] ?? null
                        );
                        
                        $perawatId = $this->safeInsert([
                            'id' => Str::uuid()->toString(),
                            'name' => $perawatName,
                            'email' => $perawatEmail,
                            'no_wa' => $data['nomor_hp'] ?? null,
                            'status_pegawai' => $data['status_pegawai'] ?? null,
                            'keterangan' => $data['keterangan'] ?? null,
                            'role' => 'perawat',
                            'parent_id' => $pustuId,
                            'password' => Hash::make('perawat123'),
                            'village' => $data['kelurahan'] ?? null,
                            'district' => $data['kecamatan'] ?? null,
                            'regency' => $data['kabupaten_kota'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        if ($perawatId) {
                            $this->importLog['perawat']++;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Log error but continue processing
                Log::error('Error processing row #' . ($index + 2) . ': ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString(),
                    'row_data' => $row
                ]);
                $this->importLog['skipped']++;
            }
        }
        
        // Log final import results
        Log::info('Excel import completed', [
            'total_rows' => $this->importLog['total'],
            'created_puskesmas' => $this->importLog['puskesmas'],
            'created_pustu' => $this->importLog['pustu'],
            'created_perawat' => $this->importLog['perawat'],
            'skipped' => $this->importLog['skipped']
        ]);
    }
    
    /**
     * Get import log statistics
     */
    public function getImportLog()
    {
        return $this->importLog;
    }
}