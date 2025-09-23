<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Auth')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/kebun-raya-bogor.png') }}" />
    @vite('resources/css/app.css')
    @livewireStyles
    @fluxAppearance
</head>

<body class="bg-white min-h-screen">

    {{-- biasanya login/register tanpa navbar/footer; kalau mau, tinggal include --}}
    {{-- @include('livewire.components.partials.navbar') --}}

    <main class="">
        {{ $slot }}
    </main>

    {{-- @include('livewire.components.partials.footer') --}}

    @livewireScripts
    @vite('resources/js/app.js')
    @fluxScripts {{-- <— sebelumnya kamu taruh @fluxAppearance di sini; yang benar @fluxScripts --}} </body>

</html>