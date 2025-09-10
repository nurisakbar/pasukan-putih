<?php

namespace App\Jobs;

use App\Models\Pasien;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncronisasiPasienCarik implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $syncId;
    
    // Konfigurasi untuk memory management
    const BATCH_SIZE = 500;             // Jumlah record per batch insert (dikurangi untuk stabilitas)
    const MEMORY_LIMIT_THRESHOLD = 0.7; // 70% dari memory limit (lebih konservatif)
    const CHUNK_SIZE = 50;              // Ukuran chunk untuk query existing NIK (dikurangi)
    const API_PAGE_SIZE = 100;          // Jumlah record per halaman API (untuk mengurangi frekuensi request)
    
    // Timeout konfigurasi
    public $timeout = 10800; // 3 jam timeout untuk job (diperpanjang)
    public $tries = 3;       // 3 kali percobaan untuk retry
    public $maxExceptions = 5;
    public $backoff = [60, 300, 900]; // Backoff: 1min, 5min, 15min

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $syncId)
    {
        $this->userId = $userId;
        $this->syncId = $syncId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Set memory limit jika memungkinkan
        if (function_exists('ini_set')) {
            ini_set('memory_limit', '512M');
        }
        
        $baseUrl = 'https://carik.jakarta.go.id/api/v1/dilan/activity-daily-living';
        $headers = [
            'carik-api-key' => 'WydtKanwCc0dhbaclOLy2uUBl7WVICQA',
            'Cookie' => 'TS01f239ec=01b53461a6e068c46f652602c6a09733f49a58e0f31899b767a13a3358d6cac47368fe86ad7fb78a2034b98e8cb19c758b6dc2c1bf',
        ];

        $cacheKey = $this->syncId;
        Log::info('Starting sync with cache key', [$cacheKey]);

        // Set initial progress
        $this->updateProgress($cacheKey, [
            'status' => 'started',
            'current_page' => 0,
            'total_pages' => 0,
            'processed_records' => 0,
            'failed_pages' => [],
            'memory_usage' => $this->getMemoryUsage(),
            'message' => 'Memulai sinkronisasi...'
        ]);

        try {
            $currentPage = 1;
            $totalPages = 1;
            $failedPages = [];
            $processedRecords = 0;
            $batchBuffer = [];

            // Get total pages first
            $initialResponse = $this->makeApiRequest($baseUrl, $headers, ['page' => 1, 'size' => self::API_PAGE_SIZE]);
            if (!$initialResponse) {
                throw new \Exception('Gagal mengambil data awal dari API');
            }

            $initialData = $initialResponse->json();
            $totalPages = $initialData['pagination']['totalPages'] ?? 1;

            $this->updateProgress($cacheKey, [
                'status' => 'processing',
                'current_page' => 0,
                'total_pages' => $totalPages,
                'processed_records' => 0,
                'failed_pages' => [],
                'memory_usage' => $this->getMemoryUsage(),
                'message' => "Memproses {$totalPages} halaman..."
            ]);

            // Process pages
            while ($currentPage <= $totalPages) {
                // Check memory usage before processing
                if ($this->isMemoryLimitReached()) {
                    Log::warning("Memory limit reached at page {$currentPage}, forcing garbage collection");
                    $this->forceGarbageCollection();
                    
                    if ($this->isMemoryLimitReached()) {
                        throw new \Exception("Memory limit exceeded at page {$currentPage}");
                    }
                }

                try {
                    $this->updateProgress($cacheKey, [
                        'current_page' => $currentPage,
                        'memory_usage' => $this->getMemoryUsage(),
                        'message' => "Memproses halaman {$currentPage} dari {$totalPages}..."
                    ], false);

                    $response = $this->makeApiRequest($baseUrl, $headers, ['page' => $currentPage, 'size' => self::API_PAGE_SIZE]);
                    
                    if (!$response) {
                        $failedPages[] = $currentPage;
                        $currentPage++;
                        continue;
                    }

                    $responseData = $response->json();

                    if (!isset($responseData['data'])) {
                        Log::error("Invalid API response structure at page {$currentPage}");
                        $failedPages[] = $currentPage;
                        $currentPage++;
                        continue;
                    }

                    $data = $responseData['data'] ?? [];
                    
                    if (empty($data)) {
                        Log::info("Page {$currentPage} has no data");
                        $currentPage++;
                        continue;
                    }

                    // Process data in smaller chunks to avoid memory issues
                    $processedCount = $this->processPageData($data, $batchBuffer, $processedRecords);
                    $processedRecords += $processedCount;

                    // Update progress after successful processing
                    $this->updateProgress($cacheKey, [
                        'processed_records' => $processedRecords,
                        'memory_usage' => $this->getMemoryUsage(),
                        'message' => "Berhasil memproses {$currentPage} dari {$totalPages}"
                    ], false);

                    Log::info("Successfully processed page {$currentPage}/{$totalPages}", [
                        'processed_records' => $processedRecords,
                        'memory_usage' => $this->getMemoryUsage()
                    ]);

                    // Clear variables to free memory
                    unset($response, $responseData, $data);

                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Connection timeout at page {$currentPage}: " . $e->getMessage());
                    $failedPages[] = $currentPage;
                    
                    $this->updateProgress($cacheKey, [
                        'failed_pages' => $failedPages,
                        'message' => "Timeout pada halaman {$currentPage} - akan dicoba ulang"
                    ], false);
                    
                    // Retry halaman yang timeout dengan delay
                    if (count($failedPages) <= 3) {
                        sleep(5); // Wait 5 seconds before retry
                        $currentPage--; // Retry current page
                    }

                } catch (\Exception $e) {
                    Log::error("Error at page {$currentPage}: " . $e->getMessage());
                    $failedPages[] = $currentPage;
                    
                    $this->updateProgress($cacheKey, [
                        'failed_pages' => $failedPages,
                        'message' => "Error pada halaman {$currentPage}: " . $e->getMessage()
                    ], false);
                }

                // Force garbage collection every 5 pages (lebih sering)
                if ($currentPage % 5 === 0) {
                    $this->forceGarbageCollection();
                }

                $currentPage++;
                
                // Small delay to prevent API rate limiting
                usleep(100000); // 0.1 second
            }

            // Process remaining items in buffer
            if (!empty($batchBuffer)) {
                $this->processBatch($batchBuffer);
                $processedRecords += count($batchBuffer);
            }

            // Final progress update
            $finalStatus = empty($failedPages) ? 'completed' : 'completed_with_errors';
            $this->updateProgress($cacheKey, [
                'status' => $finalStatus,
                'current_page' => $totalPages,
                'total_pages' => $totalPages,
                'processed_records' => $processedRecords,
                'failed_pages' => $failedPages,
                'memory_usage' => $this->getMemoryUsage(),
                'message' => empty($failedPages) ? 'Sinkronisasi berhasil!' : 'Sinkronisasi selesai dengan beberapa error'
            ]);

            Log::info("Sync completed", [
                'sync_id' => $this->syncId,
                'success' => empty($failedPages),
                'processed_records' => $processedRecords,
                'failed_pages' => $failedPages,
                'final_memory_usage' => $this->getMemoryUsage()
            ]);

        } catch (\Exception $e) {
            $this->updateProgress($cacheKey, [
                'status' => 'failed',
                'current_page' => $currentPage ?? 0,
                'total_pages' => $totalPages ?? 0,
                'processed_records' => $processedRecords ?? 0,
                'failed_pages' => $failedPages ?? [],
                'memory_usage' => $this->getMemoryUsage(),
                'message' => 'Sinkronisasi gagal: ' . $e->getMessage()
            ]);

            Log::error('Sync failed: ' . $e->getMessage(), [
                'sync_id' => $this->syncId,
                'memory_usage' => $this->getMemoryUsage()
            ]);

            throw $e;
        }
    }

    /**
     * Make API request with error handling
     */
    private function makeApiRequest($url, $headers, $params, $retries = 5)
    {
        for ($i = 0; $i < $retries; $i++) {
            try {
                // Progressive timeout: 60s, 90s, 120s, 150s, 180s
                $timeout = 60 + ($i * 30);
                
                $response = Http::withOptions([
                    'proxy' => 'http://10.15.3.20:80',
                    'verify' => true,
                    'connect_timeout' => 30,
                    'read_timeout' => $timeout,
                ])->withHeaders($headers)
                    ->timeout($timeout)
                    ->get($url, $params);

                if ($response->successful()) {
                    return $response;
                }

                Log::warning("API request failed (attempt " . ($i + 1) . "): " . $response->status());
                
                if ($i < $retries - 1) {
                    // Exponential backoff: 2s, 4s, 8s, 16s
                    $waitTime = pow(2, $i + 1);
                    sleep($waitTime);
                }

            } catch (\Exception $e) {
                Log::error("API request exception (attempt " . ($i + 1) . "): " . $e->getMessage());
                
                if ($i < $retries - 1) {
                    // Exponential backoff: 2s, 4s, 8s, 16s
                    $waitTime = pow(2, $i + 1);
                    sleep($waitTime);
                }
            }
        }

        return null;
    }

    /**
     * Process page data efficiently
     */
    private function processPageData($data, &$batchBuffer, &$processedRecords)
    {
        $processedCount = 0;
        
        $dataChunks = array_chunk($data, self::CHUNK_SIZE);
        
        foreach ($dataChunks as $chunk) {
            $niks = array_column($chunk, 'nik');
            // Optimize query dengan select hanya kolom yang dibutuhkan
            $existingNiks = Pasien::whereIn('nik', $niks)->select('nik')->pluck('nik')->toArray();
            
            foreach ($chunk as $item) {
                if (!in_array($item['nik'], $existingNiks)) {
                    $batchBuffer[] = [
                        'id'            => Str::uuid()->toString(),
                        'name'          => $item['nama'],
                        'nik'           => $item['nik'],
                        'alamat'        => $item['alamat'] ?? '-',
                        'jenis_kelamin' => $item['gender'] == '1' ? 'Laki-laki' : 'Perempuan',
                        'village_id'    => $item['kelurahan'],
                        'jenis_ktp'     => 'DKI',
                        'tanggal_lahir' => null,
                        'rt'            => '00',
                        'rw'            => '00',
                        'user_id'       => '-',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                    
                    $processedCount++;

                    // Process batch when it reaches the limit
                    if (count($batchBuffer) >= self::BATCH_SIZE) {
                        $this->processBatch($batchBuffer);
                        $batchBuffer = []; // Clear buffer
                        
                        // Force garbage collection after batch processing
                        $this->forceGarbageCollection();
                    }
                }
            }
            
            // Clear chunk data from memory
            unset($chunk, $niks, $existingNiks);
        }

        return $processedCount;
    }

    /**
     * Process batch insert
     */
    private function processBatch($batch)
    {
        if (empty($batch)) {
            return;
        }

        try {
            DB::transaction(function () use ($batch) {
                // Insert in smaller chunks to avoid query size limits
                $chunks = array_chunk($batch, 500);
                foreach ($chunks as $chunk) {
                    Pasien::insert($chunk);
                }
            }, 300); // 5 minute transaction timeout for large batches

        } catch (\Exception $e) {
            Log::error("Failed to insert batch: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update progress cache
     */
    private function updateProgress($cacheKey, $data, $merge = true)
    {
        if ($merge) {
            $existing = Cache::get($cacheKey, []);
            $data = array_merge($existing, $data);
        }
        
        Cache::put($cacheKey, $data, 3600);
    }

    /**
     * Check if memory limit is reached
     */
    private function isMemoryLimitReached()
    {
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $currentMemory = memory_get_usage(true);
        
        return ($currentMemory / $memoryLimit) > self::MEMORY_LIMIT_THRESHOLD;
    }

    /**
     * Parse memory limit string to bytes
     */
    private function parseMemoryLimit($memoryLimit)
    {
        if ($memoryLimit == -1) {
            return PHP_INT_MAX;
        }
        
        $value = (int) $memoryLimit;
        $unit = strtolower(substr($memoryLimit, -1));
        
        switch ($unit) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }

    /**
     * Get current memory usage info
     */
    private function getMemoryUsage()
    {
        return [
            'current' => round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB',
            'peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB',
            'percentage' => round((memory_get_usage(true) / $this->parseMemoryLimit(ini_get('memory_limit'))) * 100, 2) . '%'
        ];
    }

    /**
     * Force garbage collection
     */
    private function forceGarbageCollection()
    {
        if (function_exists('gc_collect_cycles')) {
            $collected = gc_collect_cycles();
            Log::debug("Garbage collection freed {$collected} objects");
        }
        
        // Clear opcode cache if available
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception)
    {
        $cacheKey = $this->syncId;
        
        $this->updateProgress($cacheKey, [
            'status' => 'failed',
            'message' => 'Job failed: ' . $exception->getMessage(),
            'memory_usage' => $this->getMemoryUsage()
        ]);

        Log::error('Job failed permanently', [
            'sync_id' => $this->syncId,
            'exception' => $exception->getMessage(),
            'memory_usage' => $this->getMemoryUsage()
        ]);
    }
}