{{-- resources/views/livewire/pages/admin/dashboard.blade.php --}}
<div class="bg-gray-50" wire:poll.4000ms="tick">
    <main class="px-4 sm:px-6 py-6">
        <div class="space-y-8">

            {{-- HERO --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
                <div class="pointer-events-none absolute inset-0 opacity-10">
                    <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                    <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
                </div>
                <div class="relative z-10 p-6 sm:p-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6V4m0 16v-2m0-10v2m0 6v2M6 12H4m16 0h-2m-10 0h2m6 0h2M9 17l-2 2M15 7l2-2M7 7l-2-2M17 17l2 2" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Welcome, {{ $admin_name }}!</h2>
                            <p class="text-sm text-white/80">Here is the summary of tickets, comments, bookings, and users.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATS CARDS (4 kolom, mirip contoh) --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($stats as $s)
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-gray-500">{{ $s['label'] }}</p>
                        <div class="mt-2 flex items-end gap-2">
                            <h3 class="text-2xl font-semibold text-gray-900">{{ $s['value'] }}</h3>
                            @if(!empty($s['suffix']))
                                <span class="text-sm text-gray-500">{{ $s['suffix'] }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </section>

            {{-- WIDGETS BARIS 1: Recent Tickets + Notifications (Comments) --}}
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Recent Tickets (Ticketing Support) --}}
                <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Recent Tickets</h3>
                        <p class="text-sm text-gray-500">Latest 6 tickets</p>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse($recentTickets as $t)
                            <li class="px-5 py-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 truncate">
                                            #{{ $t['id'] }} — {{ $t['subject'] }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-600">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-100">
                                                <span class="text-gray-500">Pri:</span><span class="font-medium capitalize">{{ $t['priority'] }}</span>
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-100 ml-2">
                                                <span class="text-gray-500">Status:</span><span class="font-medium capitalize">{{ str_replace('_',' ',$t['status']) }}</span>
                                            </span>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $t['user'] }} • {{ $t['dept'] }} • {{ $t['when'] }}
                                        </p>
                                    </div>
                                    <div class="shrink-0">
                                        <a href="{{ $t['url'] }}"
                                           class="px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none">
                                            Open
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-8 text-center text-sm text-gray-500">No tickets found.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Notifications (Comments) --}}
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Notifications</h3>
                            <p class="text-sm text-gray-500">New ticket comments</p>
                        </div>
                        <button wire:click="markAllRead"
                                class="px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none">
                            Mark all read
                        </button>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse($unreadComments as $n)
                            <li class="px-5 py-4">
                                <div class="text-[12px] text-gray-500 mb-1">{{ $n['when'] }}</div>
                                <div class="text-sm">
                                    <span class="font-semibold">{{ $n['by'] }}</span>
                                    commented on
                                    <span class="font-medium">#{{ $n['ticket_id'] }} — {{ $n['ticket_subject'] }}</span>
                                </div>
                                <p class="text-[12px] text-gray-600 mt-1">{{ $n['text'] }}</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <a href="{{ $n['url'] }}"
                                       class="px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none">
                                        Open
                                    </a>
                                    <button wire:click="markReadUpTo({{ $n['id'] }})"
                                            class="px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none">
                                        Mark read
                                    </button>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-8 text-center text-sm text-gray-500">No new comments.</li>
                        @endforelse
                    </ul>
                </div>
            </section>

            {{-- WIDGETS BARIS 2: Booking Room + Recent Users (User Management) --}}
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Recent Bookings --}}
                <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Recent Room Bookings</h3>
                        <p class="text-sm text-gray-500">Latest 5 bookings</p>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse($recentBookings as $b)
                            <li class="px-5 py-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 truncate">
                                            {{ $b['meeting_title'] }} — {{ $b['room_label'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $b['by'] }} • {{ $b['when'] }}
                                        </p>
                                    </div>
                                    <div class="shrink-0">
                                        <a href="{{ $b['url'] }}"
                                           class="px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none">
                                            Open
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-8 text-center text-sm text-gray-500">No recent bookings.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Recent Users --}}
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Recent User Signups</h3>
                        <p class="text-sm text-gray-500">Latest 5 users</p>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse($recentUsers as $u)
                            <li class="px-5 py-4 flex items-center justify-between">
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $u['full_name'] }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $u['company_name'] ?? 'No Company' }}</p>
                                </div>
                                <span class="text-sm text-gray-500">{{ $u['when'] }}</span>
                            </li>
                        @empty
                            <li class="px-5 py-8 text-center text-sm text-gray-500">No new users found.</li>
                        @endforelse
                    </ul>
                </div>
            </section>

        </div>
    </main>
</div>
