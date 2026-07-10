<x-app-layout>
    <x-slot name="title">Edit Surat: {{ $suratMasuk->no_agenda }}</x-slot>

    <div class="animate-fade-in-up max-w-5xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('surat-masuk.show', $suratMasuk) }}" class="btn-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="section-title">Edit Surat Masuk</h1>
                <p class="section-subtitle">{{ $suratMasuk->no_agenda }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('surat-masuk.update', $suratMasuk) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Data Forms -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="glass-card p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 border-b pb-2">Data Penerimaan</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">No Agenda <span class="text-red-500">*</span></label>
                                <input type="text" name="no_agenda" value="{{ old('no_agenda', $suratMasuk->no_agenda) }}" class="form-input" required>
                                @error('no_agenda') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="form-label">Tanggal Masuk <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $suratMasuk->tanggal_masuk->format('Y-m-d')) }}" class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">Penerima <span class="text-red-500">*</span></label>
                                <input type="text" name="penerima" value="{{ old('penerima', $suratMasuk->penerima) }}" class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">Sifat</label>
                                <select name="sifat" class="form-select">
                                    <option value="">- Pilih Sifat -</option>
                                    <option value="biasa" {{ old('sifat', $suratMasuk->sifat) == 'biasa' ? 'selected' : '' }}>Biasa</option>
                                    <option value="penting" {{ old('sifat', $suratMasuk->sifat) == 'penting' ? 'selected' : '' }}>Penting</option>
                                    <option value="segera" {{ old('sifat', $suratMasuk->sifat) == 'segera' ? 'selected' : '' }}>Segera</option>
                                    <option value="rahasia" {{ old('sifat', $suratMasuk->sifat) == 'rahasia' ? 'selected' : '' }}>Rahasia</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 border-b pb-2">Data Surat</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Nomor Surat</label>
                                <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $suratMasuk->nomor_surat) }}" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Pengirim</label>
                                <input type="text" name="pengirim" value="{{ old('pengirim', $suratMasuk->pengirim) }}" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Tanggal Surat</label>
                                <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', $suratMasuk->tanggal_surat ? $suratMasuk->tanggal_surat->format('Y-m-d') : '') }}" class="form-input">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Perihal</label>
                                <textarea name="perihal" rows="3" class="form-input">{{ old('perihal', $suratMasuk->perihal) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6 border-t pt-4">
                            <p class="font-medium text-slate-700 dark:text-slate-300 mb-3">Detail Acara (Opsional)</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Hari</label>
                                    <input type="text" name="hari_acara" value="{{ old('hari_acara', $suratMasuk->hari_acara) }}" class="form-input">
                                </div>
                                <div>
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" name="tanggal_acara" value="{{ old('tanggal_acara', $suratMasuk->tanggal_acara ? $suratMasuk->tanggal_acara->format('Y-m-d') : '') }}" class="form-input">
                                </div>
                                <div>
                                    <label class="form-label">Waktu</label>
                                    <input type="text" name="waktu_acara" value="{{ old('waktu_acara', $suratMasuk->waktu_acara) }}" class="form-input">
                                </div>
                                <div>
                                    <label class="form-label">Tempat</label>
                                    <input type="text" name="tempat_acara" value="{{ old('tempat_acara', $suratMasuk->tempat_acara) }}" class="form-input">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="btn-primary">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('surat-masuk.show', $suratMasuk) }}" class="btn-secondary">Batal</a>
                    </div>
                </div>

                <!-- Right: File Upload -->
                <div class="space-y-6">
                    <div class="glass-card p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Ganti PDF (Opsional)</h3>
                        @if($suratMasuk->file_path)
                            <div class="mb-4 p-3 bg-sky-50 dark:bg-sky-900/30 rounded-xl flex items-start gap-3">
                                <svg class="w-5 h-5 text-sky-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-sm font-medium text-sky-900 dark:text-sky-100">Surat ini sudah memiliki file.</p>
                                    <p class="text-xs text-sky-700 dark:text-sky-300 mt-1">Mengupload file baru akan menggantikan file yang lama.</p>
                                </div>
                            </div>
                        @endif

                        <input type="file" name="file_pdf" accept=".pdf" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        @error('file_pdf') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>