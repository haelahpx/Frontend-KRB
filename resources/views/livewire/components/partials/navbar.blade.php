<div>
    <style>
        .nav-link {
            position: relative;
            transition: all .3s ease
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #fff;
            transition: width .3s ease
        }

        .nav-link:hover::after {
            width: 100%
        }

        .mobile-menu-slide {
            transform: translateY(-8px);
            opacity: 0;
            transition: transform .2s ease, opacity .2s ease
        }

        .mobile-menu-slide.show {
            transform: translateY(0);
            opacity: 1
        }

        .hamburger-line {
            transition: all .3s ease;
            transform-origin: center
        }

        .hamburger-active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px)
        }

        .hamburger-active .hamburger-line:nth-child(2) {
            opacity: 0
        }

        .hamburger-active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px)
        }

        .btn-hover {
            position: relative;
            overflow: hidden
        }

        .btn-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .1), transparent);
            transition: left .5s ease
        }

        .btn-hover:hover::before {
            left: 100%
        }

        .shadow-elegant {
            box-shadow: 0 4px 20px rgba(0, 0, 0, .1)
        }

        .profile-dropdown {
            display: none;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity .2s ease, transform .2s ease;
        }

        .profile-dropdown.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
    </style>

    <div class="bg-black border-b border-gray-800 shadow-elegant sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="transition-transform duration-300 hover:scale-105">
                        <img src="{{ asset('images/logo/kebun-raya-bogor.png') }}" alt="KRBS Logo"
                            class="h-10 w-auto filter brightness-0 invert">
                    </a>
                </div>

                {{-- DESKTOP NAV --}}
                <nav class="hidden md:flex items-center gap-2">
                    <a href="{{ route('user.home') }}" class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50
            {{ request()->routeIs('home') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">
                        Home
                    </a>
                    <a href="{{ route('create-ticket') }}"
                        class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50
            {{ request()->routeIs('create-ticket') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">
                        Create Ticket
                    </a>
                    <a href="{{ route('book-room') }}" class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50
            {{ request()->routeIs('book-room') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">
                        Book Room
                    </a>
                    <a href="{{ route('book-vehicle') }}" class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50
            {{ request()->routeIs('book-vehicle') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">
                        Book Vehicle
                    </a>


                    {{-- Tampilkan Login/Register hanya untuk guest --}}
                    @guest
                        <a href="{{ route('login') }}"
                            class="ml-2 btn-hover bg-white text-black px-6 py-2.5 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">
                            Login / Register
                        </a>
                    @endguest

                    {{-- Tampilkan menu berdasarkan role untuk user yang sudah login --}}
                    @auth
                        @if(auth()->user()->role->name === 'Superadmin')
                            {{-- SuperAdmin: Profile with Dropdown --}}
                            <div class="relative ml-2">
                                <button id="profileDropdownBtn" type="button"
                                    class="nav-link px-4 py-3 text-sm font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50 flex items-center gap-2">
                                    Profile
                                    <svg id="dropdownArrow" class="w-4 h-4 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div id="profileDropdown"
                                    class="profile-dropdown absolute right-0 mt-2 w-48 bg-gray-900 rounded-lg shadow-lg border border-gray-800 py-1 z-50">
                                    <a href="{{ route('profile') }}"
                                        class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                                        My Profile
                                    </a>
                                    <a href="{{ route('superadmin.dashboard') }}"
                                        class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                                        SuperAdmin Dashboard
                                    </a>
                                    <div class="border-t border-gray-800 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @elseif(auth()->user()->role->name === 'Admin')
                            {{-- Admin: Profile with Dropdown --}}
                            <div class="relative ml-2">
                                <button id="adminDropdownBtn" type="button"
                                    class="nav-link px-4 py-3 text-sm font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50 flex items-center gap-2">
                                    Profile
                                    <svg id="adminDropdownArrow" class="w-4 h-4 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div id="adminDropdown"
                                    class="profile-dropdown absolute right-0 mt-2 w-48 bg-gray-900 rounded-lg shadow-lg border border-gray-800 py-1 z-50">
                                    <a href="{{ route('profile') }}"
                                        class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                                        My Profile
                                    </a>
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                                        Admin Dashboard
                                    </a>
                                    <div class="border-t border-gray-800 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @elseif(auth()->user()->role->name === 'Receptionist')
                            {{-- Receptionist: Profile with Dropdown --}}
                            <div class="relative ml-2">
                                <button id="receptionistDropdownBtn" type="button"
                                    class="nav-link px-4 py-3 text-sm font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50 flex items-center gap-2">
                                    Profile
                                    <svg id="receptionistDropdownArrow" class="w-4 h-4 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div id="receptionistDropdown"
                                    class="profile-dropdown absolute right-0 mt-2 w-48 bg-gray-900 rounded-lg shadow-lg border border-gray-800 py-1 z-50">
                                    <a href="{{ route('profile') }}"
                                        class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                                        My Profile
                                    </a>
                                    <a href="{{ route('receptionist.dashboard') }}"
                                        class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                                        Receptionist Dashboard
                                    </a>
                                    <div class="border-t border-gray-800 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>

                        @else
                            {{-- Regular User: Profile + Logout --}}
                            <a href="{{ route('profile') }}"
                                class="nav-link px-4 py-3 text-sm font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50">
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="ml-2">
                                @csrf
                                <button type="submit"
                                    class="btn-hover bg-white text-black px-6 py-2.5 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">
                                    Logout
                                </button>
                            </form>
                        @endif
                    @endauth
                </nav>

                {{-- HAMBURGER --}}
                <div class="md:hidden">
                    <button id="hamburger" aria-label="Toggle navigation" aria-expanded="false"
                        aria-controls="mobile-menu"
                        class="hamburger inline-flex items-center justify-center p-2 rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50 transition-all">
                        <span class="sr-only">Open main menu</span>
                        <div class="w-6 h-6 flex flex-col justify-center space-y-1">
                            <span class="hamburger-line block w-6 h-0.5 bg-current"></span>
                            <span class="hamburger-line block w-6 h-0.5 bg-current"></span>
                            <span class="hamburger-line block w-6 h-0.5 bg-current"></span>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        {{-- MOBILE MENU --}}
        <div id="mobile-menu" class="md:hidden hidden border-t border-gray-800">
            <div class="px-4 py-3 bg-black mobile-menu-slide">
                <a href="{{ route('home') }}"
                    class="block px-4 py-3 text-base font-medium rounded-lg
        {{ request()->routeIs('home') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">
                    Home
                </a>
                <a href="{{ route('create-ticket') }}"
                    class="block px-4 py-3 text-base font-medium rounded-lg
        {{ request()->routeIs('create-ticket') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">
                    Create Ticket
                </a>
                <a href="{{ route('book-room') }}"
                    class="block px-4 py-3 text-base font-medium rounded-lg
        {{ request()->routeIs('book-room') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">
                    Book Room
                </a>
                <a href="{{ route('book-vehicle') }}"
                    class="block px-4 py-3 text-base font-medium rounded-lg
        {{ request()->routeIs('book-vehicle') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">
                    Book Vehicle
                </a>

                <a href="{{ route('package') }}"
                    class="block px-4 py-3 text-base font-medium rounded-lg
        {{ request()->routeIs('package') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">
                    Package
                </a>

                {{-- Guest: tombol Login/Register --}}
                @guest
                    <div class="pt-2">
                        <a href="{{ route('login') }}"
                            class="block w-full text-center px-4 py-3 text-base font-medium text-black bg-white rounded-md hover:bg-gray-200 transition-colors">
                            Login / Register
                        </a>
                    </div>
                @endguest

                {{-- Auth: Menu berdasarkan role --}}
                @auth
                    @if(auth()->user()->role->name === 'Superadmin')
                        {{-- SuperAdmin Mobile Menu --}}
                        <a href="{{ route('profile') }}"
                            class="mt-2 block px-4 py-3 text-base font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50">
                            My Profile
                        </a>
                        <a href="{{ route('superadmin.dashboard') }}"
                            class="block px-4 py-3 text-base font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50">
                            SuperAdmin Dashboard
                        </a>
                    @elseif(auth()->user()->role->name === 'Admin')
                        {{-- Admin Mobile Menu --}}
                        <a href="{{ route('profile') }}"
                            class="mt-2 block px-4 py-3 text-base font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50">
                            Profile
                        </a>
                        <a href="{{ route('admin.dashboard') }}"
                            class="block px-4 py-3 text-base font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50">
                            Admin Dashboard
                        </a>
                    @elseif(auth()->user()->role->name === 'Receptionist')
                        {{-- Receptionist Mobile Menu --}}
                        <a href="{{ route('profile') }}"
                            class="mt-2 block px-4 py-3 text-base font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50">
                            Profile
                        </a>
                        <a href="{{ route('receptionist.dashboard') }}"
                            class="block px-4 py-3 text-base font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50">
                            Receptionist Dashboard
                        </a>
                    @else
                        {{-- Regular User Mobile Menu --}}
                        <a href="{{ route('profile') }}"
                            class="mt-2 block px-4 py-3 text-base font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50">
                            Profile
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                        @csrf
                        <button type="submit"
                            class="block w-full text-center px-4 py-3 text-base font-medium text-black bg-white rounded-md hover:bg-gray-200 transition-colors">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>

    <script>
        function setupDropdown(btnId, menuId, arrowId) {
            const btn = document.getElementById(btnId);
            const menu = document.getElementById(menuId);
            const arrow = document.getElementById(arrowId);
            if (!btn || !menu) return;
            let isOpen = false;
            btn.setAttribute('aria-expanded', 'false');
            btn.setAttribute('aria-haspopup', 'true');
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                isOpen = !isOpen;
                menu.classList.toggle('show', isOpen);
                if (arrow) arrow.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
                btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
            document.addEventListener('click', (e) => {
                if (!isOpen) return;
                if (!btn.contains(e.target) && !menu.contains(e.target)) {
                    isOpen = false;
                    menu.classList.remove('show');
                    if (arrow) arrow.style.transform = 'rotate(0deg)';
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && isOpen) {
                    isOpen = false;
                    menu.classList.remove('show');
                    if (arrow) arrow.style.transform = 'rotate(0deg)';
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        }

        setupDropdown('profileDropdownBtn', 'profileDropdown', 'dropdownArrow');
        setupDropdown('adminDropdownBtn', 'adminDropdown', 'adminDropdownArrow');
        setupDropdown('receptionistDropdownBtn', 'receptionistDropdown', 'receptionistDropdownArrow');

        const menu = document.getElementById('mobile-menu');
        const sheet = menu.querySelector('.mobile-menu-slide');
        const burger = document.getElementById('hamburger');

        function openMenu() {
            menu.classList.remove('hidden');
            requestAnimationFrame(() => sheet.classList.add('show'));
            burger.classList.add('hamburger-active');
            burger.setAttribute('aria-expanded', 'true');
        }

        function closeMenu() {
            sheet.classList.remove('show');
            burger.classList.remove('hamburger-active');
            burger.setAttribute('aria-expanded', 'false');
            setTimeout(() => menu.classList.add('hidden'), 180);
        }

        burger.addEventListener('click', () =>
            burger.classList.contains('hamburger-active') ? closeMenu() : openMenu()
        );

        document.addEventListener('click', (e) => {
            const navbar = document.querySelector('.bg-black');
            if (!navbar.contains(e.target) && burger.classList.contains('hamburger-active')) closeMenu();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && burger.classList.contains('hamburger-active')) closeMenu();
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                sheet.classList.remove('show');
                menu.classList.add('hidden');
                burger.classList.remove('hamburger-active');
                burger.setAttribute('aria-expanded', 'false');
            }
        });
    </script>

</div>