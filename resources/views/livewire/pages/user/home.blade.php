<div class="min-h-screen bg-white">
    <section class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-6 md:py-8 space-y-8">
        {{-- HEADER --}}
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <x-heroicon-o-home class="w-8 h-8 text-black" />
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-black">KRBS Home</h1>
                    <p class="mt-1 text-gray-600">Selamat datang! Kebun Raya Bogor System.</p>
                </div>
            </div>
        </div>

        {{-- ANNOUNCEMENT & INFORMATION --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- ANNOUNCEMENT --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-black space-y-4">
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

            {{-- INFORMATION --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-black space-y-4">
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

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
            {{-- TICKET SUMMARY --}}
            {{-- TICKET SUMMARY --}}
            <div class="bg-white rounded-xl p-6 transition-all duration-300 hover:shadow-xl border border-black flex flex-col h-full">
                <div class="flex items-center gap-3 mb-3">
                    {{-- changed icon from ticket to chat-bubble-left-right --}}
                    <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 text-black" />
                    <h2 class="text-xl font-semibold text-black">Support Tickets</h2>
                </div>
                <p class="text-gray-600 mb-4">Ringkasan tiket dukungan yang belum selesai.</p>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between px-3 py-2 border rounded-md">
                        <span class="text-gray-700">Open</span>
                        <span class="font-semibold text-black">{{ $openTicketsCount ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between px-3 py-2 border rounded-md">
                        <span class="text-gray-700">In Progress</span>
                        <span class="font-semibold text-black">{{ $inProgressTicketsCount ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between px-3 py-2 border rounded-md">
                        <span class="text-gray-700">Resolved (7d)</span>
                        <span class="font-semibold text-black">{{ $resolvedLast7d ?? 0 }}</span>
                    </div>
                </div>

                <div class="mt-auto"></div>
                <div class="flex justify-between items-center pt-4">
                    <div class="text-sm text-gray-500">
                        Total: <span class="font-semibold text-black">
                            {{ ($openTicketsCount ?? 0) + ($inProgressTicketsCount ?? 0) }}
                        </span>
                    </div>
                    <a href="{{ route('ticketstatus') }}"
                        class="flex items-center gap-1 bg-black text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">
                        <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                        Manage
                    </a>
                </div>
            </div>


            {{-- BOOKING SUMMARY --}}
            <div class="bg-white rounded-xl p-6 transition-all duration-300 hover:shadow-xl border border-black flex flex-col h-full">
                <div class="flex items-center gap-3 mb-3">
                    <x-heroicon-o-calendar class="w-6 h-6 text-black" />
                    <h2 class="text-xl font-semibold text-black">Booking History</h2>
                </div>
                <p class="text-gray-600 mb-4">Ringkasan booking kamu.</p>

                @if(!empty($nextBooking))
                <div class="rounded-lg border border-gray-200 p-4 mb-4 bg-gray-50">
                    <div class="text-sm text-gray-500 mb-1">My Next Booking</div>
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="font-semibold text-black">{{ $nextBooking['meeting_title'] }}</div>
                        <div class="text-gray-700">• Room {{ $nextBooking['room_name'] }}</div>
                        <div class="text-gray-700">
                            • {{ \Carbon\Carbon::parse($nextBooking['date'])->format('D, M j') }}
                            {{ \Carbon\Carbon::parse($nextBooking['start_time'])->format('H:i') }}–{{ \Carbon\Carbon::parse($nextBooking['end_time'])->format('H:i') }}
                        </div>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <button wire:click="rebook({{ $nextBooking['id'] }})"
                            class="px-3 py-1.5 text-sm border rounded-md hover:bg-gray-100">Rebook</button>
                        <button wire:click="cancelBooking({{ $nextBooking['id'] }})"
                            class="px-3 py-1.5 text-sm border rounded-md hover:bg-gray-100">Cancel</button>
                    </div>
                </div>
                @else
                <div class="rounded-lg border border-dashed border-gray-300 p-4 mb-4 text-sm text-gray-600">
                    Belum ada booking mendatang.
                </div>
                @endif

                <div class="flex justify-between items-center pt-4 mt-auto">
                    <div class="text-sm text-gray-500">
                        Minggu ini: <span class="font-semibold text-black">{{ $upcomingBookings ?? 0 }}</span>
                    </div>
                    <a href="{{ route('bookingstatus') }}"
                        class="flex items-center gap-1 bg-black text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">
                        <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                        Manage
                    </a>
                </div>
            </div>
        </div>

        {{-- SHORTCUTS --}}
        <div class="bg-white rounded-xl p-6 shadow-lg border border-black">
            <div class="flex items-center gap-2 mb-4">
                <x-heroicon-o-command-line class="w-6 h-6 text-black" />
                <h3 class="text-xl font-semibold text-black">Shortcuts</h3>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="button" wire:click="openQuickTicket"
                    class="flex items-center gap-1 bg-black text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">
                    <x-heroicon-o-plus-circle class="w-5 h-5" />
                    Ticket
                </button>

                <button type="button" wire:click="openQuickBook"
                    class="flex items-center gap-1 bg-black text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">
                    <x-heroicon-o-plus-circle class="w-5 h-5" />
                    Booking
                </button>
            </div>

            <div class="my-6 h-px bg-black/20"></div>

            {{-- INFO CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- WIFI --}}
                <div class="rounded-xl p-6 border border-green-500 bg-emerald-900 text-white flex flex-col">
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

                {{-- HELP --}}
                <div class="rounded-xl p-6 border border-yellow-500 bg-yellow-700 text-white flex flex-col">
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

                {{-- BUG REPORT --}}
                <div class="rounded-xl p-6 border border-red-500 bg-red-900 text-white flex flex-col">
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
            </div>
        </div>

        {{-- Livewire modals --}}
        <livewire:booking.quick-book-modal />
        <livewire:tickets.quick-ticket-modal />
    </section>
</div>