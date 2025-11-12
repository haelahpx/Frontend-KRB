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

    $card  = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $chip  = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
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
                                      d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Vehicle Status</h2>
                            <p class="text-sm text-white/80">Kelola peminjaman: Pending / Approved / On Progress / Returned.</p>
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-white/90 cursor-pointer">
                        <input type="checkbox" wire:model.live="includeDeleted"
                               class="w-4 h-4 rounded border-white/30 bg-white/10 text-gray-900 focus:ring-2 focus:ring-white/20 cursor-pointer">
                        <span>Include Deleted</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- LIST --}}
            <section class="{{ $card }} md:col-span-3">
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Vehicle Bookings</h3>
                            <p class="text-xs text-gray-500">Daftar peminjaman kendaraan.</p>
                        </div>

                        {{-- Tabs --}}
                        <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-xs font-medium">
                            @foreach(['pending'=>'Pending','approved'=>'Approved','on_progress'=>'On Progress','returned'=>'Returned'] as $key=>$lbl)
                                <button type="button"
                                        wire:click="$set('statusTab','{{ $key }}')"
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
                                <input type="text" class="{{ $input }} pl-9" placeholder="Search purpose, destination, borrower…"
                                       wire:model.live.debounce.400ms="q">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                                </svg>
                            </div>
                        </div>

                        <div>
                            <label class="{{ $label }}">Tanggal</label>
                            <input type="date" wire:model.live="selectedDate" class="{{ $input }}">
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

                {{-- LIST BODY --}}
                <div class="divide-y divide-gray-200">
                    @forelse($bookings as $b)
                        @php
                            $vehicleName = $vehicleMap[$b->vehicle_id] ?? 'Unknown';
                            $avatarChar  = strtoupper(substr($vehicleName, 0, 1));
                            $beforeC = $photoCounts[$b->vehiclebooking_id]['before'] ?? 0;
                            $afterC  = $photoCounts[$b->vehiclebooking_id]['after']  ?? 0;
                            $statusColors = [
                                'pending'      => ['bg'=>'bg-amber-100','text'=>'text-amber-800','label'=>'Pending'],
                                'approved'     => ['bg'=>'bg-emerald-100','text'=>'text-emerald-800','label'=>'Approved'],
                                'on_progress'  => ['bg'=>'bg-blue-100','text'=>'text-blue-800','label'=>'On Progress'],
                                'returned'     => ['bg'=>'bg-indigo-100','text'=>'text-indigo-800','label'=>'Returned'],
                                'rejected'     => ['bg'=>'bg-rose-100','text'=>'text-rose-800','label'=>'Rejected'],
                                'completed'    => ['bg'=>'bg-emerald-100','text'=>'text-emerald-800','label'=>'Completed'],
                            ];
                            $statusStyle = $statusColors[$b->status] ?? ['bg'=>'bg-gray-100','text'=>'text-gray-800','label'=>ucfirst($b->status)];
                        @endphp

                        <div wire:key="booking-{{ $b->vehiclebooking_id }}" class="px-4 sm:px-6 py-5 hover:bg-gray-50 transition-colors">
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
                                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-gray-50 text-gray-700 border border-gray-200">#{{ $b->vehiclebooking_id }}</span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-4 text-[13px] text-gray-600">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                                </svg>
                                                {{ $vehicleName }}
                                            </span>
                                            @if(!empty($b->borrower_name))
                                            <div class="text-xs text-gray-600">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    Borrower: <span class="font-medium">{{ $b->borrower_name }}</span>
                                                </span>
                                            </div>
                                        @endif
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
                                        </div>

                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-gray-50 border border-gray-200">
                                                Before: {{ $beforeC }}
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-gray-50 border border-gray-200">
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
                                                    wire:target="reject({{ $b->vehiclebooking_id }})"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300/20 disabled:opacity-60 transition">
                                                Reject
                                            </button>

                                            <button type="button"
                                                    wire:click="approve({{ $b->vehiclebooking_id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="approve({{ $b->vehiclebooking_id }})"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition">
                                                Approve
                                            </button>
                                        @elseif($b->status === 'on_progress')
                                            <button type="button"
                                                    wire:click="markReturned({{ $b->vehiclebooking_id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="markReturned({{ $b->vehiclebooking_id }})"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 disabled:opacity-60 transition">
                                                Mark Returned
                                            </button>
                                        @elseif($b->status === 'returned')
                                            <button type="button"
                                                    wire:click="markDone({{ $b->vehiclebooking_id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="markDone({{ $b->vehiclebooking_id }})"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg {{ $afterC === 0 ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600/20' }}"
                                                    @disabled($afterC === 0)>
                                                Mark Done
                                            </button>
                                            @if($afterC === 0)
                                                <span class="text-[11px] text-gray-500 block mt-1">
                                                    Waiting for after photos
                                                </span>
                                            @endif
                                        @endif
                                    </div>

                                    <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                                        {{ optional($b->created_at)->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                    </span>
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

            {{-- SIDEBAR: vehicle filter --}}
            <aside class="hidden md:flex md:flex-col md:col-span-1 gap-4">
                <section class="{{ $card }}">
                    <div class="px-4 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Filter by Vehicle</h3>
                        <p class="text-xs text-gray-500 mt-1">Klik kendaraan untuk mem-filter.</p>
                    </div>

                    <div class="px-4 py-3 max-h-64 overflow-y-auto">
                        <button type="button"
                                wire:click="$set('vehicleFilter', null)"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium {{ is_null($vehicleFilter) ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
                            <span class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md border border-gray-300 text-[11px]">All</span>
                                <span>All Vehicles</span>
                            </span>
                            @if(is_null($vehicleFilter))
                                <span class="text-[10px] uppercase tracking-wide opacity-80">Active</span>
                            @endif
                        </button>

                        <div class="mt-2 space-y-1.5">
                            @forelse($vehicles as $v)
                                @php
                                    $vLabel = $v->name ?? $v->plate_number ?? ('#'.$v->vehicle_id);
                                    $active = !is_null($vehicleFilter) && (int)$vehicleFilter === (int)$v->vehicle_id;
                                @endphp
                                <button type="button"
                                        wire:click="$set('vehicleFilter', {{ $v->vehicle_id }})"
                                        class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs {{ $active ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
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
                </section>
            </aside>
        </div>
    </main>
</div>
