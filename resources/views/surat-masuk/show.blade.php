<x-app-layout>
    <x-slot name="title">Detail Surat: {{ $suratMasuk->no_agenda }}</x-slot>

    <div class="animate-fade-in-up">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('surat-masuk.index') }}" class="btn-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="section-title">Detail Surat Masuk</h1>
                <p class="section-subtitle">{{ $suratMasuk->no_agenda }}</p>
            </div>
            <div class="ml-auto flex gap-2">
                <a href="{{ route('surat-masuk.edit', $suratMasuk) }}" class="btn-secondary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                @if($suratMasuk->file_path)
                <a href="{{ route('file.download', $suratMasuk) }}" class="btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download PDF
                </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Data Column -->
            <div class="space-y-6">
                <!-- Info Utama -->
                <div class="glass-card p-6">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Informasi Utama</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-500">Nomor Surat (Pengirim)</p>
                            <p class="text-sm font-medium mt-1">{{ $suratMasuk->nomor_surat ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-500">Pengirim</p>
                            <p class="text-sm font-medium mt-1">{{ $suratMasuk->pengirim ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-500">Sifat</p>
                            <p class="text-slate-800 dark:text-slate-200 mt-1">
                                @if($suratMasuk->sifat)
                                    <span class="badge {{ $suratMasuk->getSifatBadgeClass() }}">{{ ucfirst($suratMasuk->sifat) }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-500">Perihal</p>
                            <p class="text-sm font-medium mt-1 whitespace-pre-wrap">{{ $suratMasuk->perihal ?? '-' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase text-slate-500">Tanggal Surat</p>
                                <p class="text-sm font-medium mt-1">{{ $suratMasuk->tanggal_surat ? $suratMasuk->tanggal_surat->format('d/m/Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase text-slate-500">Tanggal Diterima</p>
                                <p class="text-sm font-medium mt-1">{{ $suratMasuk->tanggal_masuk?->format('d/m/Y') ?? '-' }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-500">Penerima (Internal)</p>
                            <p class="text-sm font-medium mt-1">{{ $suratMasuk->penerima }}</p>
                        </div>
                    </div>
                </div>

                <!-- Info Acara (Jika ada) -->
                @if($suratMasuk->hari_acara || $suratMasuk->tanggal_acara || $suratMasuk->waktu_acara || $suratMasuk->tempat_acara)
                <div class="glass-card p-6 border-l-4 border-sky-500">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Informasi Acara</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-500">Hari, Tanggal</p>
                            <p class="text-sm font-medium mt-1">
                                {{ $suratMasuk->hari_acara ? $suratMasuk->hari_acara . ', ' : '' }}
                                {{ $suratMasuk->tanggal_acara ? $suratMasuk->tanggal_acara->format('d M Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-500">Waktu</p>
                            <p class="text-sm font-medium mt-1">{{ $suratMasuk->waktu_acara ?? '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs font-semibold uppercase text-slate-500">Tempat</p>
                            <p class="text-sm font-medium mt-1">{{ $suratMasuk->tempat_acara ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="text-xs text-slate-500">
                    Diinput oleh: <strong>{{ $suratMasuk->uploader->name ?? 'System' }}</strong> pada {{ $suratMasuk->created_at->format('d/m/Y H:i') }}
                </div>
            </div>

            <!-- PDF Preview Column -->
            <div class="lg:col-span-2">
                <div class="glass-card overflow-hidden h-full min-h-[600px] flex flex-col">
                    <div class="p-4 border-b border-slate-200/50 dark:border-slate-700/50 flex justify-between items-center bg-slate-50 dark:bg-slate-800">
                        <h3 class="font-semibold text-slate-900 dark:text-white">Dokumen Digital</h3>
                        @if($suratMasuk->file_original_name)
                            <span class="text-xs text-slate-500">{{ $suratMasuk->file_original_name }}</span>
                        @endif
                    </div>
                    <div class="flex-1 bg-slate-100 dark:bg-slate-900 flex items-center justify-center">
                        @if($suratMasuk->file_path)
                            <iframe src="{{ route('file.preview', $suratMasuk) }}" class="w-full h-full border-0"></iframe>
                        @else
                            <div class="text-center p-8">
                                <svg class="w-16 h-16 mx-auto mb-4 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-slate-500 font-medium">Tidak ada dokumen PDF yang dilampirkan.</p>
                                <a href="{{ route('surat-masuk.edit', $suratMasuk) }}" class="text-sm text-sky-600 hover:underline mt-2 block">Upload file sekarang</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>