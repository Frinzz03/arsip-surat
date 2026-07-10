<x-app-layout>
    <x-slot name="title">Kelola User (Admin)</x-slot>

    <div class="animate-fade-in-up">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="section-title">Kelola User</h1>
                <p class="section-subtitle">Manajemen akses dan akun sistem (Hanya Admin)</p>
            </div>
            <a href="{{ route('users.create') }}" class="mt-4 sm:mt-0 btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah User Baru
            </a>
        </div>

        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Role / Hak Akses</th>
                            <th>Tgl Terdaftar</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50">
                            <td class="font-medium">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center text-white text-xs font-semibold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->isAdmin())
                                    <span class="badge bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300">Admin</span>
                                @else
                                    <span class="badge bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">Staf</span>
                                @endif
                            </td>
                            <td class="text-sm">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('users.edit', $user) }}" class="btn-icon text-sky-600 hover:text-sky-800" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    
                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="confirmSubmit(event, { title: 'Hapus Akun', message: 'Anda yakin ingin menghapus akun {{ $user->name }}? Akses sistem akan dicabut.', type: 'danger', confirmText: 'Ya, Hapus' })">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon text-red-500 hover:text-red-700" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @else
                                    <span class="w-8"></span> <!-- placeholder agar sejajar -->
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
            <div class="px-4 py-3 border-t border-slate-200/50 dark:border-slate-700/50">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>