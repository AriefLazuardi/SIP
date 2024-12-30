<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-whiteColor flex items-center justify-center min-h-screen">

    <div class="flex flex-col justify-center items-center text-center bg-baseColor w-2/4 h-60 mx-auto text-customColor">
        <h1 class="text-4xl font-bold">404 - Halaman Tidak Ditemukan</h1>
        <p class="mt-4 text-lg">Maaf, halaman yang Anda cari tidak dapat ditemukan.</p>
        <a href="{{ url('/') }}" class="mt-6 w-60 px-6 py-3 bg-primaryColor text-whiteColor rounded-md flex justify-center items-center">
            Kembali ke Beranda
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>