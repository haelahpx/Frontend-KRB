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
                    if (preg_match('/^\d{2}:\d{2}/', $v)) return str_replace(':', '.', substr($v, 0, 5));
                    if (preg_match('/^\d{2}\.\d{2}/', $v)) return substr($v, 0, 5);
                }
                return '—';
            }
        }
    }

    $card   = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label  = 'block text-sm font-medium text-gray-700 mb-2';
    $input  = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $chip   = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    $icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
@endphp

<div class="min-h-screen bg-gray-50">
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
                                      d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Vehicle Status</h2>
                            <p class="text-sm text-white/80">Pantau peminjaman kendaraan yang masih Pending atau sedang In Use.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Include Deleted Checkbox --}}
                        <label class="inline-flex items-center gap-2 text-sm text-white/90 cursor-pointer">
                            <input type="checkbox" 
                                   wire:model.live="includeDeleted"
                                   class="w-4 h-4 rounded border-white/30 bg-white/10 text-gray-900 focus:ring-2 focus:ring-white/20 cursor-pointer">
                            <span>Include Deleted</span>
                        </label>
                    </div>
                </div>

                {{-- Flash message --}}
                @if(session()->has('success'))
                    <div class="mt-4 flex items-start gap-2 text-sm text-emerald-100 bg-emerald-900/30 border border-emerald-500/30 rounded-xl px-4 py-3 backdrop-blur-sm">
                        <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- MAIN LAYOUT: LEFT (BOOKINGS) + RIGHT (SIDEBAR) --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- LEFT: VEHICLE BOOKING LIST CARD (md:col-span-3) --}}
            <section class="{{ $card }} md:col-span-3">
                {{-- Header --}}
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Vehicle Bookings</h3>
                            <p class="text-xs text-gray-500">Daftar peminjaman kendaraan perusahaan.</p>
                        </div>

                        {{-- Tabs Pending / In Use --}}
                        <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-xs font-medium">
                            <button type="button"
                                    wire:click="$set('statusTab','pending')"
                                    class="px-3 py-1 rounded-full transition
                                        {{ $statusTab === 'pending'
                                            ? 'bg-gray-900 text-white shadow-sm'
                                            : 'text-gray-700 hover:bg-gray-200' }}">
                                Pending
                            </button>
                            <button type="button"
                                    wire:click="$set('statusTab','in_use')"
                                    class="px-3 py-1 rounded-full transition
                                        {{ $statusTab === 'in_use'
                                            ? 'bg-gray-900 text-white shadow-sm'
                                            : 'text-gray-700 hover:bg-gray-200' }}">
                                In Use
                            </button>
                        </div>
                    </div>

                    {{-- Active vehicle filter badge --}}
                    <div class="flex flex-wrap items-center gap-2 text-xs mt-1">
                        @if(!is_null($vehicleFilter))
                            @php
                                $activeVehicle = $vehicleMap[$vehicleFilter] ?? 'Unknown';
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-900 text-white border border-gray-800">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                </svg>
                                <span>Vehicle: {{ $activeVehicle }}</span>
                                <button type="button" class="ml-1 hover:text-gray-200" wire:click="$set('vehicleFilter', null)">×</button>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 border border-dashed border-gray-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 4h18M4 9h16M6 14h12M9 19h6" />
                                </svg>
                                <span>No vehicle filter</span>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Filters (Search, Tanggal, Urutkan) --}}
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Search --}}
                        <div>
                            <label class="{{ $label }}">Search</label>
                            <div class="relative">
                                <input type="text"
                                       class="{{ $input }} pl-9"
                                       placeholder="Search purpose, destination, borrower…"
                                       wire:model.live.debounce.400ms="q">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Tanggal --}}
                        <div>
                            <label class="{{ $label }}">Tanggal</label>
                            <input type="date"
                                   wire:model.live="selectedDate"
                                   class="{{ $input }}">
                        </div>

                        {{-- Urutkan --}}
                        <div>
                            <label class="{{ $label }}">Urutkan</label>
                            <select wire:model.live="sortFilter" class="{{ $input }}">
                                <option value="recent">Default (terbaru)</option>
                                <option value="oldest">Terlama dulu</option>
                                <option value="nearest">Paling dekat dengan sekarang</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- LIST --}}
                <div class="divide-y divide-gray-200">
                    @forelse($bookings as $b)
                        @php
                            $start = \Carbon\Carbon::parse($b->start_at, 'Asia/Jakarta');
                            $end = \Carbon\Carbon::parse($b->end_at, 'Asia/Jakarta');
                            $vehicleName = $vehicleMap[$b->vehicle_id] ?? 'Unknown';
                            $avatarChar = strtoupper(substr($vehicleName, 0, 1));
                            
                            $statusColors = [
                                'pending'   => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'label' => 'Pending'],
                                'in_use'    => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'In Use'],
                                'returned'  => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'label' => 'Returned'],
                                'rejected'  => ['bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'label' => 'Rejected'],
                                'completed' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Completed'],
                            ];
                            
                            $statusStyle = $statusColors[$b->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($b->status)];
                            
                            $beforeC = $photoCounts[$b->vehiclebooking_id]['before'] ?? 0;
                            $afterC  = $photoCounts[$b->vehiclebooking_id]['after'] ?? 0;
                        @endphp

                        <div class="px-4 sm:px-6 py-5 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="{{ $icoAvatar }}">{{ $avatarChar }}</div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-gray-900 text-base truncate">
                                                {{ $b->purpose ? ucfirst($b->purpose) : 'Vehicle Booking' }}
                                            </h4>
                                            <span class="text-[11px] px-2 py-0.5 rounded-full {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }}">
                                                {{ $statusStyle['label'] }}
                                            </span>
                                            <span class="text-[11px] px-2 py-0.5 rounded-full border border-gray-300 text-gray-700 bg-gray-50">
                                                #{{ $b->vehiclebooking_id }}
                                            </span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-4 text-[13px] text-gray-600">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                                </svg>
                                                {{ $vehicleName }}
                                            </span>
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ fmtDate($b->start_at) }}
                                            </span>
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ fmtTime($b->start_at) }}–{{ fmtTime($b->end_at) }}
                                            </span>

                                            @if(!empty($b->destination))
                                                <span class="{{ $chip }}">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span class="font-medium text-gray-700">{{ $b->destination }}</span>
                                                </span>
                                            @endif
                                        </div>

                                        @if(!empty($b->borrower_name))
                                            <div class="mt-2 text-xs text-gray-600">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    Borrower: <span class="font-medium">{{ $b->borrower_name }}</span>
                                                </span>
                                            </div>
                                        @endif

                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-gray-50 border border-gray-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Before: {{ $beforeC }}
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-gray-50 border border-gray-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                After: {{ $afterC }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right shrink-0 space-y-2">
                                    <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                        @if($b->status === 'pending')
                                            <button type="button"
                                                    wire:click="reject({{ $b->vehiclebooking_id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="reject"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition">
                                                Reject
                                            </button>
                                            <button type="button"
                                                    wire:click="approve({{ $b->vehiclebooking_id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="approve"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg bg-black text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition">
                                                Approve
                                            </button>
                                        @elseif(in_array($b->status, ['in_use','returned']))
                                            <button type="button"
                                                    wire:click="markDone({{ $b->vehiclebooking_id }})"
                                                    @if($afterC === 0) disabled @endif
                                                    wire:loading.attr="disabled"
                                                    wire:target="markDone"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg
                                                        @if($afterC === 0)
                                                            bg-gray-200 text-gray-500 cursor-not-allowed
                                                        @else
                                                            bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600/20
                                                        @endif
                                                        disabled:opacity-60 transition">
                                                Mark Done
                                            </button>
                                            @if($afterC === 0)
                                                <span class="text-[11px] text-gray-500 block mt-1">
                                                    Waiting for after photos
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 sm:px-6 py-14 text-center text-gray-500 text-sm">
                            Tidak ada data pada filter ini.
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if(method_exists($bookings, 'links'))
                    <div class="px-4 sm:px-6 py-5 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-center">
                            {{ $bookings->links() }}
                        </div>
                    </div>
                @endif
            </section>

            {{-- RIGHT: SIDEBAR (DESKTOP / TABLET) --}}
            <aside class="hidden md:flex md:flex-col md:col-span-1 gap-4">
                {{-- Filter by Vehicle --}}
                <section class="{{ $card }}">
                    <div class="px-4 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Filter by Vehicle</h3>
                        <p class="text-xs text-gray-500 mt-1">Klik salah satu kendaraan untuk mem-filter daftar booking.</p>
                    </div>

                    <div class="px-4 py-3 max-h-64 overflow-y-auto">
                        {{-- All vehicles --}}
                        <button type="button"
                                wire:click="$set('vehicleFilter', null)"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium
                                    {{ is_null($vehicleFilter) ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                            <span class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md border border-gray-300 text-[11px]">
                                    All
                                </span>
                                <span>All Vehicles</span>
                            </span>
                            @if(is_null($vehicleFilter))
                                <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                            @endif
                        </button>

                        {{-- Each vehicle --}}
                        <div class="mt-2 space-y-1.5">
                            @forelse($vehicles as $v)
                                @php
                                    $vLabel = $v->name ?? $v->plate_number ?? ('#'.$v->vehicle_id);
                                    $active = !is_null($vehicleFilter) && (int) $vehicleFilter === (int) $v->vehicle_id;
                                @endphp
                                <button type="button"
                                        wire:click="$set('vehicleFilter', {{ $v->vehicle_id }})"
                                        class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs
                                            {{ $active ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                                    <span class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-md border border-gray-300 text-[11px]">
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

                    {{-- Quick Stats --}}
                    <div class="px-4 pt-3 pb-4 border-t border-gray-200 bg-gray-50">
                        <h4 class="text-xs font-semibold text-gray-900 mb-2">Quick Stats</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600">Total Vehicles</span>
                                <span class="font-semibold text-gray-900">{{ count($vehicles) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600">{{ ucfirst($statusTab) }}</span>
                                <span class="font-semibold text-gray-900">{{ $bookings->total() }}</span>
                            </div>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </main>
</div>
