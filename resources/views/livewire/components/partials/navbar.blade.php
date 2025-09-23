<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
</head>

<body class="bg-gray-100">

    <div class="bg-black border-b border-gray-800 shadow-elegant sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="transition-transform duration-300 hover:scale-105">
                        <img src="{{ asset('images/logo/kebun-raya-bogor.png') }}" alt="KRBS Logo" class="h-10 w-auto filter brightness-0 invert">
                    </a>
                </div>

                <nav class="hidden md:flex items-center gap-2">
                    <a href="{{ route('home') }}"
                        class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50
             {{ request()->routeIs('home') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">
                        Home
                    </a>

                    <a href="{{ route('create-ticket') }}"
                        class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50
             {{ request()->routeIs('create-ticket') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">
                        Create Ticket
                    </a>

                    <a href="{{ route('book-room') }}"
                        class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50
            {{ request()->routeIs('book-room') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">
                        Book Room
                    </a>

                    <a href="{{ route('package') }}"
                        class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50
            {{ request()->routeIs('package') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">
                        Package
                    </a>

                    <a href="{{ route('login') }}"
                        class="ml-2 btn-hover bg-white text-black px-6 py-2.5 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">
                        Login / Register
                    </a>
                </nav>

                <div class="md:hidden">
                    <button id="hamburger"
                        aria-label="Toggle navigation"
                        aria-expanded="false"
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

        <div id="mobile-menu" class="md:hidden hidden border-t border-gray-800">
            <div class="px-4 py-3 bg-black mobile-menu-slide">
                <a href="{{ route('home') }}" class="block px-4 py-3 text-base font-medium rounded-lg
           {{ request()->routeIs('home') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">
                    Home
                </a>
                <a href="{{ route('create-ticket') }}" class="block px-4 py-3 text-base font-medium rounded-lg
           {{ request()->routeIs('create-ticket') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">
                    Create Ticket
                </a>
                <a href="{{ route('book-room') }}" class="block px-4 py-3 text-base font-medium rounded-lg
           {{ request()->routeIs('book-room') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">
                    Book Room
                </a>
                <a href="{{ route('package') }}" class="block px-4 py-3 text-base font-medium rounded-lg
           {{ request()->routeIs('package') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">
                    Package
                </a>    
                <div class="pt-2">
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 text-base font-medium text-black bg-white rounded-md hover:bg-gray-200 transition-colors">
                        Login / Register
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        burger.addEventListener('click', () => {
            if (burger.classList.contains('hamburger-active')) closeMenu();
            else openMenu();
        });

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
</body>

</html>