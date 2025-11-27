<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'App' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/kebun-raya-bogor.png') }}" />
    @vite('resources/css/app.css')
    @livewireStyles
    @fluxAppearance
</head>

<body class="bg-white min-h-screen">

    <main class="">
        {{ $slot }}
    </main>

    @livewireScripts
    @vite('resources/js/app.js')
    @fluxScripts </body>

</html>