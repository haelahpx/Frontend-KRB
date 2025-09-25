<flux:sidebar
    sticky
    collapsible="mobile"
    class="
        fixed top-0 left-0 h-screen
        bg-zinc-50 dark:bg-zinc-900
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
        <flux:sidebar.item icon="home" href="{{ route('superadmin.dashboard') }}" :current="request()->routeIs('superadmin.dashboard')">Home</flux:sidebar.item>
        <flux:sidebar.item icon="inbox" href="{{ route('superadmin.announcement') }}" :current="request()->routeIs('superadmin.announcement')">Announcement</flux:sidebar.item>
        <flux:sidebar.item icon="document-text" href="{{ route('superadmin.information') }}" :current="request()->routeIs('superadmin.information')">Information</flux:sidebar.item>
        <flux:sidebar.item icon="users" href="{{ route('superadmin.user') }}" :current="request()->routeIs('superadmin.user')">User Management</flux:sidebar.item>
    </flux:sidebar.nav>

    <flux:sidebar.spacer />

    <flux:sidebar.nav>
        <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
        <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>

        {{-- Tombol Logout untuk MOBILE (karena dropdown di bawah hanya tampil desktop) --}}
        <flux:sidebar.item
            class="lg:hidden"
            icon="arrow-right-start-on-rectangle"
            as="button"
            type="submit"
            form="logout-form">
            Logout
        </flux:sidebar.item>
    </flux:sidebar.nav>

    {{-- Desktop-only profile + menu --}}
    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        <flux:sidebar.profile avatar="https://fluxui.dev/img/demo/user.png" name="Olivia Martin" />
        <flux:menu>
            <flux:menu.radio.group>
                <flux:menu.radio checked>Olivia Martin</flux:menu.radio>
                <flux:menu.radio>Truly Delta</flux:menu.radio>
            </flux:menu.radio.group>
            <flux:menu.separator />

            {{-- Item Logout memicu FORM tersembunyi --}}
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