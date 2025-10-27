{{-- resources/views/livewire/pages/booking-history.blade.php --}}
@php
    use Carbon\Carbon;

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
                return $v ? Carbon::parse($v)->format('H:i') : '—';
            } catch (\Throwable) {
                return (is_string($v) && preg_match('/^\d{2}:\d{2}/', $v)) ? substr($v, 0, 5) : '—';
            }
        }
    }

    // Theme tokens
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
@endphp

<div class="bg-gray-50">
    <main class="px-4 sm:px-6 py-6 space-y-8">

        {{-- Gradient Hero --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Booking History</h2>
                            <p class="text-sm text-white/80">Kelola riwayat booking yang selesai dan ditolak.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <label class="inline-flex items-center gap-2 text-sm text-white/90">
                            <input type="checkbox" wire:model.live="withTrashed"
                                class="rounded border-white/30 bg-white/10">
                            <span>Include deleted</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Bar --}}
        <section class="{{ $card }}">
            <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="space-y-1">
                    <h3 class="text-base font-semibold text-gray-900">Filter</h3>
                    <p class="text-sm text-gray-500">Cari judul dan tanggal pengajuan.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <input type="date" wire:model.live="selectedDate" class="{{ $input }}" />

                    {{-- Mode tanggal [semua, terbaru, terlama] --}}
                    <select wire:model.live="dateMode" class="{{ $input }}">
                        <option value="semua">Semua tanggal</option>
                        <option value="terbaru">Terbaru</option>
                        <option value="terlama">Terlama</option>
                    </select>
                </div>
            </div>
        </section>

        {{-- TWO BOXES SIDE BY SIDE --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- DONE BOX --}}
            <section class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Done</h3>
                        <p class="text-sm text-gray-500">Booking yang telah selesai</p>
                    </div>
                    <span class="text-[11px] px-2 py-1 rounded-full bg-green-100 text-green-800">Completed</span>
                </div>

                <div class="px-5 py-4 border-b border-gray-200">
                    <input type="text" wire:model.live="qDone" placeholder="Search title..." class="{{ $input }}" />
                </div>

                <div class="p-5 grid grid-cols-1 gap-4">
                    @forelse($doneRows as $row)
                        @php $isOnline = ($row->booking_type === 'onlinemeeting'); @endphp
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-gray-900">
                                        #{{ $row->bookingroom_id }} — {{ $row->meeting_title ?? '—' }}
                                    </span>
                                    <span
                                        class="text-[11px] px-2 py-0.5 rounded-full border
                                                {{ $isOnline ? 'border-blue-300 text-blue-700 bg-blue-50' : 'border-gray-300 text-gray-700 bg-gray-50' }}">
                                        {{ strtoupper($row->booking_type) }}
                                    </span>
                                    <span
                                        class="text-[11px] px-2 py-0.5 rounded-full bg-green-100 text-green-800">Done</span>
                                    @if($row->deleted_at)
                                        <span
                                            class="text-[11px] px-2 py-0.5 rounded-full bg-red-100 text-red-800">Deleted</span>
                                    @endif
                                </div>

                                <div class="text-sm text-gray-600">
                                    @php
                                        $st = is_string($row->start_time) ? substr($row->start_time, 0, 5) : $row->start_time;
                                        $et = is_string($row->end_time) ? substr($row->end_time, 0, 5) : $row->end_time;
                                    @endphp
                                    {{ $row->date ?: '—' }} • {{ ($st ?: '—') }}–{{ ($et ?: '—') }}
                                    @if($row->booking_type === 'bookingroom')
                                        • Room: {{ $row->room_id ?? '—' }}
                                    @else
                                        • Provider: {{ ucfirst(str_replace('_', ' ', $row->online_provider ?? '—')) }}
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 pt-2">
                                    <button
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 bg-white hover:bg-gray-50 transition"
                                        wire:click="edit({{ $row->bookingroom_id }})">
                                        Edit
                                    </button>

                                    @if(!$row->deleted_at)
                                        <button
                                            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-red-300 text-red-700 bg-white hover:bg-red-50 transition"
                                            wire:click="destroy({{ $row->bookingroom_id }})">
                                            Delete
                                        </button>
                                    @else
                                        <button
                                            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-green-300 text-green-700 bg-white hover:bg-green-50 transition"
                                            wire:click="restore({{ $row->bookingroom_id }})">
                                            Restore
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">No data</p>
                    @endforelse
                </div>

                <div class="px-5 pb-5">
                    {{ $doneRows->onEachSide(1)->links() }}
                </div>
            </section>

            {{-- REJECTED BOX --}}
            <section class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Rejected</h3>
                        <p class="text-sm text-gray-500">Booking yang ditolak</p>
                    </div>
                    <span class="text-[11px] px-2 py-1 rounded-full bg-red-100 text-red-800">Declined</span>
                </div>

                <div class="px-5 py-4 border-b border-gray-200">
                    <input type="text" wire:model.live="qRejected" placeholder="Search title..." class="{{ $input }}" />
                </div>

                <div class="p-5 grid grid-cols-1 gap-4">
                    @forelse($rejectedRows as $row)
                        @php $isOnline = ($row->booking_type === 'onlinemeeting'); @endphp
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-gray-900">
                                        #{{ $row->bookingroom_id }} — {{ $row->meeting_title ?? '—' }}
                                    </span>
                                    <span
                                        class="text-[11px] px-2 py-0.5 rounded-full border
                                                {{ $isOnline ? 'border-blue-300 text-blue-700 bg-blue-50' : 'border-gray-300 text-gray-700 bg-gray-50' }}">
                                        {{ strtoupper($row->booking_type) }}
                                    </span>
                                    <span
                                        class="text-[11px] px-2 py-0.5 rounded-full bg-red-100 text-red-800">Rejected</span>
                                    @if($row->deleted_at)
                                        <span
                                            class="text-[11px] px-2 py-0.5 rounded-full bg-red-100 text-red-800">Deleted</span>
                                    @endif
                                </div>

                                <div class="text-sm text-gray-600">
                                    @php
                                        $st = is_string($row->start_time) ? substr($row->start_time, 0, 5) : $row->start_time;
                                        $et = is_string($row->end_time) ? substr($row->end_time, 0, 5) : $row->end_time;
                                    @endphp
                                    {{ $row->date ?: '—' }} • {{ ($st ?: '—') }}–{{ ($et ?: '—') }}
                                    @if($row->booking_type === 'bookingroom')
                                        • Room: {{ $row->room_id ?? '—' }}
                                    @else
                                        • Provider: {{ ucfirst(str_replace('_', ' ', $row->online_provider ?? '—')) }}
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 pt-2">
                                    <button
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 bg-white hover:bg-gray-50 transition"
                                        wire:click="edit({{ $row->bookingroom_id }})">
                                        Edit
                                    </button>

                                    @if(!$row->deleted_at)
                                        <button
                                            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-red-300 text-red-700 bg-white hover:bg-red-50 transition"
                                            wire:click="destroy({{ $row->bookingroom_id }})">
                                            Delete
                                        </button>
                                    @else
                                        <button
                                            class="px-3 py-1.5 text-xs font-medium rounded-lg border border-green-300 text-green-700 bg-white hover:bg-green-50 transition"
                                            wire:click="restore({{ $row->bookingroom_id }})">
                                            Restore
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-8">No data</p>
                    @endforelse
                </div>

                <div class="px-5 pb-5">
                    {{ $rejectedRows->onEachSide(1)->links() }}
                </div>
            </section>
        </div>
    </main>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-2xl bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="font-semibold">{{ $modalMode === 'create' ? 'Create' : 'Edit' }} History Item</h3>
                        <button class="text-gray-500 hover:text-gray-700" wire:click="$set('showModal', false)">×</button>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="{{ $label }}">Type</label>
                                <select class="{{ $input }}" wire:model.live="form.booking_type">
                                    <option value="bookingroom">Booking Room</option>
                                    <option value="onlinemeeting">Online Meeting</option>
                                </select>
                            </div>
                            <div>
                                <label class="{{ $label }}">Status</label>
                                <select class="{{ $input }}" wire:model.live="form.status">
                                    <option value="done">Done</option>
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

                        @if($form['booking_type'] === 'bookingroom')
                            <div>
                                <label class="{{ $label }}">Room ID</label>
                                <input type="number" class="{{ $input }}" wire:model.live="form.room_id">
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

                        <div>
                            <label class="{{ $label }}">Notes</label>
                            <textarea class="{{ $input }} !h-auto resize-none" rows="3"
                                wire:model.live="form.notes"></textarea>
                        </div>
                    </div>

                    <div class="px-5 py-4 border-t border-gray-200 flex items-center justify-end gap-2">
                        <button
                            class="h-10 px-4 rounded-xl border border-gray-300 bg-white text-sm font-medium hover:bg-gray-50"
                            wire:click="$set('showModal', false)">
                            Cancel
                        </button>
                        <button
                            class="h-10 px-4 rounded-xl bg-gray-900 text-white text-sm font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20"
                            wire:click="save">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>