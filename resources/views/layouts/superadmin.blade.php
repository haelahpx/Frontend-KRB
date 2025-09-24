<!DOCTYPE html>
<html lang="en" data-theme="light">
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'App' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/kebun-raya-bogor.png') }}" />
    @vite('resources/css/app.css')
    @livewireStyles
    @fluxAppearance
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-white" data-theme="light">
    <main class="container mx-auto py-8">
        {{ $slot }}
    </main>

    @livewire('components.ui.toast')

    @livewireScripts
    @vite('resources/js/app.js')
    @fluxScripts
    @fluxAppearance
</body>

</html>