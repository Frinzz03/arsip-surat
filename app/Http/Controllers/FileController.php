<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Services\StorageService;
use Illuminate\Http\Request;

class FileController extends Controller
{
    protected StorageService $storage;

    public function __construct(StorageService $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Preview PDF inline in browser.
     */
    public function preview(SuratMasuk $suratMasuk)
    {
        if (!$suratMasuk->file_path || !$this->storage->fileExists($suratMasuk->file_path)) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        $content = $this->storage->getFile($suratMasuk->file_path);

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . ($suratMasuk->file_original_name ?? 'surat.pdf') . '"',
        ]);
    }

    /**
     * Download PDF file.
     */
    public function download(SuratMasuk $suratMasuk)
    {
        if (!$suratMasuk->file_path || !$this->storage->fileExists($suratMasuk->file_path)) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        $content = $this->storage->getFile($suratMasuk->file_path);
        $filename = $suratMasuk->file_original_name ?? 'surat_' . $suratMasuk->no_agenda . '.pdf';

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
