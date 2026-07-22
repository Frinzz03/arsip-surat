<x-app-layout>
    <x-slot name="title">Ekspor & Impor CSV</x-slot>

    <div class="animate-fade-in-up max-w-5xl">
        <div class="mb-6">
            <h1 class="section-title">Ekspor & Impor Data</h1>
            <p class="section-subtitle">Laporan data surat dan migrasi data massal via CSV</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Ekspor Section -->
            <div class="glass-card p-6 border-t-4 border-sky-500">
                <div class="flex items-center gap-3 mb-4 border-b pb-4 border-slate-200/50 dark:border-slate-700/50">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Ekspor Data (Laporan)</h3>
                        <p class="text-xs text-slate-500">Unduh data surat masuk dalam format CSV</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('csv.export') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select">
                            <option value="">Semua Bulan</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Tahun</label>
                        <select name="tahun" class="form-select">
                            <option value="">Semua Tahun</option>
                            @foreach(range(date('Y'), date('Y') - 5) as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Format File</label>
                        <select name="format" class="form-select">
                            <option value="xlsx" selected>XLSX (Microsoft Excel)</option>
                            <option value="csv">CSV (Comma Separated Values)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary w-full mt-2">
                        Generate & Download
                    </button>
                </form>
            </div>

            <!-- Impor Section -->
            <div class="glass-card p-6 border-t-4 border-emerald-500">
                <div class="flex items-center gap-3 mb-4 border-b pb-4 border-slate-200/50 dark:border-slate-700/50">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Impor Data Massal</h3>
                        <p class="text-xs text-slate-500">Unggah file CSV atau XLSX untuk memasukkan banyak data</p>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800 p-3 rounded-lg text-sm mb-4 border border-slate-200 dark:border-slate-700">
                    <p class="font-semibold text-slate-700 dark:text-slate-300 mb-1">Format Kolom CSV/XLSX yang didukung:</p>
                    <ol class="list-decimal pl-4 text-xs text-slate-500 space-y-1">
                        <li>NO (Diabaikan)</li>
                        <li>DARI (Pengirim)</li>
                        <li>NOMOR (Nomor Surat)</li>
                        <li>TGL SURAT (Contoh: 1 Januari 2026 atau DD/MM/YYYY)</li>
                        <li>NO AGENDA</li>
                        <li>SIFAT (biasa/penting/segera/rahasia)</li>
                        <li>DITERIMA TGL (Contoh: 1 Januari 2026 atau DD/MM/YYYY)</li>
                        <li>PERIHAL (Isi Surat)</li>
                    </ol>
                </div>

                <form method="POST" action="{{ route('csv.import') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="form-label">File CSV atau XLSX</label>
                        <input type="file" name="file_csv" accept=".csv,.txt,.xlsx" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" required>
                        @error('file_csv') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <button type="submit" class="btn-success w-full mt-2" onclick="confirmSubmit(event, { title: 'Impor Data', message: 'Pastikan format file sudah benar. Proses impor mungkin memakan waktu. Lanjutkan?', type: 'warning', confirmText: 'Ya, Mulai Impor' })">
                        Mulai Impor Data
                    </button>
                </form>

                @if(session('import_errors') && count(session('import_errors')) > 0)
                <div class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                    <p class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2">Terdapat {{ session('import_skipped') }} baris yang gagal diimpor:</p>
                    <ul class="list-disc pl-4 text-xs text-red-600 dark:text-red-400 space-y-1 max-h-32 overflow-y-auto">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>