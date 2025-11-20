<div>
    <style>
        .nav-link {
            position: relative;
            transition: all .3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #fff;
            transition: width .3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }

        .mobile-menu.open {
            max-height: 100vh;
        }

        .hamburger-line {
            transition: all .3s ease;
            transform-origin: center;
        }

        .hamburger-active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .hamburger-active .hamburger-line:nth-child(2) {
            opacity: 0;
        }

        .hamburger-active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        .btn-hover {
            position: relative;
            overflow: hidden;
        }

        .btn-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .1), transparent);
            transition: left .5s ease;
        }

        .btn-hover:hover::before {
            left: 100%;
        }

        .shadow-elegant {
            box-shadow: 0 4px 20px rgba(0, 0, 0, .1);
        }

        .dropdown-menu {
            display: none;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity .2s ease, transform .2s ease;
        }

        .dropdown-menu.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .logo-white {
            filter: brightness(0) invert(1);
        }

        .mobile-dropdown-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .mobile-dropdown-content.open {
            max-height: 500px;
        }

        /* Badge for agent status */
        .agent-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>

    {{-- FIXED NAVBAR --}}
    <nav class="bg-black border-b border-gray-800 shadow-elegant fixed inset-x-0 top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <div class="flex-shrink-0">
                    @php
                    $company = auth()->user()?->company;
                    $rawLogo = $company?->image;
                    $fallback = asset('images/logo/kebun-raya-bogor.png');
                    $logoUrl = $fallback;

                    if (!empty($rawLogo)) {
                    if (preg_match('#^https?://#i', $rawLogo)) {
                    $logoUrl = $rawLogo;
                    } else {
                    if (file_exists(public_path($rawLogo))) {
                    $logoUrl = asset($rawLogo);
                    } elseif (file_exists(public_path('storage/'.$rawLogo))) {
                    $logoUrl = asset('storage/'.$rawLogo);
                    } elseif (file_exists(public_path('images/'.$rawLogo))) {
                    $logoUrl = asset('images/'.$rawLogo);
                    }
                    }
                    }
                    @endphp

                    <a href="{{ route('home') }}" class="transition-transform duration-300 hover:scale-105 flex items-center gap-2">
                        <img src="{{ $logoUrl }}" alt="{{ $company?->company_name ?? 'KRBS' }} Logo" class="h-10 w-auto logo-white">
                    </a>
                </div>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('user.home') }}"
                        class="nav-link px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                        Home
                    </a>

                    <a href="{{ route('create-ticket') }}"
                        class="nav-link px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('create-ticket') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                        Create Ticket
                    </a>

                    <a href="{{ route('book-room') }}"
                        class="nav-link px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('book-room') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                        Book Room
                    </a>

                    <a href="{{ route('book-vehicle') }}"
                        class="nav-link px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('book-vehicle') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                        Book Vehicle
                    </a>

                    @if(auth()->user() && auth()->user()->is_agent == 'yes')
                    <a href="{{ route('user.ticket.queue') }}"
                        class="nav-link px-4 py-2 text-sm font-medium rounded-lg transition-colors flex items-center gap-2 {{ request()->routeIs('user.ticket.queue') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                        <x-heroicon-o-queue-list class="w-4 h-4" />
                        Ticket Queue
                    </a>
                    @endif

                    {{-- Status Dropdown --}}
                    <div class="relative">
                        <button id="statusDropdownBtn" type="button"
                            class="nav-link px-4 py-2 text-sm font-medium rounded-lg transition-colors flex items-center gap-2 {{ request()->routeIs('ticketstatus') || request()->routeIs('bookingstatus') || request()->routeIs('vehiclestatus') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}"
                            aria-haspopup="true" aria-expanded="false">
                            <x-heroicon-o-chart-bar class="w-4 h-4" />
                            Status
                            <x-heroicon-o-chevron-down id="statusDropdownArrow" class="w-4 h-4 transition-transform" />
                        </button>
                        <div id="statusDropdown" class="dropdown-menu absolute right-0 mt-2 w-52 bg-gray-900 rounded-lg shadow-xl border border-gray-700 py-1 z-50">
                            <a href="{{ route('ticketstatus') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                                <x-heroicon-o-ticket class="w-4 h-4" />
                                Ticket Status
                            </a>
                            <a href="{{ route('bookingstatus') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                                <x-heroicon-o-calendar class="w-4 h-4" />
                                Meeting Status
                            </a>
                            <a href="{{ route('vehiclestatus') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                                <x-heroicon-o-truck class="w-4 h-4" />
                                Vehicle Status
                            </a>
                        </div>
                    </div>

                    @guest
                    <a href="{{ route('login') }}" class="ml-2 btn-hover bg-white text-black px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-all flex items-center gap-2">
                        <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" />
                        Login
                    </a>
                    @endguest

                    @auth
                    <div class="relative ml-2">
                        <button id="profileDropdownBtn" type="button"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg text-gray-300 hover:text-white hover:bg-gray-800/50 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-xs">
                                {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="hidden lg:block">{{ explode(' ', auth()->user()->full_name ?? auth()->user()->name ?? 'User')[0] }}</span>
                            <x-heroicon-o-chevron-down id="dropdownArrow" class="w-4 h-4 transition-transform" />
                        </button>
                        <div id="profileDropdown" class="dropdown-menu absolute right-0 mt-2 w-64 bg-gray-900 rounded-lg shadow-xl border border-gray-700 py-2 z-50">
                            <div class="px-4 py-3 border-b border-gray-800">
                                <p class="text-sm font-semibold text-white">{{ auth()->user()->full_name ?? auth()->user()->name ?? 'User' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ auth()->user()->email }}</p>
                                @if(auth()->user()->is_agent == 'yes')
                                <span class="agent-badge mt-2">
                                    <x-heroicon-s-check-badge class="w-3 h-3" />
                                    Agent
                                </span>
                                @endif
                            </div>

                            <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                                <x-heroicon-o-user-circle class="w-5 h-5" />
                                My Profile
                            </a>

                            @if(auth()->user()->role->name === 'Superadmin')
                            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                                <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                                SuperAdmin Dashboard
                            </a>
                            @elseif(auth()->user()->role->name === 'Admin')
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                                <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                                Admin Dashboard
                            </a>
                            @elseif(auth()->user()->role->name === 'Receptionist')
                            <a href="{{ route('receptionist.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                                <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                                Receptionist Dashboard
                            </a>
                            @endif

                            <div class="border-t border-gray-800 my-2"></div>

                            <form method="POST" action="{{ route('logout') }}" class="px-2">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-2 py-2.5 text-sm text-white hover:text-red-300 hover:bg-gray-800 rounded-md transition-colors">
                                    <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5" />
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>

                {{-- Mobile Hamburger --}}
                <button id="hamburger"
                    class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800/50 transition-all focus:outline-none focus:ring-2 focus:ring-gray-700"
                    aria-label="Toggle navigation"
                    aria-expanded="false"
                    aria-controls="mobile-menu">
                    <div class="w-6 h-6 flex flex-col justify-center space-y-1.5">
                        <span class="hamburger-line block w-6 h-0.5 bg-current rounded-full"></span>
                        <span class="hamburger-line block w-6 h-0.5 bg-current rounded-full"></span>
                        <span class="hamburger-line block w-6 h-0.5 bg-current rounded-full"></span>
                    </div>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="md:hidden mobile-menu border-t border-gray-800 bg-black">
            <div class="px-4 py-4 space-y-1">
                @auth
                <div class="mb-4 p-3 bg-gray-900 rounded-lg border border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr(auth()->user()->full_name ?? auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->full_name ?? auth()->user()->name ?? 'User' }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    @if(auth()->user()->is_agent == 'yes')
                    <div class="mt-2">
                        <span class="agent-badge">
                            <x-heroicon-s-check-badge class="w-3 h-3" />
                            Agent
                        </span>
                    </div>
                    @endif
                </div>
                @endauth

                <a href="{{ route('user.home') }}"
                    class="flex items-center gap-3 px-4 py-3 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                    <x-heroicon-o-home class="w-5 h-5" />
                    Home
                </a>

                <a href="{{ route('create-ticket') }}"
                    class="flex items-center gap-3 px-4 py-3 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('create-ticket') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                    <x-heroicon-o-ticket class="w-5 h-5" />
                    Create Ticket
                </a>

                <a href="{{ route('book-room') }}"
                    class="flex items-center gap-3 px-4 py-3 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('book-room') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                    <x-heroicon-o-building-office class="w-5 h-5" />
                    Book Room
                </a>

                <a href="{{ route('book-vehicle') }}"
                    class="flex items-center gap-3 px-4 py-3 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('book-vehicle') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                    <x-heroicon-o-truck class="w-5 h-5" />
                    Book Vehicle
                </a>

                @if(auth()->user() && auth()->user()->is_agent == 'yes')
                <a href="{{ route('user.ticket.queue') }}"
                    class="flex items-center gap-3 px-4 py-3 text-base font-medium rounded-lg transition-colors {{ request()->routeIs('user.ticket.queue') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                    <x-heroicon-o-queue-list class="w-5 h-5" />
                    Ticket Queue
                </a>
                @endif

                {{-- Mobile Status Dropdown --}}
                <div>
                    <button id="statusMobileBtn" type="button"
                        class="w-full flex items-center justify-between px-4 py-3 text-base font-medium rounded-lg text-gray-300 hover:text-white hover:bg-gray-800/50 transition-colors">
                        <span class="flex items-center gap-3">
                            <x-heroicon-o-chart-bar class="w-5 h-5" />
                            Status
                        </span>
                        <x-heroicon-o-chevron-down id="statusMobileArrow" class="w-5 h-5 transition-transform" />
                    </button>
                    <div id="statusMobileMenu" class="mobile-dropdown-content pl-4">
                        <a href="{{ route('ticketstatus') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-gray-800/50 rounded-lg transition-colors">
                            <x-heroicon-o-ticket class="w-4 h-4" />
                            Ticket Status
                        </a>
                        <a href="{{ route('bookingstatus') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-gray-800/50 rounded-lg transition-colors">
                            <x-heroicon-o-calendar class="w-4 h-4" />
                            Meeting Status
                        </a>
                        <a href="{{ route('vehiclestatus') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-gray-800/50 rounded-lg transition-colors">
                            <x-heroicon-o-truck class="w-4 h-4" />
                            Vehicle Status
                        </a>
                    </div>
                </div>

                @auth
                <div class="border-t border-gray-800 pt-3 mt-3">
                    <a href="{{ route('profile') }}"
                        class="flex items-center gap-3 px-4 py-3 text-base font-medium rounded-lg text-gray-300 hover:text-white hover:bg-gray-800/50 transition-colors">
                        <x-heroicon-o-user-circle class="w-5 h-5" />
                        My Profile
                    </a>

                    @if(auth()->user()->role->name === 'Superadmin')
                    <a href="{{ route('superadmin.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 text-base font-medium rounded-lg text-gray-300 hover:text-white hover:bg-gray-800/50 transition-colors">
                        <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                        SuperAdmin Dashboard
                    </a>
                    @elseif(auth()->user()->role->name === 'Admin')
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 text-base font-medium rounded-lg text-gray-300 hover:text-white hover:bg-gray-800/50 transition-colors">
                        <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                        Admin Dashboard
                    </a>
                    @elseif(auth()->user()->role->name === 'Receptionist')
                    <a href="{{ route('receptionist.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 text-base font-medium rounded-lg text-gray-300 hover:text-white hover:bg-gray-800/50 transition-colors">
                        <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                        Receptionist Dashboard
                    </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3 text-base font-medium rounded-lg text-white hover:text-red-300 hover:bg-gray-800/50 transition-colors">
                            <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5" />
                            Logout
                        </button>
                    </form>
                </div>
                @endauth

                @guest
                <div class="border-t border-gray-800 pt-3 mt-3">
                    <a href="{{ route('login') }}"
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 text-base font-semibold text-black bg-white rounded-lg hover:bg-gray-200 transition-all">
                        <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                        Login / Register
                    </a>
                </div>
                @endguest
            </div>
        </div>
    </nav>

    {{-- Spacer --}}
    <div class="h-16"></div>

    <script>
        // Dropdown Handler
        function setupDropdown(btnId, menuId, arrowId) {
            const btn = document.getElementById(btnId);
            const menu = document.getElementById(menuId);
            const arrow = document.getElementById(arrowId);
            if (!btn || !menu) return;

            let isOpen = false;

            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                isOpen = !isOpen;
                menu.classList.toggle('show', isOpen);
                if (arrow) arrow.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
                btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            document.addEventListener('click', (e) => {
                if (isOpen && !btn.contains(e.target) && !menu.contains(e.target)) {
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

        setupDropdown('statusDropdownBtn', 'statusDropdown', 'statusDropdownArrow');
        setupDropdown('profileDropdownBtn', 'profileDropdown', 'dropdownArrow');

        // Mobile Menu Handler
        const mobileMenu = document.getElementById('mobile-menu');
        const hamburger = document.getElementById('hamburger');

        hamburger.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.contains('open');
            mobileMenu.classList.toggle('open');
            hamburger.classList.toggle('hamburger-active');
            hamburger.setAttribute('aria-expanded', !isOpen);
        });

        // Mobile Status Dropdown
        const statusMobileBtn = document.getElementById('statusMobileBtn');
        const statusMobileMenu = document.getElementById('statusMobileMenu');
        const statusMobileArrow = document.getElementById('statusMobileArrow');

        if (statusMobileBtn && statusMobileMenu) {
            statusMobileBtn.addEventListener('click', () => {
                const isOpen = statusMobileMenu.classList.contains('open');
                statusMobileMenu.classList.toggle('open');
                if (statusMobileArrow) {
                    statusMobileArrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            });
        }

        // Close mobile menu on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                mobileMenu.classList.remove('open');
                hamburger.classList.remove('hamburger-active');
                hamburger.setAttribute('aria-expanded', 'false');
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !hamburger.contains(e.target) && mobileMenu.classList.contains('open')) {
                mobileMenu.classList.remove('open');
                hamburger.classList.remove('hamburger-active');
                hamburger.setAttribute('aria-expanded', 'false');
            }
        });
    </script>
</div>