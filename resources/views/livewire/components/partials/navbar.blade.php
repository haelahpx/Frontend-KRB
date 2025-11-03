<div>
    <style>
        .nav-link{position:relative;transition:all .3s ease}
        .nav-link::after{content:'';position:absolute;bottom:-2px;left:0;width:0;height:2px;background:#fff;transition:width .3s ease}
        .nav-link:hover::after{width:100%}
        .mobile-menu-slide{transform:translateY(-8px);opacity:0;transition:transform .2s ease,opacity .2s ease}
        .mobile-menu-slide.show{transform:translateY(0);opacity:1}
        .hamburger-line{transition:all .3s ease;transform-origin:center}
        .hamburger-active .hamburger-line:nth-child(1){transform:rotate(45deg) translate(6px,6px)}
        .hamburger-active .hamburger-line:nth-child(2){opacity:0}
        .hamburger-active .hamburger-line:nth-child(3){transform:rotate(-45deg) translate(6px,-6px)}
        .btn-hover{position:relative;overflow:hidden}
        .btn-hover::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.1),transparent);transition:left .5s ease}
        .btn-hover:hover::before{left:100%}
        .shadow-elegant{box-shadow:0 4px 20px rgba(0,0,0,.1)}
        .profile-dropdown{display:none;opacity:0;transform:translateY(-10px);transition:opacity .2s ease,transform .2s ease}
        .profile-dropdown.show{display:block;opacity:1;transform:translateY(0)}
        .logo-white{filter:brightness(0) invert(1)}
    </style>

    {{-- FIXED (sticky) NAVBAR --}}
    <div class="bg-black border-b border-gray-800 shadow-elegant fixed inset-x-0 top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo (from companies.image with fallbacks) --}}
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

                    <a href="{{ route('home') }}" class="transition-transform duration-300 hover:scale-105">
                        <img src="{{ $logoUrl }}" alt="{{ $company?->company_name ?? 'KRBS' }} Logo" class="h-10 w-auto logo-white">
                    </a>
                </div>

                {{-- Desktop nav --}}
                <nav class="hidden md:flex items-center gap-2">
                    <a href="{{ route('user.home') }}" class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50 {{ request()->routeIs('home') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">Home</a>
                    <a href="{{ route('create-ticket') }}" class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50 {{ request()->routeIs('create-ticket') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">Create Ticket</a>
                    <a href="{{ route('book-room') }}" class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50 {{ request()->routeIs('book-room') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">Book Room</a>
                    <a href="{{ route('book-vehicle') }}" class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50 {{ request()->routeIs('book-vehicle') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">Book Vehicle</a>
                    <a href="{{ route('user.ticket.queue') }}" class="nav-link px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-800/50 {{ request()->routeIs('user.ticket.queue') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300' }}">Ticket Queue</a>

                    <div class="relative ml-2">
                        <button id="statusDropdownBtn" type="button"
                                class="nav-link px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('ticketstatus') || request()->routeIs('bookingstatus') || request()->routeIs('vehiclestatus') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }} flex items-center gap-2"
                                aria-haspopup="true" aria-expanded="false">
                            Status
                            <svg id="statusDropdownArrow" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="statusDropdown" class="profile-dropdown absolute right-0 mt-2 w-56 bg-gray-900 rounded-lg shadow-lg border border-gray-800 py-1 z-50">
                            <a href="{{ route('ticketstatus') }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">Ticket Status</a>
                            <a href="{{ route('bookingstatus') }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">Booking Status</a>
                            <a href="{{ route('vehiclestatus') }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">Vehicle Status</a>
                        </div>
                    </div>

                    @guest
                        <a href="{{ route('login') }}" class="ml-2 btn-hover bg-white text-black px-6 py-2.5 rounded-md text-sm font-medium hover:bg-gray-200 transition-colors">Login / Register</a>
                    @endguest

                    @auth
                        <div class="relative ml-2">
                            <button id="profileDropdownBtn" type="button"
                                    class="nav-link px-4 py-3 text-sm font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50 flex items-center gap-2">
                                <x-heroicon-o-user class="w-4 h-4" />
                                Profile
                                <svg id="dropdownArrow" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div id="profileDropdown" class="profile-dropdown absolute right-0 mt-2 w-56 bg-gray-900 rounded-lg shadow-lg border border-gray-800 py-2 z-50">
                                {{-- User header --}}
                                <div class="px-4 pb-2 text-xs text-gray-300">
                                    {{ auth()->user()->full_name ?? auth()->user()->name ?? 'User' }}
                                </div>
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors flex items-center gap-2">
                                    <x-heroicon-o-user class="w-4 h-4" />
                                    <span>My Profile</span>
                                </a>

                                @if(auth()->user()->role->name === 'Superadmin')
                                    <a href="{{ route('superadmin.dashboard') }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">SuperAdmin Dashboard</a>
                                @elseif(auth()->user()->role->name === 'Admin')
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">Admin Dashboard</a>
                                @elseif(auth()->user()->role->name === 'Receptionist')
                                    <a href="{{ route('receptionist.dashboard') }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">Receptionist Dashboard</a>
                                @endif

                                <div class="border-t border-gray-800 my-2"></div>
                                <form method="POST" action="{{ route('logout') }}" class="px-2">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-2 py-2 text-sm text-white hover:bg-gray-800 rounded-md transition-colors">Logout</button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </nav>

                {{-- Mobile burger --}}
                <div class="md:hidden">
                    <button id="hamburger" aria-label="Toggle navigation" aria-expanded="false" aria-controls="mobile-menu" class="hamburger inline-flex items-center justify-center p-2 rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50 transition-all">
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

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="md:hidden hidden border-t border-gray-800">
            <div class="px-4 py-3 bg-black mobile-menu-slide">
                <a href="{{ route('home') }}" class="block px-4 py-3 text-base font-medium rounded-lg {{ request()->routeIs('home') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">Home</a>
                <a href="{{ route('create-ticket') }}" class="block px-4 py-3 text-base font-medium rounded-lg {{ request()->routeIs('create-ticket') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">Create Ticket</a>
                <a href="{{ route('book-room') }}" class="block px-4 py-3 text-base font-medium rounded-lg {{ request()->routeIs('book-room') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">Book Room</a>
                <a href="{{ route('book-vehicle') }}" class="block px-4 py-3 text-base font-medium rounded-lg {{ request()->routeIs('book-vehicle') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">Book Vehicle</a>
                <a href="{{ route('user.ticket.queue') }}" class="block px-4 py-3 text-base font-medium rounded-lg {{ request()->routeIs('user.ticket.queue') ? 'bg-gray-800/50 text-white' : 'text-white hover:text-gray-300 hover:bg-gray-800/50' }}">Ticket Queue</a>

                <button id="statusMobileBtn" type="button" class="w-full text-left block px-4 py-3 text-base font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50 flex items-center justify-between">
                    <span>Status</span>
                    <svg id="statusMobileArrow" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="statusMobileMenu" class="hidden pl-2">
                    <a href="{{ route('ticketstatus') }}" class="block px-4 py-2 text-base font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50">Ticket Status</a>
                    <a href="{{ route('bookingstatus') }}" class="block px-4 py-2 text-base font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50">Booking Status</a>
                    <a href="{{ route('vehiclestatus') }}" class="block px-4 py-2 text-base font-medium rounded-lg text-white hover:text-gray-300 hover:bg-gray-800/50">Vehicle Status</a>
                </div>

                @guest
                    <div class="pt-2">
                        <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 text-base font-medium text-black bg-white rounded-md hover:bg-gray-200 transition-colors">Login / Register</a>
                    </div>
                @endguest

                @auth
                    <div class="mt-2 rounded-lg border border-gray-800">
                        <div class="px-4 py-2 text-xs text-gray-300">{{ auth()->user()->full_name ?? auth()->user()->name ?? 'User' }}</div>
                        <a href="{{ route('profile') }}" class="block px-4 py-2 text-base font-medium text-white hover:text-gray-300 hover:bg-gray-800/50 flex items-center gap-2">
                            <x-heroicon-o-user class="w-5 h-5" />
                            <span>My Profile</span>
                        </a>
                        @if(auth()->user()->role->name === 'Superadmin')
                            <a href="{{ route('superadmin.dashboard') }}" class="block px-4 py-2 text-base font-medium text-white hover:text-gray-300 hover:bg-gray-800/50">SuperAdmin Dashboard</a>
                        @elseif(auth()->user()->role->name === 'Admin')
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-base font-medium text-white hover:text-gray-300 hover:bg-gray-800/50">Admin Dashboard</a>
                        @elseif(auth()->user()->role->name === 'Receptionist')
                            <a href="{{ route('receptionist.dashboard') }}" class="block px-4 py-2 text-base font-medium text-white hover:text-gray-300 hover:bg-gray-800/50">Receptionist Dashboard</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-800">@csrf
                            <button type="submit" class="block w-full text-left px-4 py-3 text-base font-medium text-white hover:text-gray-300 hover:bg-gray-800/50">Logout</button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- Spacer so content doesn't slide under fixed navbar --}}
    <div class="h-16"></div>

    <script>
        function setupDropdown(btnId, menuId, arrowId){
            const btn=document.getElementById(btnId);
            const menu=document.getElementById(menuId);
            const arrow=document.getElementById(arrowId);
            if(!btn||!menu)return;
            let isOpen=false;
            btn.setAttribute('aria-expanded','false');
            btn.setAttribute('aria-haspopup','true');
            btn.addEventListener('click',(e)=>{
                e.stopPropagation();
                isOpen=!isOpen;
                menu.classList.toggle('show',isOpen);
                if(arrow) arrow.style.transform=isOpen?'rotate(180deg)':'rotate(0deg)';
                btn.setAttribute('aria-expanded',isOpen?'true':'false');
            });
            document.addEventListener('click',(e)=>{
                if(!isOpen)return;
                if(!btn.contains(e.target)&&!menu.contains(e.target)){
                    isOpen=false;menu.classList.remove('show');
                    if(arrow) arrow.style.transform='rotate(0deg)';
                    btn.setAttribute('aria-expanded','false');
                }
            });
            document.addEventListener('keydown',(e)=>{
                if(e.key==='Escape'&&isOpen){
                    isOpen=false;menu.classList.remove('show');
                    if(arrow) arrow.style.transform='rotate(0deg)';
                    btn.setAttribute('aria-expanded','false');
                }
            });
        }
        setupDropdown('statusDropdownBtn','statusDropdown','statusDropdownArrow');
        setupDropdown('profileDropdownBtn','profileDropdown','dropdownArrow');

        const menu=document.getElementById('mobile-menu');
        const sheet=menu.querySelector('.mobile-menu-slide');
        const burger=document.getElementById('hamburger');
        function openMenu(){menu.classList.remove('hidden');requestAnimationFrame(()=>sheet.classList.add('show'));burger.classList.add('hamburger-active');burger.setAttribute('aria-expanded','true');}
        function closeMenu(){sheet.classList.remove('show');burger.classList.remove('hamburger-active');burger.setAttribute('aria-expanded','false');setTimeout(()=>menu.classList.add('hidden'),180);}
        burger.addEventListener('click',()=>burger.classList.contains('hamburger-active')?closeMenu():openMenu());

        document.addEventListener('click',(e)=>{
            const navbar=document.querySelector('.bg-black');
            if(!navbar.contains(e.target)&&burger.classList.contains('hamburger-active')) closeMenu();
        });
        document.addEventListener('keydown',(e)=>{if(e.key==='Escape'&&burger.classList.contains('hamburger-active')) closeMenu();});
        window.addEventListener('resize',()=>{if(window.innerWidth>=768){sheet.classList.remove('show');menu.classList.add('hidden');burger.classList.remove('hamburger-active');burger.setAttribute('aria-expanded','false');}});
        
        const statusMobileBtn=document.getElementById('statusMobileBtn');
        const statusMobileMenu=document.getElementById('statusMobileMenu');
        const statusMobileArrow=document.getElementById('statusMobileArrow');
        if(statusMobileBtn&&statusMobileMenu){
            let isOpen=false;
            statusMobileBtn.addEventListener('click',()=>{
                isOpen=!isOpen;
                statusMobileMenu.classList.toggle('hidden',!isOpen);
                if(statusMobileArrow) statusMobileArrow.style.transform=isOpen?'rotate(180deg)':'rotate(0deg)';
            });
        }
    </script>
</div>
