<x-app-layout>
    <x-slot name="title">Tambah Surat Masuk</x-slot>

    <div class="animate-fade-in-up max-w-5xl">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('surat-masuk.index') }}" class="btn-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="section-title">Tambah Surat Masuk</h1>
                <p class="section-subtitle">Input data surat baru manual atau otomatis dari PDF</p>
            </div>
        </div>

        <form method="POST" action="{{ route('surat-masuk.store') }}" enctype="multipart/form-data" id="formSuratMasuk">
            @csrf

            @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Terjadi kesalahan pada isian form</h3>
                        <ul class="mt-1 text-sm text-red-700 dark:text-red-300 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400 font-semibold">Catatan: Harap unggah ulang file PDF Anda karena file tersebut di-reset saat terjadi error.</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Manual Input -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Section 1: Data Manual -->
                    <div class="glass-card p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-sky-100 dark:bg-sky-900/50 flex items-center justify-center text-xs font-bold text-sky-600">1</div>
                            Data Penerimaan
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">No Agenda <span class="text-red-500">*</span></label>
                                <input type="text" name="no_agenda" value="{{ old('no_agenda') }}" class="form-input" required placeholder="000.0/0.000">
                                @error('no_agenda') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Tanggal Masuk <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" class="form-input" required>
                                @error('tanggal_masuk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Penerima <span class="text-red-500">*</span></label>
                                <input type="text" name="penerima" value="{{ old('penerima') }}" class="form-input" required placeholder="Nama penerima di institusi">
                                @error('penerima') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Sifat</label>
                                <select name="sifat" class="form-select">
                                    <option value="">- Pilih Sifat -</option>
                                    <option value="biasa" {{ old('sifat') == 'biasa' ? 'selected' : '' }}>Biasa</option>
                                    <option value="penting" {{ old('sifat') == 'penting' ? 'selected' : '' }}>Penting</option>
                                    <option value="segera" {{ old('sifat') == 'segera' ? 'selected' : '' }}>Segera</option>
                                    <option value="rahasia" {{ old('sifat') == 'rahasia' ? 'selected' : '' }}>Rahasia</option>
                                </select>
                                @error('sifat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Data Surat (Autofill area) -->
                    <div class="glass-card p-6" id="autofillSection">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center text-xs font-bold text-emerald-600">2</div>
                            Data Surat
                            <span class="text-xs font-normal text-slate-500">(Otomatis dari PDF atau isi manual)</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Nomor Surat</label>
                                <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat') }}" class="form-input" placeholder="Nomor dari surat">
                            </div>
                            <div>
                                <label class="form-label">Pengirim</label>
                                <input type="text" name="pengirim" id="pengirim" value="{{ old('pengirim') }}" class="form-input" placeholder="Asal surat">
                            </div>
                            <div>
                                <label class="form-label">Tanggal Surat</label>
                                <input type="date" name="tanggal_surat" id="tanggal_surat" value="{{ old('tanggal_surat') }}" class="form-input">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Perihal / Isi Surat</label>
                                <textarea name="perihal" id="perihal" rows="3" class="form-input" placeholder="Perihal atau ringkasan isi surat">{{ old('perihal') }}</textarea>
                            </div>
                        </div>

                        <!-- Acara Details (collapsible) -->
                        <div x-data="{ showAcara: false }" class="mt-4">
                            <button type="button" @click="showAcara = !showAcara" class="text-sm text-sky-600 dark:text-sky-400 hover:underline flex items-center gap-1">
                                <svg class="w-4 h-4 transition-transform" :class="showAcara ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                Detail Acara (jika undangan)
                            </button>
                            <div x-show="showAcara" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <label class="form-label">Hari Acara</label>
                                    <input type="text" name="hari_acara" id="hari_acara" value="{{ old('hari_acara') }}" class="form-input" placeholder="Senin">
                                </div>
                                <div>
                                    <label class="form-label">Tanggal Acara</label>
                                    <input type="date" name="tanggal_acara" id="tanggal_acara" value="{{ old('tanggal_acara') }}" class="form-input">
                                </div>
                                <div>
                                    <label class="form-label">Waktu Acara</label>
                                    <input type="text" name="waktu_acara" id="waktu_acara" value="{{ old('waktu_acara') }}" class="form-input" placeholder="09.00 WIB">
                                </div>
                                <div>
                                    <label class="form-label">Tempat Acara</label>
                                    <input type="text" name="tempat_acara" id="tempat_acara" value="{{ old('tempat_acara') }}" class="form-input" placeholder="Aula Gedung ...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center gap-3">
                        <button type="submit" class="btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Surat
                        </button>
                        <a href="{{ route('surat-masuk.index') }}" class="btn-secondary">Batal</a>
                    </div>
                </div>

                <!-- Right: PDF Upload -->
                <div class="space-y-6">
                    <div class="glass-card p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            Upload PDF <span class="text-xs font-normal text-slate-500">(Opsional - Bisa diupload nanti saat edit)</span>
                        </h3>

                        <!-- Dropzone -->
                        <div
                            id="dropzone"
                            class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-6 text-center hover:border-sky-400 hover:bg-sky-50/50 dark:hover:bg-sky-900/20 transition-all cursor-pointer"
                            onclick="document.getElementById('file_pdf').click()"
                        >
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Klik atau seret file PDF</p>
                            <p class="text-xs text-slate-400 mt-1">Maks. 10MB</p>
                            <input type="file" name="file_pdf" id="file_pdf" accept=".pdf" class="hidden" onchange="handleFileSelect(this)">
                        </div>

                        <!-- File info -->
                        <div id="fileInfo" class="hidden mt-3 p-3 bg-slate-50 dark:bg-slate-700 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM14 8V2l6 6h-6z"/></svg>
                                <span id="fileName" class="text-sm font-medium truncate"></span>
                            </div>
                            <button type="button" onclick="clearFile()" class="p-1 rounded-full text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Batal upload">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Extract button -->
                        <button type="button" id="btnExtract" class="hidden w-full mt-3 btn-success" onclick="extractPdf()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            <span id="btnExtractText">Ekstrak Data Otomatis</span>
                        </button>

                        <!-- Extraction status -->
                        <div id="extractStatus" class="hidden mt-3 p-3 rounded-xl text-sm"></div>

                        @error('file_pdf') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>

                    <!-- PDF Preview -->
                    <div id="pdfPreview" class="hidden glass-card overflow-hidden">
                        <div class="p-3 border-b border-slate-200/50 dark:border-slate-700/50">
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Pratinjau PDF</p>
                        </div>
                        <iframe id="pdfFrame" class="w-full h-80 border-0"></iframe>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
    function clearFile() {
        const fileInput = document.getElementById('file_pdf');
        fileInput.value = ''; // Clear the input
        
        // Hide UI elements
        document.getElementById('fileInfo').classList.add('hidden');
        document.getElementById('btnExtract').classList.add('hidden');
        document.getElementById('pdfPreview').classList.add('hidden');
        document.getElementById('extractStatus').classList.add('hidden');
        
        // Clear iframe source
        document.getElementById('pdfFrame').src = '';
    }

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileInfo').classList.remove('hidden');
            document.getElementById('btnExtract').classList.remove('hidden');

            // Preview PDF
            const url = URL.createObjectURL(file);
            document.getElementById('pdfFrame').src = url;
            document.getElementById('pdfPreview').classList.remove('hidden');
        }
    }

    // Drag and drop
    const dropzone = document.getElementById('dropzone');
    ['dragenter', 'dragover'].forEach(e => {
        dropzone.addEventListener(e, (ev) => {
            ev.preventDefault();
            dropzone.classList.add('border-sky-500', 'bg-sky-50');
        });
    });
    ['dragleave', 'drop'].forEach(e => {
        dropzone.addEventListener(e, (ev) => {
            ev.preventDefault();
            dropzone.classList.remove('border-sky-500', 'bg-sky-50');
        });
    });
    dropzone.addEventListener('drop', (ev) => {
        const files = ev.dataTransfer.files;
        if (files.length && files[0].type === 'application/pdf') {
            document.getElementById('file_pdf').files = files;
            handleFileSelect(document.getElementById('file_pdf'));
        }
    });

    function extractPdf() {
        const fileInput = document.getElementById('file_pdf');
        if (!fileInput.files[0]) return;

        const btn = document.getElementById('btnExtract');
        const btnText = document.getElementById('btnExtractText');
        const status = document.getElementById('extractStatus');

        btn.disabled = true;
        btnText.textContent = 'Mengekstrak...';
        status.className = 'mt-3 p-3 rounded-xl text-sm bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 flex items-center gap-2';
        status.innerHTML = '<div class="spinner"></div> Memproses PDF, mohon tunggu...';
        status.classList.remove('hidden');

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        fetch('{{ route("surat-masuk.extract-pdf") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data) {
                const d = data.data;
                if (d.nomor_surat) document.getElementById('nomor_surat').value = d.nomor_surat;
                if (d.pengirim) document.getElementById('pengirim').value = d.pengirim;
                if (d.tanggal_surat) document.getElementById('tanggal_surat').value = d.tanggal_surat;
                if (d.perihal) document.getElementById('perihal').value = d.perihal;
                if (d.hari_acara) document.getElementById('hari_acara').value = d.hari_acara;
                if (d.tanggal_acara) document.getElementById('tanggal_acara').value = d.tanggal_acara;
                if (d.waktu_acara) document.getElementById('waktu_acara').value = d.waktu_acara;
                if (d.tempat_acara) document.getElementById('tempat_acara').value = d.tempat_acara;

                status.className = 'mt-3 p-3 rounded-xl text-sm bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300';
                status.innerHTML = 'Data berhasil diekstrak! Silakan review dan perbaiki jika diperlukan.';

                // Highlight autofilled fields
                document.querySelectorAll('#autofillSection input, #autofillSection textarea').forEach(el => {
                    if (el.value) {
                        el.classList.add('ring-2', 'ring-emerald-300', 'bg-emerald-50');
                        setTimeout(() => el.classList.remove('ring-2', 'ring-emerald-300', 'bg-emerald-50'), 3000);
                    }
                });
            } else {
                status.className = 'mt-3 p-3 rounded-xl text-sm bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300';
                status.innerHTML = (data.message || 'Gagal mengekstrak. Silakan isi manual.');
            }
        })
        .catch(() => {
            status.className = 'mt-3 p-3 rounded-xl text-sm bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300';
            status.innerHTML = 'Service ekstraksi tidak tersedia. Silakan isi data secara manual.';
        })
        .finally(() => {
            btn.disabled = false;
            btnText.textContent = 'Ekstrak Data Otomatis';
        });
    }
    </script>
</x-app-layout>