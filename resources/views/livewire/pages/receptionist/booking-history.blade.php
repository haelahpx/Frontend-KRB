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

// Theme tokens adopted to match the requested style template
$card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
$label = 'block text-sm font-medium text-gray-700 mb-2';
$input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
$chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
$icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';

// Refined Button tokens to match the requested style template
$btnEdit = 'px-2.5 py-1.5 text-xs font-medium rounded-lg bg-black text-white focus:outline-none focus:ring-2 focus:ring-gray-500/20 transition';
$btnDelete = 'px-2.5 py-1.5 text-xs font-medium rounded-lg bg-rose-700 text-white hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-700/20 transition';
$btnRestore = 'px-2.5 py-1.5 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition';
$btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition'; // Kept original for modal save button/fallback.
@endphp

<div class="min-h-screen bg-gray-50" wire:poll.1000ms.keep-alive>
    <main class="px-4 sm:px-6 py-6 space-y-6">
        {{-- HERO (Styled to match the requested template) --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <x-heroicon-o-clock class="w-6 h-6 text-white"/>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Booking History — Monitor</h2>
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

                        {{-- MOBILE FILTER BUTTON (Styled to match the requested template) --}}
                        <button type="button"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 text-xs font-medium border border-white/30 hover:bg-white/20 md:hidden"
                            wire:click="openFilterModal">
                            <x-heroicon-o-funnel class="w-4 h-4"/>
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
                {{-- Header: title + tabs + room badge + type scope --}}
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">History</h3>
                            <p class="text-xs text-gray-500">
                                Riwayat booking berdasarkan status.
                            </p>
                        </div>

                        {{-- Tabs (Styled to match the requested template's segmented buttons) --}}
                        <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-[11px] font-medium">
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

                    {{-- Room badge + type scope (Styled to match the requested template's segmented buttons) --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs mt-1">
                        <div class="flex flex-wrap items-center gap-2">
                            @if(!is_null($roomFilterId))
                                @php $activeRoom = collect($roomsOptions)->firstWhere('id', $roomFilterId); @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-900 text-white border border-gray-800">
                                    <x-heroicon-o-building-office class="w-3.5 h-3.5"/>
                                    <span>Room: {{ $activeRoom['label'] ?? 'Unknown' }}</span>
                                    <button type="button" class="ml-1 hover:text-gray-200" wire:click="clearRoomFilter">×</button>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 border border-dashed border-gray-300">
                                    <x-heroicon-o-funnel class="w-3.5 h-3.5"/>
                                    <span>No room filter</span>
                                </span>
                            @endif
                        </div>

                        <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-[11px] font-medium">
                            <button type="button"
                                wire:click="setTypeScope('all')"
                                class="px-3 py-1 rounded-full transition
                                    {{ $typeScope === 'all' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200' }}">
                                All
                            </button>
                            <button type="button"
                                wire:click="setTypeScope('offline')"
                                class="px-3 py-1 rounded-full transition
                                    {{ $typeScope === 'offline' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200' }}">
                                Offline
                            </button>
                            <button type="button"
                                wire:click="setTypeScope('online')"
                                class="px-3 py-1 rounded-full transition
                                    {{ $typeScope === 'online' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200' }}">
                                Online
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="{{ $label }}">Search</label>
                            <div class="relative">
                                <input type="text"
                                    class="{{ $input }} pl-9"
                                    placeholder="Cari judul…"
                                    wire:model.live="q">
                                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"/>
                            </div>
                        </div>

                        <div>
                            <label class="{{ $label }}">Tanggal</label>
                            <div class="relative">
                                <input type="date"
                                    class="{{ $input }} pl-9"
                                    wire:model.live="selectedDate">
                                <x-heroicon-o-calendar-days class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"/>
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

                {{-- LIST AREA (grid cards, with requested list item style) --}}
                <div class="p-4 sm:p-6 bg-gray-50/50">
                    @php
                        $rows = $activeTab === 'done' ? $doneRows : $rejectedRows;
                        $listName = $activeTab === 'done' ? 'done' : 'rejected';
                    @endphp

                    @if($rows->isEmpty())
                        <div class="lg:col-span-2 py-14 text-center text-gray-500 text-sm">
                            Tidak ada data.
                        </div>
                    @else
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @foreach($rows as $row)
                                @php
                                    $isDone = $activeTab === 'done';
                                    $isRejected = $activeTab === 'rejected';
                                    $isOnline = in_array($row->booking_type, ['onlinemeeting','online_meeting']);
                                    $isRoomType = in_array($row->booking_type, ['bookingroom','meeting']);
                                    $isTrashed = $row->deleted_at !== null;
                                    $avatarChar = strtoupper(substr($row->meeting_title ?? '—', 0, 1));
                                    $statusLabel = $isDone ? 'Done' : 'Rejected';
                                    $statusBg = $isDone ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800';
                                    
                                    $typeBg = $isOnline ? 'border-emerald-300 text-emerald-700 bg-emerald-50' : 'border-blue-300 text-blue-700 bg-blue-50';

                                    $platform = $row->online_meeting_platform ?? $row->platform ?? $row->meeting_platform ?? $row->online_provider ?? ($isOnline ? 'Online Meeting' : null);

                                    $meetingUrl = $row->online_meeting_url ?? null;
                                    $meetingCode = $row->online_meeting_code ?? null;
                                    $meetingPassword = $row->online_meeting_password ?? null;

                                    $requesterName = $row->user?->name ?? $row->requester_name ?? null;
                                    $requesterDept = $row->user?->department?->department_name ?? $row->user?->department?->dept_name ?? $row->department_name ?? null;
                                @endphp

                                <div wire:key="{{ $listName }}-{{ $row->bookingroom_id }}-{{ $isTrashed ? 'trash' : 'ok' }}"
                                    class="flex flex-col justify-between p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200">
                                    <div class="flex items-start gap-3 mb-4">
                                        <div class="{{ $icoAvatar }}">{{ $avatarChar }}</div>
                                        <div class="min-w-0 flex-1">
                                            {{-- Title and Tags --}}
                                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                                <h4 class="font-semibold text-gray-900 text-base truncate max-w-full">
                                                    {{ $row->meeting_title ?? '—' }}
                                                </h4>
                                                
                                                <span class="text-[10px] px-1.5 py-0.5 rounded-full border {{ $typeBg }}">
                                                    {{ $isOnline ? 'Online Meeting' : 'Offline Room' }}
                                                </span>
                                                <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $statusBg }}">
                                                    {{ $statusLabel }}
                                                </span>
                                                @if($isTrashed)
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-700 border border-gray-300">Deleted</span>
                                                @endif
                                            </div>

                                            {{-- Details --}}
                                            <div class="space-y-1 text-[13px] text-gray-600">
                                                <div class="flex flex-wrap items-center gap-4">
                                                    <span class="flex items-center gap-1.5">
                                                        <x-heroicon-o-calendar-days class="w-3.5 h-3.5 text-gray-400"/>
                                                        {{ fmtDate($row->date) }}
                                                    </span>
                                                    <span class="flex items-center gap-1.5">
                                                        <x-heroicon-o-clock class="w-3.5 h-3.5 text-gray-400"/>
                                                        {{ fmtTime($row->start_time) }}–{{ fmtTime($row->end_time) }}
                                                    </span>
                                                    @if($isRoomType)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 border border-gray-200 text-xs">
                                                            <x-heroicon-o-building-office class="w-3.5 h-3.5 text-gray-500"/>
                                                            <span class="font-medium">Room: {{ optional($row->room)->room_name ?? '—' }}</span>
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- ONLINE extras (Simplified into one block) --}}
                                                @if($isOnline && ($platform || $meetingUrl || $meetingCode || $meetingPassword))
                                                    <div class="pt-1 text-[12px] text-gray-600 space-y-1">
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            @if($platform)
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-900 text-white text-[11px] font-medium">
                                                                    <x-heroicon-o-signal class="w-3.5 h-3.5"/>
                                                                    {{ $platform }}
                                                                </span>
                                                            @endif
                                                            @if($meetingUrl)
                                                                <a href="{{ $meetingUrl }}" target="_blank"
                                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[11px] hover:bg-gray-200 border border-gray-300">
                                                                    <x-heroicon-o-link class="w-3.5 h-3.5"/>
                                                                    Join Link
                                                                </a>
                                                            @endif
                                                            @if($meetingCode)
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 border border-gray-200 text-[11px]">
                                                                    Code: <span class="font-mono">{{ $meetingCode }}</span>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- REQUESTER --}}
                                                @if($requesterName || $requesterDept)
                                                    <div class="pt-1 text-[12px] text-gray-600 flex flex-wrap items-center gap-2">
                                                        @if($requesterName)
                                                            <span>
                                                                Req. by <span class="font-medium text-gray-800">{{ $requesterName }}</span>
                                                            </span>
                                                        @endif
                                                        @if($requesterDept)
                                                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                                                                {{ $requesterDept }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- Reject Reason (Only visible if rejected) --}}
                                                @if($isRejected && $row->book_reject)
                                                    <div class="mt-2 text-xs text-rose-700 bg-rose-50 border border-rose-100 rounded-lg px-2 py-1">
                                                        Alasan penolakan: {{ $row->book_reject }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Actions and ID --}}
                                    <div class="pt-3 mt-auto border-t border-gray-100 flex items-center justify-between">
                                        <span class="text-[10px] text-gray-400 font-medium">
                                            No. {{ $rows->firstItem() + $loop->index }}
                                        </span>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <button type="button"
                                                wire:click="edit({{ $row->bookingroom_id }})"
                                                wire:loading.attr="disabled"
                                                class="{{ $btnEdit }}">
                                                Edit
                                            </button>

                                            @if(!$isTrashed)
                                                <button type="button"
                                                    wire:click="destroy({{ $row->bookingroom_id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="destroy"
                                                    class="{{ $btnDelete }}"
                                                    wire:confirm="Hapus entri ini?">
                                                    Delete
                                                </button>
                                            @else
                                                <button type="button"
                                                    wire:click="restore({{ $row->bookingroom_id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="restore"
                                                    class="{{ $btnRestore }}">
                                                    Restore
                                                </button>
                                                <button type="button"
                                                    wire:click="destroyForever({{ $row->bookingroom_id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="destroyForever"
                                                    class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-rose-900 text-white hover:bg-rose-900/90 focus:outline-none focus:ring-2 focus:ring-rose-900/20 transition"
                                                    wire:confirm="Hapus permanen entri ini? Tindakan tidak bisa dibatalkan!">
                                                    Perm. Delete
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- PAGINATION (Background changed to bg-white for consistency) --}}
                <div class="px-4 sm:px-6 py-5 bg-white border-t border-gray-200 rounded-b-2xl">
                    <div class="flex justify-center">
                        @if($activeTab === 'done')
                            {{ $doneRows->onEachSide(1)->links() }}
                        @else
                            {{ $rejectedRows->onEachSide(1)->links() }}
                        @endif
                    </div>
                </div>
            </section>

            {{-- RIGHT: SIDEBAR (ROOM FILTER) --}}
            <aside class="hidden md:flex md:flex-col md:col-span-1 gap-4">
                <section class="{{ $card }}">
                    <div class="px-4 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Filter by Room</h3>
                        <p class="text-xs text-gray-500 mt-1">Klik salah satu ruangan untuk mem-filter daftar history.</p>
                    </div>

                    <div class="px-4 py-3 max-h-64 overflow-y-auto">
                        {{-- All rooms (Styled to match the requested template's side menu buttons) --}}
                        <button type="button"
                                wire:click="clearRoomFilter"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium
                                    {{ is_null($roomFilterId) ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                            <span class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md border border-gray-300 text-[11px] {{ is_null($roomFilterId) ? 'bg-white/10 border-white/20' : '' }}">
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
                                @php $active = !is_null($roomFilterId) && (int) $roomFilterId === (int) $r['id']; @endphp
                                <button type="button"
                                        wire:click="selectRoom({{ $r['id'] }})"
                                        class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs
                                            {{ $active ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                    <span class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-md border border-gray-300 text-[11px] {{ $active ? 'bg-white/10 border-white/20' : '' }}">
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
                </section>
            </aside>
        </div>

        {{-- EDIT / CREATE MODAL (Styled to match the requested template's modal) --}}
        @if($showModal)
            <div class="fixed inset-0 z-50">
                <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="w-full max-w-2xl bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="font-semibold text-black">
                                {{ $modalMode === 'create' ? 'Create' : 'Edit' }} History Item
                            </h3>
                            <button type="button"
                                class="text-gray-500 hover:text-gray-700"
                                wire:click="$set('showModal', false)">
                                <x-heroicon-o-x-mark class="w-5 h-5"/>
                            </button>
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

                            @if(($form['status'] ?? null) === 'rejected')
                                <div>
                                    <label class="{{ $label }}">Reject Reason <span class="text-rose-600">*</span></label>
                                    <textarea
                                        class="{{ $input }} !h-auto resize-none"
                                        rows="3"
                                        placeholder="Tuliskan alasan penolakan…"
                                        wire:model.live="form.book_reject"></textarea>
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
                                class="h-10 px-4 rounded-xl bg-gray-900 text-white text-sm font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition shadow-sm">
                                <span wire:loading.remove wire:target="save">Save</span>
                                <span wire:loading wire:target="save" class="flex items-center gap-2">
                                    <x-heroicon-o-arrow-path class="animate-spin -ml-1 mr-1 h-4 w-4 text-white"/>
                                    Saving…
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- MOBILE FILTER MODAL (Styled to match the requested template's modal) --}}
        @if($showFilterModal)
            <div class="fixed inset-0 z-40 md:hidden">
                <div class="absolute inset-0 bg-black/40" wire:click="closeFilterModal"></div>
                <div class="absolute inset-x-0 bottom-0 bg-white rounded-t-2xl shadow-xl max-h-[80vh] overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Filter & Recent</h3>
                            <p class="text-[11px] text-gray-500">Filter berdasarkan ruangan & lihat aktivitas terbaru.</p>
                        </div>
                         <button type="button" class="text-gray-500 hover:text-gray-700" wire:click="closeFilterModal">
                            <x-heroicon-o-x-mark class="w-5 h-5"/>
                        </button>
                    </div>

                    <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
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
                                    @php $active = !is_null($roomFilterId) && (int) $roomFilterId === (int) $r['id']; @endphp
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