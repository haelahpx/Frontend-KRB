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
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zm7-6v3l2 2" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Bookings Approval</h2>
                        <p class="text-sm text-white/80">Kelola pengajuan, setujui/ tolak, dan lihat link meeting.</p>
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

        {{-- Google connect notice --}}
        @if(!$this->googleConnected)
            <div class="rounded-2xl border border-yellow-300 bg-yellow-50/70 text-yellow-900 p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div><strong>Google not connected.</strong> Connect to enable automatic Google Meet creation.</div>
                    <a href="{{ route('google.connect') }}"
                        class="inline-flex items-center justify-center h-10 px-4 rounded-xl border border-gray-300 bg-white text-sm font-medium hover:bg-gray-50">
                        Connect Google
                    </a>
                </div>
            </div>
        @endif

        {{-- TWO BOXES --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Pending --}}
            <section class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Pending</h3>
                        <p class="text-sm text-gray-500">Menunggu persetujuan</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800">Queue</span>
                </div>

                <div class="px-5 py-4 border-b border-gray-200">
                    <input type="text" wire:model.live="qDone" placeholder="Search title..." class="{{ $input }}" />
                </div>

                <div class="p-5 grid grid-cols-1 gap-4">
                    @forelse($pending as $b)
                        @php $isOnline = ($b->booking_type === 'online_meeting'); @endphp
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                {{-- Left info --}}
                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-semibold text-gray-900">
                                            #{{ $b->bookingroom_id }} — {{ $b->meeting_title }}
                                        </span>
                                        <span
                                            class="text-[11px] px-2 py-0.5 rounded-full border
                                                {{ $isOnline ? 'border-blue-300 text-blue-700 bg-blue-50' : 'border-gray-300 text-gray-700 bg-gray-50' }}">
                                            {{ strtoupper($b->booking_type) }}
                                        </span>
                                        <span
                                            class="text-[11px] px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    </div>

                                    <div class="text-sm text-gray-600">
                                        {{ fmtDate($b->date) }} • {{ fmtTime($b->start_time) }}–{{ fmtTime($b->end_time) }}
                                        @if($isOnline && $b->online_provider)
                                            • Provider: {{ ucfirst(str_replace('_', ' ', $b->online_provider)) }}
                                        @elseif(!$isOnline && $b->room_id)
                                            • Room: {{ $b->room_id }}
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions (ONLY IN PENDING) --}}
                                <div class="flex items-center gap-2">
                                    <button wire:click="approve({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                        class="{{ $btnBlk }}">
                                        Approve
                                    </button>
                                    <button wire:click="openReject({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                        class="px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Tidak ada data pending.</p>
                    @endforelse
                </div>

                <div class="px-5 pb-5">
                    {{ $pending->links() }}
                </div>
            </section>

            {{-- On-Going (approved) --}}
            <section class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">On-Going</h3>
                        <p class="text-sm text-gray-500">Sudah disetujui</p>
                    </div>
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-green-100 text-green-800">Live</span>
                </div>

                <div class="px-5 py-4 border-b border-gray-200">
                    <input type="text" wire:model.live="qDone" placeholder="Search title..." class="{{ $input }}" />
                </div>

                <div class="p-5 grid grid-cols-1 gap-4">
                    @forelse($ongoing as $b)
                        @php $isOnline = ($b->booking_type === 'online_meeting'); @endphp
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-semibold text-gray-900">
                                            #{{ $b->bookingroom_id }} — {{ $b->meeting_title }}
                                        </span>
                                        <span
                                            class="text-[11px] px-2 py-0.5 rounded-full border
                                                {{ $isOnline ? 'border-blue-300 text-blue-700 bg-blue-50' : 'border-gray-300 text-gray-700 bg-gray-50' }}">
                                            {{ strtoupper($b->booking_type) }}
                                        </span>
                                        <span
                                            class="text-[11px] px-2 py-0.5 rounded-full bg-green-100 text-green-800">On-Going</span>
                                    </div>

                                    <div class="text-sm text-gray-600">
                                        {{ fmtDate($b->date) }} • {{ fmtTime($b->start_time) }}–{{ fmtTime($b->end_time) }}
                                        @if($isOnline && $b->online_provider)
                                            • Provider: {{ ucfirst(str_replace('_', ' ', $b->online_provider)) }}
                                        @elseif(!$isOnline && $b->room_id)
                                            • Room: {{ $b->room_id }}
                                        @endif
                                    </div>

                                    {{-- Online link if approved --}}
                                    @if($isOnline && $b->online_meeting_url)
                                        <div class="text-sm">
                                            <a href="{{ $b->online_meeting_url }}" target="_blank"
                                                class="text-blue-600 underline">Meeting Link</a>
                                            @if($b->online_meeting_code)
                                                <span class="text-gray-600 ml-2">Code: <span
                                                        class="font-medium">{{ $b->online_meeting_code }}</span></span>
                                            @endif
                                            @if($b->online_meeting_password)
                                                <span class="text-gray-600 ml-2">Password: <span
                                                        class="font-medium">{{ $b->online_meeting_password }}</span></span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- No buttons here --}}
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Tidak ada data on-going.</p>
                    @endforelse
                </div>

                <div class="px-5 pb-5">
                    {{ $ongoing->links() }}
                </div>
            </section>
        </div>
    </main>

    {{-- Livewire-only Reject Modal --}}
    @if($showRejectModal)
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-lg bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="font-semibold">Reject Booking</h3>
                        <button class="text-gray-500 hover:text-gray-700"
                            wire:click="$set('showRejectModal', false)">×</button>
                    </div>
                    <div class="p-5 space-y-4">
                        <p class="text-sm text-gray-600">
                            Berikan alasan penolakan. Alasan ini akan tersimpan dan dapat dilihat di halaman user.
                        </p>
                        <textarea wire:model.defer="rejectReason" rows="4" class="{{ $input }} !h-auto resize-none"
                            placeholder="Contoh: Jadwal bertabrakan dengan kegiatan penting lain."></textarea>
                        @error('rejectReason')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="px-5 py-4 border-t border-gray-200 flex items-center justify-end gap-2">
                        <button
                            class="h-10 px-4 rounded-xl border border-gray-300 bg-white text-sm font-medium hover:bg-gray-50"
                            wire:click="$set('showRejectModal', false)">Cancel</button>
                        <button
                            class="h-10 px-4 rounded-xl bg-rose-600 text-white text-sm font-medium hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20"
                            wire:click="confirmReject" wire:loading.attr="disabled">
                            Save Reason & Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>