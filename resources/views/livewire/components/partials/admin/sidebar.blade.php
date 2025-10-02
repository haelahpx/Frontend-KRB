@php
// Ambil nama lengkap & inisial
$fullName = trim(Auth::user()->full_name ?? 'User');
$parts = preg_split('/\s+/', $fullName);
$firstInitial = strtoupper(substr($parts[0] ?? 'U', 0, 1));
$lastInitial = strtoupper(substr($parts[count($parts)-1] ?? '', 0, 1));
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
        <flux:sidebar.brand
            href="#"
            logo="{{ asset('images/logo/kebun-raya-bogor.png') }}"
            name="Kebun Raya Bogor." />
        <flux:sidebar.collapse class="lg:hidden" />
    </flux:sidebar.header>

    <flux:sidebar.search placeholder="Search..." />

    <flux:sidebar.nav>
        <flux:sidebar.item icon="home" href="{{ route('admin.dashboard') }}" :current="request()->routeIs('admin.dashboard')">Home</flux:sidebar.item>
        <flux:sidebar.item icon="inbox" href="{{ route('admin.ticket') }}" :current="request()->routeIs('admin.ticket')">Ticket</flux:sidebar.item>
        <flux:sidebar.item icon="document-text" href="#">Documents</flux:sidebar.item>
        <flux:sidebar.item icon="calendar" href="#">Calendar</flux:sidebar.item>
    </flux:sidebar.nav>

    <flux:sidebar.spacer />
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

    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        <flux:sidebar.profile
            avatar=""
            name="{{ $fullName }}" />
        <flux:menu>
            <flux:menu.radio.group>
                <flux:menu.radio checked>{{ $fullName }}</flux:menu.radio>
                <flux:sidebar.item
                    icon="user"
                    href="{{ route('user.home') }}"
                    class="cursor-pointer">User Page
                </flux:sidebar.item>
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

{{-- FORM LOGOUT TERSEMBUNYI (satu kali saja, di luar komponen) --}}
<form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>