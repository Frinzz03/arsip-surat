<x-app-layout>
    <x-slot name="title">Edit User: {{ $user->name }}</x-slot>

    <div class="animate-fade-in-up max-w-2xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('users.index') }}" class="btn-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="section-title">Edit User</h1>
                <p class="section-subtitle">Perbarui data atau role untuk {{ $user->name }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('users.update', $user) }}" onsubmit="if(document.querySelector('input[name=\'role\']:checked').value === 'admin' && '{{ $user->role }}' !== 'admin') { return confirm('Apakah anda yakin untuk mengubah user ini menjadi admin?'); }">
            @csrf
            @method('PUT')
            
            <div class="glass-card p-6 space-y-6">
                <div>
                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                    <h4 class="text-sm font-semibold text-amber-800 dark:text-amber-300 mb-3">Ubah Password (Opsional)</h4>
                    <p class="text-xs text-amber-700 dark:text-amber-400 mb-3">Kosongkan jika tidak ingin mengubah password.</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-input">
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-input">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="form-label">Role Akses <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-slate-800 p-4 shadow-sm focus:outline-none {{ old('role', $user->role) == 'staf' ? 'border-sky-500 ring-1 ring-sky-500' : 'border-slate-300 dark:border-slate-600' }}">
                            <input type="radio" name="role" value="staf" class="sr-only" required {{ old('role', $user->role) == 'staf' ? 'checked' : '' }} onchange="this.closest('.grid').querySelectorAll('label').forEach(l => l.classList.remove('border-sky-500', 'ring-1', 'ring-sky-500')); this.parentNode.classList.add('border-sky-500', 'ring-1', 'ring-sky-500');">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-slate-900 dark:text-white">Staf</span>
                                    <span class="mt-1 flex items-center text-xs text-slate-500">Akses entry dan pencarian data biasa.</span>
                                </span>
                            </span>
                        </label>
                        
                        <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-slate-800 p-4 shadow-sm focus:outline-none {{ old('role', $user->role) == 'admin' ? 'border-sky-500 ring-1 ring-sky-500' : 'border-slate-300 dark:border-slate-600' }}">
                            <input type="radio" name="role" value="admin" class="sr-only" required {{ old('role', $user->role) == 'admin' ? 'checked' : '' }} onchange="this.closest('.grid').querySelectorAll('label').forEach(l => l.classList.remove('border-sky-500', 'ring-1', 'ring-sky-500')); this.parentNode.classList.add('border-sky-500', 'ring-1', 'ring-sky-500');">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-slate-900 dark:text-white">Admin</span>
                                    <span class="mt-1 flex items-center text-xs text-slate-500">Akses penuh termasuk manajemen user.</span>
                                </span>
                            </span>
                        </label>
                    </div>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    
                    @if($user->id === auth()->id())
                    <p class="text-xs text-amber-600 mt-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Peringatan: Mengubah role Anda sendiri dapat menyebabkan Anda kehilangan akses ke halaman ini.
                    </p>
                    @endif
                </div>
            </div>
            
            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                <a href="{{ route('users.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>