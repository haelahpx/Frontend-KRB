{{-- resources/views/livewire/pages/admin/dashboard.blade.php --}}
<div class="bg-gray-50" wire:poll.3500ms="tick">
    <main class="px-4 sm:px-6 py-6 space-y-5">

        {{-- HERO --}}
        <header class="rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-xl px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/20 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6V4m0 16v-2m0-10v2m0 6v2M6 12H4m16 0h-2m-10 0h2m6 0h2M9 17l-2 2M15 7l2-2M7 7l-2-2M17 17l2 2"/>
                </svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-base sm:text-lg font-semibold truncate">Welcome, {{ $admin_name }}!</h2>
                <p class="text-xs text-white/80 truncate">Overview of tickets, comments, bookings, and activity.</p>
            </div>
        </header>

        {{-- KPI STRIP - Always visible, 3 columns on mobile, 3 columns on desktop --}}
        <section>
            <div class="grid grid-cols-3 gap-3">
                @foreach($stats as $s)
                    <div class="rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
                        <p class="text-[12px] text-gray-500 truncate">{{ $s['label'] }}</p>
                        <h3 class="text-xl font-semibold text-gray-900 mt-1 leading-none">{{ $s['value'] }}</h3>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- MAIN LAYOUT: Mobile stacks, Desktop has left sidebar + right content --}}
        <section class="grid grid-cols-1 gap-5 lg:grid-cols-[310px_1fr]">

            {{-- LEFT COLUMN: Recent Activity (tall) --}}
            <aside class="rounded-2xl border border-gray-200 bg-white shadow-sm h-full">
                <div class="px-4 sm:px-5 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Recent Activity</h3>
                    <p class="text-xs text-gray-500">Latest 10 items</p>
                </div>
                <ul class="divide-y divide-gray-100 max-h-[78vh] overflow-y-auto">
                    @forelse($recentActivities as $a)
                        <li class="px-4 sm:px-5 py-3">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-lg bg-gray-900 text-white flex items-center justify-center text-[11px] font-bold shrink-0">
                                    {{ $a['icon'] }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2 text-[13px] leading-tight">
                                        <span class="font-semibold text-gray-900">{{ $a['title'] }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-[11px]">{{ $a['type'] }}</span>
                                        <span class="text-[11px] text-gray-500">{{ $a['when'] }}</span>
                                    </div>
                                    @if(!empty($a['desc']))
                                        <p class="text-[12.5px] text-gray-700 mt-0.5 line-clamp-2">{{ $a['desc'] }}</p>
                                    @endif
                                    @if(!empty($a['url']))
                                        <a href="{{ $a['url'] }}" class="text-[12px] underline font-medium mt-1 inline-block">Open</a>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-5 py-12 text-center text-sm text-gray-500">No activity yet.</li>
                    @endforelse
                </ul>
            </aside>

            {{-- RIGHT COLUMN: one wide + two half cards --}}
            <div class="space-y-5">

                {{-- WIDE CARD (Recent Tickets) --}}
                <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-3 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Recent Tickets</h3>
                        <p class="text-xs text-gray-500">Latest 6 tickets</p>
                    </div>
                    <ul class="divide-y divide-gray-100 max-h-[34vh] overflow-y-auto">
                        @forelse($recentTickets as $t)
                            <li class="px-5 py-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 text-[14px] truncate">#{{ $t['id'] }} — {{ $t['subject'] }}</p>
                                        <p class="mt-0.5 text-[12px] text-gray-600">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-100">
                                                <span class="text-gray-500">Pri:</span><span class="font-medium capitalize">{{ $t['priority'] }}</span>
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-100 ml-2">
                                                <span class="text-gray-500">Status:</span>
                                                <span class="font-medium capitalize">{{ str_replace('_',' ',$t['status']) }}</span>
                                            </span>
                                        </p>
                                        <p class="text-[11.5px] text-gray-500 mt-0.5">
                                            {{ $t['user'] }} • {{ $t['dept'] }} • {{ $t['when'] }}
                                        </p>
                                    </div>
                                    <a href="{{ $t['url'] }}" class="px-3 py-1.5 text-[12px] font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none shrink-0">
                                        Open
                                    </a>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-10 text-center text-sm text-gray-500">No tickets found.</li>
                        @endforelse
                    </ul>
                </section>

                {{-- TWO HALF CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Notifications --}}
                    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                        <div class="px-5 py-3 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                            <p class="text-xs text-gray-500">Latest 3 ticket comments</p>
                        </div>
                        <ul class="divide-y divide-gray-100 max-h-[28vh] overflow-y-auto">
                            @forelse($latestComments as $n)
                                <li class="px-5 py-3">
                                    <div class="text-[11px] text-gray-500 mb-0.5">{{ $n['when'] }}</div>
                                    <div class="text-[13px]">
                                        <span class="font-semibold">{{ $n['by'] }}</span> commented on
                                        <span class="font-medium">#{{ $n['ticket_id'] }} — {{ $n['ticket_subject'] }}</span>
                                    </div>
                                    <p class="text-[12px] text-gray-600 mt-0.5 line-clamp-2">{{ $n['text'] }}</p>
                                    <a href="{{ $n['url'] }}" class="px-3 py-1.5 text-[12px] font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none mt-1 inline-block">
                                        Open
                                    </a>
                                </li>
                            @empty
                                <li class="px-5 py-8 text-center text-sm text-gray-500">No comments.</li>
                            @endforelse
                        </ul>
                    </section>

                    {{-- Recent Bookings --}}
                    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                        <div class="px-5 py-3 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900">Recent Room Bookings</h3>
                            <p class="text-xs text-gray-500">Latest 5 bookings</p>
                        </div>
                        <ul class="divide-y divide-gray-100 max-h-[28vh] overflow-y-auto">
                            @forelse($recentBookings as $b)
                                <li class="px-5 py-3">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 text-[14px] truncate">
                                                {{ $b['meeting_title'] }} — {{ $b['room_label'] }}
                                            </p>
                                            <p class="text-[12px] text-gray-500 mt-0.5">{{ $b['by'] }} • {{ $b['when'] }}</p>
                                        </div>
                                        <a href="{{ $b['url'] }}" class="px-3 py-1.5 text-[12px] font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none shrink-0">
                                            Open
                                        </a>
                                    </div>
                                </li>
                            @empty
                                <li class="px-5 py-8 text-center text-sm text-gray-500">No recent bookings.</li>
                            @endforelse
                        </ul>
                    </section>
                </div>
            </div>
        </section>
    </main>
</div>