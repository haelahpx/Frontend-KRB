@php
    use Carbon\Carbon;

    if (!function_exists('fmtDate')) { /* guard if copy-paste twice */ }
@endphp
@php
    if (!function_exists('fmtDate')) {
        function fmtDate($v) {
            try { return $v ? Carbon::parse($v)->format('d M Y') : '—'; }
            catch (\Throwable) { return '—'; }
        }
    }
    if (!function_exists('fmtTime')) {
        function fmtTime($v) {
            try { return $v ? Carbon::parse($v)->format('H.i') : '—'; } // 10.00 style
            catch (\Throwable) {
                if (is_string($v)) {
                    if (preg_match('/^\d{2}:\d{2}/', $v)) return str_replace(':','.', substr($v,0,5));
                    if (preg_match('/^\d{2}\.\d{2}/', $v)) return substr($v,0,5);
                }
                return '—';
            }
        }
    }

    // Base tokens
    $card   = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label  = 'block text-sm font-medium text-gray-700 mb-2';
    $input  = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';

    // Extras
    $chip       = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    $icoAvatar  = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
@endphp

<div class="bg-gray-50">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Booking History</h2>
                            <p class="text-sm text-white/80">Kelola riwayat booking yang selesai dan ditolak.</p>
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-white/90">
                        <input type="checkbox" wire:model.live="withTrashed" class="rounded border-white/30 bg-white/10">
                        <span>Include deleted</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <section class="{{ $card }}">
            <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:ml-auto sm:w-auto">
                        <div class="relative sm:w-64">
                            <input type="text" wire:model.live="q" placeholder="Cari judul…" class="{{ $input }} pl-9 w-full" />
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                            </svg>
                        </div>

                        <input type="date" wire:model.live="selectedDate" class="{{ $input }} sm:w-56" />
                        <select wire:model.live="dateMode" class="{{ $input }} sm:w-44">
                            <option value="semua">Semua tanggal</option>
                            <option value="terbaru">Terbaru</option>
                            <option value="terlama">Terlama</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- DONE --}}
            <section class="{{ $card }}">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-emerald-600 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Done</h3>
                            <p class="text-sm text-gray-500">Booking yang telah selesai</p>
                        </div>
                    </div>
                    <span class="text-[11px] px-2 py-1 rounded-full bg-green-100 text-green-800">Completed</span>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($doneRows as $row)
                        @php
                            $isOnline   = in_array($row->booking_type, ['onlinemeeting','online_meeting']);
                            $isRoomType = in_array($row->booking_type, ['bookingroom','meeting']);
                            $stateKey   = $row->deleted_at ? 'trash' : 'ok';
                            $avatarChar = strtoupper(substr($row->meeting_title ?? '—', 0, 1));
                        @endphp
                        <div wire:key="done-{{ $row->bookingroom_id }}-{{ $stateKey }}" class="px-6 py-5 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                {{-- LEFT --}}
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
                                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-red-100 text-red-800">Deleted</span>
                                            @endif
                                        </div>

                                        <div class="flex flex-wrap items-center gap-4 text-[13px] text-gray-600">
                                            {{-- DATE --}}
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ fmtDate($row->date) }}
                                            </span>
                                            {{-- TIME RANGE --}}
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
                                                    <span class="font-medium text-gray-700">Room: {{ optional($row->room)->room_name ?? '—' }}</span>
                                                </span>
                                            @else
                                                <span class="{{ $chip }}">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2" />
                                                    </svg>
                                                    <span class="font-medium text-gray-700">Provider: {{ ucfirst(str_replace('_', ' ', $row->online_provider ?? '—')) }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- RIGHT --}}
                                <div class="text-right shrink-0 space-y-2">
                                    <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                        <button type="button" wire:click="edit({{ $row->bookingroom_id }})" wire:loading.attr="disabled" class="{{ $btnBlk }}">
                                            Edit
                                        </button>

                                        @if(!$row->deleted_at)
                                            <button
                                                type="button"
                                                wire:click="destroy({{ $row->bookingroom_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="destroy"
                                                class="px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition">
                                                Delete
                                            </button>
                                        @else
                                            <button
                                                type="button"
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
                        <div class="px-6 py-14 text-center text-gray-500 text-sm">No data</div>
                    @endforelse
                </div>

                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $doneRows->onEachSide(1)->links() }}
                    </div>
                </div>
            </section>

            {{-- REJECTED --}}
            <section class="{{ $card }}">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-rose-600 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Rejected</h3>
                            <p class="text-sm text-gray-500">Booking yang ditolak</p>
                        </div>
                    </div>
                    <span class="text-[11px] px-2 py-1 rounded-full bg-red-100 text-red-800">Declined</span>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($rejectedRows as $row)
                        @php
                            $isOnline   = in_array($row->booking_type, ['onlinemeeting','online_meeting']);
                            $isRoomType = in_array($row->booking_type, ['bookingroom','meeting']);
                            $stateKey   = $row->deleted_at ? 'trash' : 'ok';
                            $avatarChar = strtoupper(substr($row->meeting_title ?? '—', 0, 1));
                        @endphp
                        <div wire:key="rej-{{ $row->bookingroom_id }}-{{ $stateKey }}" class="px-6 py-5 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                {{-- LEFT --}}
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="{{ $icoAvatar }}">{{ $avatarChar }}</div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-gray-900 text-base truncate">{{ $row->meeting_title ?? '—' }}</h4>
                                            <span class="text-[11px] px-2 py-0.5 rounded-full border {{ $isOnline ? 'border-blue-300 text-blue-700 bg-blue-50' : 'border-gray-300 text-gray-700 bg-gray-50' }}">{{ strtoupper($row->booking_type) }}</span>
                                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-red-100 text-red-800">Rejected</span>
                                            @if($row->deleted_at)
                                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-red-100 text-red-800">Deleted</span>
                                            @endif
                                        </div>

                                        <div class="flex flex-wrap items-center gap-4 text-[13px] text-gray-600">
                                            {{-- DATE --}}
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ fmtDate($row->date) }}
                                            </span>
                                            {{-- TIME RANGE --}}
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
                                                    <span class="font-medium text-gray-700">Room: {{ optional($row->room)->room_name ?? '—' }}</span>
                                                </span>
                                            @else
                                                <span class="{{ $chip }}">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2" />
                                                    </svg>
                                                    <span class="font-medium text-gray-700">Provider: {{ ucfirst(str_replace('_', ' ', $row->online_provider ?? '—')) }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- RIGHT --}}
                                <div class="text-right shrink-0 space-y-2">
                                    <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                        <button type="button" wire:click="edit({{ $row->bookingroom_id }})" wire:loading.attr="disabled" class="{{ $btnBlk }}">
                                            Edit
                                        </button>

                                        @if(!$row->deleted_at)
                                            <button
                                                type="button"
                                                wire:click="destroy({{ $row->bookingroom_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="destroy"
                                                class="px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition">
                                                Delete
                                            </button>
                                        @else
                                            <button
                                                type="button"
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
                        <div class="px-6 py-14 text-center text-gray-500 text-sm">No data</div>
                    @endforelse
                </div>

                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $rejectedRows->onEachSide(1)->links() }}
                    </div>
                </div>
            </section>
        </div>
    </main>

    {{-- Modal (unchanged) --}}
    @if($showModal)
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-2xl bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="font-semibold">{{ $modalMode === 'create' ? 'Create' : 'Edit' }} History Item</h3>
                        <button type="button" class="text-gray-500 hover:text-gray-700" wire:click="$set('showModal', false)">×</button>
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
                            @error('form.meeting_title') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
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
                                <label class="{{ $label }}">Room (Name)</label>
                                <select class="{{ $input }}" wire:model.live="form.room_id">
                                    <option value="">— Select room —</option>
                                    @foreach(($rooms ?? []) as $r)
                                        <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('form.room_id') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        @else
                            <div>
                                <label class="{{ $label }}">Online Provider</label>
                                <select class="{{ $input }}" wire:model.live="form.online_provider">
                                    <option value="zoom">Zoom</option>
                                    <option value="google_meet">Google Meet</option>
                                </select>
                                @error('form.online_provider') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="{{ $label }}">Notes</label>
                            <textarea class="{{ $input }} !h-auto resize-none" rows="3" wire:model.live="form.notes"></textarea>
                        </div>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-200 flex items-center justify-end gap-2">
                        <button type="button" wire:click="$set('showModal', false)" wire:loading.attr="disabled"
                                class="h-10 px-4 rounded-xl bg-rose-600 text-white text-sm font-medium hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition">
                            Cancel
                        </button>
                        <button type="button" wire:click="save" wire:loading.attr="disabled" class="{{ $btnBlk }}">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
