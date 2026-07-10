<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Penerimaan Surat') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900 relative selection:bg-sky-500 selection:text-white">
        <!-- Decorative Background Elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-sky-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
            <div class="absolute top-40 -left-40 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-40 left-20 w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000"></div>
            <div class="absolute inset-0 bg-white/40 backdrop-blur-[2px]"></div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-8 transform transition-transform hover:scale-105 duration-300">
                <a href="/">
                    <img src="{{ asset('logo.png') }}" alt="Logo Penerimaan Surat" class="w-28 h-28 object-contain drop-shadow-lg rounded-2xl bg-white p-2 border border-slate-100" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-2 px-8 py-10 bg-white/80 backdrop-blur-xl drop-shadow-lg overflow-hidden sm:rounded-3xl border border-white relative">
                <!-- Subtle top highlight for the card -->
                <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-sky-400 via-blue-500 to-indigo-500"></div>
                
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-sm text-slate-500 font-medium tracking-wide">
                &copy; {{ date('Y') }} Penerimaan Surat. All rights reserved.
            </div>
        </div>
    </body>
</html>