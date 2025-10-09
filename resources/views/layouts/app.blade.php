{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'App' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/kebun-raya-bogor.png') }}" />

    {{-- Tailwind + your app entry (NO Alpine import inside app.js) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Flux (appearance only once) --}}
    @fluxAppearance

    {{-- Livewire v3 styles --}}
    @livewireStyles
</head>

<body class="bg-white min-h-screen" data-theme="light">
    {{-- Navbar --}}
    @include('livewire.components.partials.navbar')

    <main class="container mx-auto py-8">
        {{ $slot }}
    </main>

    {{-- Floating chat button --}}
    <div class="fixed bottom-6 right-6 z-50">
        <button
            class="bg-black hover:bg-black text-white p-4 rounded-full shadow-lg transition-all duration-300 hover:scale-110 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <div
                class="absolute bottom-full right-0 mb-2 px-3 py-1 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                Chat with us!
            </div>
        </button>
    </div>

    {{-- Footer & toast --}}
    @include('livewire.components.partials.footer')
    @livewire('components.ui.toast')

    {{-- Livewire v3 scripts (must be present, once, near the end) --}}
    @livewireScripts

    {{-- Flux scripts (once) --}}
    @fluxScripts

    {{-- Optional tiny diag: ensure Livewire client is present --}}
    <script>
        window.addEventListener('load', () => {
            console.log('[LAYOUT DEBUG] Livewire loaded:', !!window.Livewire);
        });
    </script>
</body>

</html>