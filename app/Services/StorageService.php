<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class StorageService
{
    /**
     * Upload a PDF file to structured storage path.
     * Path format: surat_masuk/{year}/{month}/{unique_filename}.pdf
     */
    public function uploadPdf(UploadedFile $file, ?string $year = null, ?string $month = null): array
    {
        $year = $year ?? now()->format('Y');
        $month = $month ?? now()->format('m');

        $originalName = $file->getClientOriginalName();
        $uniqueName = Str::uuid() . '.pdf';
        $path = "surat_masuk/{$year}/{$month}/{$uniqueName}";

        $disk = config('filesystems.default', 'local');
        Storage::disk($disk)->put($path, file_get_contents($file));

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
        $disk = config('filesystems.default', 'local');

        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }

        return Storage::disk($disk)->get($path);
    }

    /**
     * Delete file from storage.
     */
    public function deleteFile(string $path): bool
    {
        $disk = config('filesystems.default', 'local');

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Check if file exists.
     */
    public function fileExists(string $path): bool
    {
        $disk = config('filesystems.default', 'local');
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Get full path for streaming.
     */
    public function getFullPath(string $path): string
    {
        $disk = config('filesystems.default', 'local');
        return Storage::disk($disk)->path($path);
    }
}
