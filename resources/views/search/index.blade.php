<x-app-layout>
    <x-slot name="title">Pencarian Lanjut</x-slot>

    <div class="animate-fade-in-up">
        <div class="mb-6">
            <h1 class="section-title">Pencarian Surat</h1>
            <p class="section-subtitle">Temukan arsip surat dengan kombinasi filter dan pencarian teks</p>
        </div>

        <div class="glass-card p-6 mb-8 border-t-4 border-sky-500">
            <form method="GET" action="{{ route('search.results') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div class="lg:col-span-3">
                        <label class="form-label font-bold text-sky-600 dark:text-sky-400">Kata Kunci Perihal / Isi Surat</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Ketik kata kunci perihal/isi surat" class="form-input pl-10 text-lg py-3">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">No Agenda</label>
                        <input type="text" name="no_agenda" value="{{ request('no_agenda') }}" placeholder="Cari No Agenda" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Diterima Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Diterima Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="form-input">
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Terapkan Pencarian
                    </button>
                    @if(request()->hasAny(['keyword', 'no_agenda', 'pengirim', 'sifat', 'tanggal_dari', 'tanggal_sampai']))
                    <a href="{{ route('search.index') }}" class="btn-secondary">
                        Reset Filter
                    </a>
                    @endif
                </div>
            </form>
        </div>

        @if(isset($results))
        <div>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Hasil Pencarian <span class="text-slate-500 text-sm font-normal">({{ $results->total() }} ditemukan)</span></h3>
            
            <div class="glass-card overflow-hidden">
                @if($results->count() > 0)
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($results as $surat)
                    <div class="p-5 hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-sky-700 dark:text-sky-400 text-lg">
                                        <a href="{{ route('surat-masuk.show', $surat) }}" class="hover:underline">{{ $surat->no_agenda }}</a>
                                    </h4>
                                    <span class="badge {{ $surat->getSifatBadgeClass() }}">{{ ucfirst($surat->sifat) }}</span>
                                    @if(isset($surat->relevance_score) && $surat->relevance_score > 0)
                                    <span class="text-[10px] bg-slate-100 dark:bg-slate-700 text-slate-500 px-2 py-0.5 rounded-full font-mono">
                                        Score: {{ number_format($surat->relevance_score, 2) }}
                                    </span>
                                    @endif
                                </div>
                                <p class="text-sm font-medium text-slate-800 dark:text-slate-200 mb-2">{{ $surat->pengirim ?? 'Pengirim Tidak Diketahui' }}</p>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    {{ Str::limit($surat->perihal, 250) }}
                                </p>
                                <div class="mt-3 flex items-center gap-4 text-xs text-slate-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Diterima: {{ $surat->tanggal_masuk?->format('d/m/Y') ?? '-' }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Penerima: {{ $surat->penerima }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex sm:flex-col gap-2">
                                <a href="{{ route('surat-masuk.show', $surat) }}" class="btn-secondary btn-sm whitespace-nowrap">Lihat Detail</a>
                                @if($surat->file_path)
                                <a href="{{ route('file.download', $surat) }}" class="btn-primary btn-sm whitespace-nowrap text-center">Unduh PDF</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="p-4 border-t border-slate-200/50 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-800">
                    {{ $results->links() }}
                </div>
                @else
                <div class="p-12 text-center text-slate-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-lg font-medium text-slate-700 dark:text-slate-300">Pencarian tidak menemukan hasil</p>
                    <p class="text-sm mt-1">Coba gunakan kata kunci yang lebih umum atau kurangi filter.</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</x-app-layout>