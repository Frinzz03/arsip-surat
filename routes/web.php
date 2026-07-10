<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\CsvController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Surat Masuk CRUD
    Route::resource('surat-masuk', SuratMasukController::class)->parameters([
        'surat-masuk' => 'suratMasuk',
    ]);

    // PDF extraction (AJAX)
    Route::post('/surat-masuk/extract-pdf', [SuratMasukController::class, 'extractPdf'])
        ->name('surat-masuk.extract-pdf');

    // File operations (preview/download)
    Route::get('/file/{suratMasuk}/preview', [FileController::class, 'preview'])->name('file.preview');
    Route::get('/file/{suratMasuk}/download', [FileController::class, 'download'])->name('file.download');

    // Advanced Search
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::get('/search/results', [SearchController::class, 'search'])->name('search.results');
    Route::get('/search/quick', [SearchController::class, 'quickSearch'])->name('search.quick');

    // CSV Export/Import
    Route::get('/csv', [CsvController::class, 'index'])->name('csv.index');
    Route::post('/csv/export', [CsvController::class, 'export'])->name('csv.export');
    Route::post('/csv/import', [CsvController::class, 'import'])->name('csv.import');

    // Admin-only: User Management
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';

