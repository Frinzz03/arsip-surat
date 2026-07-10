<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: true, darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Sistem Manajemen Arsip Surat Masuk - Kelola arsip surat institusi secara digital">

        <title>{{ $title ?? config('app.name', 'Arsip Surat Masuk') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 transition-colors duration-300">
        <div class="flex min-h-screen">

            <!-- Sidebar -->
            <aside
                :class="sidebarOpen ? 'w-64' : 'w-20'"
                class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 transition-all duration-300 ease-in-out shadow-2xl"
            >
                <!-- Logo Section -->
                <div class="flex items-center h-16 px-4 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl overflow-hidden bg-white flex items-center justify-center shadow-sm">
                            <img src="{{ asset('logo.png') }}" alt="Logo" class="w-full h-full object-contain" />
                        </div>
                        <span x-show="sidebarOpen" x-transition class="text-sm font-bold whitespace-nowrap tracking-tight text-slate-800 dark:text-white">Arsip Disposisi Surat<br><span class="text-xs font-normal text-sky-600 dark:text-sky-400">Management System</span></span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                    <p x-show="sidebarOpen" class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Menu Utama</p>

                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Dashboard</span>
                    </a>

                    <a href="{{ route('surat-masuk.index') }}" class="sidebar-link {{ request()->routeIs('surat-masuk.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Surat Masuk</span>
                    </a>

                    <a href="{{ route('surat-masuk.create') }}" class="sidebar-link {{ request()->routeIs('surat-masuk.create') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Tambah Surat</span>
                    </a>

                    <a href="{{ route('search.index') }}" class="sidebar-link {{ request()->routeIs('search.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Pencarian</span>
                    </a>

                    <p x-show="sidebarOpen" class="px-3 mt-6 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Laporan</p>

                    <a href="{{ route('csv.index') }}" class="sidebar-link {{ request()->routeIs('csv.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Ekspor / Impor</span>
                    </a>

                    @if(Auth::user()->isAdmin())
                    <p x-show="sidebarOpen" class="px-3 mt-6 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Administrasi</p>

                    <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">Kelola User</span>
                    </a>
                    @endif
                </nav>

                <!-- Sidebar Toggle -->
                <div class="p-3 border-t border-slate-200 dark:border-slate-800">
                    <button @click="sidebarOpen = !sidebarOpen" class="w-full flex items-center justify-center p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <svg x-show="sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                        <svg x-show="!sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </aside>

            <!-- Main Content -->
            <div :class="sidebarOpen ? 'ml-64' : 'ml-20'" class="flex-1 transition-all duration-300">

                <!-- Top Navigation -->
                <header class="sticky top-0 z-40 h-16 bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-700/50">
                    <div class="flex items-center justify-between h-full px-6">
                        <!-- Quick Search -->
                        <div class="relative w-full max-w-md" x-data="{ searchOpen: false, searchTerm: '', results: [] }">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input
                                    type="text"
                                    placeholder="Cari surat... (Kode, Pengirim, Perihal)"
                                    class="w-full pl-10 pr-4 py-2 text-sm bg-slate-100 dark:bg-slate-700 border-0 rounded-xl focus:ring-2 focus:ring-sky-500 focus:bg-white dark:focus:bg-slate-600 transition-all"
                                    x-model="searchTerm"
                                    @input.debounce.300ms="
                                        if(searchTerm.length >= 2) {
                                            fetch('{{ route('search.quick') }}?q=' + encodeURIComponent(searchTerm))
                                                .then(r => r.json())
                                                .then(data => { results = data; searchOpen = true; });
                                        } else { results = []; searchOpen = false; }
                                    "
                                    @focus="if(results.length) searchOpen = true"
                                    @click.away="searchOpen = false"
                                    id="quick-search-input"
                                >
                            </div>
                            <!-- Quick Search Results Dropdown -->
                            <div x-show="searchOpen && results.length > 0" x-transition
                                class="absolute top-full mt-2 w-full bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden z-50">
                                <template x-for="item in results" :key="item.id">
                                    <a :href="item.url" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors border-b border-slate-100 dark:border-slate-700 last:border-0">
                                        <div class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-900/50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium truncate" x-text="item.no_agenda"></p>
                                            <p class="text-xs text-slate-500 truncate" x-text="item.pengirim + ' â€” ' + item.perihal"></p>
                                        </div>
                                        <span class="ml-auto text-xs text-slate-400 flex-shrink-0" x-text="item.tanggal_masuk"></span>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <!-- Right Side -->
                        <div class="flex items-center gap-3 ml-4">
                            <!-- Dark Mode Toggle -->
                            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" id="dark-mode-toggle">
                                <svg x-show="!darkMode" class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                <svg x-show="darkMode" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </button>

                            <!-- User Dropdown -->
                            <div x-data="{ userMenuOpen: false }" class="relative">
                                <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center text-white text-sm font-semibold">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <div class="hidden sm:block text-left">
                                        <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-slate-500 capitalize">{{ Auth::user()->role }}</p>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>

                                <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-transition
                                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden z-50">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            Profil
                                        </span>
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                                Logout
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Flash Messages -->
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                    class="mx-6 mt-4 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                    class="mx-6 mt-4 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
                @endif

                <!-- Page Content -->
                <main class="p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Global Confirm Modal -->
        <div x-data="confirmModal()" x-cloak
             @confirm-modal.window="openModal($event.detail)"
             x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="cancel()"></div>
            <!-- Modal -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative w-full max-w-md bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
                <!-- Icon -->
                <div class="flex justify-center pt-6">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center"
                         :class="type === 'danger' ? 'bg-red-100 dark:bg-red-900/40' : 'bg-amber-100 dark:bg-amber-900/40'">
                        <svg class="w-7 h-7" :class="type === 'danger' ? 'text-red-500' : 'text-amber-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <!-- Content -->
                <div class="px-6 pt-4 pb-2 text-center">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white" x-text="title"></h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400" x-text="message"></p>
                </div>
                <!-- Actions -->
                <div class="flex gap-3 px-6 pb-6 pt-4">
                    <button @click="cancel()"
                            class="flex-1 px-4 py-2.5 text-sm font-medium rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-slate-400">
                        Batal
                    </button>
                    <button @click="proceed()"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-xl text-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="type === 'danger' ? 'bg-red-500 hover:bg-red-600 focus:ring-red-400' : 'bg-amber-500 hover:bg-amber-600 focus:ring-amber-400'">
                        <span x-text="confirmText"></span>
                    </button>
                </div>
            </div>
        </div>

        <script>
            function confirmModal() {
                return {
                    open: false,
                    title: '',
                    message: '',
                    type: 'warning',
                    confirmText: 'Ya, Lanjutkan',
                    targetForm: null,
                    onConfirm: null,
                    openModal(detail) {
                        this.title = detail.title || 'Konfirmasi';
                        this.message = detail.message || 'Apakah Anda yakin?';
                        this.type = detail.type || 'warning';
                        this.confirmText = detail.confirmText || 'Ya, Lanjutkan';
                        this.targetForm = detail.form || null;
                        this.onConfirm = detail.onConfirm || null;
                        this.open = true;
                    },
                    cancel() {
                        this.open = false;
                        this.targetForm = null;
                        this.onConfirm = null;
                    },
                    proceed() {
                        this.open = false;
                        if (this.targetForm) {
                            this.targetForm.submit();
                        }
                        if (this.onConfirm && typeof this.onConfirm === 'function') {
                            this.onConfirm();
                        }
                    }
                }
            }

            function confirmSubmit(event, options = {}) {
                event.preventDefault();
                window.dispatchEvent(new CustomEvent('confirm-modal', {
                    detail: {
                        title: options.title || 'Konfirmasi',
                        message: options.message || 'Apakah Anda yakin?',
                        type: options.type || 'warning',
                        confirmText: options.confirmText || 'Ya, Lanjutkan',
                        form: event.target.closest('form')
                    }
                }));
            }
        </script>
    </body>
</html>