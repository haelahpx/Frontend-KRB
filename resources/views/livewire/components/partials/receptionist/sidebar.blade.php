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

    {{-- MAIN NAV --}}
    <flux:sidebar.nav>
        <flux:sidebar.item
            icon="home"
            href="{{ route('receptionist.dashboard') }}"
            :current="request()->routeIs('receptionist.dashboard')">
            Home
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="calendar-days"
            href="{{ route('receptionist.schedule') }}"
            :current="request()->routeIs('receptionist.schedule')">
            Booking/Meeting Room
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="check-circle"
            href="{{ route('receptionist.bookings') }}"
            :current="request()->routeIs('receptionist.bookings')">
            Booking/Meeting Approval
        </flux:sidebar.item>
        
        <flux:sidebar.item
            icon="clock"
            href="{{ route('receptionist.bookinghistory') }}"
            :current="request()->routeIs('receptionist.bookinghistory')">
            Booking/Meeting History
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="inbox"
            href="{{ route('receptionist.guestbook') }}"
            :current="request()->routeIs('receptionist.guestbook*')">
            GuestBook
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="clock"
            href="{{ route('receptionist.guestbookhistory') }}"
            :current="request()->routeIs('receptionist.guestbookhistory*')">
            GuestBook History
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="gift"
            href="{{ route('receptionist.docpackform') }}"
            :current="request()->routeIs('receptionist.docpackform')">
            Document/Package Form
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="clock"
            href="{{ route('receptionist.docpackstatus') }}"
            :current="request()->routeIs('receptionist.docpackstatus')">
            Document/Package Status
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="clock"
            href="{{ route('receptionist.docpackhistory') }}"
            :current="request()->routeIs('receptionist.docpackhistory')">
            Document/Package History
        </flux:sidebar.item>
    </flux:sidebar.nav>

    <flux:sidebar.spacer />

    {{-- SETTINGS + MOBILE LOGOUT --}}
    <flux:sidebar.nav>
        <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
        <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>

        {{-- Logout for MOBILE --}}
        <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
            @csrf
            <flux:sidebar.item
                icon="arrow-right-start-on-rectangle"
                as="button"
                type="submit">
                Logout
            </flux:sidebar.item>
        </form>
    </flux:sidebar.nav>

    {{-- DESKTOP DROPDOWN --}}
    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        <flux:sidebar.profile avatar="" name="{{ $fullName }}" />

        <flux:menu>
            <flux:menu.radio.group>
                <flux:menu.radio checked>{{ $fullName }}</flux:menu.radio>

                <flux:sidebar.item
                    icon="user"
                    href="{{ route('user.home') }}"
                    class="cursor-pointer">
                    User Page
                </flux:sidebar.item>
            </flux:menu.radio.group>

            <flux:menu.separator />

            {{-- Logout for DESKTOP --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:menu.item
                    icon="arrow-right-start-on-rectangle"
                    as="button"
                    type="submit">
                    Logout
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>

<style>
    .img-white {
        filter: brightness(0) invert(1);
    }
</style>
