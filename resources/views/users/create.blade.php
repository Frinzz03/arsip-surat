<x-app-layout>
    <x-slot name="title">Tambah User Baru</x-slot>

    <div class="animate-fade-in-up max-w-2xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('users.index') }}" class="btn-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="section-title">Tambah User Baru</h1>
                <p class="section-subtitle">Buat akun untuk staf atau admin baru</p>
            </div>
        </div>

        <form method="POST" action="{{ route('users.store') }}" onsubmit="if(document.querySelector('input[name=\'role\']:checked').value === 'admin') { return confirm('Apakah anda yakin untuk menambah admin baru?'); }">
            @csrf
            
            <div class="glass-card p-6 space-y-6">
                <div>
                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" required autofocus>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="form-input" required>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>

                <div>
                    <label class="form-label">Role Akses <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-slate-800 p-4 shadow-sm focus:outline-none {{ old('role', 'staf') == 'staf' ? 'border-sky-500 ring-1 ring-sky-500' : 'border-slate-300 dark:border-slate-600' }}">
                            <input type="radio" name="role" value="staf" class="sr-only" required {{ old('role', 'staf') == 'staf' ? 'checked' : '' }} onchange="this.closest('.grid').querySelectorAll('label').forEach(l => l.classList.remove('border-sky-500', 'ring-1', 'ring-sky-500')); this.parentNode.classList.add('border-sky-500', 'ring-1', 'ring-sky-500');">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-slate-900 dark:text-white">Staf</span>
                                    <span class="mt-1 flex items-center text-xs text-slate-500">Akses entry dan pencarian data biasa.</span>
                                </span>
                            </span>
                            <svg class="h-5 w-5 text-sky-600 {{ old('role', 'staf') == 'staf' ? 'block' : 'hidden' }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                        </label>
                        
                        <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-slate-800 p-4 shadow-sm focus:outline-none {{ old('role') == 'admin' ? 'border-sky-500 ring-1 ring-sky-500' : 'border-slate-300 dark:border-slate-600' }}">
                            <input type="radio" name="role" value="admin" class="sr-only" required {{ old('role') == 'admin' ? 'checked' : '' }} onchange="this.closest('.grid').querySelectorAll('label').forEach(l => l.classList.remove('border-sky-500', 'ring-1', 'ring-sky-500')); this.parentNode.classList.add('border-sky-500', 'ring-1', 'ring-sky-500');">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-slate-900 dark:text-white">Admin</span>
                                    <span class="mt-1 flex items-center text-xs text-slate-500">Akses penuh termasuk manajemen user.</span>
                                </span>
                            </span>
                            <svg class="h-5 w-5 text-sky-600 {{ old('role') == 'admin' ? 'block' : 'hidden' }}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                        </label>
                    </div>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="btn-primary">Buat Akun</button>
                <a href="{{ route('users.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>