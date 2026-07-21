<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class StorageService
{
    /**
     * Get the configured storage disk name.
     */
    protected function disk(): string
    {
        return config('filesystems.default', 'local');
    }

    /**
     * Upload a PDF file to structured storage path.
     * Path format: surat_masuk/{year}/{month}/{unique_filename}.pdf
     *
     * Supports both local disk and S3/MinIO.
     */
    public function uploadPdf(UploadedFile $file, ?string $year = null, ?string $month = null): array
    {
        $year = $year ?? now()->format('Y');
        $month = $month ?? now()->format('m');

        $originalName = $file->getClientOriginalName();
        $uniqueName = Str::uuid() . '.pdf';
        $path = "surat_masuk/{$year}/{$month}/{$uniqueName}";

        Storage::disk($this->disk())->put($path, file_get_contents($file));

        Log::info('PDF uploaded to storage', [
            'disk' => $this->disk(),
            'path' => $path,
            'original_name' => $originalName,
        ]);

        return [
            'file_path' => $path,
            'file_original_name' => $originalName,
        ];
    }

    /**
     * Get file content from storage.
     */
    public function getFile(string $path): ?string
    {
        if (!Storage::disk($this->disk())->exists($path)) {
            return null;
        }

        return Storage::disk($this->disk())->get($path);
    }

    /**
     * Get a readable stream for the file.
     * More memory-efficient than getFile() for large PDFs.
     *
     * @return resource|null
     */
    public function streamFile(string $path)
    {
        if (!Storage::disk($this->disk())->exists($path)) {
            return null;
        }

        return Storage::disk($this->disk())->readStream($path);
    }

    /**
     * Delete file from storage.
     */
    public function deleteFile(string $path): bool
    {
        if (Storage::disk($this->disk())->exists($path)) {
            $deleted = Storage::disk($this->disk())->delete($path);

            Log::info('File deleted from storage', [
                'disk' => $this->disk(),
                'path' => $path,
                'success' => $deleted,
            ]);

            return $deleted;
        }

        return false;
    }

    /**
     * Check if file exists.
     */
    public function fileExists(string $path): bool
    {
        return Storage::disk($this->disk())->exists($path);
    }

    /**
     * Get a temporary (pre-signed) URL for direct access to the file.
     * Only works with S3/MinIO disk. Falls back to null for local disk.
     */
    public function getTemporaryUrl(string $path, int $minutes = 30): ?string
    {
        if ($this->disk() !== 's3') {
            return null;
        }

        if (!Storage::disk($this->disk())->exists($path)) {
            return null;
        }

        try {
            return Storage::disk($this->disk())->temporaryUrl(
                $path,
                now()->addMinutes($minutes)
            );
        } catch (\Exception $e) {
            Log::warning('Failed to generate temporary URL', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get the public URL for a file.
     * For S3/MinIO, returns the configured URL. For local, returns null.
     */
    public function getUrl(string $path): ?string
    {
        try {
            return Storage::disk($this->disk())->url($path);
        } catch (\Exception $e) {
            return null;
        }
    }
}
