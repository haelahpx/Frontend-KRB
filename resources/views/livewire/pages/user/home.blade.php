<div class="min-h-screen bg-white">
    <section class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-6 md:py-8 space-y-8">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-black">KRBS Home</h1>
                <p class="mt-2 text-gray-600">Selamat datang! Kebun Raya Bogor System.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- ANNOUNCEMENT --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-black space-y-4">
                <h3 class="text-[#b10303] text-xl font-semibold mb-4">Announcement!</h3>
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
                <h3 class="text-[#b10303] text-xl font-semibold mb-4">Information</h3>
                <hr>
                @forelse ($informations as $info)
                    <div class="flex gap-4 items-start">
                        <p class="text-gray-600 font-medium min-w-[120px]">
                            {{ $info->description }}
                        </p>
                        @php $dateText = optional($info->event_at)->format('Y-m-d'); @endphp
                        <p class="text-gray-600">{{ $dateText ?? '-' }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">Belum ada informasi.</p>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
            {{-- TICKETS CARD (mini summary) --}}
            <div
                class="bg-white rounded-xl p-6 transition-all duration-300 hover:shadow-xl border border-black flex flex-col h-full">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-black text-white px-3 py-1 rounded-full text-sm font-medium">Tickets</div>
                    <h2 class="text-xl font-semibold text-black">Tickets Status</h2>
                </div>
                <p class="text-gray-600 mb-4">Ringkasan tiket yang belum selesai.</p>

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
                        Total: <span
                            class="font-semibold text-black">{{ ($openTicketsCount ?? 0) + ($inProgressTicketsCount ?? 0) }}</span>
                    </div>
                    <a href="{{ route('ticketstatus') }}"
                        class="bg-black text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">
                        Manage
                    </a>
                </div>
            </div>

            {{-- BOOKING HISTORY SUMMARY CARD --}}
            <div
                class="bg-white rounded-xl p-6 transition-all duration-300 hover:shadow-xl border border-black flex flex-col h-full">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-black text-white px-3 py-1 rounded-full text-sm font-medium">Booking</div>
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

                <div class="mt-auto"></div>
                <div class="flex justify-between items-center pt-4">
                    <div class="text-sm text-gray-500">
                        Minggu ini: <span class="font-semibold text-black">{{ $upcomingBookings ?? 0 }}</span>
                    </div>
                    <a href="{{ route('bookingstatus') }}"
                        class="bg-black text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">
                        Manage
                    </a>
                </div>
            </div>
        </div>

        {{-- SHORTCUTS --}}
        <div class="bg-white rounded-xl p-6 shadow-lg border border-black">
            <h3 class="text-xl font-semibold text-black mb-4">Shortcuts</h3>
            <div class="flex flex-wrap gap-3">
                <button type="button" wire:click="openQuickTicket"
                    class="bg-black text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">
                    + Ticket
                </button>

                {{-- pakai modal quick-book Livewire yang sama --}}
                <button type="button" wire:click="openQuickBook"
                    class="bg-black text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">
                    + Booking
                </button>
            </div>

            <div class="my-6 h-px bg-black/20"></div>

            {{-- tiga info cards (contoh statis) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    class="rounded-xl p-4 sm:p-6 border border-green-500 bg-emerald-900 text-white h-full flex flex-col">
                    <h2 class="text-base sm:text-lg font-bold tracking-wide">WIFI ACCESS</h2>
                    <div class="mt-3 sm:mt-4 space-y-3 sm:space-y-4 text-sm">
                        <div class="flex gap-3">
                            <div class="mt-1">📍</div>
                            <div>
                                <p class="font-semibold uppercase">Gedung Konservasi</p>
                                <p>Network : <span class="font-semibold">EVENT_5G</span></p>
                                <p>User / Password : <span class="font-semibold">magang-it / kebunraya</span></p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="mt-1">📍</div>
                            <div>
                                <p class="font-semibold uppercase">Kebun Raya</p>
                                <p>Network : <span class="font-semibold">blablabla</span></p>
                                <p>User / Password : <span class="font-semibold">blablalba / blablabla</span></p>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="mt-4 sm:mt-6 self-end text-[11px] sm:text-xs hover:underline">More info »</a>
                </div>

                <div
                    class="rounded-xl p-4 sm:p-6 border border-yellow-500 bg-yellow-700 text-white h-full flex flex-col">
                    <h2 class="text-base sm:text-lg font-bold tracking-wide">NEED HELP?</h2>
                    <div class="mt-3 sm:mt-4 space-y-2.5 sm:space-y-3 text-sm">
                        <div>
                            <p class="font-semibold">Technical Matters</p>
                            <a class="underline break-words" href="mailto:meowmeow@gmail.co">meowmeow@gmail.co</a>
                        </div>
                        <div>
                            <p class="font-semibold">Other Problems</p>
                            <a class="underline break-words" href="mailto:meowmeow@gmail.co">meowmeow@gmail.co</a>
                        </div>
                    </div>
                    <a href="#" class="mt-4 sm:mt-6 self-end text-[11px] sm:text-xs hover:underline">More info »</a>
                </div>

                <div class="rounded-xl p-4 sm:p-6 border border-red-500 bg-red-900 text-white h-full flex flex-col">
                    <h2 class="text-base sm:text-lg font-bold tracking-wide">BUG REPORTING</h2>
                    <div class="mt-3 sm:mt-4 space-y-2.5 sm:space-y-3 text-sm">
                        <div>
                            <p class="font-semibold">KRBS</p>
                            <a class="underline break-words" href="mailto:meowmeow@gmail.co">meowmeow@gmail.co</a>
                        </div>
                        <div>
                            <p class="font-semibold">Lost and Found</p>
                            <a class="underline break-words" href="mailto:meowmeow@gmail.co">meowmeow@gmail.co</a>
                        </div>
                    </div>
                    <a href="#" class="mt-4 sm:mt-6 self-end text-[11px] sm:text-xs hover:underline">More info »</a>
                </div>
            </div>
        </div>

        {{-- NEW TICKET MODAL (biarkan seperti punyamu / contoh kosong) --}}
        <flux:modal name="new-ticket" variant="flyout" class="text-black">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Create Support Ticket</flux:heading>
                    <flux:text class="mt-2">Fill out the form below to submit a new support ticket.</flux:text>
                </div>
                {{-- … isi form ticket milikmu … --}}
            </div>
        </flux:modal>

        {{-- Quick-book modal Livewire (re-use komponen yang sama) --}}
        <livewire:booking.quick-book-modal />
        <livewire:tickets.quick-ticket-modal />
    </section>
</div>