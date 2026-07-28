<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <title>Filmincele</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-20CZKHTLS5"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-20CZKHTLS5');
    </script>
</head>
<body class="antialiased bg-gray-950 text-white min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md mx-auto px-4">
        <div class="text-center mb-8">
            <x-logo />
        </div>
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
