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

<form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>

<style>
    .img-white {
        filter: brightness(0) invert(1);
    }
</style>