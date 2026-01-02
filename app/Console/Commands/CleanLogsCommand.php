<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CleanLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean 
                            {--days=7 : Hapus log yang lebih tua dari X hari}
                            {--size=100 : Hapus log yang lebih besar dari X MB}
                            {--all : Hapus semua file log}
                            {--dry-run : Tampilkan file yang akan dihapus tanpa menghapus}
                            {--force : Konfirmasi penghapusan tanpa prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan file log berdasarkan umur, ukuran, atau menghapus semua';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Memulai pembersihan log...');
        
        $logPath = storage_path('logs');
        
        if (!File::exists($logPath)) {
            $this->error('❌ Direktori log tidak ditemukan: ' . $logPath);
            return 1;
        }

        $filesToDelete = [];
        $totalSize = 0;

        // Ambil semua file log
        $logFiles = File::files($logPath);
        
        if (empty($logFiles)) {
            $this->info('✅ Tidak ada file log yang ditemukan.');
            return 0;
        }

        $this->info('📁 Menemukan ' . count($logFiles) . ' file log...');

        foreach ($logFiles as $file) {
            $filePath = $file->getPathname();
            $fileName = $file->getFilename();
            $fileSize = $file->getSize();
            $fileModified = Carbon::createFromTimestamp($file->getMTime());
            $fileSizeMB = round($fileSize / 1024 / 1024, 2);

            $shouldDelete = false;
            $reason = '';

            // Opsi --all: hapus semua file log
            if ($this->option('all')) {
                $shouldDelete = true;
                $reason = 'Semua file log';
            }
            // Opsi --days: hapus file yang lebih tua dari X hari
            elseif ($this->option('days')) {
                $days = (int) $this->option('days');
                if ($fileModified->lt(Carbon::now()->subDays($days))) {
                    $shouldDelete = true;
                    $reason = "Lebih tua dari {$days} hari ({$fileModified->format('Y-m-d H:i:s')})";
                }
            }
            // Opsi --size: hapus file yang lebih besar dari X MB
            elseif ($this->option('size')) {
                $sizeMB = (int) $this->option('size');
                if ($fileSizeMB > $sizeMB) {
                    $shouldDelete = true;
                    $reason = "Lebih besar dari {$sizeMB}MB ({$fileSizeMB}MB)";
                }
            }
            // Default: hapus file yang lebih tua dari 7 hari
            else {
                if ($fileModified->lt(Carbon::now()->subDays(7))) {
                    $shouldDelete = true;
                    $reason = "Lebih tua dari 7 hari ({$fileModified->format('Y-m-d H:i:s')})";
                }
            }

            if ($shouldDelete) {
                $filesToDelete[] = [
                    'path' => $filePath,
                    'name' => $fileName,
                    'size' => $fileSizeMB,
                    'modified' => $fileModified->format('Y-m-d H:i:s'),
                    'reason' => $reason
                ];
                $totalSize += $fileSizeMB;
            }
        }

        if (empty($filesToDelete)) {
            $this->info('✅ Tidak ada file log yang perlu dihapus.');
            return 0;
        }

        // Tampilkan file yang akan dihapus
        $this->info("\n📋 File log yang akan dihapus:");
        $this->table(
            ['File', 'Ukuran (MB)', 'Terakhir Dimodifikasi', 'Alasan'],
            array_map(function ($file) {
                return [
                    $file['name'],
                    $file['size'],
                    $file['modified'],
                    $file['reason']
                ];
            }, $filesToDelete)
        );

        $this->info("💾 Total ruang yang akan dibebaskan: " . round($totalSize, 2) . " MB");

        // Dry run mode
        if ($this->option('dry-run')) {
            $this->info('🔍 Mode dry-run: Tidak ada file yang dihapus.');
            return 0;
        }

        // Konfirmasi penghapusan
        if (!$this->option('force')) {
            if (!$this->confirm('Apakah Anda yakin ingin menghapus file-file ini?')) {
                $this->info('❌ Operasi dibatalkan.');
                return 0;
            }
        }

        // Hapus file
        $deletedCount = 0;
        $failedCount = 0;

        foreach ($filesToDelete as $file) {
            try {
                File::delete($file['path']);
                $deletedCount++;
                $this->line("✅ Dihapus: {$file['name']}");
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("❌ Gagal menghapus: {$file['name']} - {$e->getMessage()}");
            }
        }

        // Hasil akhir
        $this->info("\n🎉 Pembersihan log selesai!");
        $this->info("✅ Berhasil dihapus: {$deletedCount} file");
        if ($failedCount > 0) {
            $this->error("❌ Gagal dihapus: {$failedCount} file");
        }
        $this->info("💾 Ruang yang dibebaskan: " . round($totalSize, 2) . " MB");

        return 0;
    }
}
