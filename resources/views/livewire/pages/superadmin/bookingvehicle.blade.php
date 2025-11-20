@php
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

if (!function_exists('fmtDate')) {
    function fmtDate($v){
        try { return $v ? Carbon::parse($v)->format('d M Y') : '—'; }
        catch (\Throwable) { return '—'; }
    }
}
if (!function_exists('fmtTime')) {
    function fmtTime($v){
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
if (!function_exists('photoUrl')) {
    function photoUrl($path) {
        if (!$path) return null;
        if (preg_match('#^https?://#', $path)) return $path;
        return Storage::url($path);
    }
}

$card      = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
$label     = 'block text-sm font-medium text-gray-700 mb-2';
$input     = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
$btnBlk    = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
$btnRed    = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
$btnLt     = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition';
$chip      = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
$icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
$mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
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
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Vehicle History</h2>
                            <p class="text-sm text-white/80">
                                @if($statusTab === 'rejected')
                                    Riwayat peminjaman kendaraan yang <span class="font-semibold">Rejected</span>.
                                @else
                                    Riwayat peminjaman kendaraan yang sudah <span class="font-semibold">Completed (Done)</span>.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <label class="inline-flex items-center gap-2 text-sm text-white/90 cursor-pointer">
                            <input type="checkbox"
                                   wire:model.live="includeDeleted"
                                   class="w-4 h-4 rounded border-white/30 bg-white/10 text-gray-900 focus:ring-2 focus:ring-white/20 cursor-pointer">
                            <span>Include Deleted</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl px-4 py-3 text-sm text-gray-800">
            {{ session('success') }}
        </div>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <section class="{{ $card }} md:col-span-3">
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Vehicle History</h3>
                            <p class="text-xs text-gray-500">
                                @if($statusTab === 'rejected')
                                    Riwayat lengkap peminjaman kendaraan yang ditolak (Rejected).
                                @else
                                    Riwayat lengkap peminjaman kendaraan yang sudah selesai (Done / Completed).
                                @endif
                            </p>
                        </div>

                        <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-xs font-medium">
                            <button type="button"
                                    wire:click="$set('statusTab','done')"
                                    class="px-3 py-1 rounded-full transition
                                        {{ $statusTab === 'done'
                                            ? 'bg-gray-900 text-white shadow-sm'
                                            : 'text-gray-700 hover:bg-gray-200' }}">
                                Done
                            </button>
                            <button type="button"
                                    wire:click="$set('statusTab','rejected')"
                                    class="px-3 py-1 rounded-full transition
                                        {{ $statusTab === 'rejected'
                                            ? 'bg-gray-900 text-white shadow-sm'
                                            : 'text-gray-700 hover:bg-gray-200' }}">
                                Rejected
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-xs mt-1">
                        @if(!is_null($vehicleFilter))
                            @php $activeVehicle = $vehicleMap[$vehicleFilter] ?? 'Unknown'; @endphp
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

                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

                        <div>
                            <label class="{{ $label }}">Tanggal</label>
                            <input type="date" wire:model.live="selectedDate" class="{{ $input }}">
                        </div>

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

                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 divide-y md:divide-y-0">
                        @forelse($bookings as $b)
                            @php
                                $vehicleName = $vehicleMap[$b->vehicle_id] ?? 'Unknown';
                                $avatarChar = strtoupper(substr($vehicleName, 0, 1));
                                $isRejected = ($b->status === 'rejected');
                                $statusStyle = $isRejected
                                    ? ['bg' => 'bg-rose-100', 'text' => 'text-rose-800', 'label' => 'Rejected']
                                    : ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'label' => 'Completed'];

                                $beforeC = $photoCounts[$b->vehiclebooking_id]['before'] ?? 0;
                                $afterC  = $photoCounts[$b->vehiclebooking_id]['after'] ?? 0;

                                $before = $this->photosByBooking[$b->vehiclebooking_id]['before'] ?? collect();
                                $after  = $this->photosByBooking[$b->vehiclebooking_id]['after'] ?? collect();
                            @endphp

                            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow
                                {{ $b->deleted_at ? 'opacity-60 border-rose-300' : '' }}
                            ">
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        <div class="{{ $icoAvatar }} w-8 h-8 rounded-lg text-sm">{{ $avatarChar }}</div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                                                <div class="flex items-center flex-wrap gap-2">
                                                    <h4 class="font-semibold text-gray-900 text-sm truncate">
                                                        {{ $b->purpose ? ucfirst($b->purpose) : 'Vehicle Booking' }}
                                                    </h4>
                                                    <span class="text-[10px] px-2 py-0.5 rounded-full {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }} font-medium">
                                                        {{ $statusStyle['label'] }}
                                                    </span>
                                                    <span class="text-[10px] px-2 py-0.5 rounded-full border border-gray-300 text-gray-700 bg-gray-50">
                                                        #{{ $b->vehiclebooking_id }}
                                                    </span>
                                                    @if($b->deleted_at)
                                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-600 text-white font-medium">
                                                            DELETED
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="flex items-center gap-2 text-sm ml-auto">
                                                    @if(!$b->deleted_at)
                                                        <button type="button" 
                                                                wire:click="editBooking({{ $b->vehiclebooking_id }})"
                                                                class="p-1.5 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition"
                                                                title="Edit Booking">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7-7l4-4m-4 4l5.5 5.5m-5.5-5.5l1.5-1.5M17 3a2.828 2.828 0 114 4L13.5 15.5H10v-3.5L17 3z"></path></svg>
                                                        </button>
                                                        
                                                        <button type="button" 
                                                                wire:click="deleteBooking({{ $b->vehiclebooking_id }})"
                                                                class="p-1.5 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-100 transition"
                                                                title="Delete Booking (Soft Delete)">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                                wire:click="restoreBooking({{ $b->vehiclebooking_id }})"
                                                                class="text-[11px] px-2 py-1 rounded bg-green-500 text-white hover:bg-green-600 transition"
                                                                title="Restore Booking">
                                                            Restore
                                                        </button>
                                                        
                                                        <button type="button" 
                                                                wire:click="forceDeleteBooking({{ $b->vehiclebooking_id }})"
                                                                onclick="return confirm('Are you sure you want to permanently delete this booking? This action is irreversible.')"
                                                                class="text-[11px] px-2 py-1 rounded bg-rose-600 text-white hover:bg-rose-700 transition"
                                                                title="Permanently Delete Booking">
                                                            Force Delete
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>

                                            <p class="text-[12px] text-gray-500 truncate">
                                                Borrower: <span class="font-medium text-gray-700">{{ $b->borrower_name ?? 'N/A' }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="space-y-1 text-xs text-gray-600 border-t pt-3 border-gray-100">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                            </svg>
                                            <span class="font-medium text-gray-700">{{ $vehicleName }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ fmtDate($b->start_at) }}
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ fmtTime($b->start_at) }}–{{ fmtTime($b->end_at) }}
                                        </div>
                                        @if(!empty($b->destination))
                                            <span class="{{ $chip }} bg-gray-50 border border-gray-200">
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

                                    @if(!empty($b->notes) && $isRejected)
                                        <div class="text-xs bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">
                                            <div class="font-semibold text-rose-700 inline-flex items-center gap-1 mb-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                Reject Reason
                                            </div>
                                            <div class="text-rose-800">{{ $b->notes }}</div>
                                        </div>
                                    @endif

                                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600 border-t pt-3 border-gray-100">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-gray-50 border border-gray-200">
                                            <span class="text-gray-500">Photos:</span> Before: **{{ $beforeC }}**, After: **{{ $afterC }}**
                                        </span>
                                        @if($includeDeleted)
                                            <span class="text-[11px] text-gray-500">(* incl. deleted)</span>
                                        @endif
                                    </div>

                                    <div x-data="{ photoOpen: false }">
                                        <button @click="photoOpen = !photoOpen" type="button" class="mt-2 text-xs font-medium text-gray-600 hover:text-gray-900 focus:outline-none">
                                            <span x-text="photoOpen ? 'Hide Photo Gallery' : 'Show Photo Gallery'"></span>
                                        </button>

                                        <div x-show="photoOpen" x-cloak class="mt-3 space-y-4">
                                            <div>
                                                <div class="text-[11px] font-semibold text-gray-700 mb-2">Before Photos ({{ $beforeC }} items)</div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    @forelse($before as $p)
                                                        <div class="relative group border rounded-xl overflow-hidden bg-gray-50">
                                                            <img src="{{ photoUrl($p->photo_path) }}" alt="before" class="w-full h-16 object-cover">
                                                            @if($p->deleted_at)
                                                                <span class="absolute top-1 left-1 text-[10px] bg-rose-600 text-white px-1 py-0.5 rounded">Deleted</span>
                                                            @endif
                                                            <div class="absolute inset-x-0 bottom-0 p-1 bg-gradient-to-t from-black/60 to-black/0 opacity-0 group-hover:opacity-100 transition flex gap-1 justify-end">
                                                                @if(!$p->deleted_at)
                                                                    <button type="button" class="text-[10px] px-1 py-0.5 rounded bg-white/90 text-gray-800 hover:bg-gray-200"
                                                                            wire:click="deletePhoto({{ $p->id }})">Soft Delete</button>
                                                                @else
                                                                    <button type="button" class="text-[10px] px-1 py-0.5 rounded bg-white/90 text-green-700 hover:bg-green-100"
                                                                            wire:click="restorePhoto({{ $p->id }})">Restore</button>
                                                                    <button type="button" class="text-[10px] px-1 py-0.5 rounded bg-rose-600 text-white hover:bg-rose-700"
                                                                            wire:click="forceDeletePhoto({{ $p->id }})"
                                                                            onclick="return confirm('Are you sure you want to permanently delete this photo?')">Force Delete</button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="col-span-full text-[11px] text-gray-500">Tidak ada foto before.</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-[11px] font-semibold text-gray-700 mb-2">After Photos ({{ $afterC }} items)</div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    @forelse($after as $p)
                                                        <div class="relative group border rounded-xl overflow-hidden bg-gray-50">
                                                            <img src="{{ photoUrl($p->photo_path) }}" alt="after" class="w-full h-16 object-cover">
                                                            @if($p->deleted_at)
                                                                <span class="absolute top-1 left-1 text-[10px] bg-rose-600 text-white px-1 py-0.5 rounded">Deleted</span>
                                                            @endif
                                                            <div class="absolute inset-x-0 bottom-0 p-1 bg-gradient-to-t from-black/60 to-black/0 opacity-0 group-hover:opacity-100 transition flex gap-1 justify-end">
                                                                @if(!$p->deleted_at)
                                                                    <button type="button" class="text-[10px] px-1 py-0.5 rounded bg-white/90 text-gray-800 hover:bg-gray-200"
                                                                            wire:click="deletePhoto({{ $p->id }})">Soft Delete</button>
                                                                @else
                                                                    <button type="button" class="text-[10px] px-1 py-0.5 rounded bg-white/90 text-green-700 hover:bg-green-100"
                                                                            wire:click="restorePhoto({{ $p->id }})">Restore</button>
                                                                    <button type="button" class="text-[10px] px-1 py-0.5 rounded bg-rose-600 text-white hover:bg-rose-700"
                                                                            wire:click="forceDeletePhoto({{ $p->id }})"
                                                                            onclick="return confirm('Are you sure you want to permanently delete this photo?')">Force Delete</button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="col-span-full text-[11px] text-gray-500">Tidak ada foto after.</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-1 text-[10px] text-gray-500 flex flex-wrap items-center gap-2 border-t pt-3 border-gray-100">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Created: {{ optional($b->created_at)->format('Y-m-d H:i') }}
                                        </span>
                                        <span>•</span>
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Updated: {{ optional($b->updated_at)->format('Y-m-d H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="md:col-span-2 px-4 sm:px-6 py-14 text-center text-gray-500 text-sm">
                                Belum ada riwayat peminjaman pada tab ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                @if(method_exists($bookings, 'links'))
                    <div class="px-4 sm:px-6 py-5 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-center">
                            {{ $bookings->links() }}
                        </div>
                    </div>
                @endif
            </section>

            <aside class="hidden md:flex md:flex-col md:col-span-1 gap-4">
                <section class="{{ $card }}">
                    <div class="px-4 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Filter by Vehicle</h3>
                        <p class="text-xs text-gray-500 mt-1">Klik salah satu kendaraan untuk mem-filter riwayat.</p>
                    </div>

                    <div class="px-4 py-3 max-h-64 overflow-y-auto">
                        <button type="button"
                                wire:click="$set('vehicleFilter', null)"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium
                                    {{ is_null($vehicleFilter) ? 'bg-gray-900 text-white' : 'text-gray-800 hover:bg-gray-100' }}">
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

                    <div class="px-4 pt-3 pb-4 border-t border-gray-200 bg-gray-50">
                        <h4 class="text-xs font-semibold text-gray-900 mb-2">Quick Stats</h4>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Total Vehicles</span>
                                <span class="font-semibold text-gray-900">{{ count($vehicles) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">
                                    {{ $statusTab === 'rejected' ? 'Rejected records' : 'Completed records' }}
                                </span>
                                <span class="font-semibold text-gray-900">{{ $bookings->total() }}</span>
                            </div>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </main>
</div>