{{-- resources/views/livewire/components/partials/superadmin/sidebar.blade.php --}}
@php
    // Ambil nama lengkap & inisial
    $fullName = trim(Auth::user()->full_name ?? 'User');
    $parts = preg_split('/\s+/', $fullName);
    $firstInitial = strtoupper(substr($parts[0] ?? 'U', 0, 1));
    $lastInitial  = strtoupper(substr($parts[count($parts)-1] ?? '', 0, 1));
    $initials = $firstInitial . $lastInitial;

    // (Opsional) tampilkan status/role singkat
    $roleName = Auth::user()->role->name ?? 'Member';
@endphp

<flux:sidebar
    sticky
    collapsible="mobile"
    class="
        fixed top-0 left-0 h-screen
        bg-zinc-900 
        border-r border-zinc-200 dark:border-zinc-700
        lg:w-64 w-[85vw] max-w-sm
        z-40
    ">

    <flux:sidebar.header>
        {{-- Brand: text putih + logo putih --}}
        <flux:sidebar.brand
            href="#"
            logo="{{ asset('images/logo/kebun-raya-bogor.png') }}"
            name="Kebun Raya Bogor."
            class="text-white"
            style="filter: brightness(0) invert(1);" />
        <flux:sidebar.collapse class="lg:hidden" />
    </flux:sidebar.header>

    <flux:sidebar.search placeholder="Search..." />

    <flux:sidebar.nav>
        <flux:sidebar.item
            icon="home"
            href="{{ route('superadmin.dashboard') }}"
            :current="request()->routeIs('superadmin.dashboard')">
            Home
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="inbox"
            href="{{ route('superadmin.announcement') }}"
            :current="request()->routeIs('superadmin.announcement')">
            Announcement
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="document-text"
            href="{{ route('superadmin.information') }}"
            :current="request()->routeIs('superadmin.information')">
            Information
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="users"
            href="{{ route('superadmin.user') }}"
            :current="request()->routeIs('superadmin.user')">
            User Management
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="building-office"
            href="{{ route('superadmin.department') }}"
            :current="request()->routeIs('superadmin.department')">
            Department Management
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="calendar"
            href="{{ route('superadmin.bookingroom') }}"
            :current="request()->routeIs('superadmin.bookingroom')">
            Booking Room
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="ticket"
            href="{{ route('superadmin.ticketsupport') }}"
            :current="request()->routeIs('superadmin.ticketsupport')">
            Ticket Support
        </flux:sidebar.item>
    </flux:sidebar.nav>

    <flux:sidebar.spacer />
    {{-- Nav kedua (Settings/Help + Logout mobile) --}}
    <flux:sidebar.nav>
        <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
        <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>

        {{-- Logout khusus MOBILE (karena dropdown di bawah hanya tampil desktop) --}}
        <flux:sidebar.item
            class="lg:hidden"
            icon="arrow-right-start-on-rectangle"
            as="button"
            type="submit"
            form="logout-form">
            Logout
        </flux:sidebar.item>
    </flux:sidebar.nav>

    {{-- Dropdown menu (desktop) --}}
    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        {{-- Pakai profil default FLUX untuk pemicu dropdown (tanpa avatar, biar tidak dobel) --}}
        <flux:sidebar.profile
            avatar=""
            name="{{ $fullName }}" />

        <flux:menu>
            <flux:menu.radio.group>
                <flux:menu.radio checked>{{ $fullName }}</flux:menu.radio>
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

</flux:sidebar>

{{-- FORM LOGOUT TERSEMBUNYI --}}
<form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>

{{-- Optional utility kalau mau pakai class, bukan inline style, untuk logo putih --}}
<style>
    .img-white { filter: brightness(0) invert(1); }
</style>
