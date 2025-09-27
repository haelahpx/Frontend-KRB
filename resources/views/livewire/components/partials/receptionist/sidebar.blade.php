<flux:sidebar sticky collapsible="mobile" class="
        fixed top-0 left-0 h-screen
        bg-zinc-900
        border-r border-zinc-200 dark:border-zinc-700
        lg:w-64 w-[85vw] max-w-sm
        z-40
    ">
    <flux:sidebar.header>
        <flux:sidebar.brand href="#" logo="{{ asset('images/logo/kebun-raya-bogor.png') }}" name="Kebun Raya Bogor." />
        <flux:sidebar.collapse class="lg:hidden" />
    </flux:sidebar.header>

    <flux:sidebar.search placeholder="Search..." />

    <flux:sidebar.nav>
        <flux:sidebar.item icon="home" href="{{ route('receptionist.dashboard') }}"
            :current="request()->routeIs('receptionist.dashboard')">
            Home
        </flux:sidebar.item>

        <flux:sidebar.item icon="calendar-days" href="{{ route('receptionist.schedule') }}"
            :current="request()->routeIs('meeting.schedule')">
            Meeting Schedule
        </flux:sidebar.item>

        <flux:sidebar.item icon="inbox" href="{{ route('receptionist.guestbook') }}"
            :current="request()->routeIs('receptionist.guestbook*')">
            GuestBook
        </flux:sidebar.item>

        <flux:sidebar.item icon="document-text" href="{{ route('receptionist.documents') }}">
            Documents
        </flux:sidebar.item>

        <flux:sidebar.item icon="calendar" href="#">
            Calendar
        </flux:sidebar.item>
    </flux:sidebar.nav>

    <flux:sidebar.spacer />

    <flux:sidebar.nav>
        <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
        <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>

        <flux:sidebar.item class="lg:hidden" icon="arrow-right-start-on-rectangle" as="button" type="submit"
            form="logout-form">
            Logout
        </flux:sidebar.item>
    </flux:sidebar.nav>

    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        <flux:sidebar.profile avatar="https://fluxui.dev/img/demo/user.png" name="Meow Lala" />
        <flux:menu>
            <flux:menu.radio.group>
                <flux:menu.radio checked>Meow Lala</flux:menu.radio>
                <flux:menu.radio>Woofy Boo</flux:menu.radio>
            </flux:menu.radio.group>
            <flux:menu.separator />
            <flux:menu.item icon="arrow-right-start-on-rectangle" as="button" type="submit" form="logout-form">
                Logout
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>

<form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>