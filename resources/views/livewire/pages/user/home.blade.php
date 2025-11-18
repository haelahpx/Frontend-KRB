<div class="min-h-screen bg-[#0f0f10] text-white p-6 flex flex-col justify-center">
    
    <style>
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #52525b; }
    </style>

    <div class="max-w-[1400px] mx-auto w-full">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 auto-rows-[320px]">

            <div class="lg:col-span-6 relative group overflow-hidden rounded-2xl bg-gradient-to-b from-white/10 to-white/0 p-[1px]">
                <div class="relative h-full w-full bg-[#18181b] rounded-2xl p-8 flex flex-col overflow-hidden">
                    
                    <div class="absolute right-0 top-0 w-[300px] h-[300px] bg-blue-500/10 blur-[80px] rounded-full pointer-events-none"></div>

                    <div class="relative z-10 flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-semibold text-white">Ticket Center</h2>
                            <p class="text-zinc-400 text-sm">Operasional & Antrian</p>
                        </div>
                        <a href="{{ route('user.ticket.queue') }}" class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-medium transition">
                            Open App &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6 relative z-10">
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 hover:border-blue-500/30 transition">
                            <p class="text-xs text-zinc-400 uppercase tracking-wider">Dept Queue</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ $ticketQueueCount }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 hover:border-emerald-500/30 transition">
                            <p class="text-xs text-zinc-400 uppercase tracking-wider">My Claims</p>
                            <p class="text-3xl font-bold text-emerald-400 mt-1">{{ $ticketClaimsCount }}</p>
                        </div>
                    </div>

                    <div class="flex-1 min-h-0 flex flex-col relative z-10">
                        <p class="text-xs font-medium text-zinc-500 mb-3 uppercase">Latest Activity</p>
                        <div class="overflow-y-auto custom-scroll pr-2 space-y-2">
                            @forelse ($ticketQueuePreview as $ticket)
                                <div class="flex items-center justify-between p-2.5 rounded-lg hover:bg-white/5 transition group/item">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                        <span class="text-sm text-zinc-300 truncate">{{ $ticket->subject }}</span>
                                    </div>
                                    <span class="text-[10px] px-2 py-1 rounded bg-zinc-800 text-zinc-400 border border-zinc-700">
                                        {{ $ticket->priority }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-zinc-600 text-sm italic">Tidak ada antrian aktif.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3 relative group overflow-hidden rounded-2xl bg-gradient-to-b from-emerald-500/20 to-white/0 p-[1px]">
                <div class="relative h-full w-full bg-[#18181b] rounded-2xl p-6 flex flex-col justify-between group-hover:bg-[#18181b]/80 transition">
                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/20 blur-3xl rounded-full"></div>
                    
                    <div>
                        <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 mb-4">
                            <x-heroicon-o-wifi class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-medium text-white">Wifi Access</h3>
                        <p class="text-zinc-400 text-sm mt-1">Gedung Konservasi</p>
                    </div>

                    <div class="space-y-3 mt-4">
                        <div class="p-3 rounded-lg bg-black/40 border border-white/5">
                            <p class="text-[10px] text-zinc-500 uppercase">SSID</p>
                            <p class="text-sm font-mono text-emerald-400">EVENT_5G</p>
                        </div>
                        <div class="p-3 rounded-lg bg-black/40 border border-white/5">
                            <p class="text-[10px] text-zinc-500 uppercase">Password</p>
                            <p class="text-sm font-mono text-white">kebunraya</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3 relative group overflow-hidden rounded-2xl bg-gradient-to-b from-amber-500/20 to-white/0 p-[1px]">
                <div class="relative h-full w-full bg-[#18181b] rounded-2xl p-6 flex flex-col">
                    <div class="absolute -left-6 -top-6 w-32 h-32 bg-amber-500/20 blur-3xl rounded-full"></div>

                    <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-400 mb-4 relative z-10">
                        <x-heroicon-o-lifebuoy class="w-5 h-5" />
                    </div>
                    <h3 class="text-lg font-medium text-white relative z-10">Help Center</h3>
                    
                    <div class="mt-auto space-y-2 relative z-10">
                        <a href="mailto:support@krbs.id" class="block w-full p-3 rounded-lg bg-white/5 hover:bg-amber-500/10 border border-white/5 hover:border-amber-500/30 transition group/link">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-zinc-300">Report Bug</span>
                                <x-heroicon-o-bug-ant class="w-4 h-4 text-zinc-500 group-hover/link:text-amber-400" />
                            </div>
                        </a>
                        <a href="mailto:support@krbs.id" class="block w-full p-3 rounded-lg bg-white/5 hover:bg-blue-500/10 border border-white/5 hover:border-blue-500/30 transition group/link">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-zinc-300">Tech Support</span>
                                <x-heroicon-o-wrench-screwdriver class="w-4 h-4 text-zinc-500 group-hover/link:text-blue-400" />
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3 relative group overflow-hidden rounded-2xl bg-gradient-to-b from-red-500/20 to-white/0 p-[1px]">
                <div class="relative h-full w-full bg-[#18181b] rounded-2xl p-6 flex flex-col">
                    <div class="flex items-center gap-3 mb-4">
                        <x-heroicon-o-megaphone class="w-5 h-5 text-red-400" />
                        <h3 class="text-base font-medium text-white">Announcement</h3>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto custom-scroll pr-2 space-y-3">
                        @forelse ($announcements as $a)
                            <div class="bg-white/5 p-3 rounded-lg border border-white/5">
                                <span class="text-[10px] text-red-400 font-bold bg-red-950/30 px-1.5 py-0.5 rounded border border-red-900/50">
                                    {{ optional($a->event_at)->format('d M') ?? '-' }}
                                </span>
                                <p class="text-sm text-zinc-300 mt-2 leading-snug">{{ $a->description }}</p>
                            </div>
                        @empty
                            <p class="text-zinc-600 text-sm">Tidak ada pengumuman.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3 relative group overflow-hidden rounded-2xl bg-gradient-to-b from-blue-500/20 to-white/0 p-[1px]">
                <div class="relative h-full w-full bg-[#18181b] rounded-2xl p-6 flex flex-col">
                    <div class="flex items-center gap-3 mb-4">
                        <x-heroicon-o-information-circle class="w-5 h-5 text-blue-400" />
                        <h3 class="text-base font-medium text-white">Information</h3>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto custom-scroll pr-2 space-y-3">
                        @forelse ($informations as $info)
                            <div class="flex gap-3 border-b border-white/5 pb-3 last:border-0">
                                <div class="w-1 h-full min-h-[20px] rounded-full bg-blue-500/50"></div>
                                <div>
                                    <p class="text-sm text-zinc-300">{{ $info->description }}</p>
                                    <p class="text-[10px] text-zinc-500 mt-1">{{ optional($info->event_at)->format('Y-m-d') ?? '-' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-zinc-600 text-sm">Tidak ada informasi.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 relative overflow-hidden rounded-2xl bg-[#18181b] border border-white/10">
                <div class="absolute inset-0 bg-gradient-to-br from-zinc-800 via-[#18181b] to-black opacity-50"></div>
                <div class="absolute right-0 bottom-0 opacity-30 translate-x-10 translate-y-10">
                     <x-heroicon-o-building-library class="w-64 h-64 text-white" />
                </div>

                <div class="relative z-10 h-full p-8 flex flex-col justify-center">
                    <span class="inline-block w-fit px-3 py-1 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-[10px] font-bold uppercase tracking-wider mb-4 shadow-lg shadow-purple-500/20">
                        Kebun Raya Bogor System
                    </span>
                    <h1 class="text-4xl md:text-5xl font-bold text-white tracking-tight mb-4">
                        Welcome Back, <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">Team Member</span>
                    </h1>
                    <p class="text-zinc-400 max-w-md leading-relaxed mb-8">
                        Akses cepat ke semua layanan operasional. Pastikan selalu cek pengumuman terbaru hari ini.
                    </p>
                    
                    <div class="flex gap-4">
                        <button class="px-6 py-2.5 bg-white text-black rounded-lg text-sm font-bold hover:bg-zinc-200 transition">
                            View Profile
                        </button>
                        <button class="px-6 py-2.5 bg-white/5 text-white border border-white/10 rounded-lg text-sm font-bold hover:bg-white/10 transition">
                            Documentation
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>