<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        // Stats
        $totalSurat = SuratMasuk::count();
        $suratHariIni = SuratMasuk::whereDate('tanggal_masuk', $today)->count();
        $suratBulanIni = SuratMasuk::whereBetween('tanggal_masuk', [$monthStart, $monthEnd])->count();

        // Surat per bulan (12 bulan terakhir)
        $suratPerBulan = SuratMasuk::selectRaw("DATE_FORMAT(tanggal_masuk, '%Y-%m') as bulan, COUNT(*) as jumlah")
            ->where('tanggal_masuk', '>=', now()->subMonths(12)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Distribusi sifat surat
        $distribusiSifat = SuratMasuk::selectRaw("sifat, COUNT(*) as jumlah")
            ->groupBy('sifat')
            ->get()
            ->pluck('jumlah', 'sifat')
            ->toArray();

        // 5 surat terbaru
        $suratTerbaru = SuratMasuk::with('uploader')
            ->latest('tanggal_masuk')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalSurat',
            'suratHariIni',
            'suratBulanIni',
            'suratPerBulan',
            'distribusiSifat',
            'suratTerbaru'
        ));
    }
}
