<!DOCTYPE html>

<html lang="en"  data-theme="light">
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'App')</title>
    
    @vite('resources/css/app.css')
    @livewireStyles
    @fluxAppearance

</head>

<body class="bg-white">
    @include('livewire.components.partials.navbar')

    <main class="container mx-auto py-8">
        {{ $slot }} {{-- penting: Livewire mengisi konten view di sini --}}
    </main>

    @include('livewire.components.partials.footer')

    @livewireScripts
    @vite('resources/js/app.js')
    @fluxScripts
=======
    @fluxAppearance
</body>

</html>