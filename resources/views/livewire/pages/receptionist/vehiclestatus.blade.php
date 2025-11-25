@php
    use Carbon\Carbon;

    // --- UTILITY FUNCTIONS ---
    if (!function_exists('fmtDate')) {
        function fmtDate($v)
        {
            try {
                return $v ? Carbon::parse($v)->format('d M Y') : '—';
            } catch (\Throwable) {
                return '—';
            }
        }
    }
    if (!function_exists('fmtTime')) {
        function fmtTime($v)
        {
            try {
                return $v ? Carbon::parse($v)->format('H.i') : '—';
            } catch (\Throwable) {
                if (is_string($v)) {
                    if (preg_match('/^\d{2}:\d{2}/', $v))
                        return str_replace(':', '.', substr($v, 0, 5));
                    if (preg_match('/^\d{2}\.\d{2}/', $v))
                        return substr($v, 0, 5);
                }
                return '—';
            }
        }
    }

    // --- ADOPTED THEME TOKENS FROM TARGET TEMPLATE ---
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $textareaInput = 'w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition'; // Kept for textarea
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    $icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    $detailItem = 'py-3 border-b border-gray-100'; // Added for detail modal

    // Refined Button tokens to match the target's style (using gray/green/rose)
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition inline-flex items-center justify-center';
    $btnApprove = 'px-3 py-2 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-600/20 disabled:opacity-60 transition inline-flex items-center justify-center';
    // Reject button now uses a stronger rose color from the target
    $btnReject = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-700 text-white hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-700/20 disabled:opacity-60 transition inline-flex items-center justify-center';
    $btnGhost = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300/20 disabled:opacity-60 transition inline-flex items-center justify-center';

    // Original buttons (Return, Done) are re-styled to match the target's conventions for secondary actions
    $btnReturn = 'px-3 py-2 text-xs font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 disabled:opacity-60 transition inline-flex items-center justify-center';
    $btnDone = 'px-3 py-2 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition inline-flex items-center justify-center';

    // Add style for select to fix styling issues based on the target page
    $selectStyle = 'select, option { color:#111827 !important; background:#ffffff !important; -webkit-text-fill-color:#111827 !important; } option:checked { background:#e5e7eb !important; color:#111827 !important; }';

@endphp

<style>
    :root {
        color-scheme: light;
    }

    {!! $selectStyle !!}
</style>

<div class="min-h-screen bg-gray-50" wire:poll.1000ms.keep-alive>
    <main class="px-4 sm:px-6 py-6 space-y-6">
        {{-- HERO (Adopted Style from Target Template) --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            {{-- Icon updated to match the feeling of the target template (e.g. check badge for
                            approval) --}}
                            <x-heroicon-o-check-badge class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Vehicle Status Management</h2>
                            <p class="text-sm text-white/80">Kelola peminjaman: Approve, Reject, On Progress, dan
                                Returned.</p>
                        </div>
                    </div>

                    {{-- Include Deleted is kept but styled to match the hero section of the target template --}}
                    <label class="inline-flex items-center gap-2 text-sm text-white/90 cursor-pointer">
                        <input type="checkbox" wire:model.live="includeDeleted"
                            class="w-4 h-4 rounded border-white/30 bg-white/10 text-gray-900 focus:ring-2 focus:ring-white/20 cursor-pointer">
                        <span>Include Deleted</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- LIST (md:col-span-3) --}}
            <section class="{{ $card }} md:col-span-3">
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Vehicle Bookings</h3>
                            <p class="text-xs text-gray-500">Daftar peminjaman kendaraan.</p>
                        </div>

                        {{-- Tabs (Adopted Segmented Style) --}}
                        <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-[11px] font-medium">
                            @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'on_progress' => 'On Progress', 'returned' => 'Returned'] as $key => $lbl)
                                <button type="button" wire:click="$set('statusTab','{{ $key }}')"
                                    class="px-3 py-1 rounded-full transition {{ $statusTab === $key ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200' }}">
                                    {{ $lbl }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="{{ $label }}">Search</label>
                            <div class="relative">
                                <input type="text" class="{{ $input }} pl-9"
                                    placeholder="Search purpose, destination, borrower…"
                                    wire:model.live.debounce.400ms="q">
                                <x-heroicon-o-magnifying-glass
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            </div>
                        </div>

                        <div>
                            <label class="{{ $label }}">Tanggal</label>
                            <div class="relative">
                                <input type="date" wire:model.live="selectedDate" class="{{ $input }} pl-9">
                                <x-heroicon-o-calendar-days
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            </div>
                        </div>

                        <div>
                            <label class="{{ $label }}">Urutkan</label>
                            <select wire:model.live="sortFilter" class="{{ $input }}">
                                <option value="recent">Default (terbaru)</option>
                                <option value="oldest">Terlama dulu</option>
                                <option value="nearest">Paling dekat sekarang</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- LIST BODY – 2-column bento cards --}}
                <div class="p-4 sm:p-6 bg-gray-50/50">
                    @if($bookings->isEmpty())
                        <div class="lg:col-span-2 py-14 text-center text-gray-500 text-sm">
                            Tidak ada data pada filter ini.
                        </div>
                    @else
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @forelse($bookings as $b)
                                @php
                                    $vehicleName = $vehicleMap[$b->vehicle_id] ?? 'Unknown';
                                    $avatarChar = strtoupper(substr($vehicleName, 0, 1));
                                    $beforeC = $photoCounts[$b->vehiclebooking_id]['before'] ?? 0;
                                    $afterC = $photoCounts[$b->vehiclebooking_id]['after'] ?? 0;
                                    $statusColors = [
                                        'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'label' => 'Pending'],
                                        'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Approved'],
                                        'on_progress' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'On Progress'],
                                        'returned' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'label' => 'Returned'],
                                        'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'label' => 'Rejected'],
                                        'completed' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Completed'],
                                    ];
                                    $statusStyle = $statusColors[$b->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($b->status)];
                                @endphp

                                {{-- List Item Card (Adopted Style) --}}
                                <div wire:key="booking-{{ $b->vehiclebooking_id }}"
                                    class="flex flex-col justify-between p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200">
                                    <div class="flex items-start gap-3 mb-4">
                                        <div class="{{ $icoAvatar }}">{{ $avatarChar }}</div>
                                        <div class="min-w-0 flex-1">
                                            {{-- Title and Tags --}}
                                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                                <h4 class="font-semibold text-gray-900 text-base truncate cursor-pointer"
                                                    wire:click="showDetails({{ $b->vehiclebooking_id }})">
                                                    {{ $b->purpose ? ucfirst($b->purpose) : 'Vehicle Booking' }}
                                                </h4>
                                                <span
                                                    class="text-[10px] px-1.5 py-0.5 rounded-full {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }}">
                                                    {{ $statusStyle['label'] }}
                                                </span>
                                                <span
                                                    class="text-[10px] px-1.5 py-0.5 rounded-full bg-gray-50 text-gray-700 border border-gray-200">
                                                    #{{ $b->vehiclebooking_id }}
                                                </span>
                                            </div>

                                            {{-- Details --}}
                                            <div class="space-y-1 text-[13px] text-gray-600">
                                                <span class="flex items-center gap-1.5">
                                                    <x-heroicon-o-truck class="w-4 h-4 text-gray-500" />
                                                    <span class="font-medium text-gray-800">{{ $vehicleName }}</span>
                                                </span>

                                                @if(!empty($b->borrower_name))
                                                    <span class="flex items-center gap-1.5 text-xs">
                                                        <x-heroicon-o-user class="w-3.5 h-3.5 text-gray-500" />
                                                        Borrower:
                                                        <span class="font-medium text-gray-800">{{ $b->borrower_name }}</span>
                                                    </span>
                                                @endif

                                                <div class="flex flex-wrap items-center gap-4 text-xs">
                                                    <span class="flex items-center gap-1.5">
                                                        <x-heroicon-o-calendar-days class="w-4 h-4 text-gray-500" />
                                                        {{ fmtDate($b->start_at) }}
                                                    </span>
                                                    <span class="flex items-center gap-1.5">
                                                        <x-heroicon-o-clock class="w-4 h-4 text-gray-500" />
                                                        {{ fmtTime($b->start_at) }}–{{ fmtTime($b->end_at) }}
                                                    </span>
                                                </div>

                                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                                                    <span class="{{ $chip }} bg-gray-50 border border-gray-200 text-gray-700">
                                                        Before Photos: {{ $beforeC }}
                                                    </span>
                                                    <span class="{{ $chip }} bg-gray-50 border border-gray-200 text-gray-700">
                                                        After Photos: {{ $afterC }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- RIGHT: actions (Button classes updated) --}}
                                    <div class="pt-3 mt-auto border-t border-gray-100 flex items-center justify-between">
                                        <button type="button" wire:click="showDetails({{ $b->vehiclebooking_id }})"
                                            class="{{ $btnGhost }}"> {{-- Changed from $btnBlk to $btnGhost as per target style
                                            for detail --}}
                                            <x-heroicon-o-eye class="w-3.5 h-3.5 mr-0.5" />
                                            Detail
                                        </button>
                                        <div class="flex flex-wrap gap-2 justify-end shrink-0">
                                            @if($b->status === 'pending')
                                                {{-- Reject Button using new $btnReject --}}
                                                <button type="button" wire:click.stop="confirmReject({{ $b->vehiclebooking_id }})"
                                                    wire:loading.attr="disabled" class="{{ $btnReject }}"> {{-- Now strong rose
                                                    color (bg-rose-700) --}}
                                                    Reject
                                                </button>

                                                <button type="button" wire:click.stop="approve({{ $b->vehiclebooking_id }})"
                                                    wire:loading.attr="disabled" class="{{ $btnApprove }}"> {{-- Green Approve
                                                    button --}}
                                                    Approve
                                                </button>
                                            @elseif($b->status === 'approved')
                                                {{-- Mark On Progress - using a custom color for secondary action --}}
                                                <button type="button" wire:click.stop="markOnProgress({{ $b->vehiclebooking_id }})"
                                                    wire:loading.attr="disabled"
                                                    class="{{ $btnReturn }} bg-blue-600 hover:bg-blue-700 focus:ring-blue-600/20"
                                                    @disabled($beforeC === 0)>
                                                    Mark On Progress
                                                </button>
                                                @if($beforeC === 0)
                                                    <span class="text-[11px] text-gray-500 block self-end">Waiting for before
                                                        photos</span>
                                                @endif
                                            @elseif($b->status === 'on_progress')
                                                {{-- Mark Returned - using a custom color for secondary action --}}
                                                <button type="button" wire:click.stop="markReturned({{ $b->vehiclebooking_id }})"
                                                    wire:loading.attr="disabled" class="{{ $btnReturn }}">
                                                    Mark Returned
                                                </button>
                                            @elseif($b->status === 'returned')
                                                {{-- Mark Done - using a custom color for final completion --}}
                                                <button type="button" wire:click.stop="markDone({{ $b->vehiclebooking_id }})"
                                                    wire:loading.attr="disabled"
                                                    class="{{ $afterC === 0 ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : $btnDone }}"
                                                    @disabled($afterC === 0)>
                                                    Mark Done
                                                </button>
                                                @if($afterC === 0)
                                                    <span class="text-[11px] text-gray-500 block self-end">Waiting for after
                                                        photos</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center text-gray-500 text-sm py-6">
                                    Tidak ada data pada filter ini.
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>

                {{-- Pagination (Background changed to bg-white for consistency) --}}
                @if(method_exists($bookings, 'links'))
                    <div class="px-4 sm:px-6 py-5 bg-white border-t border-gray-200 rounded-b-2xl">
                        <div class="flex justify-center">
                            {{ $bookings->links() }}
                        </div>
                    </div>
                @endif
            </section>

            {{-- SIDEBAR: vehicle filter (Adopted Style) --}}
            <aside class="hidden md:flex md:flex-col md:col-span-1 gap-4">
                <section class="{{ $card }}">
                    <div class="px-4 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Filter by Vehicle</h3>
                        <p class="text-xs text-gray-500 mt-1">Klik kendaraan untuk mem-filter.</p>
                    </div>

                    <div class="px-4 py-3 max-h-64 overflow-y-auto">
                        <button type="button" wire:click="$set('vehicleFilter', null)"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ is_null($vehicleFilter) ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                            <span class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-md border border-gray-300 text-[11px] {{ is_null($vehicleFilter) ? 'bg-white/10 border-white/20' : '' }}">All</span>
                                <span>All Vehicles</span>
                            </span>
                            @if(is_null($vehicleFilter))
                                <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                            @endif
                        </button>

                        <div class="mt-2 space-y-1.5">
                            @forelse($vehicles as $v)
                                @php
                                    $vLabel = $v->name ?? $v->plate_number ?? ('#' . $v->vehicle_id);
                                    $active = !is_null($vehicleFilter) && (int) $vehicleFilter === (int) $v->vehicle_id;
                                @endphp
                                <button type="button" wire:click="$set('vehicleFilter', {{ $v->vehicle_id }})"
                                    class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs {{ $active ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                    <span class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center justify-center w-6 h-6 rounded-md border border-gray-300 text-[11px] {{ $active ? 'bg-white/10 border-white/20' : '' }}">
                                            {{ substr($vLabel, 0, 2) }}
                                        </span>
                                        <span class="truncate">{{ $vLabel }}</span>
                                    </span>
                                    @if($active)
                                        <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                                    @endif
                                </button>
                            @empty
                                <p class="text-xs text-gray-500">Tidak ada data kendaraan.</p>
                            @endforelse
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </main>

    {{-- DETAIL MODAL (Adopted Style) --}}
    @if($showDetailModal && $selectedBooking)
        <div x-data="{ show: @entangle('showDetailModal') }" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeDetailModal"></div>

            {{-- Modal Content --}}
            <div x-show="show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative z-10 w-full max-w-3xl bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden">

                {{-- Header (Adopted Style) --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-eye class="w-5 h-5 text-gray-700" />
                        <h3 class="text-base font-semibold text-gray-900">
                            Detail Booking #{{ $selectedBooking->vehiclebooking_id }}
                        </h3>
                    </div>
                    <button type="button" wire:click="closeDetailModal"
                        class="p-1 text-gray-500 hover:text-gray-700 transition">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                {{-- Body (Minor styling updates) --}}
                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    {{-- Detail Grid --}}
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
                        <div class="sm:pr-4">
                            <span class="block text-xs text-gray-500">Peminjam</span>
                            <span class="font-medium text-gray-800">{{ $selectedBooking->borrower_name }}</span>
                        </div>
                        <div class="pt-4 sm:pt-0 sm:pl-4">
                            <span class="block text-xs text-gray-500">Kendaraan</span>
                            <span
                                class="font-medium text-gray-800">{{ $vehicleMap[$selectedBooking->vehicle_id] ?? 'N/A' }}</span>
                        </div>
                        <div class="pt-4 sm:pt-4 sm:pr-4">
                            <span class="block text-xs text-gray-500">Tujuan</span>
                            <span class="font-medium text-gray-800">{{ $selectedBooking->destination ?? 'N/A' }}</span>
                        </div>
                        <div class="pt-4 sm:pt-4 sm:pl-4">
                            <span class="block text-xs text-gray-500">Tipe Keperluan</span>
                            <span class="font-medium text-gray-800">{{ ucfirst($selectedBooking->purpose_type) }}</span>
                        </div>
                        <div class="pt-4 sm:pt-4 sm:pr-4">
                            <span class="block text-xs text-gray-500">Mulai</span>
                            <span class="font-medium text-gray-800">{{ fmtDate($selectedBooking->start_at) }},
                                {{ fmtTime($selectedBooking->start_at) }}</span>
                        </div>
                        <div class="pt-4 sm:pt-4 sm:pl-4">
                            <span class="block text-xs text-gray-500">Selesai</span>
                            <span class="font-medium text-gray-800">{{ fmtDate($selectedBooking->end_at) }},
                                {{ fmtTime($selectedBooking->end_at) }}</span>
                        </div>
                    </div>

                    <hr class="border-gray-200" />

                    {{-- Foto Sebelum --}}
                    <div>
                        <h4 class="text-base font-semibold text-gray-800 mb-3">Foto Sebelum Peminjaman
                            ({{ count($selectedPhotos['before']) }})</h4>
                        @forelse($selectedPhotos['before'] as $photo)
                            <div class="mb-4 bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank"
                                    class="block rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto Before"
                                        class="w-full h-auto object-cover">
                                </a>
                                <span class="text-xs text-gray-500 mt-2 block">
                                    Di-upload oleh: **{{ $photo->user->full_name ?? 'N/A' }}** pada
                                    {{ optional($photo->created_at)->format('d M Y H:i') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Tidak ada foto 'before' yang di-upload.</p>
                        @endforelse
                    </div>

                    <hr class="border-gray-200" />

                    {{-- Foto Sesudah --}}
                    <div>
                        <h4 class="text-base font-semibold text-gray-800 mb-3">Foto Setelah Peminjaman
                            ({{ count($selectedPhotos['after']) }})</h4>
                        @forelse($selectedPhotos['after'] as $photo)
                            <div class="mb-4 bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank"
                                    class="block rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto After"
                                        class="w-full h-auto object-cover">
                                </a>
                                <span class="text-xs text-gray-500 mt-2 block">
                                    Di-upload oleh: **{{ $photo->user->full_name ?? 'N/A' }}** pada
                                    {{ optional($photo->created_at)->format('d M Y H:i') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Tidak ada foto 'after' yang di-upload.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Footer (Adopted Style) --}}
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200 text-right">
                    <button type="button" wire:click="closeDetailModal" class="{{ $btnGhost }}"> {{-- Changed from
                        $btnReject to $btnGhost as per target style for closing modal --}}
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- REJECT MODAL (Adopted Style) --}}
    @if($showRejectModal && $rejectId)
        <div x-data="{ show: @entangle('showRejectModal') }" x-show="show"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="cancelReject"></div>

            {{-- Modal Content --}}
            <div x-show="show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative z-10 w-full max-w-lg bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden">

                <form wire:submit.prevent="submitReject">
                    {{-- Header (Adopted Style) --}}
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-x-circle class="w-5 h-5 text-rose-600" />
                            <h3 class="text-base font-semibold text-gray-900">Tolak Booking #{{ $rejectId }}</h3>
                        </div>
                        <button type="button" wire:click="cancelReject"
                            class="p-1 text-gray-500 hover:text-gray-700 transition">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-5 space-y-4">
                        <p class="text-sm text-gray-600">
                            Masukkan alasan penolakan untuk booking kendaraan ini. Alasan ini akan dicatat.
                        </p>

                        <div>
                            <label for="reject-note" class="{{ $label }}">Alasan Penolakan <span
                                    class="text-rose-600">*</span></label>
                            {{-- Adjusted class to use $textareaInput (for consistency with target) --}}
                            <textarea id="reject-note" wire:model.defer="rejectNote" rows="4"
                                placeholder="Contoh: Kendaraan sedang perbaikan, atau tanggal bentrok."
                                class="{{ $textareaInput }} resize-none @error('rejectNote') border-red-500 @enderror"></textarea>
                            @error('rejectNote')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Footer (Adopted Style) --}}
                    <div class="px-5 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-2">
                        <button type="button" wire:click="cancelReject" class="{{ $btnGhost }}"> {{-- Changed from
                            $btnReject to $btnGhost for cancel --}}
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="{{ $btnReject }}"> {{-- Using new strong
                            rose $btnReject --}}
                            Tolak Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>