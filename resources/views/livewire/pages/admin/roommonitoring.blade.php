{{-- resources/views/livewire/pages/admin/roommonitoring-history.blade.php --}}
<div class="bg-gray-50" wire:key="room-monitoring-history">
    @php
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnLt = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition';
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $titleC = 'text-base font-semibold text-gray-900';
    $field = 'text-sm text-gray-600';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">
        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <!-- Soft glow background -->
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-6 w-28 h-28 bg-white/20 rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-6 w-20 h-20 bg-white/10 rounded-full blur-lg"></div>
            </div>

            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <!-- LEFT: Icon + Title + Meta -->
                    <div class="flex items-start gap-4 sm:gap-6">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3M5 11h14M5 19h14M5 11a2 2 0 012-2h10a2 2 0 012 2M5 19a2 2 0 002 2h10a2 2 0 002-2" />
                            </svg>
                        </div>

                        <div class="space-y-1.5">
                            <h2 class="text-xl sm:text-2xl font-semibold leading-tight">
                                History Room Booking
                            </h2>

                            <p class="text-sm text-white/80">
                                Perusahaan: <span class="font-semibold">{{ $company_name }}</span>
                                <span class="mx-2">•</span>
                                Departemen: <span class="font-semibold">{{ $department_name }}</span>
                            </p>

                            @if (!$showSwitcher)
                            <p class="text-xs text-white/60">
                                Riwayat untuk departemen Anda (tidak ada data multi-department).
                            </p>
                            @else
                            <p class="text-xs text-white/60">
                                Riwayat pemesanan ruang untuk departemen terpilih.
                            </p>
                            @endif
                        </div>
                    </div>

                    <!-- RIGHT: Switcher or Search -->
                    @if ($showSwitcher)
                    <div class="w-full lg:w-[32rem] lg:ml-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-white/80 mb-2">
                                    Pilih Departemen
                                </label>
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                    <select
                                        wire:model.live="selected_department_id"
                                        class="w-full h-11 sm:h-12 px-3 sm:px-4 rounded-lg border border-white/20 bg-white/10 text-white text-sm placeholder:text-white/60 focus:border-white focus:ring-2 focus:ring-white/30 focus:outline-none transition">
                                        @foreach ($deptOptions as $opt)
                                        <option class="text-gray-900" value="{{ $opt['id'] }}">
                                            {{ $opt['name'] }}{{ $opt['id'] === $primary_department_id ? ' — Primary' : '' }}
                                        </option>
                                        @endforeach
                                    </select>

                                    <button
                                        type="button"
                                        wire:click="resetToPrimaryDepartment"
                                        class="inline-flex items-center justify-center gap-2 px-3 py-2.5 text-xs font-medium rounded-lg bg-white/10 text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/30 transition">
                                        <x-heroicon-o-star class="w-4 h-4" />
                                        Primary
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Search (no switcher) -->
                    <div class="w-full lg:w-80 lg:ml-auto">
                        <label class="sr-only">Search</label>
                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Cari judul atau catatan…"
                            class="w-full h-11 px-3 sm:px-3.5 bg-white/10 border border-white/20 rounded-lg text-sm placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white transition">
                    </div>
                    @endif
                </div>
            </div>
        </div>


        <div class="space-y-6">
            {{-- DESKTOP SEARCH --}}
            <div class="hidden sm:block">
                <label class="block text-xs font-medium text-white/80 mb-1">Search</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.400ms="search"
                        placeholder="Cari judul atau catatan…"
                        class="{{ $input }} pl-10 placeholder:text-gray-300 bg-white/95">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 sm:hidden">
                <label class="sr-only">Search</label>
                <input type="text" wire:model.live.debounce.400ms="search"
                    placeholder="Cari judul atau catatan…"
                    class="{{ $input }} placeholder:text-gray-300 bg-white/95">
            </div>


            {{-- GRID: OFFLINE (left) & ONLINE (right) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- OFFLINE --}}
                <section class="{{ $card }}">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Offline Meetings</h3>
                        <span class="{{ $chip }}">Total: {{ $offline->count() }}</span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($offline as $b)
                        @php
                        $status = strtolower($b->status);
                        $color = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-green-100 text-green-800',
                        'completed' => 'bg-blue-100 text-blue-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        ][$status] ?? 'bg-gray-100 text-gray-800';
                        @endphp

                        <div class="px-5 py-4 hover:bg-gray-50 transition-colors" wire:key="off-{{ $b->bookingroom_id }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">
                                        {{ $b->meeting_title }}
                                    </div>
                                    <div class="{{ $field }} mt-0.5">
                                        <span class="{{ $mono }}">#{{ $b->bookingroom_id }}</span>
                                        <span class="mx-2">•</span>
                                        {{ \Illuminate\Support\Carbon::parse($b->start_time)->format('d M Y, H:i') }}
                                        – {{ \Illuminate\Support\Carbon::parse($b->end_time)->format('H:i') }}
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $color }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 mt-3">
                                <div>
                                    <div class="text-gray-500">Room</div>
                                    <div class="font-medium">{{ $b->room->room_name ?? '—' }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Attendees</div>
                                    <div class="font-medium">{{ $b->number_of_attendees }}</div>
                                </div>
                                <div class="col-span-2">
                                    <div class="text-gray-500">Notes</div>
                                    <div class="line-clamp-2">{{ $b->special_notes ?: '—' }}</div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="px-5 py-14 text-center text-gray-500 text-sm">Tidak ada riwayat offline.</div>
                        @endforelse
                    </div>

                    <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                        <button class="{{ $btnLt }}" wire:click="loadMore('offline')" @disabled($offline->isEmpty())>
                            Load more
                        </button>
                    </div>
                </section>

                {{-- ONLINE --}}
                <section class="{{ $card }}">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Online Meetings</h3>
                        <span class="{{ $chip }}">Total: {{ $online->count() }}</span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($online as $b)
                        @php
                        $status = strtolower($b->status);
                        $color = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-green-100 text-green-800',
                        'completed' => 'bg-blue-100 text-blue-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        ][$status] ?? 'bg-gray-100 text-gray-800';
                        @endphp

                        <div class="px-5 py-4 hover:bg-gray-50 transition-colors" wire:key="on-{{ $b->bookingroom_id }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">
                                        {{ $b->meeting_title }}
                                    </div>
                                    <div class="{{ $field }} mt-0.5">
                                        <span class="{{ $mono }}">#{{ $b->bookingroom_id }}</span>
                                        <span class="mx-2">•</span>
                                        {{ \Illuminate\Support\Carbon::parse($b->start_time)->format('d M Y, H:i') }}
                                        – {{ \Illuminate\Support\Carbon::parse($b->end_time)->format('H:i') }}
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $color }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 mt-3">
                                <div>
                                    <div class="text-gray-500">Provider</div>
                                    <div class="font-medium capitalize">{{ str_replace('_',' ', $b->online_provider ?? '—') }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Attendees</div>
                                    <div class="font-medium">{{ $b->number_of_attendees }}</div>
                                </div>
                                <div class="col-span-2">
                                    <div class="text-gray-500">Meeting URL</div>
                                    @if ($b->online_meeting_url)
                                    <a href="{{ $b->online_meeting_url }}" target="_blank" class="text-blue-600 hover:underline break-all">
                                        {{ $b->online_meeting_url }}
                                    </a>
                                    @else
                                    <div class="text-gray-700">—</div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-gray-500">Meeting Code</div>
                                    <div class="font-medium">{{ $b->online_meeting_code ?: '—' }}</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Password</div>
                                    <div class="font-medium">{{ $b->online_meeting_password ?: '—' }}</div>
                                </div>
                                <div class="col-span-2">
                                    <div class="text-gray-500">Notes</div>
                                    <div class="line-clamp-2">{{ $b->special_notes ?: '—' }}</div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="px-5 py-14 text-center text-gray-500 text-sm">Tidak ada riwayat online.</div>
                        @endforelse
                    </div>

                    <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                        <button class="{{ $btnLt }}" wire:click="loadMore('online')" @disabled($online->isEmpty())>
                            Load more
                        </button>
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>