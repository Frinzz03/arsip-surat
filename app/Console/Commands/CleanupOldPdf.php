<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuratMasuk;
use App\Services\StorageService;
use Illuminate\Support\Facades\Log;

class CleanupOldPdf extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'storage:cleanup-old-pdf
                            {--months=9 : Hapus file PDF yang lebih dari N bulan}
                            {--dry-run : Tampilkan file yang akan dihapus tanpa menghapus}';

    /**
     * The console command description.
     */
    protected $description = 'Hapus file PDF dari Cloudflare R2 untuk surat yang lebih dari 9 bulan. Data di database tetap disimpan.';

    public function __construct(protected StorageService $storage)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $months = (int) $this->option('months');
        $dryRun = $this->option('dry-run');
        $cutoffDate = now()->subMonths($months);

        $this->info("Menghapus file PDF surat yang lebih dari {$months} bulan.");
        $this->info("Batas tanggal: {$cutoffDate->format('d-m-Y')}");

        if ($dryRun) {
            $this->comment('Mode dry-run: tidak ada file yang dihapus.');
        }

        $this->newLine();

        // Cari surat yang tanggal_masuk lebih dari N bulan dan masih punya file
        $suratList = SuratMasuk::where('tanggal_masuk', '<', $cutoffDate)
            ->whereNotNull('file_path')
            ->where('file_path', '!=', '')
            ->get();

        if ($suratList->isEmpty()) {
            $this->info('Tidak ada file PDF yang perlu dihapus.');
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$suratList->count()} surat dengan file PDF yang sudah lebih dari {$months} bulan.");
        $this->newLine();

        $deleted = 0;
        $failed = 0;

        foreach ($suratList as $surat) {
            $info = "#{$surat->id} - {$surat->perihal} ({$surat->tanggal_masuk->format('d-m-Y')})";

            if ($dryRun) {
                $this->line("  → Akan dihapus: {$info}");
                $this->line("    File: {$surat->file_path}");
                $deleted++;
                continue;
            }

            try {
                // Hapus file dari R2
                $this->storage->deleteFile($surat->file_path);

                // Kosongkan path di database, data surat tetap ada
                $surat->update([
                    'file_path' => null,
                    'file_original_name' => null,
                ]);

                $deleted++;

                Log::info('Cleanup: PDF lama dihapus dari R2', [
                    'surat_id' => $surat->id,
                    'file_path' => $surat->file_path,
                    'tanggal_masuk' => $surat->tanggal_masuk->format('Y-m-d'),
                ]);
            } catch (\Exception $e) {
                $this->error("  ✗ Gagal hapus {$info}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info('═══ Ringkasan Cleanup ═══');
        $this->info("  File dihapus  : {$deleted}");

        if ($failed > 0) {
            $this->warn("  Gagal         : {$failed}");
        }

        if ($dryRun) {
            $this->newLine();
            $this->comment('Jalankan tanpa --dry-run untuk menghapus file sebenarnya.');
        }

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
