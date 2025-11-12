<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border-2 border-black p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Vehicle Booking</h1>

            <div class="ml-auto flex items-center gap-3">
                <div class="inline-flex rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                    <a href="{{ route('bookingstatus') }}"
                       class="px-3 md:px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors border-r border-gray-200 inline-flex items-center gap-2">
                        <x-heroicon-o-calendar-days class="w-4 h-4"/>
                        Room Booking
                    </a>
                    <a href="{{ route('vehiclestatus') }}"
                       class="px-3 md:px-4 py-2 text-sm font-medium bg-gray-900 text-white transition-colors inline-flex items-center gap-2">
                        <x-heroicon-o-truck class="w-4 h-4"/>
                        Vehicle Booking
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4 md:p-5">
        <div class="flex flex-col gap-3 md:gap-4 md:flex-row md:items-center md:justify-between pb-3 mb-3 border-b border-gray-200">
            <div class="w-full md:w-80">
                <div class="relative">
                    <input type="text"
                           wire:model.live.debounce.400ms="q"
                           placeholder="Search purpose / destination / vehicle…"
                           class="w-full h-10 pl-9 pr-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-black/20">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"/>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 mb-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Vehicle</label>
                <select wire:model.live="vehicleFilter"
                        class="w-full h-10 px-3 border border-gray-300 rounded-md text-sm bg-white focus:outline-none focus:ring-2 focus:ring-black/20">
                    <option value="">All vehicles</option>
                    @foreach($vehicles as $v)
                        @php $label = $v->name ?? $v->plate_number ?? ('#'.$v->vehicle_id); @endphp
                        <option value="{{ $v->vehicle_id }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sort</label>
                <select wire:model.live="sortFilter"
                        class="w-full h-10 px-3 border border-gray-300 rounded-md text-sm bg-white focus:outline-none focus:ring-2 focus:ring-black/20">
                    <option value="recent">Newest first</option>
                    <option value="oldest">Oldest first</option>
                    <option value="nearest">Nearest time</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select wire:model.live="dbStatusFilter"
                        class="w-full h-10 px-3 border border-gray-300 rounded-md text-sm bg-white focus:outline-none focus:ring-2 focus:ring-black/20">
                    <option value="all">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    {{-- GANTI: 'in_use' menjadi 'on_progress' --}}
                    <option value="on_progress">On Progress</option> 
                    <option value="returned">Returned</option>
                    <option value="rejected">Rejected</option>
                    <option value="completed">Completed</option>
                    {{-- BARU: Menambahkan status 'cancelled' --}}
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <div class="space-y-5">
            @forelse($bookings as $b)
                @php
                    $start = \Carbon\Carbon::parse($b->start_at, 'Asia/Jakarta');
                    $end = \Carbon\Carbon::parse($b->end_at, 'Asia/Jakarta');
                    $dateStr = $start->format('D, M j, Y');
                    $timeStr = $start->format('H:i').'–'.$end->format('H:i');
                    $vehicleName = $vehicleMap[$b->vehicle_id] ?? 'Unknown';
                    
                    // GANTI: 'in_use' menjadi 'on_progress' dan tambahkan status baru
                    $statusColor = [
                        'pending'     => 'bg-amber-50 text-amber-700 border-2 border-amber-500',
                        'approved'    => 'bg-emerald-50 text-emerald-700 border-2 border-emerald-500',
                        'on_progress' => 'bg-blue-50 text-blue-700 border-2 border-blue-500', // Ganti dari in_use
                        'returned'    => 'bg-indigo-50 text-indigo-700 border-2 border-indigo-500',
                        'rejected'    => 'bg-rose-50 text-rose-700 border-2 border-rose-500',
                        'completed'   => 'bg-slate-50 text-slate-700 border-2 border-slate-500',
                        'cancelled'   => 'bg-gray-50 text-gray-700 border-2 border-gray-400',
                    ][$b->status] ?? 'bg-slate-50 text-slate-700 border-2 border-slate-400';

                    // FIX: Safely get photo counts to prevent errors on bookings with no photos.
                    $currentPhotoCounts = $photoCounts[$b->vehiclebooking_id] ?? [];
                    $beforeC = $currentPhotoCounts['before'] ?? 0;
                    $afterC  = $currentPhotoCounts['after'] ?? 0;

                    // BARU: Tentukan apakah card bisa di-klik (untuk upload foto)
                    $isClickable = in_array($b->status, ['approved', 'returned']);
                    $cardTag = $isClickable ? 'a' : 'div';
                    $cardLink = $isClickable ? route('book-vehicle', ['id' => $b->vehiclebooking_id]) : null;
                @endphp

                {{-- BARU: Wrapper card dibuat dinamis (<a> atau <div>) --}}
                <{{ $cardTag }} 
                    @if($isClickable) 
                        href="{{ $cardLink }}" 
                        wire:navigate 
                        class="block relative bg-white rounded-xl border-2 border-black/80 shadow-md p-4 md:p-5 hover:shadow-lg hover:border-blue-500 hover:-translate-y-0.5 transition"
                    @else
                        class="relative bg-white rounded-xl border-2 border-black/80 shadow-md p-4 md:p-5 transition"
                    @endif
                >
                    {{-- BARU: Tambahkan notifikasi jika bisa di-klik untuk upload foto --}}
                    @if($isClickable)
                        <div class="absolute top-3 right-16 px-2 py-0.5 rounded-md 
                                    @if($b->status == 'approved') bg-emerald-100 text-emerald-700 
                                    @else bg-indigo-100 text-indigo-700 @endif 
                                    text-xs font-bold animate-pulse">
                            @if($b->status == 'approved')
                                Upload Foto (Before)
                            @else
                                Upload Foto (After)
                            @endif
                        </div>
                    @endif

                    <div class="flex items-start justify-between gap-4 mb-2">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base md:text-lg font-semibold text-gray-900 truncate">
                                {{ $b->purpose ? ucfirst($b->purpose) : 'Vehicle Booking' }}
                            </h3>
                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                                <span class="font-mono font-medium text-gray-800 inline-flex items-center gap-1">
                                    <x-heroicon-o-hashtag class="w-3.5 h-3.5"/> {{ $b->vehiclebooking_id }}
                                </span>

                                <span class="text-gray-300">•</span>
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 border-2 border-gray-400">
                                    <x-heroicon-o-truck class="w-3.5 h-3.5"/>
                                    {{ $vehicleName }}
                                </span>

                                <span class="text-gray-300">•</span>
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 border-2 border-gray-400">
                                    <x-heroicon-o-calendar-days class="w-3.5 h-3.5"/>
                                    {{ $dateStr }}
                                </span>

                                <span class="text-gray-300">•</span>
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 border-2 border-gray-400">
                                    <x-heroicon-o-clock class="w-3.5 h-3.5"/>
                                    {{ $timeStr }}
                                </span>

                                @if(!empty($b->destination))
                                    <span class="text-gray-300">•</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 border-2 border-gray-400">
                                        <x-heroicon-o-map-pin class="w-3.5 h-3.5"/>
                                        {{ $b->destination }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium {{ $statusColor }}">
                                <x-heroicon-o-check-badge class="w-4 h-4"/>
                                {{-- GANTI: Tampilkan status 'On Progress' --}}
                                {{ str_replace('_',' ',ucfirst($b->status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700">
                        @if(!empty($b->borrower_name))
                            <div class="inline-flex items-center gap-1">
                                <x-heroicon-o-user class="w-4 h-4"/>
                                <span class="font-semibold">Borrower:</span> {{ $b->borrower_name }}
                            </div>
                        @endif
                        @if(!empty($b->purpose_type))
                            <div class="inline-flex items-center gap-1">
                                <x-heroicon-o-tag class="w-4 h-4"/>
                                <span class="font-semibold">Type:</span> {{ ucfirst($b->purpose_type) }}
                            </div>
                        @endif
                        @if(isset($b->odd_even_area))
                            <div class="inline-flex items-center gap-1">
                                <x-heroicon-o-arrows-right-left class="w-4 h-4"/>
                                {{-- GANTI: Logika 'ya'/'tidak' dari DB lama diubah --}}
                                <span class="font-semibold">Odd/Even Area:</span> {{ ucfirst($b->odd_even_area) }}
                            </div>
                        @endif
                        @if(!empty($b->destination))
                            <div class="inline-flex items-center gap-1">
                                <x-heroicon-o-map-pin class="w-4 h-4"/>
                                <span class="font-semibold">Destination:</span> {{ $b->destination }}
                            </div>
                        @endif
                    </div>

                    @if(!empty($b->notes))
                        <p class="mt-2 text-sm text-gray-600 leading-relaxed inline-flex items-start gap-2">
                            <x-heroicon-o-chat-bubble-left-ellipsis class="w-4 h-4 mt-0.5"/>
                            {{ $b->notes }}
                        </p>
                    @endif

                    @if($b->status === 'rejected' && !empty($b->notes))
                        <div class="mt-3 rounded-lg border-2 border-rose-400 bg-rose-50 p-3">
                            <div class="text-xs font-semibold text-rose-700 inline-flex items-center gap-1">
                                <x-heroicon-o-no-symbol class="w-4 h-4"/>
                                Reject Reason
                            </div>
                            <div class="mt-1 text-sm text-rose-800">{{ $b->notes }}</div>
                        </div>
                    @endif

                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-gray-50 border-2 border-gray-400">
                            <x-heroicon-o-photo class="w-4 h-4"/>
                            Before Photo: {{ $beforeC }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-gray-50 border-2 border-gray-400">
                            <x-heroicon-o-photo class="w-4 h-4"/>
                            After Photo: {{ $afterC }}
                        </span>
                    </div>

                    <div class="mt-3 text-[11px] text-gray-500 inline-flex items-center gap-2">
                        <x-heroicon-o-calendar class="w-3.5 h-3.5"/>
                        <span>Created: {{ optional($b->created_at)->format('Y-m-d H:i') }}</span>
                        <span class="mx-2">•</span>
                        <x-heroicon-o-arrow-path class="w-3.5 h-3.5"/>
                        <span>Updated: {{ optional($b->updated_at)->format('Y-m-d H:i') }}</span>
                    </div>
                </{{ $cardTag }}> {{-- BARU: Penutup <a> atau <div> --}}
            @empty
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-10 text-center text-gray-600">
                    Belum ada data pada filter ini.
                </div>
            @endforelse
        </div>

        @if(method_exists($bookings, 'links'))
            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>