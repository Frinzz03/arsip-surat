<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Services\StorageService;
use App\Services\PdfExtractionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratMasukController extends Controller
{
    protected StorageService $storage;
    protected PdfExtractionService $pdfService;

    public function __construct(StorageService $storage, PdfExtractionService $pdfService)
    {
        $this->storage = $storage;
        $this->pdfService = $pdfService;
    }

    /**
     * Display a listing of surat masuk.
     */
    public function index(Request $request)
    {
        $query = SuratMasuk::with('uploader');

        // Apply filters

        if ($request->filled('pengirim')) {
            $query->filterByPengirim($request->pengirim);
        }
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_masuk', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_masuk', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('sifat')) {
            $query->where('sifat', $request->sifat);
        }

        $perPage = in_array((int) $request->input('per_page'), [25, 50, 100, 200]) ? (int) $request->input('per_page') : 25;

        $suratList = $query->latest('tanggal_masuk')->paginate($perPage)->appends($request->query());

        return view('surat-masuk.index', compact('suratList'));
    }

    /**
     * Show the form for creating a new surat masuk.
     */
    public function create()
    {
        return view('surat-masuk.create');
    }

    /**
     * Store a newly created surat masuk.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_agenda' => 'required|string|max:50|unique:surat_masuk,no_agenda',
            'nomor_surat' => 'nullable|string|max:255',
            'tanggal_surat' => 'nullable|date',
            'tanggal_masuk' => 'required|date',
            'pengirim' => 'nullable|string|max:255',
            'sifat' => 'nullable|in:biasa,penting,segera,rahasia',
            'perihal' => 'nullable|string',
            'hari_acara' => 'nullable|string|max:100',
            'tanggal_acara' => 'nullable|date',
            'waktu_acara' => 'nullable|string|max:50',
            'tempat_acara' => 'nullable|string|max:255',
            'penerima' => 'required|string|max:255',
            'file_pdf' => 'nullable|file|mimes:pdf|max:1024', // Max 1MB
        ]);

        $validated['uploaded_by'] = Auth::id();

        // Handle PDF upload
        if ($request->hasFile('file_pdf')) {
            $fileData = $this->storage->uploadPdf(
                $request->file('file_pdf'),
                date('Y', strtotime($validated['tanggal_masuk'])),
                date('m', strtotime($validated['tanggal_masuk']))
            );
            $validated['file_path'] = $fileData['file_path'];
            $validated['file_original_name'] = $fileData['file_original_name'];
        }

        unset($validated['file_pdf']);

        SuratMasuk::create($validated);

        return redirect()->route('surat-masuk.index')
            ->with('success', 'Surat masuk berhasil disimpan.');
    }

    /**
     * Display the specified surat masuk.
     */
    public function show(SuratMasuk $suratMasuk)
    {
        $suratMasuk->load('uploader');
        return view('surat-masuk.show', compact('suratMasuk'));
    }

    /**
     * Show the form for editing the specified surat masuk.
     */
    public function edit(SuratMasuk $suratMasuk)
    {
        return view('surat-masuk.edit', compact('suratMasuk'));
    }

    /**
     * Update the specified surat masuk.
     */
    public function update(Request $request, SuratMasuk $suratMasuk)
    {
        $validated = $request->validate([
            'no_agenda' => 'required|string|max:50|unique:surat_masuk,no_agenda,' . $suratMasuk->id,
            'nomor_surat' => 'nullable|string|max:255',
            'tanggal_surat' => 'nullable|date',
            'tanggal_masuk' => 'required|date',
            'pengirim' => 'nullable|string|max:255',
            'sifat' => 'nullable|in:biasa,penting,segera,rahasia',
            'perihal' => 'nullable|string',
            'hari_acara' => 'nullable|string|max:100',
            'tanggal_acara' => 'nullable|date',
            'waktu_acara' => 'nullable|string|max:50',
            'tempat_acara' => 'nullable|string|max:255',
            'penerima' => 'required|string|max:255',
            'file_pdf' => 'nullable|file|mimes:pdf|max:1024', // Max 1MB
        ]);



        // Handle PDF re-upload
        if ($request->hasFile('file_pdf')) {
            // Delete old file
            if ($suratMasuk->file_path) {
                $this->storage->deleteFile($suratMasuk->file_path);
            }

            $fileData = $this->storage->uploadPdf(
                $request->file('file_pdf'),
                date('Y', strtotime($validated['tanggal_masuk'])),
                date('m', strtotime($validated['tanggal_masuk']))
            );
            $validated['file_path'] = $fileData['file_path'];
            $validated['file_original_name'] = $fileData['file_original_name'];
        }

        unset($validated['file_pdf']);

        $suratMasuk->update($validated);

        return redirect()->route('surat-masuk.show', $suratMasuk)
            ->with('success', 'Surat masuk berhasil diperbarui.');
    }

    /**
     * Soft delete the specified surat masuk.
     */
    public function destroy(SuratMasuk $suratMasuk)
    {
        $suratMasuk->delete();

        return redirect()->route('surat-masuk.index')
            ->with('success', 'Surat masuk berhasil dihapus.');
    }

    /**
     * Extract data from uploaded PDF via Hugging Face Space OCR service.
     * Returns JSON response for AJAX autofill.
     */
    public function extractPdf(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:1024', // Max 1MB
        ]);

        $result = $this->pdfService->extractWithDetails($request->file('file'));

        return response()->json($result);
    }
}
