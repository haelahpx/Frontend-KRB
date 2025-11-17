<div class="min-h-screen bg-gray-50" wire:poll.1000ms.keep-alive>
    @php
    use Carbon\Carbon;

    if (!function_exists('fmtDate')) {
    function fmtDate($v) {
    try { return $v ? Carbon::parse($v)->format('d M Y') : '—'; }
    catch (\Throwable) { return '—'; }
    }
    }
    if (!function_exists('fmtTime')) {
    function fmtTime($v) {
    try { return $v ? Carbon::parse($v)->format('H.i') : '—'; }
    catch (\Throwable) {
    if (is_string($v)) {
    if (preg_match('/^\d{2}:\d{2}/', $v)) return str_replace(':','.', substr($v,0,5));
    if (preg_match('/^\d{2}\.\d{2}/', $v)) return substr($v,0,5);
    }
    return '—';
    }
    }
    }

    /** @var int|null $roomFilterId */
    $roomFilterId = $roomFilterId ?? null;

    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnGhost = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300/20 disabled:opacity-60 transition';
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    $icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    @endphp

    <style>
        :root {
            color-scheme: light;
        }

        select,
        option {
            color: #111827 !important;
            background: #ffffff !important;
            -webkit-text-fill-color: #111827 !important;
        }

        option:checked {
            background: #e5e7eb !important;
            color: #111827 !important;
        }
    </style>

    <main class="px-4 sm:px-6 py-6 space-y-6">
        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="space-y-1">
                        <h2 class="text-lg sm:text-xl font-semibold">Bookings Approval (Receptionist)</h2>
                        <p class="text-sm text-white/80">
                            Kelola permintaan booking ruangan (online/offline): approve, reject (wajib isi alasan), atau reschedule.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- MOBILE FILTER BUTTON --}}
                        <button type="button"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 text-xs font-medium border border-white/30 hover:bg-white/20 md:hidden"
                            wire:click="openFilterModal">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h18M4 9h16M6 14h12M9 19h6" />
                            </svg>
                            <span>Filter</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN LAYOUT --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- LEFT: APPROVAL LIST --}}
            <section class="{{ $card }} md:col-span-3">
                {{-- Header + tabs + room scope --}}
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Approval Queue</h3>
                            <p class="text-xs text-gray-500">
                                Kelola booking yang menunggu persetujuan atau sedang berlangsung.
                            </p>
                        </div>

                        <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-xs font-medium">
                            <button type="button"
                                wire:click="setTab('pending')"
                                class="px-3 py-1 rounded-full {{ $activeTab === 'pending' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-200' }}">
                                Pending
                            </button>
                            <button type="button"
                                wire:click="setTab('ongoing')"
                                class="px-3 py-1 rounded-full {{ $activeTab === 'ongoing' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-200' }}">
                                Ongoing
                            </button>
                        </div>
                    </div>

                    {{-- Room badge + Type scope --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs mt-1">
                        <div class="flex flex-wrap items-center gap-2">
                            @if(!is_null($roomFilterId))
                            @php $activeRoom = collect($roomsOptions)->firstWhere('id', $roomFilterId); @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-900 text-white border border-gray-800">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>Room: {{ $activeRoom['label'] ?? 'Unknown' }}</span>
                                <button type="button" class="ml-1 hover:text-gray-200" wire:click="clearRoomFilter">×</button>
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 border border-dashed border-gray-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4h18M4 9h16M6 14h12M9 19h6" />
                                </svg>
                                <span>No room filter</span>
                            </span>
                            @endif
                        </div>

                        <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-[11px] font-medium">
                            <button type="button" wire:click="setTypeScope('all')"
                                class="px-3 py-1 rounded-full {{ $typeScope === 'all' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-200' }}">
                                All
                            </button>
                            <button type="button" wire:click="setTypeScope('offline')"
                                class="px-3 py-1 rounded-full {{ $typeScope === 'offline' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-200' }}">
                                Offline
                            </button>
                            <button type="button" wire:click="setTypeScope('online')
                                " class="px-3 py-1 rounded-full {{ $typeScope === 'online' ? 'bg-gray-900 text-white' : 'text-gray-700' }}">
                                Online
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Filter bar: Search + Date + Sort --}}
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="{{ $label }}">Search</label>
                            <div class="relative">
                                <input type="text" class="{{ $input }} pl-9"
                                    placeholder="Cari judul meeting…"
                                    wire:model.debounce.500ms="q">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                                </svg>
                            </div>
                        </div>

                        <div>
                            <label class="{{ $label }}">Tanggal</label>
                            <div class="relative">
                                <input type="date" class="{{ $input }} pl-9" wire:model.live="selectedDate">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>

                        <div>
                            <label class="{{ $label }}">Urutkan</label>
                            <select wire:model.live="dateMode" class="{{ $input }}">
                                <option value="semua">Default (terbaru)</option>
                                <option value="terbaru">Tanggal terbaru</option>
                                <option value="terlama">Tanggal terlama</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- LIST AREA (Pending / Ongoing) --}}
                <div class="divide-y divide-gray-200">
                    @php $list = $activeTab === 'pending' ? $pending : $ongoing; @endphp

                    {{-- PENDING TAB --}}
                    @if($activeTab === 'pending')
                    @forelse($list as $b)
                    @php
                    $isOnline = in_array($b->booking_type, ['online_meeting','onlinemeeting']);
                    $isRoomType = in_array($b->booking_type, ['bookingroom','meeting']);
                    $avatarChar = strtoupper(substr($b->meeting_title ?? '—', 0, 1));
                    @endphp

                    <div wire:key="pending-{{ $b->bookingroom_id }}" class="px-4 sm:px-6 py-5 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            {{-- LEFT: info --}}
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="{{ $icoAvatar }}">{{ $b->meeting_title ? $avatarChar : '?' }}</div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                        <h4 class="font-semibold text-gray-900 text-base truncate">
                                            {{ $b->meeting_title ?? 'Untitled meeting' }}
                                        </h4>
                                        <span class="text-[11px] px-2 py-0.5 rounded-full border {{ $isOnline ? 'border-emerald-300 text-emerald-700 bg-emerald-50' : 'border-blue-300 text-blue-700 bg-blue-50' }}">
                                            {{ $isOnline ? 'Online Meeting' : 'Offline Room' }}
                                        </span>
                                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                                            {{ strtoupper($b->status) }}
                                        </span>
                                    </div>

                                    <div class="flex flex-col gap-2 text-[13px] text-gray-600">
                                        <div class="flex flex-wrap items-center gap-4">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ fmtDate($b->date) }}
                                            </span>
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ fmtTime($b->start_time) }}–{{ fmtTime($b->end_time) }}
                                            </span>

                                            @if($isRoomType)
                                            <span class="{{ $chip }}">
                                                <svg class="w-3.5 h-3.5 text-gray-500"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span class="font-medium {{ $b->room?->room_name ? 'text-gray-700' : 'text-rose-600' }}">
                                                    Room: {{ $b->room?->room_name ?? 'Belum dipilih' }}
                                                </span>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($b->book_reject)
                                    <div class="mt-2 text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-2 py-1 inline-block">
                                        Catatan: {{ $b->book_reject }}
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- RIGHT: actions --}}
                            <div class="text-right shrink-0 space-y-2">
                                <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                    <button type="button"
                                        wire:click="approve({{ $b->bookingroom_id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="approve"
                                        class="{{ $btnBlk }}">
                                        <svg class="w-3.5 h-3.5 inline-block mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Approve
                                    </button>

                                    <button type="button"
                                        wire:click="openReject({{ $b->bookingroom_id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="openReject"
                                        class="{{ $btnGhost }}">
                                        <svg class="w-3.5 h-3.5 inline-block mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Reject
                                    </button>
                                </div>

                                <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                                    {{ optional($b->created_at)->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-4 sm:px-6 py-14 text-center text-gray-500 text-sm">
                        Tidak ada booking pending dengan filter saat ini.
                    </div>
                    @endforelse
                    @endif

                    {{-- ONGOING TAB --}}
                    @if($activeTab === 'ongoing')
                    @forelse($list as $b)
                    @php
                    $isOnline = in_array($b->booking_type, ['online_meeting','onlinemeeting']);
                    $isRoomType = in_array($b->booking_type, ['bookingroom','meeting']);
                    $avatarChar = strtoupper(substr($b->meeting_title ?? '—', 0, 1));
                    @endphp

                    <div wire:key="ongoing-{{ $b->bookingroom_id }}" class="px-4 sm:px-6 py-5 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="{{ $icoAvatar }}">{{ $b->meeting_title ? $avatarChar : '?' }}</div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                        <h4 class="font-semibold text-gray-900 text-base truncate">
                                            {{ $b->meeting_title ?? 'Untitled meeting' }}
                                        </h4>
                                        <span class="text-[11px] px-2 py-0.5 rounded-full border {{ $isOnline ? 'border-emerald-300 text-emerald-700 bg-emerald-50' : 'border-blue-300 text-blue-700 bg-blue-50' }}">
                                            {{ $isOnline ? 'Online Meeting' : 'Offline Room' }}
                                        </span>
                                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-green-100 text-green-800">
                                            {{ strtoupper($b->status) }}
                                        </span>
                                    </div>

                                    <div class="flex flex-col gap-2 text-[13px] text-gray-600">
                                        <div class="flex flex-wrap items-center gap-4">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ fmtDate($b->date) }}
                                            </span>
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ fmtTime($b->start_time) }}–{{ fmtTime($b->end_time) }}
                                            </span>

                                            @if($isRoomType)
                                            <span class="{{ $chip }}">
                                                <svg class="w-3.5 h-3.5 text-gray-500"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span class="font-medium text-gray-700">
                                                    Room: {{ $b->room?->room_name ?? '—' }}
                                                </span>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($b->book_reject)
                                    <div class="mt-2 text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-2 py-1 inline-block">
                                        Catatan: {{ $b->book_reject }}
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- RIGHT: actions (Only Cancel / Reschedule for ongoing) --}}
                            <div class="text-right shrink-0 space-y-2">
                                <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                    <button type="button"
                                        x-data
                                        @click="
                                                    if (confirm('Are you sure want to cancel this request?')) {
                                                        $wire.openReschedule({{ $b->bookingroom_id }});
                                                    }
                                                "
                                        wire:loading.attr="disabled"
                                        wire:target="openReschedule"
                                        class="px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition">
                                        <svg class="w-3.5 h-3.5 inline-block mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Cancel
                                    </button>
                                </div>

                                <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                                    {{ optional($b->created_at)->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-4 sm:px-6 py-14 text-center text-gray-500 text-sm">
                        Tidak ada booking ongoing dengan filter saat ini.
                    </div>
                    @endforelse
                    @endif
                </div>

                {{-- PAGINATION --}}
                <div class="px-4 sm:px-6 py-5 bg-gray-50 border-top border-gray-200">
                    <div class="flex justify-center">
                        @if($activeTab === 'pending')
                        {{ $pending->onEachSide(1)->links() }}
                        @else
                        {{ $ongoing->onEachSide(1)->links() }}
                        @endif
                    </div>
                </div>
            </section>

            {{-- RIGHT: SIDEBAR (Rooms + Recent) --}}
            <aside class="hidden md:flex md:flex-col md:col-span-1 gap-4">
                <section class="{{ $card }}">
                    <div class="px-4 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Filter by Room</h3>
                        <p class="text-xs text-gray-500 mt-1">Klik salah satu ruangan untuk mem-filter daftar approval.</p>
                    </div>

                    <div class="px-4 py-3 max-h-64 overflow-y-auto">
                        {{-- All rooms --}}
                        <button type="button"
                            wire:click="clearRoomFilter"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium
                                {{ is_null($roomFilterId) ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                            <span class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md border border-gray-300 text-[11px]">
                                    All
                                </span>
                                <span>All Rooms</span>
                            </span>
                            @if(is_null($roomFilterId))
                            <span class="text[10px] uppercase tracking-wide opacity-80">Active</span>
                            @endif
                        </button>

                        <div class="mt-2 space-y-1.5">
                            @forelse($roomsOptions as $r)
                            @php $active = !is_null($roomFilterId) && (int)$roomFilterId === (int)$r['id']; @endphp
                            <button type="button"
                                wire:click="selectRoom({{ $r['id'] }})"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs
                                        {{ $active ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                <span class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md border border-gray-300 text-[11px]">
                                        {{ substr($r['label'],0,2) }}
                                    </span>
                                    <span class="truncate">{{ $r['label'] }}</span>
                                </span>
                                @if($active)
                                <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                                @endif
                            </button>
                            @empty
                            <p class="text-xs text-gray-500">Tidak ada data ruangan.</p>
                            @endforelse
                        </div>
                    </div>


                </section>
            </aside>
        </div>

        {{-- REJECT MODAL (Alasan wajib) --}}
        @if($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" wire:click="closeReject"></div>

            <div class="relative bg-white rounded-2xl shadow-xl w-full max-width-[36rem] mx-4">
                <form wire:submit.prevent="confirmReject">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Tolak Booking</h3>
                            <p class="text-xs text-gray-500">Berikan alasan penolakan. Field ini wajib diisi.</p>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-700" wire:click="closeReject">✕</button>
                    </div>

                    <div class="px-5 py-4 space-y-4">
                        <div>
                            <label class="{{ $label }}">Alasan penolakan <span class="text-rose-600">*</span></label>
                            <textarea wire:model.live="rejectReason"
                                rows="4"
                                class="{{ $input }} resize-none"
                                placeholder="Contoh: Jadwal bentrok dengan rapat lain / Ruangan tidak tersedia"
                                required></textarea>
                            @error('rejectReason')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-gray-200 flex items-center justify-end gap-2 bg-gray-50">
                        <button type="button" class="{{ $btnGhost }}" wire:click="closeReject" wire:loading.attr="disabled" wire:target="confirmReject">
                            Batal
                        </button>
                        <button type="submit" class="{{ $btnBlk }}" wire:loading.attr="disabled" wire:target="confirmReject">
                            Konfirmasi Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- RESCHEDULE MODAL --}}
        @if($showRescheduleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" wire:click="closeReschedule"></div>

            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4">
                <form wire:submit.prevent="submitReschedule">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Reschedule Booking</h3>
                            <p class="text-xs text-gray-500">
                                Atur ulang tanggal, waktu, dan ruangan. Alasan reschedule wajib diisi.
                            </p>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-600" wire:click="closeReschedule">✕</button>
                    </div>

                    <div class="px-5 py-4 space-y-4">
                        <div>
                            <label class="{{ $label }}">Tanggal baru</label>
                            <input type="date" class="{{ $input }}" wire:model.live="rescheduleDate" required>
                            @error('rescheduleDate') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="{{ $label }}">Jam mulai</label>
                                <input type="time" class="{{ $input }}" wire:model.live="rescheduleStart" required>
                                @error('rescheduleStart') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">Jam selesai</label>
                                <input type="time" class="{{ $input }}" wire:model.live="rescheduleEnd" required>
                                @error('rescheduleEnd') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="{{ $label }}">Ruangan (opsional)</label>
                            <select class="{{ $input }}" wire:model.live="rescheduleRoomId">
                                <option value="">Pilih ruangan…</option>
                                @foreach($roomsOptions as $r)
                                <option value="{{ $r['id'] }}">{{ $r['label'] }}</option>
                                @endforeach
                            </select>
                            @error('rescheduleRoomId') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Alasan reschedule <span class="text-rose-600">*</span></label>
                            <textarea rows="3" class="{{ $input }} resize-none" wire:model.live="rescheduleReason" required></textarea>
                            @error('rescheduleReason') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="px-5 py-3 border-t border-gray-200 flex items-center justify-end gap-2 bg-gray-50">
                        <button type="button" class="{{ $btnGhost }}" wire:click="closeReschedule" wire:loading.attr="disabled" wire:target="submitReschedule">
                            Batal
                        </button>
                        <button type="submit" class="{{ $btnBlk }}" wire:loading.attr="disabled" wire:target="submitReschedule">
                            Simpan Reschedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- MOBILE FILTER MODAL (rooms + recent) --}}
        @if($showFilterModal)
        <div class="fixed inset-0 z-40 md:hidden">
            <div class="absolute inset-0 bg-black/40" wire:click="closeFilterModal"></div>
            <div class="absolute inset-x-0 bottom-0 bg-white rounded-t-2xl shadow-xl max-h-[80vh] overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Filter & Recent</h3>
                        <p class="text-[11px] text-gray-500">Filter berdasarkan ruangan & lihat aktivitas terbaru.</p>
                    </div>
                </div>

                <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
                    {{-- Rooms --}}
                    <div>
                        <h4 class="text-xs font-semibold text-gray-800 mb-2">Filter by Room</h4>

                        <button type="button"
                            wire:click="clearRoomFilter"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium
                                        {{ is_null($roomFilterId) ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                            <span class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md border border-gray-300 text-[11px]">
                                    All
                                </span>
                                <span>All Rooms</span>
                            </span>
                            @if(is_null($roomFilterId))
                            <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                            @endif
                        </button>

                        <div class="mt-2 space-y-1.5">
                            @forelse($roomsOptions as $r)
                            @php
                            $active = !is_null($roomFilterId) && (int) $roomFilterId === (int) $r['id'];
                            @endphp
                            <button type="button"
                                wire:click="selectRoom({{ $r['id'] }})"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs
                                                {{ $active ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                <span class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md border border-gray-300 text-[11px]">
                                        {{ substr($r['label'], 0, 2) }}
                                    </span>
                                    <span class="truncate">{{ $r['label'] }}</span>
                                </span>
                                @if($active)
                                <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                                @endif
                            </button>
                            @empty
                            <p class="text-xs text-gray-500">Tidak ada data ruangan.</p>
                            @endforelse
                        </div>
                    </div>

                </div>

                <div class="px-4 py-3 border-t border-gray-200">
                    <button type="button"
                        class="w-full h-10 rounded-xl bg-gray-900 text-white text-xs font-medium"
                        wire:click="closeFilterModal">
                        Apply & Close
                    </button>
                </div>
            </div>
        </div>
        @endif
    </main>
</div>