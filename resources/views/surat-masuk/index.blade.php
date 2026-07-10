<x-app-layout>
    <x-slot name="title">Daftar Surat Masuk</x-slot>

    <div class="animate-fade-in-up">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="section-title">Surat Masuk</h1>
                <p class="section-subtitle">Kelola semua arsip surat masuk</p>
            </div>
            <a href="{{ route('surat-masuk.create') }}" class="mt-4 sm:mt-0 btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Surat
            </a>
        </div>

        <!-- Filters -->
        <div class="glass-card p-4 mb-6">
            <form method="GET" action="{{ route('surat-masuk.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

                <div>
                    <input type="text" name="pengirim" value="{{ request('pengirim') }}" placeholder="Pengirim" class="form-input">
                </div>
                <div>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="form-input" placeholder="Dari Tanggal">
                </div>
                <div>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="form-input" placeholder="Sampai Tanggal">
                </div>
                <div class="flex gap-2">
                    <select name="sifat" class="form-select flex-1">
                        <option value="">Semua Sifat</option>
                        <option value="biasa" {{ request('sifat') == 'biasa' ? 'selected' : '' }}>Biasa</option>
                        <option value="penting" {{ request('sifat') == 'penting' ? 'selected' : '' }}>Penting</option>
                        <option value="segera" {{ request('sifat') == 'segera' ? 'selected' : '' }}>Segera</option>
                        <option value="rahasia" {{ request('sifat') == 'rahasia' ? 'selected' : '' }}>Rahasia</option>
                    </select>
                    <button type="submit" class="btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </button>
                    @if(request()->hasAny(['pengirim','tanggal_dari','tanggal_sampai','sifat']))
                    <a href="{{ route('surat-masuk.index') }}" class="btn-secondary btn-sm" title="Reset">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="glass-card">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No Agenda</th>

                            <th>Pengirim</th>
                            <th>Perihal</th>
                            <th>Sifat</th>
                            <th>Tgl Masuk</th>
                            <th>PDF</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suratList as $surat)
                        <tr class="animate-fade-in-up">
                            <td class="font-mono text-xs font-semibold">{{ $surat->no_agenda }}</td>

                            <td>{{ $surat->pengirim ?? '-' }}</td>
                            <td class="max-w-xs truncate">{{ Str::limit($surat->perihal, 50) }}</td>
                            <td>
                                @if($surat->sifat)
                                    <span class="badge {{ $surat->getSifatBadgeClass() }}">{{ ucfirst($surat->sifat) }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="text-sm">{{ $surat->tanggal_masuk->format('d/m/Y') }}</td>
                            <td>
                                @if($surat->file_path)
                                <span class="inline-flex items-center text-emerald-600 dark:text-emerald-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                @else
                                <span class="inline-flex items-center text-red-500/70 dark:text-red-400/70" title="Tidak ada file">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('surat-masuk.show', $surat) }}" class="btn-icon" title="Lihat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('surat-masuk.edit', $surat) }}" class="btn-icon" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @if($surat->file_path)
                                    <a href="{{ route('file.download', $surat) }}" class="btn-icon" title="Download PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    @endif
                                    <form method="POST" action="{{ route('surat-masuk.destroy', $surat) }}" onsubmit="confirmSubmit(event, { title: 'Hapus Surat', message: 'Yakin ingin menghapus surat ini? Data yang dihapus tidak dapat dikembalikan.', type: 'danger', confirmText: 'Ya, Hapus' })">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon text-red-500 hover:text-red-700" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p>Belum ada surat masuk</p>
                                <a href="{{ route('surat-masuk.create') }}" class="text-sky-600 hover:underline text-sm mt-1 inline-block">Tambah surat pertama</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer: Per Page & Pagination -->
            <div class="px-4 py-3 border-t border-slate-200/50 dark:border-slate-700/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-1.5">
                        <span>Tampilkan</span>
                        <select id="perPageSelect" onchange="changePerPage(this.value)"
                            class="h-8 min-w-[3.5rem] rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium py-1 pl-2.5 pr-7 focus:ring-1 focus:ring-sky-500 focus:border-sky-500 cursor-pointer transition-colors appearance-none"
                            style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22%236b7280%22><path fill-rule=%22evenodd%22 d=%22M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z%22 clip-rule=%22evenodd%22/></svg>'); background-position: right 0.4rem center; background-repeat: no-repeat; background-size: 1em;">
                            @foreach([25, 50, 100, 200] as $option)
                                <option value="{{ $option }}" {{ request('per_page', 25) == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="text-slate-400 dark:text-slate-500">|</span>
                    <span>{{ $suratList->firstItem() ?? 0 }}-{{ $suratList->lastItem() ?? 0 }} dari {{ $suratList->total() }}</span>
                </div>
                @if($suratList->hasPages())
                <div>
                    {{ $suratList->links('vendor.pagination.custom') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function changePerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page'); // Reset ke halaman 1
            window.location.href = url.toString();
        }
    </script>
</x-app-layout>