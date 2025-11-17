<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h1 class="text-3xl font-bold text-gray-900">KRBS Home</h1>
            <p class="text-gray-600">Selamat datang! Kebun Raya Bogor System.</p>

            <div class="flex items-center gap-3">
                <div class="flex bg-gray-100 rounded-md p-1">
                    <a href="#announcement" class="px-4 py-2 text-sm font-medium rounded transition-colors
                        text-gray-700 hover:text-gray-900">
                        Announcement
                    </a>
                    <a href="#information" class="px-4 py-2 text-sm font-medium rounded transition-colors
                        text-gray-700 hover:text-gray-900">
                        Information
                    </a>
                    <a href="#shortcuts" class="px-4 py-2 text-sm font-medium rounded transition-colors
                        text-gray-700 hover:text-gray-900">
                        Shortcuts
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ANNOUNCEMENT & INFORMATION -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div id="announcement" class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-6">
            <div class="flex items-center gap-2 mb-2">
                <x-heroicon-o-megaphone class="w-6 h-6 text-[#b10303]" />
                <h3 class="text-[#b10303] text-xl font-semibold">Announcement!</h3>
            </div>
            <hr>
            @forelse ($announcements as $a)
                <div class="flex gap-4 items-start">
                    <h4 class="text-[#b10303] font-medium min-w-[120px]">
                        {{ optional($a->event_at)->format('Y-m-d') ?? '-' }}
                    </h4>
                    <p class="text-gray-600">{{ $a->description }}</p>
                </div>
            @empty
                <p class="text-gray-500">Belum ada pengumuman.</p>
            @endforelse
        </div>

        <div id="information" class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-6">
            <div class="flex items-center gap-2 mb-2">
                <x-heroicon-o-information-circle class="w-6 h-6 text-[#b10303]" />
                <h3 class="text-[#b10303] text-xl font-semibold">Information</h3>
            </div>
            <hr>
            @forelse ($informations as $info)
                <div class="flex gap-4 items-start">
                    <p class="text-gray-600 font-medium min-w-[120px]">{{ $info->description }}</p>
                    <p class="text-gray-600">{{ optional($info->event_at)->format('Y-m-d') ?? '-' }}</p>
                </div>
            @empty
                <p class="text-gray-500">Belum ada informasi.</p>
            @endforelse
        </div>
    </div>

    <!-- SHORTCUTS -->
    <div id="shortcuts" class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Wifi Access -->
            <div class="rounded-lg p-6 border border-green-500 bg-emerald-900 text-white flex flex-col">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-wifi class="w-5 h-5" />
                    <h2 class="text-lg font-bold tracking-wide">WIFI ACCESS</h2>
                </div>
                <div class="mt-4 space-y-4 text-sm">
                    <div>
                        <p class="font-semibold uppercase">Gedung Konservasi</p>
                        <p>Network: <span class="font-semibold">EVENT_5G</span></p>
                        <p>User / Password: <span class="font-semibold">magang-it / kebunraya</span></p>
                    </div>
                    <div>
                        <p class="font-semibold uppercase">Kebun Raya</p>
                        <p>Network: <span class="font-semibold">BLABLABLA</span></p>
                        <p>User / Password: <span class="font-semibold">blabla / blabla</span></p>
                    </div>
                </div>
            </div>

            <!-- Need Help -->
            <div class="rounded-lg p-6 border border-yellow-500 bg-yellow-700 text-white flex flex-col">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-lifebuoy class="w-5 h-5" />
                    <h2 class="text-lg font-bold tracking-wide">NEED HELP?</h2>
                </div>
                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <p class="font-semibold">Technical Matters</p>
                        <a href="mailto:meowmeow@gmail.co" class="underline break-words">meowmeow@gmail.co</a>
                    </div>
                    <div>
                        <p class="font-semibold">Other Problems</p>
                        <a href="mailto:meowmeow@gmail.co" class="underline break-words">meowmeow@gmail.co</a>
                    </div>
                </div>
            </div>

            <!-- Bug Reporting -->
            <div class="rounded-lg p-6 border border-red-500 bg-red-900 text-white flex flex-col">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-bug-ant class="w-5 h-5" />
                    <h2 class="text-lg font-bold tracking-wide">BUG REPORTING</h2>
                </div>
                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <p class="font-semibold">KRBS</p>
                        <a href="mailto:meowmeow@gmail.co" class="underline break-words">meowmeow@gmail.co</a>
                    </div>
                    <div>
                        <p class="font-semibold">Lost and Found</p>
                        <a href="mailto:meowmeow@gmail.co" class="underline break-words">meowmeow@gmail.co</a>
                    </div>
                </div>
            </div>

            <!-- My Tickets -->
            <div class="rounded-lg p-6 border border-slate-500 bg-slate-900 text-white flex flex-col">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5" />
                    <h2 class="text-lg font-bold tracking-wide">MY TICKETS</h2>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs uppercase text-slate-300">Dept Queue</p>
                        <p class="text-2xl font-bold">{{ $ticketQueueCount }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase text-slate-300">My Claims</p>
                        <p class="text-2xl font-bold">{{ $ticketClaimsCount }}</p>
                    </div>
                </div>

                <div class="mt-4 text-xs text-slate-200">
                    <p class="font-semibold mb-2">Latest claimed by you</p>
                    <ul class="space-y-1 max-h-28 overflow-y-auto pr-1">
                        @forelse ($ticketClaimsPreview as $claim)
                            <li class="flex items-center justify-between gap-2">
                                <span class="truncate">
                                    {{ $claim->ticket->subject ?? 'No subject' }}
                                </span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-700/70">
                                    {{ $claim->ticket->status ?? '-' }}
                                </span>
                            </li>
                        @empty
                            <li class="italic text-slate-400">
                                Belum ada tiket yang kamu claim.
                            </li>
                        @endforelse
                    </ul>
                </div>

                <div class="mt-4 text-xs text-slate-200">
                    <p class="font-semibold mb-2">Queue waiting in your department</p>
                    <ul class="space-y-1 max-h-24 overflow-y-auto pr-1">
                        @forelse ($ticketQueuePreview as $ticket)
                            <li class="flex items-center justify-between gap-2">
                                <span class="truncate">
                                    {{ $ticket->subject ?? 'No subject' }}
                                </span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-700/70">
                                    {{ $ticket->priority ?? '-' }}
                                </span>
                            </li>
                        @empty
                            <li class="italic text-slate-400">
                                Tidak ada tiket antrian untuk saat ini.
                            </li>
                        @endforelse
                    </ul>
                </div>

                <a href="{{ route('user.ticket.queue') }}" class="mt-4 inline-flex items-center text-xs font-semibold underline decoration-slate-300 hover:decoration-white">
                    Buka Ticket Center
                    <x-heroicon-o-arrow-right class="w-4 h-4 ml-1" />
                </a>
            </div>
        </div>
    </div>
</div>
