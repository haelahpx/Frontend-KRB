<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Navbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="bg-black border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/logo/kebun-raya-bogor.png') }}"
                            alt="KRBS Logo"
                            class="h-16 w-auto filter brightness-0 invert">
                    </a>
                </div>



                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="text-white hover:text-gray-400 px-3 py-2 text-sm font-medium transition-colors duration-200 border-b-2 border-transparent hover:border-white">
                        Home
                    </a>
                    <a href="{{ route('create-ticket') }}" class="text-white hover:text-gray-400 px-3 py-2 text-sm font-medium transition-colors duration-200 border-b-2 border-transparent hover:border-white">
                        Create Ticket
                    </a>
                    <a href="{{ route('book-room') }}" class="text-white hover:text-gray-400 px-3 py-2 text-sm font-medium transition-colors duration-200 border-b-2 border-transparent hover:border-white">
                        Book Room
                    </a>
                    <a href="{{ route('login') }}"
                        class="ml-4 bg-white text-black px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors duration-200">
                        Login / Register
                    </a>
                </nav>

                <div class="md:hidden">
                    <button
                        onclick="toggleMobileMenu()"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors duration-200"
                        aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <!-- Hamburger icon -->
                        <svg id="menu-icon" class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg id="close-icon" class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="md:hidden hidden border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-white">
                <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200">
                    Home
                </a>
                <a href="{{ route('create-ticket') }}" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200">
                    Create Ticket
                </a>
                <a href="{{ route('book-room') }}" class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200">
                    Book Room
                </a>
                <a href="{{ route('login') }}"
                    class="block w-full text-center mt-2 px-3 py-2 text-base font-medium text-white bg-black rounded-md hover:bg-gray-800 transition-colors duration-200">
                    Login / Register
                </a>
            </div>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');

            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }
        }
    </script>
</body>

</html>