<!DOCTYPE html>
<html lang="en" data-theme="light">

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

<body class="min-h-screen bg-white dark:bg-zinc-800 flex">
    {{-- Mobile header only (<lg) --}}
    <flux:header class="lg:hidden fixed top-0 inset-x-0 z-50 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <div class="font-medium">Kebun Raya Bogor</div>

        <flux:spacer />

        <flux:dropdown position="top" align="start">
            <flux:profile avatar="https://fluxui.dev/img/demo/user.png" />
            <flux:menu>
                <flux:menu.radio.group>
                    <flux:menu.radio checked>{{ Auth::user()->full_name }}</flux:menu.radio>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.item
                    icon="arrow-right-start-on-rectangle"
                    as="button"
                    type="submit"
                    form="logout-form">
                    Logout
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{-- Form logout tersembunyi (di luar dropdown) --}}
    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>


    {{-- Sidebar (always full height) --}}
    @include('livewire.components.partials.superadmin.sidebar')

    <main class="flex-1 overflow-y-auto pt-14 lg:pt-0 lg:ml-[var(--sbw)] px-4 sm:px-6 lg:px-8
                [&_.container]:max-w-none [&_.container]:mx-0 [&_.container]:px-0">
        {{ $slot }}
    </main>


    @livewire('components.ui.toast')

    @livewireScripts
    @vite('resources/js/app.js')
    @fluxScripts
</body>

</html>