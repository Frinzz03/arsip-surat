<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\SuratMasuk;

class MigrateToR2 extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'storage:migrate-to-r2
                            {--dry-run : Tampilkan file yang akan dimigrasi tanpa memindahkan}';

    /**
     * The console command description.
     */
    protected $description = 'Migrasi file PDF surat masuk dari penyimpanan lokal ke Cloudflare R2';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        // Verify R2 configuration
        $r2Config = config('filesystems.disks.r2');
        if (empty($r2Config['endpoint']) || empty($r2Config['bucket'])) {
            $this->error('Konfigurasi Cloudflare R2 belum lengkap. Periksa file .env.');
            return Command::FAILURE;
        }

        $this->info("R2 Endpoint: {$r2Config['endpoint']}");
        $this->info("Bucket: {$r2Config['bucket']}");
        $this->newLine();

        // Test R2 connection
        if (!$dryRun) {
            try {
                Storage::disk('r2')->directories('/');
                $this->info('✓ Koneksi ke Cloudflare R2 berhasil.');
            } catch (\Exception $e) {
                $this->error('✗ Gagal koneksi ke Cloudflare R2: ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        // Get all surat with file_path
        $suratList = SuratMasuk::whereNotNull('file_path')
            ->where('file_path', '!=', '')
            ->get();

        if ($suratList->isEmpty()) {
            $this->info('Tidak ada file surat untuk dimigrasi.');
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$suratList->count()} surat dengan file PDF.");
        $this->newLine();

        $migrated = 0;
        $skipped = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar($suratList->count());
        $bar->start();

        foreach ($suratList as $surat) {
            $path = $surat->file_path;

            // Check if already exists in R2
            try {
                if (Storage::disk('r2')->exists($path)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }
            } catch (\Exception $e) {
                // If we can't check, try to migrate anyway
            }

            // Check if file exists locally
            if (!Storage::disk('local')->exists($path)) {
                $this->newLine();
                $this->warn("  ⚠ File lokal tidak ditemukan: {$path} (Surat #{$surat->id})");
                $failed++;
                $bar->advance();
                continue;
            }

            if ($dryRun) {
                $this->newLine();
                $this->line("  → Akan migrasi: {$path}");
                $migrated++;
                $bar->advance();
                continue;
            }

            // Migrate: read from local, write to R2
            try {
                $content = Storage::disk('local')->get($path);
                Storage::disk('r2')->put($path, $content);
                $migrated++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  ✗ Gagal migrasi {$path}: {$e->getMessage()}");
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('═══ Ringkasan Migrasi ═══');
        $this->info("  Berhasil dimigrasi  : {$migrated}");
        $this->info("  Sudah ada di R2     : {$skipped}");

        if ($failed > 0) {
            $this->warn("  Gagal               : {$failed}");
        }

        if ($dryRun) {
            $this->newLine();
            $this->comment('Mode dry-run: tidak ada file yang dipindahkan.');
            $this->comment('Jalankan tanpa --dry-run untuk migrasi sebenarnya.');
        }

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
