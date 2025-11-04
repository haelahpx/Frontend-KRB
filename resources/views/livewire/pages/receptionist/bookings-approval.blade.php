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
    <main class="px-4 sm:px-6 py-6 space-y-8">
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
                            <h2 class="text-lg sm:text-xl font-semibold">Bookings Approval</h2>
                            <p class="text-sm text-white/80">Setujui, tolak, atau reschedule pengajuan booking.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <label class="inline-flex items-center gap-2 text-sm text-white/90">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 14h-2v-2h2v2zm0-4h-2V6h2v6z"/>
                            </svg>
                            <span>{{ $this->googleConnected ? 'Google Connected' : 'Google Not Connected' }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTERS --}}
        <section class="{{ $card }}">
            <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="{{ $label }}">Search</label>
                        <div class="relative">
                            <input type="text" class="{{ $input }} pl-9" placeholder="Cari judul…" wire:model.live="q">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <label class="{{ $label }}">Tanggal</label>
                        <div class="relative">
                            <input type="date" class="{{ $input }} pl-9" wire:model.live="selectedDate">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <label class="{{ $label }}">Urutkan</label>
                        <select class="{{ $input }}" wire:model.live="dateMode">
                            <option value="semua">Default (terbaru)</option>
                            <option value="terbaru">Terbaru</option>
                            <option value="terlama">Terlama</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        {{-- TWO COLUMNS --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- PENDING --}}
            <section class="{{ $card }}">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-amber-600 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Pending</h3>
                            <p class="text-sm text-gray-500">Menunggu persetujuan.</p>
                        </div>
                    </div>
                    <span class="text-[11px] px-2 py-1 rounded-full bg-amber-100 text-amber-800">Pending</span>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($pending as $row)
                        @php
                            $isOnline   = in_array($row->booking_type, ['onlinemeeting', 'online_meeting']);
                            $isRoomType = in_array($row->booking_type, ['bookingroom', 'meeting']);
                            $stateKey   = $row->status . '-' . optional($row->updated_at)->timestamp;
                            $avatarChar = strtoupper(substr($row->meeting_title ?? '—', 0, 1));
                        @endphp

                        <div wire:key="pend-{{ $row->bookingroom_id }}-{{ $stateKey }}" class="px-6 py-5 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="{{ $icoAvatar }}">{{ $avatarChar }}</div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-gray-900 text-base truncate">{{ $row->meeting_title ?? '—' }}</h4>
                                            <span class="text-[11px] px-2 py-0.5 rounded-full border {{ $isOnline ? 'border-blue-300 text-blue-700 bg-blue-50' : 'border-gray-300 text-gray-700 bg-gray-50' }}">
                                                {{ strtoupper($row->booking_type) }}
                                            </span>
                                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Pending</span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-4 text-[13px] text-gray-600">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ fmtDate($row->date) }}
                                            </span>
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ fmtTime($row->start_time) }}–{{ fmtTime($row->end_time) }}
                                            </span>

                                            @if($isRoomType)
                                                <span class="{{ $chip }}">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    <span class="font-medium text-gray-700">
                                                        Room: {{ optional($row->room)->room_name ?? '—' }}
                                                    </span>
                                                </span>
                                            @endif
                                        </div>

                                        @if($row->book_reject)
                                            <div class="mt-2 text-xs text-rose-700 bg-rose-50 border border-rose-100 rounded-lg px-2 py-1 inline-block">
                                                Alasan penolakan sebelumnya: {{ $row->book_reject }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-right shrink-0 space-y-2">
                                    <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                        <button type="button"
                                                wire:click="approve({{ $row->bookingroom_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="approve"
                                                class="px-3 py-2 text-xs font-medium rounded-lg bg-black text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition">
                                            Approve
                                        </button>
                                        <button type="button"
                                                wire:click="openReject({{ $row->bookingroom_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="openReject"
                                                class="px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition">
                                            Reject
                                        </button>
                                    </div>
                                    <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                                        No. {{ $pending->firstItem() + $loop->index }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-14 text-center text-gray-500 text-sm">Tidak ada data</div>
                    @endforelse
                </div>

                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $pending->onEachSide(1)->links() }}
                    </div>
                </div>
            </section>

            {{-- ONGOING --}}
            <section class="{{ $card }}">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Ongoing</h3>
                            <p class="text-sm text-gray-500">Booking yang sudah disetujui.</p>
                        </div>
                    </div>
                    <span class="text-[11px] px-2 py-1 rounded-full bg-blue-100 text-blue-800">Approved</span>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($ongoing as $row)
                        @php
                            $isOnline   = in_array($row->booking_type, ['onlinemeeting', 'online_meeting']);
                            $isRoomType = in_array($row->booking_type, ['bookingroom', 'meeting']);
                            $stateKey   = $row->status . '-' . optional($row->updated_at)->timestamp;
                            $avatarChar = strtoupper(substr($row->meeting_title ?? '—', 0, 1));
                        @endphp

                        <div wire:key="ong-{{ $row->bookingroom_id }}-{{ $stateKey }}" class="px-6 py-5 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="{{ $icoAvatar }}">{{ $avatarChar }}</div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-gray-900 text-base truncate">{{ $row->meeting_title ?? '—' }}</h4>
                                            <span class="text-[11px] px-2 py-0.5 rounded-full border {{ $isOnline ? 'border-blue-300 text-blue-700 bg-blue-50' : 'border-gray-300 text-gray-700 bg-gray-50' }}">
                                                {{ strtoupper($row->booking_type) }}
                                            </span>
                                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">Approved</span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-4 text-[13px] text-gray-600">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ fmtDate($row->date) }}
                                            </span>
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ fmtTime($row->start_time) }}–{{ fmtTime($row->end_time) }}
                                            </span>

                                            @if($isRoomType)
                                                <span class="{{ $chip }}">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    <span class="font-medium text-gray-700">
                                                        Room: {{ optional($row->room)->room_name ?? '—' }}
                                                    </span>
                                                </span>
                                            @endif
                                        </div>

                                        @if($isOnline && $row->online_meeting_url)
                                            <div class="mt-2 text-xs">
                                                <a href="{{ $row->online_meeting_url }}" target="_blank" class="underline text-blue-700">Meeting link</a>
                                                @if($row->online_meeting_code) • Code: {{ $row->online_meeting_code }} @endif
                                                @if($row->online_meeting_password) • Pass: {{ $row->online_meeting_password }} @endif
                                            </div>
                                        @endif

                                        @if($row->book_reject)
                                            <div class="mt-2 text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-2 py-1 inline-block">
                                                Catatan reschedule / cancel: {{ $row->book_reject }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-right shrink-0 space-y-2">
                                    <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                        {{-- TOMBOL CANCEL/RESCHEDULE HANYA UNTUK NON-ONLINE MEETING --}}
                                        @if(!$isOnline)
                                            <button type="button"
                                                    wire:click="openReschedule({{ $row->bookingroom_id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="openReschedule"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg bg-amber-500 text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500/20 disabled:opacity-60 transition">
                                                Cancel / Reschedule
                                            </button>
                                        @endif
                                    </div>
                                    <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                                        No. {{ $ongoing->firstItem() + $loop->index }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-14 text-center text-gray-500 text-sm">Tidak ada data</div>
                    @endforelse
                </div>

                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $ongoing->onEachSide(1)->links() }}
                    </div>
                </div>
            </section>
        </div>
    </main>

    {{-- REJECT MODAL --}}
    @if($showRejectModal)
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-lg bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="font-semibold">Tuliskan alasan penolakan</h3>
                        <button type="button" class="text-gray-500 hover:text-gray-700" wire:click="$set('showRejectModal', false)">×</button>
                    </div>
                    <div class="p-5 space-y-3">
                        <div>
                            <label class="{{ $label }}">Alasan</label>
                            <textarea rows="4" class="{{ $input }} !h-auto resize-none"
                                      wire:model.live="rejectReason"
                                      placeholder="Contoh: Ruangan dipakai acara lain, jadwal bentrok, dll."></textarea>
                            @error('rejectReason') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-200 flex items-center justify-end gap-2">
                        <button type="button"
                                wire:click="$set('showRejectModal', false)"
                                class="h-10 px-4 rounded-xl bg-gray-200 text-gray-900 text-sm font-medium hover:bg-gray-300 focus:outline-none">
                            Batal
                        </button>
                        <button type="button"
                                wire:click="confirmReject"
                                wire:loading.attr="disabled"
                                wire:target="confirmReject"
                                class="h-10 px-4 rounded-xl bg-rose-600 text-white text-sm font-medium hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition">
                            Simpan Penolakan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- RESCHEDULE (CANCEL) MODAL --}}
    @if($showRescheduleModal)
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-lg bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="font-semibold">Reschedule Booking</h3>
                        <button type="button" class="text-gray-500 hover:text-gray-700" wire:click="closeReschedule">×</button>
                    </div>
                    <div class="p-5 space-y-4">
                        <p class="text-sm text-gray-600">
                            Kamu akan mengubah jadwal booking ini. Pengguna akan melihat jadwal baru di halaman mereka.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- ROOM DROPDOWN --}}
                            @if($rescheduleRoomEnabled)
                                <div class="md:col-span-2">
                                    <label class="{{ $label }}">Room Baru</label>
                                    <select class="{{ $input }}" wire:model.live="rescheduleRoomId">
                                        <option value="">-- Select Room --</option>
                                        @foreach($roomsOptions as $r)
                                            <option value="{{ $r['id'] }}">{{ $r['label'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('rescheduleRoomId') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            <div class="md:col-span-2">
                                <label class="{{ $label }}">Tanggal Baru</label>
                                <input type="date" class="{{ $input }}" wire:model.live="rescheduleDate">
                                @error('rescheduleDate') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">Start Time Baru</label>
                                <input type="time" class="{{ $input }}" wire:model.live="rescheduleStart">
                                @error('rescheduleStart') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">End Time Baru</label>
                                <input type="time" class="{{ $input }}" wire:model.live="rescheduleEnd">
                                @error('rescheduleEnd') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="{{ $label }}">Alasan Cancel / Reschedule <span class="text-rose-500">*</span></label>
                            <textarea rows="4"
                                      class="{{ $input }} !h-auto resize-none"
                                      wire:model.live="rescheduleReason"
                                      placeholder="Contoh: Meeting diundur, ruangan dipakai untuk event lain, dll."></textarea>
                            @error('rescheduleReason') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Catatan ini disimpan di kolom <code>book_reject</code> (sama seperti alasan reject).
                            </p>
                        </div>
                    </div>

                    <div class="px-5 py-4 border-t border-gray-200 flex items-center justify-end gap-2">
                        <button type="button"
                                wire:click="closeReschedule"
                                class="h-10 px-4 rounded-xl bg-gray-200 text-gray-900 text-sm font-medium hover:bg-gray-300 focus:outline-none">
                            Batal
                        </button>
                        <button type="button"
                                wire:click="submitReschedule"
                                wire:loading.attr="disabled"
                                wire:target="submitReschedule"
                                class="h-10 px-4 rounded-xl bg-amber-500 text-white text-sm font-medium hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500/20 disabled:opacity-60 transition">
                            Simpan Jadwal Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
