<x-app-layout>
    <x-slot name="title">Dashboard Arsip Surat Masuk</x-slot>

    <div class="animate-fade-in-up">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="section-title">Dashboard</h1>
                <p class="section-subtitle">Ringkasan arsip surat masuk {{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <a href="{{ route('surat-masuk.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Surat
                </a>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
            <!-- Total Surat -->
            <div class="stat-card sky">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Arsip</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($totalSurat) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 dark:bg-sky-900/50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                </div>
            </div>

            <!-- Surat Bulan Ini -->
            <div class="stat-card emerald">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Bulan Ini</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($suratBulanIni) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Surat Hari Ini -->
            <div class="stat-card amber">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Hari Ini</p>
                        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($suratHariIni) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">
            <!-- Line Chart: Surat per Bulan -->
            <div class="lg:col-span-2 glass-card p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Tren Surat Masuk</h3>
                <div class="relative h-64">
                    <canvas id="chartSuratPerBulan"></canvas>
                </div>
            </div>

            <!-- Doughnut Chart: Distribusi Sifat -->
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Distribusi Sifat</h3>
                <div class="relative h-64 flex items-center justify-center">
                    <canvas id="chartSifat"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Quick Actions -->
            <div class="space-y-3">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Aksi Cepat</h3>
                <a href="{{ route('surat-masuk.create') }}" class="quick-action">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-900/50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">Tambah Surat Baru</p>
                        <p class="text-xs text-slate-500">Input manual atau upload PDF</p>
                    </div>
                </a>
                <a href="{{ route('search.index') }}" class="quick-action">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">Pencarian Lanjut</p>
                        <p class="text-xs text-slate-500">Cari berdasarkan berbagai filter</p>
                    </div>
                </a>
                <a href="{{ route('csv.index') }}" class="quick-action">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">Ekspor / Impor CSV</p>
                        <p class="text-xs text-slate-500">Laporan dan migrasi data</p>
                    </div>
                </a>
            </div>

            <!-- Recent Surat -->
            <div class="lg:col-span-2 glass-card overflow-hidden">
                <div class="p-5 border-b border-slate-200/50 dark:border-slate-700/50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Surat Terbaru</h3>
                    <a href="{{ route('surat-masuk.index') }}" class="text-sm text-sky-600 dark:text-sky-400 hover:underline">Lihat Semua</a>
                </div>
                @if($suratTerbaru->count() > 0)
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($suratTerbaru as $surat)
                    <a href="{{ route('surat-masuk.show', $surat) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $surat->no_agenda }}</p>
                                <span class="badge {{ $surat->getSifatBadgeClass() }}">{{ ucfirst($surat->sifat) }}</span>
                            </div>
                            <p class="text-xs text-slate-500 truncate mt-0.5">{{ $surat->pengirim }} {{ Str::limit($surat->perihal, 60) }}</p>
                        </div>
                        <span class="text-xs text-slate-400 flex-shrink-0">{{ $surat->tanggal_masuk->format('d/m/Y') }}</span>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="p-10 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    <p class="text-sm">Belum ada surat masuk</p>
                    <a href="{{ route('surat-masuk.create') }}" class="text-sm text-sky-600 hover:underline mt-1 inline-block">Tambah surat pertama</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Surat per Bulan Chart
        const monthlyData = @json($suratPerBulan);
        const labels = monthlyData.map(d => {
            const [y, m] = d.bulan.split('-');
            const monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
            return monthNames[parseInt(m)-1] + ' ' + y;
        });

        new Chart(document.getElementById('chartSuratPerBulan'), {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['Belum ada data'],
                datasets: [{
                    label: 'Surat Masuk',
                    data: monthlyData.map(d => d.jumlah),
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(255, 255, 255, 0.93)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointBackgroundColor: '#0ea5e9',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(148, 163, 184, 0.1)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Sifat Doughnut Chart
        const sifatData = @json($distribusiSifat);
        const sifatLabels = ['Biasa', 'Penting', 'Segera', 'Rahasia'];
        const sifatKeys = ['biasa', 'penting', 'segera', 'rahasia'];
        const sifatColors = ['#94a3b8', '#f59e0b', '#f97316', '#ef4444'];

        new Chart(document.getElementById('chartSifat'), {
            type: 'doughnut',
            data: {
                labels: sifatLabels,
                datasets: [{
                    data: sifatKeys.map(k => sifatData[k] || 0),
                    backgroundColor: sifatColors,
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15, usePointStyle: true, pointStyle: 'circle' }
                    }
                }
            }
        });
    });
    </script>
</x-app-layout>