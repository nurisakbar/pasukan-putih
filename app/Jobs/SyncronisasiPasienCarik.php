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

    /**
     * Create a new job instance.
     *
     * @param int $userId
     * @return void
     */
    public function __construct($userId, $syncId)
    {
        $this->userId = $userId;
        $this->syncId = $syncId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $baseUrl = 'https://carik.jakarta.go.id/api/v1/dilan/activity-daily-living';
        $headers = [
            'carik-api-key' => 'WydtKanwCc0dhbaclOLy2uUBl7WVICQA',
            'Cookie' => 'TS01f239ec=01b53461a6e068c46f652602c6a09733f49a58e0f31899b767a13a3358d6cac47368fe86ad7fb78a2034b98e8cb19c758b6dc2c1bf',
        ];

        $cacheKey = $this->syncId;
        Log::info('get cache key', [$cacheKey]);

        // Set initial progress
        Cache::put($cacheKey, [
            'status' => 'started',
            'current_page' => 0,
            'total_pages' => 0,
            'processed_records' => 0,
            'failed_pages' => [],
            'message' => 'Memulai sinkronisasi...'
        ], 3600); // Cache for 1 hour

        try {
            $allData = [];
            $currentPage = 1;
            $totalPages = 1;
            $failedPages = [];
            $processedRecords = 0;

            $existingNiks = Pasien::pluck('nik')->toArray();

            $initialResponse = Http::withHeaders($headers)
                ->timeout(30)
                ->retry(3, 1000)
                ->get($baseUrl, ['page' => 1]);

            if (!$initialResponse->successful()) {
                throw new \Exception('Gagal mengambil data awal dari API');
            }

            $initialData = $initialResponse->json();
            $totalPages = $initialData['pagination']['totalPages'] ?? 1;

            // Update progress with total pages
            Cache::put($cacheKey, [
                'status' => 'processing',
                'current_page' => 0,
                'total_pages' => $totalPages,
                'processed_records' => 0,
                'failed_pages' => [],
                'message' => "Memproses {$totalPages} halaman..."
            ], 3600);

            while ($currentPage <= $totalPages) {
                try {
                    $progress = Cache::get($cacheKey, []);
                    $progress['current_page'] = $currentPage;
                    $progress['message'] = "Memproses halaman {$currentPage} dari {$totalPages}...";
                    Cache::put($cacheKey, $progress, 3600);

                    $response = Http::withHeaders($headers)
                        ->timeout(45)
                        ->retry(3, 2000)
                        ->get($baseUrl, ['page' => $currentPage]);

                    if ($response->successful()) {
                        $responseData = $response->json();

                        if (!isset($responseData['data']) || !isset($responseData['pagination'])) {
                            Log::error("Struktur respons API tidak valid pada halaman {$currentPage}.");
                            $failedPages[] = $currentPage;
                            $currentPage++;
                            continue;
                        }

                        $data = $responseData['data'] ?? [];

                        if (empty($data)) {
                            Log::warning("Halaman {$currentPage} tidak memiliki data.");
                            $currentPage++;
                            continue;
                        }

                        $newItems = [];
                        foreach ($data as $item) {
                            if (!in_array($item['nik'], $existingNiks)) {
                                $newItems[] = [
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
                                $existingNiks[] = $item['nik'];
                            }
                        }

                        // Save data in batch with timeout handling
                        if (!empty($newItems)) {
                            try {
                                DB::transaction(function () use ($newItems) {
                                    Pasien::insert($newItems);
                                }, 5); // Transaction timeout 5 seconds

                                $processedRecords += count($newItems);
                            } catch (\Exception $e) {
                                Log::error("Gagal menyimpan data halaman {$currentPage}: " . $e->getMessage());
                                $failedPages[] = $currentPage;
                            }
                        }

                        $allData = array_merge($allData, $data);

                        // Update progress after successful processing
                        $progress = Cache::get($cacheKey, []);
                        $progress['processed_records'] = $processedRecords;
                        $progress['message'] = "Berhasil memproses halaman {$currentPage} dari {$totalPages}";
                        Cache::put($cacheKey, $progress, 3600);

                        Log::info("Berhasil memproses halaman {$currentPage} dari {$totalPages}");

                    } else {
                        throw new \Exception("API Error: Status {$response->status()}, Body: {$response->body()}");
                    }

                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("Connection timeout pada halaman {$currentPage}: " . $e->getMessage());
                    $failedPages[] = $currentPage;

                    // Update progress with error
                    $progress = Cache::get($cacheKey, []);
                    $progress['failed_pages'] = $failedPages;
                    $progress['message'] = "Timeout pada halaman {$currentPage}";
                    Cache::put($cacheKey, $progress, 3600);

                } catch (\Exception $e) {
                    Log::error("Error pada halaman {$currentPage}: " . $e->getMessage());
                    $failedPages[] = $currentPage;

                    // Update progress with error
                    $progress = Cache::get($cacheKey, []);
                    $progress['failed_pages'] = $failedPages;
                    $progress['message'] = "Error pada halaman {$currentPage}: " . $e->getMessage();
                    Cache::put($cacheKey, $progress, 3600);
                }

                sleep(3); // Delay to avoid rate limiting
                $currentPage++;
            }

            // Final progress update
            $finalStatus = empty($failedPages) ? 'completed' : 'completed_with_errors';
            Cache::put($cacheKey, [
                'status' => $finalStatus,
                'current_page' => $totalPages,
                'total_pages' => $totalPages,
                'processed_records' => $processedRecords,
                'failed_pages' => $failedPages,
                'message' => empty($failedPages) ? 'Sinkronisasi berhasil!' : 'Sinkronisasi selesai dengan beberapa error'
            ], 3600);

            // Log final status (no HTTP response in a job)
            Log::info("Sinkronisasi selesai", [
                'sync_id' => $syncId,
                'success' => empty($failedPages),
                'processed_records' => $processedRecords,
                'failed_pages' => $failedPages,
            ]);

        } catch (\Exception $e) {
            // Update cache with error status
            Cache::put($cacheKey, [
                'status' => 'failed',
                'current_page' => $currentPage ?? 0,
                'total_pages' => $totalPages ?? 0,
                'processed_records' => $processedRecords ?? 0,
                'failed_pages' => $failedPages ?? [],
                'message' => 'Sinkronisasi gagal: ' . $e->getMessage()
            ], 3600);

            Log::error('Sinkronisasi Carik gagal: ' . $e->getMessage(), [
                'sync_id' => $syncId,
            ]);

            // Re-throw exception to mark job as failed
            throw $e;
        }
    }
}