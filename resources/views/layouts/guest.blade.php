<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SPP SMK Bina Bangsa') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
        </style>
    </head>
    <body class="h-full bg-gradient-to-br from-slate-50 via-sky-50/40 to-emerald-50/30 text-slate-900 antialiased flex flex-col justify-center items-center p-4">
        
        <!-- Decorative Ambient Blur -->
        <div class="fixed top-0 left-1/4 w-96 h-96 bg-emerald-200/25 rounded-full blur-3xl pointer-events-none -z-10"></div>
        <div class="fixed bottom-0 right-1/4 w-96 h-96 bg-sky-200/25 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <div class="w-full sm:max-w-md bg-white/95 backdrop-blur-md p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100">
            {{ $slot }}
        </div>

        <!-- Footer Copyright -->
        <div class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} SMK Bina Bangsa • Sistem Pembayaran SPP Virtual Account
        </div>
    </body>
</html>
