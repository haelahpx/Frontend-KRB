{{-- ADDED: wire:poll di ROOT ELEMENT agar seluruh komponen refresh otomatis --}}
<div class="bg-white text-zinc-900 p-6 flex flex-col justify-center pb-28" wire:poll.10s="checkSystemHealth">

    <style>
        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #d4d4d8;
            border-radius: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #a1a1aa;
        }
    </style>

    <div class="max-w-[1400px] mx-auto w-full">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 auto-rows-[320px]">

            {{-- 1. TICKET CENTER (USER SUMMARY MODE) --}}
            <div class="lg:col-span-6 relative overflow-hidden rounded-2xl border-2 border-zinc-100 shadow-sm flex flex-col md:flex-row group transition-all hover:shadow-md hover:border-zinc-300">
                
                {{-- BAGIAN KIRI: USER ACTION & TOTAL --}}
                <div class="md:w-[35%] bg-[#0a0a0a] text-white p-6 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-zinc-800 rounded-full blur-[50px] opacity-20 -mr-10 -mt-10 pointer-events-none"></div>
                    
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center border border-zinc-700">
                                <x-heroicon-o-ticket class="w-4 h-4 text-white" />
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest text-zinc-400">My Summary</span>
                        </div>

                        <div class="text">
                            <p class="text-5xl font-black text-white tracking-tighter">
                                {{ $totalTickets ?? 0 }}
                            </p>
                            <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider mt-1">Total Submitted</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <a href="{{ route('create-ticket') }}" class="w-full py-3 bg-white text-black text-xs font-black uppercase tracking-wider rounded hover:bg-zinc-200 transition text-center flex items-center justify-center gap-2">
                            <x-heroicon-m-plus class="w-3 h-3" />
                            Create New
                        </a>
                        <a href="{{ route('ticketstatus') }}" class="w-full py-3 bg-zinc-900 text-zinc-400 border border-zinc-800 text-xs font-bold uppercase tracking-wider rounded hover:bg-zinc-800 hover:text-white transition text-center block">
                            View All List
                        </a>
                    </div>
                </div>

                {{-- BAGIAN KANAN: STATUS GRID --}}
                <div class="md:w-[65%] bg-white p-6">
                    <div class="h-full flex flex-col justify-center">
                        <div class="grid grid-cols-2 gap-4">
                            {{-- OPEN --}}
                            <div class="p-4 rounded-xl border border-zinc-100 bg-zinc-50 flex flex-col items-center text-center group/stat hover:border-blue-200 hover:bg-blue-50 transition-colors">
                                <span class="mb-2 p-2 rounded-full bg-white border border-zinc-200 text-blue-600 group-hover/stat:border-blue-200">
                                    <x-heroicon-o-inbox class="w-5 h-5" />
                                </span>
                                <span class="text-2xl font-black text-zinc-900 group-hover/stat:text-blue-700">{{ $ticketsOpen ?? 0 }}</span>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider group-hover/stat:text-blue-400">Open</span>
                            </div>
                            {{-- ON PROCESS --}}
                            <div class="p-4 rounded-xl border border-zinc-100 bg-zinc-50 flex flex-col items-center text-center group/stat hover:border-yellow-200 hover:bg-yellow-50 transition-colors">
                                <span class="mb-2 p-2 rounded-full bg-white border border-zinc-200 text-yellow-600 group-hover/stat:border-yellow-200">
                                    <x-heroicon-o-arrow-path class="w-5 h-5" />
                                </span>
                                <span class="text-2xl font-black text-zinc-900 group-hover/stat:text-yellow-700">{{ $ticketsProgress ?? 0 }}</span>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider group-hover/stat:text-yellow-500">On Process</span>
                            </div>
                            {{-- RESOLVED --}}
                            <div class="p-4 rounded-xl border border-zinc-100 bg-zinc-50 flex flex-col items-center text-center group/stat hover:border-green-200 hover:bg-green-50 transition-colors">
                                <span class="mb-2 p-2 rounded-full bg-white border border-zinc-200 text-green-600 group-hover/stat:border-green-200">
                                     <x-heroicon-o-check-circle class="w-5 h-5" />
                                </span>
                                <span class="text-2xl font-black text-zinc-900 group-hover/stat:text-green-700">{{ $ticketsResolved ?? 0 }}</span>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider group-hover/stat:text-green-500">Resolved</span>
                            </div>
                            {{-- CLOSED --}}
                            <div class="p-4 rounded-xl border border-zinc-100 bg-zinc-50 flex flex-col items-center text-center group/stat hover:border-zinc-300 hover:bg-zinc-100 transition-colors">
                                <span class="mb-2 p-2 rounded-full bg-white border border-zinc-200 text-zinc-600 group-hover/stat:border-zinc-300">
                                     <x-heroicon-o-lock-closed class="w-5 h-5" />
                                </span>
                                <span class="text-2xl font-black text-zinc-900 group-hover/stat:text-black">{{ $ticketsClosed ?? 0 }}</span>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider group-hover/stat:text-zinc-600">Closed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. WIFI ACCESS (Carousel Mode) --}}
            <div class="lg:col-span-3 relative group overflow-hidden rounded-2xl bg-white border-2 border-zinc-100 shadow-sm hover:border-yellow-400 transition-all"
                x-data="{ 
                    active: 0, 
                    wifis: [
                        { location: 'Gedung Konservasi', ssid: 'EVENT_5G', pass: 'kebunraya' },
                        { location: 'Lobby Utama', ssid: 'GUEST_KRB', pass: 'tamu2025' },
                        { location: 'Ruang Meeting Lt.2', ssid: 'MEETING_ROOM', pass: 'presentasi' },
                        { location: 'Kantin Staff', ssid: 'KRB_SNACK', pass: 'kopi_pagi' }
                    ],
                    next() { this.active = (this.active + 1) % this.wifis.length },
                    prev() { this.active = (this.active - 1 + this.wifis.length) % this.wifis.length },
                    copy(text) { navigator.clipboard.writeText(text); alert('Password copied!') }
                }">

                <div class="relative h-full w-full p-6 flex flex-col">
                    {{-- Header --}}
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-yellow-100 rounded-lg border border-yellow-200 group-hover:bg-yellow-500 group-hover:text-white transition-colors text-yellow-600">
                                <x-heroicon-o-wifi class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-black leading-none">Wifi</h3>
                                <p class="text-[10px] font-bold text-zinc-400 uppercase mt-1">Access</p>
                            </div>
                        </div>
                        {{-- Controls --}}
                        <div class="flex gap-1">
                            <button @click="prev()" class="p-1.5 rounded-full bg-zinc-50 hover:bg-black hover:text-white text-zinc-400 transition border border-zinc-100">
                                <x-heroicon-s-chevron-left class="w-4 h-4" />
                            </button>
                            <button @click="next()" class="p-1.5 rounded-full bg-zinc-50 hover:bg-black hover:text-white text-zinc-400 transition border border-zinc-100">
                                <x-heroicon-s-chevron-right class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 relative mt-2">
                        <template x-for="(wifi, index) in wifis" :key="index">
                            <div x-show="active === index" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-x-4"
                                x-transition:enter-end="opacity-100 translate-x-0"
                                x-transition:leave="transition ease-in duration-300 absolute top-0 left-0 w-full"
                                x-transition:leave-start="opacity-100 translate-x-0"
                                x-transition:leave-end="opacity-0 -translate-x-4"
                                class="w-full flex flex-col justify-center h-full">

                                <p class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
                                    <span x-text="wifi.location"></span>
                                </p>

                                <div class="space-y-3">
                                    <div class="p-3 rounded-lg bg-zinc-50 border border-zinc-200 group-hover:border-yellow-200 transition-colors">
                                        <p class="text-[10px] font-bold text-zinc-400 uppercase">SSID</p>
                                        <p class="text-sm font-mono font-bold text-black" x-text="wifi.ssid"></p>
                                    </div>
                                    <div class="p-3 rounded-lg bg-zinc-50 border border-zinc-200 group-hover:border-yellow-200 transition-colors relative">
                                        <p class="text-[10px] font-bold text-zinc-400 uppercase">Password</p>
                                        <div class="flex justify-between items-center">
                                            <p class="text-sm font-mono font-bold text-black" x-text="wifi.pass"></p>
                                            <button @click="copy(wifi.pass)" class="text-zinc-400 hover:text-yellow-600 transition" title="Copy Password">
                                                <x-heroicon-m-clipboard-document class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    {{-- Dots --}}
                    <div class="mt-4 flex justify-center gap-1.5">
                        <template x-for="(wifi, index) in wifis" :key="index">
                            <button @click="active = index" class="h-1 rounded-full transition-all duration-300"
                                :class="active === index ? 'w-6 bg-yellow-500' : 'w-1.5 bg-zinc-200 hover:bg-zinc-300'">
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- 3. HELP CENTER & SYSTEM STATUS (REALTIME) --}}
            {{-- Note: Poll sudah dipindah ke ROOT, div ini hanya render data --}}
            <div class="lg:col-span-3 relative group overflow-hidden rounded-2xl bg-white border-2 border-zinc-100 shadow-sm hover:border-red-500 transition-all">
                <div class="p-6 h-full flex flex-col">

                    {{-- Header dengan Timestamp agar terlihat hidup --}}
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-red-50 rounded-lg border border-red-100 group-hover:bg-red-600 group-hover:text-white transition-colors text-red-600">
                                <x-heroicon-o-lifebuoy class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-black leading-none">Support</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-[10px] font-bold text-zinc-400 uppercase">Center</p>
                                    {{-- Visual feedback update --}}
                                    <span class="text-[9px] text-zinc-300 bg-zinc-50 px-1 rounded font-mono border border-zinc-100 animate-pulse">
                                        Updated: {{ now()->format('H:i:s') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        {{-- Indikator Global --}}
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $dbStatus === 'OK' ? 'bg-green-400' : 'bg-red-400' }} opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 {{ $dbStatus === 'OK' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        </span>
                    </div>

                    {{-- System Health Monitor --}}
                    <div class="bg-zinc-50 rounded-xl p-3 border border-zinc-100 mb-4 space-y-2">
                        <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2">Live System Health</p>

                        {{-- Server --}}
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <x-heroicon-s-server class="w-3.5 h-3.5 text-zinc-400" />
                                <span class="text-xs font-bold text-zinc-600">Main Server</span>
                            </div>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $serverStatus === 'OK' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }}">
                                {{ $serverStatus }}
                            </span>
                        </div>

                        {{-- Database --}}
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <x-heroicon-s-circle-stack class="w-3.5 h-3.5 text-zinc-400" />
                                <span class="text-xs font-bold text-zinc-600">Database</span>
                            </div>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $dbStatus === 'OK' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }}">
                                {{ $dbStatus }}
                            </span>
                        </div>

                        {{-- Network (Dengan Warna Dinamis dari PHP) --}}
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <x-heroicon-s-globe-alt class="w-3.5 h-3.5 text-zinc-400" />
                                <span class="text-xs font-bold text-zinc-600">Network</span>
                            </div>
                            {{-- Variabel $networkColor dikirim dari PHP (Home.php) --}}
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $networkColor }}">
                                {{ $networkLatency }}
                            </span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-auto grid grid-cols-2 gap-3">
                        <a href="mailto:support@krbs.id" class="flex flex-col items-center justify-center p-3 rounded-xl border-2 border-dashed border-zinc-200 hover:border-red-500 hover:bg-red-50 transition group/btn text-center">
                            <x-heroicon-o-bug-ant class="w-6 h-6 text-zinc-400 group-hover/btn:text-red-600 mb-1 transition-colors" />
                            <span class="text-[10px] font-black uppercase text-zinc-600 group-hover/btn:text-red-700">Report<br>Bug</span>
                        </a>
                        <a href="mailto:support@krbs.id" class="flex flex-col items-center justify-center p-3 rounded-xl border-2 border-dashed border-zinc-200 hover:border-black hover:bg-zinc-50 transition group/btn text-center">
                            <x-heroicon-o-wrench-screwdriver class="w-6 h-6 text-zinc-400 group-hover/btn:text-black mb-1 transition-colors" />
                            <span class="text-[10px] font-black uppercase text-zinc-600 group-hover/btn:text-black">IT<br>Support</span>
                        </a>
                    </div>

                </div>
            </div>

            {{-- 4. ANNOUNCEMENT --}}
            <div class="lg:col-span-3 relative group overflow-hidden rounded-2xl bg-white border-2 border-zinc-100 shadow-sm hover:border-red-500 transition-all">
                <div class="relative h-full w-full p-6 flex flex-col">
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b-2 border-red-100">
                        <x-heroicon-o-megaphone class="w-6 h-6 text-red-600" />
                        <h3 class="text-base font-bold text-black">Announcement</h3>
                    </div>
                    <div class="flex-1 overflow-y-auto custom-scroll pr-2 space-y-4">
                        @forelse ($announcements as $a)
                            <div>
                                <span class="text-[10px] text-white font-bold bg-red-600 px-2 py-0.5 rounded-full">
                                    {{ optional($a->event_at)->format('d M') ?? '-' }}
                                </span>
                                <p class="text-sm font-medium text-zinc-800 mt-2 leading-snug">{{ $a->description }}</p>
                            </div>
                        @empty
                            <p class="text-zinc-400 text-sm">Tidak ada pengumuman.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 5. INFORMATION --}}
            <div class="lg:col-span-3 relative group overflow-hidden rounded-2xl bg-white border-2 border-zinc-100 shadow-sm hover:border-yellow-400 transition-all">
                <div class="relative h-full w-full p-6 flex flex-col">
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b-2 border-yellow-100">
                        <x-heroicon-o-information-circle class="w-6 h-6 text-yellow-500" />
                        <h3 class="text-base font-bold text-black">Information</h3>
                    </div>
                    <div class="flex-1 overflow-y-auto custom-scroll pr-2 space-y-3">
                        @forelse ($informations as $info)
                            <div class="flex gap-3">
                                <div class="w-1 h-full min-h-[20px] bg-yellow-400"></div>
                                <div>
                                    <p class="text-sm font-medium text-zinc-800">{{ $info->description }}</p>
                                    <p class="text-[10px] font-bold text-zinc-400 mt-1">
                                        {{ optional($info->event_at)->format('Y-m-d') ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-zinc-400 text-sm">Tidak ada informasi.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 6. WELCOME HEADER --}}
            <div class="lg:col-span-6 relative overflow-hidden rounded-2xl bg-black border-2 border-black shadow-lg">
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px]"></div>
                <div class="absolute right-0 bottom-0 opacity-10 translate-x-10 translate-y-10">
                    <x-heroicon-o-building-library class="w-64 h-64 text-white" />
                </div>
                <div class="relative z-10 h-full p-8 flex flex-col justify-center">
                    <span class="inline-block w-fit px-3 py-1 rounded-sm bg-yellow-500 text-black text-[10px] font-black uppercase tracking-widest mb-6">
                        KRBS System
                    </span>
                    <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4">
                        Welcome Back, <br>
                        <span class="text-yellow-400">Team Member</span>
                    </h1>
                    <p class="text-zinc-400 max-w-md leading-relaxed mb-8 font-medium">
                        Akses cepat ke semua layanan operasional. Pastikan selalu cek pengumuman terbaru hari ini.
                    </p>
                    <div class="flex gap-4">
                        <button class="px-6 py-3 bg-white text-black rounded-lg text-sm font-bold hover:bg-zinc-200 transition uppercase tracking-wider">
                            View Profile
                        </button>
                        <button class="px-6 py-3 bg-transparent text-yellow-400 border-2 border-yellow-400 rounded-lg text-sm font-bold hover:bg-yellow-400 hover:text-black transition uppercase tracking-wider">
                            Documentation
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>