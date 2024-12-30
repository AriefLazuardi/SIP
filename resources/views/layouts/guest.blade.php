<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-customColor antialiased">
<div class="flex min-h-screen w-full">

        <!-- Kolom untuk tulisan di kiri atas -->
        <div class="w-full lg:w-3/4 flex items-start justify-start p-4 bg-gradient-to-tl from-darkPrimaryColor to-lightColor">
            <h1 class="text-7xl text-whiteColor font-bold mx-12">Sistem<br>Informasi<br> Penjadwalan</h1>
        </div>

        <!-- Kolom untuk konten lain -->
        <div class="w-full lg:w-1/2 flex items-center justify-center">
            <div class="w-full ml-16 mr-12 bg-white overflow-hidden rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>