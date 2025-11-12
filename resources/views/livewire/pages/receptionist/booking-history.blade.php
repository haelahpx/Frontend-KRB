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
            try { return $v ? Carbon::parse($v)->format('H.i') : '—'; } // 10.00
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

    $card      = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label     = 'block text-sm font-medium text-gray-700 mb-2';
    $input     = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk    = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $chip      = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    $icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
@endphp

<div class="min-h-screen bg-gray-50" wire:poll.1000ms.keep-alive>
    <main class="px-4 sm:px-6 py-6 space-y-6">
        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Booking History</h2>
                            <p class="text-sm text-white/80">
                                Lihat dan kelola riwayat booking yang sudah selesai atau ditolak.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Show deleted toggle --}}
                        <label class="inline-flex items-center gap-2 text-sm text-white/90">
                            <input type="checkbox"
                                   wire:model.live="withTrashed"
                                   class="rounded border-white/30 bg-white/10 focus:ring-white/40">
                            <span>Show deleted records</span>
                        </label>

                        {{-- MOBILE FILTER BUTTON (rooms + recent) --}}
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
            {{-- LEFT: HISTORY LIST CARD --}}
            <section class="{{ $card }} md:col-span-3">
                {{-- Header: title + tabs + active room chip + type scope --}}
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">History</h3>
                            <p class="text-xs text-gray-500">
                                Riwayat booking berdasarkan status.
                            </p>
                        </div>

                        {{-- Tabs: Done / Rejected --}}
                        <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-xs font-medium">
                            <button type="button"
                                    wire:click="setTab('done')"
                                    class="px-3 py-1 rounded-full transition
                                        {{ $activeTab === 'done'
                                            ? 'bg-gray-900 text-white shadow-sm'
                                            : 'text-gray-700 hover:bg-gray-200' }}">
                                Done
                            </button>
                            <button type="button"
                                    wire:click="setTab('rejected')"
                                    class="px-3 py-1 rounded-full transition
                                        {{ $activeTab === 'rejected'
                                            ? 'bg-gray-900 text-white shadow-sm'
                                            : 'text-gray-700 hover:bg-gray-200' }}">
                                Rejected
                            </button>
                        </div>
                    </div>

                    {{-- Room badge + Type scope (All / Offline / Online) --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs mt-1">
                        {{-- Active room filter badge --}}
                        <div class="flex flex-wrap items-center gap-2">
                            @if(!is_null($roomFilterId))
                                @php
                                    $activeRoom = collect($roomsOptions)->firstWhere('id', $roomFilterId);
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-900 text-white border border-gray-800">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span>
                                        Room: {{ $activeRoom['label'] ?? 'Unknown' }}
                                    </span>
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

                        {{-- Type scope: All / Offline / Online --}}
                        <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-[11px] font-medium">
                            <button type="button"
                                    wire:click="setTypeScope('all')"
                                    class="px-3 py-1 rounded-full transition
                                        {{ $typeScope === 'all'
                                            ? 'bg-gray-900 text-white shadow-sm'
                                            : 'text-gray-700 hover:bg-gray-200' }}">
                                All
                            </button>
                            <button type="button"
                                    wire:click="setTypeScope('offline')"
                                    class="px-3 py-1 rounded-full transition
                                        {{ $typeScope === 'offline'
                                            ? 'bg-gray-900 text-white shadow-sm'
                                            : 'text-gray-700 hover:bg-gray-200' }}">
                                Offline
                            </button>
                            <button type="button"
                                    wire:click="setTypeScope('online')"
                                    class="px-3 py-1 rounded-full transition
                                        {{ $typeScope === 'online'
                                            ? 'bg-gray-900 text-white shadow-sm'
                                            : 'text-gray-700 hover:bg-gray-200' }}">
                                Online
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Filters (Search + Date + Sort) --}}
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Search --}}
                        <div>
                            <label class="{{ $label }}">Search</label>
                            <div class="relative">
                                <input type="text"
                                       class="{{ $input }} pl-9"
                                       placeholder="Cari judul…"
                                       wire:model.live="q">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div>
                            <label class="{{ $label }}">Tanggal</label>
                            <div class="relative">
                                <input type="date"
                                       class="{{ $input }} pl-9"
                                       wire:model.live="selectedDate">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Sort --}}
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

                {{-- LIST AREA (Done / Rejected) --}}
                <div class="divide-y divide-gray-200">
                    {{-- DONE TAB --}}
                    @if($activeTab === 'done')
                        @forelse($doneRows as $row)
                            @php
                                $isOnline   = in_array($row->booking_type, ['onlinemeeting','online_meeting']);
                                $isRoomType = in_array($row->booking_type, ['bookingroom','meeting']);
                                $stateKey   = $row->deleted_at ? 'trash' : 'ok';
                                $avatarChar = strtoupper(substr($row->meeting_title ?? '—', 0, 1));
                            @endphp

                            <div wire:key="done-{{ $row->bookingroom_id }}-{{ $stateKey }}"
                                 class="px-4 sm:px-6 py-5 hover:bg-gray-50 transition-colors">
                                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                    {{-- LEFT: Info --}}
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <div class="{{ $icoAvatar }}">{{ $avatarChar }}</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                                <h4 class="font-semibold text-gray-900 text-base truncate">
                                                    {{ $row->meeting_title ?? '—' }}
                                                </h4>
                                                <span class="text-[11px] px-2 py-0.5 rounded-full border {{ $isOnline ? 'border-blue-300 text-blue-700 bg-blue-50' : 'border-gray-300 text-gray-700 bg-gray-50' }}">
                                                    {{ strtoupper($row->booking_type) }}
                                                </span>
                                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-green-100 text-green-800">Done</span>
                                                @if($row->deleted_at)
                                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-rose-100 text-rose-800">Deleted</span>
                                                @endif
                                            </div>

                                            <div class="flex flex-wrap items-center gap-4 text-[13px] text-gray-600">
                                                {{-- Date --}}
                                                <span class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ fmtDate($row->date) }}
                                                </span>

                                                {{-- Time --}}
                                                <span class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ fmtTime($row->start_time) }}–{{ fmtTime($row->end_time) }}
                                                </span>

                                                {{-- Room / Provider --}}
                                                @if($isRoomType)
                                                    <span class="{{ $chip }}">
                                                        <svg class="w-3.5 h-3.5 text-gray-500"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                        <span class="font-medium text-gray-700">
                                                            Room: {{ optional($row->room)->room_name ?? '—' }}
                                                        </span>
                                                    </span>
                                                @else
                                                    <span class="{{ $chip }}">
                                                        <svg class="w-3.5 h-3.5 text-gray-500"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2" />
                                                        </svg>
                                                        <span class="font-medium text-gray-700">
                                                            Provider: {{ ucfirst(str_replace('_', ' ', $row->online_provider ?? '—')) }}
                                                        </span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- RIGHT: actions --}}
                                    <div class="text-right shrink-0 space-y-2">
                                        <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                            <button type="button"
                                                    wire:click="edit({{ $row->bookingroom_id }})"
                                                    wire:loading.attr="disabled"
                                                    class="{{ $btnBlk }}">
                                                Edit
                                            </button>

                                            @if(!$row->deleted_at)
                                                <button type="button"
                                                        wire:click="destroy({{ $row->bookingroom_id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="destroy"
                                                        class="px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition">
                                                    Delete
                                                </button>
                                            @else
                                                <button type="button"
                                                        wire:click="restore({{ $row->bookingroom_id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="restore"
                                                        class="px-3 py-2 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition">
                                                    Restore
                                                </button>
                                            @endif
                                        </div>

                                        <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                                            No. {{ $doneRows->firstItem() + $loop->index }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 sm:px-6 py-14 text-center text-gray-500 text-sm">
                                Tidak ada data.
                            </div>
                        @endforelse
                    @endif

                    {{-- REJECTED TAB --}}
                    @if($activeTab === 'rejected')
                        @forelse($rejectedRows as $row)
                            @php
                                $isOnline   = in_array($row->booking_type, ['onlinemeeting','online_meeting']);
                                $isRoomType = in_array($row->booking_type, ['bookingroom','meeting']);
                                $stateKey   = $row->deleted_at ? 'trash' : 'ok';
                                $avatarChar = strtoupper(substr($row->meeting_title ?? '—', 0, 1));
                            @endphp

                            <div wire:key="rej-{{ $row->bookingroom_id }}-{{ $stateKey }}"
                                 class="px-4 sm:px-6 py-5 hover:bg-gray-50 transition-colors">
                                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                    {{-- LEFT: Info --}}
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <div class="{{ $icoAvatar }}">{{ $avatarChar }}</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                                <h4 class="font-semibold text-gray-900 text-base truncate">
                                                    {{ $row->meeting_title ?? '—' }}
                                                </h4>
                                                <span class="text-[11px] px-2 py-0.5 rounded-full border {{ $isOnline ? 'border-blue-300 text-blue-700 bg-blue-50' : 'border-gray-300 text-gray-700 bg-gray-50' }}">
                                                    {{ strtoupper($row->booking_type) }}
                                                </span>
                                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-red-100 text-red-800">Rejected</span>
                                                @if($row->deleted_at)
                                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-rose-100 text-rose-800">Deleted</span>
                                                @endif
                                            </div>

                                            <div class="flex flex-wrap items-center gap-4 text-[13px] text-gray-600">
                                                {{-- Date --}}
                                                <span class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ fmtDate($row->date) }}
                                                </span>

                                                {{-- Time --}}
                                                <span class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ fmtTime($row->start_time) }}–{{ fmtTime($row->end_time) }}
                                                </span>

                                                {{-- Room / Provider --}}
                                                @if($isRoomType)
                                                    <span class="{{ $chip }}">
                                                        <svg class="w-3.5 h-3.5 text-gray-500"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                        <span class="font-medium text-gray-700">
                                                            Room: {{ optional($row->room)->room_name ?? '—' }}
                                                        </span>
                                                    </span>
                                                @else
                                                    <span class="{{ $chip }}">
                                                        <svg class="w-3.5 h-3.5 text-gray-500"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2" />
                                                        </svg>
                                                        <span class="font-medium text-gray-700">
                                                            Provider: {{ ucfirst(str_replace('_', ' ', $row->online_provider ?? '—')) }}
                                                        </span>
                                                    </span>
                                                @endif
                                            </div>

                                            @if($row->book_reject)
                                                <div class="mt-2 text-xs text-rose-700 bg-rose-50 border border-rose-100 rounded-lg px-2 py-1 inline-block">
                                                    Alasan penolakan: {{ $row->book_reject }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- RIGHT: actions --}}
                                    <div class="text-right shrink-0 space-y-2">
                                        <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                            <button type="button"
                                                    wire:click="edit({{ $row->bookingroom_id }})"
                                                    wire:loading.attr="disabled"
                                                    class="{{ $btnBlk }}">
                                                Edit
                                            </button>

                                            @if(!$row->deleted_at)
                                                <button type="button"
                                                        wire:click="destroy({{ $row->bookingroom_id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="destroy"
                                                        class="px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition">
                                                    Delete
                                                </button>
                                            @else
                                                <button type="button"
                                                        wire:click="restore({{ $row->bookingroom_id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="restore"
                                                        class="px-3 py-2 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition">
                                                    Restore
                                                </button>
                                            @endif
                                        </div>

                                        <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                                            No. {{ $rejectedRows->firstItem() + $loop->index }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 sm:px-6 py-14 text-center text-gray-500 text-sm">
                                Tidak ada data.
                            </div>
                        @endforelse
                    @endif
                </div>

                {{-- PAGINATION --}}
                <div class="px-4 sm:px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        @if($activeTab === 'done')
                            {{ $doneRows->onEachSide(1)->links() }}
                        @else
                            {{ $rejectedRows->onEachSide(1)->links() }}
                        @endif
                    </div>
                </div>
            </section>

            {{-- RIGHT: SIDEBAR (ROOM FILTER + RECENT ACTIVITY) --}}
            <aside class="hidden md:flex md:flex-col md:col-span-1 gap-4">
                <section class="{{ $card }}">
                    <div class="px-4 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Filter by Room</h3>
                        <p class="text-xs text-gray-500 mt-1">Klik salah satu ruangan untuk mem-filter daftar history.</p>
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
                                <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                            @endif
                        </button>

                        {{-- Each room --}}
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

                    {{-- Recent Activity --}}
                    <div class="px-4 pt-3 pb-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-semibold text-gray-900">Recent Completed</h4>
                            <span class="text-[10px] text-gray-500 uppercase tracking-wide">History</span>
                        </div>

                        <div class="space-y-2.5 max-h-40 overflow-y-auto">
                            @forelse($recentCompleted as $row)
                                <div class="flex items-start gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-[10px] font-semibold">
                                        ✓
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-900 truncate">
                                            {{ $row->meeting_title ?? '—' }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 flex flex-wrap items-center gap-2">
                                            <span>{{ fmtDate($row->date) }}</span>
                                            <span>•</span>
                                            <span>{{ fmtTime($row->start_time) }}–{{ fmtTime($row->end_time) }}</span>
                                            @if(optional($row->room)->room_name)
                                                <span>•</span>
                                                <span>{{ optional($row->room)->room_name }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-500">Belum ada aktivitas terbaru.</p>
                            @endforelse
                        </div>
                    </div>
                </section>
            </aside>
        </div>

        {{-- EDIT / CREATE MODAL --}}
        @if($showModal)
            <div class="fixed inset-0 z-50">
                <div class="absolute inset-0 bg-black/50"></div>
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="w-full max-w-2xl bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="font-semibold">
                                {{ $modalMode === 'create' ? 'Create' : 'Edit' }} History Item
                            </h3>
                            <button type="button"
                                    class="text-gray-500 hover:text-gray-700"
                                    wire:click="$set('showModal', false)">×</button>
                        </div>

                        <div class="p-5 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="{{ $label }}">Type</label>
                                    <select class="{{ $input }}" wire:model.live="form.booking_type">
                                        <option value="bookingroom">Booking Room</option>
                                        <option value="meeting">Meeting</option>
                                        <option value="onlinemeeting">Online Meeting</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="{{ $label }}">Status</label>
                                    <select class="{{ $input }}" wire:model.live="form.status">
                                        <option value="completed">Done</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="{{ $label }}">Meeting Title</label>
                                <input type="text" class="{{ $input }}" wire:model.live="form.meeting_title">
                                @error('form.meeting_title')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="{{ $label }}">Date</label>
                                    <input type="date" class="{{ $input }}" wire:model.live="form.date">
                                </div>
                                <div>
                                    <label class="{{ $label }}">Start Time</label>
                                    <input type="time" class="{{ $input }}" wire:model.live="form.start_time">
                                </div>
                                <div>
                                    <label class="{{ $label }}">End Time</label>
                                    <input type="time" class="{{ $input }}" wire:model.live="form.end_time">
                                </div>
                            </div>

                            @if(in_array($form['booking_type'] ?? null, ['bookingroom','meeting']))
                                <div>
                                    <label class="{{ $label }}">Room</label>
                                    <select class="{{ $input }}" wire:model.live="form.room_id">
                                        <option value="">— Select room —</option>
                                        @foreach(($rooms ?? []) as $r)
                                            <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.room_id')
                                        <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @else
                                <div>
                                    <label class="{{ $label }}">Online Provider</label>
                                    <select class="{{ $input }}" wire:model.live="form.online_provider">
                                        <option value="zoom">Zoom</option>
                                        <option value="google_meet">Google Meet</option>
                                    </select>
                                    @error('form.online_provider')
                                        <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            {{-- Rejection reason (only when status = rejected) --}}
                            @if(($form['status'] ?? null) === 'rejected')
                                <div>
                                    <label class="{{ $label }}">Reject Reason <span class="text-rose-600">*</span></label>
                                    <textarea
                                        class="{{ $input }} !h-auto resize-none"
                                        rows="3"
                                        placeholder="Tuliskan alasan penolakan…"
                                        wire:model.live="form.book_reject"
                                    ></textarea>
                                    @error('form.book_reject')
                                        <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div>
                                <label class="{{ $label }}">Notes</label>
                                <textarea class="{{ $input }} !h-auto resize-none"
                                          rows="3"
                                          wire:model.live="form.notes"></textarea>
                            </div>
                        </div>

                        <div class="px-5 py-4 border-t border-gray-200 flex items-center justify-end gap-2">
                            <button type="button"
                                    wire:click="$set('showModal', false)"
                                    wire:loading.attr="disabled"
                                    class="h-10 px-4 rounded-xl bg-gray-200 text-gray-900 text-sm font-medium hover:bg-gray-300 focus:outline-none">
                                Cancel
                            </button>
                            <button type="button"
                                    wire:click="save"
                                    wire:loading.attr="disabled"
                                    class="h-10 px-4 {{ $btnBlk }} text-sm">
                                Save
                            </button>
                        </div>
                    </div>
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

                        {{-- Recent Activity --}}
                        <div>
                            <h4 class="text-xs font-semibold text-gray-800 mb-2">Recent Completed</h4>
                            <div class="space-y-2.5">
                                @forelse($recentCompleted as $row)
                                    <div class="flex items-start gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-[10px] font-semibold">
                                            ✓
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-900 truncate">
                                                {{ $row->meeting_title ?? '—' }}
                                            </p>
                                            <p class="text-[11px] text-gray-500 flex flex-wrap items-center gap-2">
                                                <span>{{ fmtDate($row->date) }}</span>
                                                <span>•</span>
                                                <span>{{ fmtTime($row->start_time) }}–{{ fmtTime($row->end_time) }}</span>
                                                @if(optional($row->room)->room_name)
                                                    <span>•</span>
                                                    <span>{{ optional($row->room)->room_name }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-500">Belum ada aktivitas terbaru.</p>
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
