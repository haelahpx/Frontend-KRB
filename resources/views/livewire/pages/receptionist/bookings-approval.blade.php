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

    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $input = 'h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btn = 'px-3 py-2 text-xs font-medium rounded-lg focus:outline-none disabled:opacity-60 transition';
@endphp

<div class="bg-gray-50" wire:poll.3s>
    <main class="px-4 sm:px-6 py-6 space-y-8">
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
                        <h2 class="text-lg sm:text-xl font-semibold">Booking Approval</h2>
                        <p class="text-sm text-white/80">Pending → Approved (On Going). Auto-complete after end time.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTERS --}}
        <section class="{{ $card }}">
            <div class="p-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Filter</h3>
                        <p class="text-sm text-gray-500">Cari judul & pilih tanggal.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full md:w-auto">
                        <input type="text" wire:model.live="q" placeholder="Search title…"
                            class="{{ $input }} w-full sm:w-64" />
                        <input type="date" wire:model.live="selected_date" class="{{ $input }} w-full sm:w-48" />
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- PENDING --}}
            <section class="{{ $card }}">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                    <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Pending</h3>
                        <p class="text-sm text-gray-500">Data baru dari MeetingSchedule/User muncul di sini.</p>
                    </div>
                </div>

                <div class="p-5 space-y-3">
                    @forelse($pending as $b)
                        @php $isOnline = $b->booking_type === 'online_meeting'; @endphp
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 bg-gray-900 rounded-xl text-white text-sm font-semibold flex items-center justify-center">
                                    {{ strtoupper(substr((string) $b->meeting_title, 0, 1)) }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <div class="font-semibold text-gray-900">
                                            #{{ $b->bookingroom_id }} — {{ $b->meeting_title }}
                                        </div>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                            {{ strtoupper($b->booking_type) }}
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-medium bg-amber-100 text-amber-800">
                                            Pending
                                        </span>
                                    </div>

                                    <div class="mt-1 text-[12px] text-gray-600 flex flex-wrap items-center gap-2">
                                        <span>{{ fmtDate($b->date) }}</span>
                                        <span>•</span>
                                        <span>{{ fmtTime($b->start_time) }}–{{ fmtTime($b->end_time) }}</span>
                                        @if($isOnline && $b->online_provider)
                                            <span>•</span><span>Provider:
                                                {{ ucfirst(str_replace('_', ' ', $b->online_provider)) }}</span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <button wire:click="approve({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                            class="{{ $btn }} bg-emerald-600 text-white hover:bg-emerald-700">
                                            <span wire:loading.remove
                                                wire:target="approve({{ $b->bookingroom_id }})">Approve → On Going</span>
                                            <span wire:loading
                                                wire:target="approve({{ $b->bookingroom_id }})">Processing…</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 text-sm">Tidak ada data pending.</div>
                    @endforelse

                    {{-- Pagination for Pending --}}
                    <div class="mt-4">
                        {{ $pending->links() }}
                    </div>
                </div>
            </section>

            {{-- ON GOING (Approved) --}}
            <section class="{{ $card }}">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                    <div class="w-2 h-2 bg-emerald-600 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">On Going</h3>
                        <p class="text-sm text-gray-500">Data yang sudah di-approve (status = approved).</p>
                    </div>
                </div>

                <div class="p-5 space-y-3">
                    @forelse($onGoing as $b)
                        @php $isOnline = $b->booking_type === 'online_meeting'; @endphp
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 bg-gray-900 rounded-xl text-white text-sm font-semibold flex items-center justify-center">
                                    {{ strtoupper(substr((string) $b->meeting_title, 0, 1)) }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <div class="font-semibold text-gray-900">
                                            #{{ $b->bookingroom_id }} — {{ $b->meeting_title }}
                                        </div>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                            {{ strtoupper($b->booking_type) }}
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-medium bg-emerald-100 text-emerald-800">
                                            On Going
                                        </span>
                                    </div>

                                    <div class="mt-1 text-[12px] text-gray-600 flex flex-wrap items-center gap-2">
                                        <span>{{ fmtDate($b->date) }}</span>
                                        <span>•</span>
                                        <span>{{ fmtTime($b->start_time) }}–{{ fmtTime($b->end_time) }}</span>
                                        @if($isOnline && $b->online_provider)
                                            <span>•</span><span>Provider:
                                                {{ ucfirst(str_replace('_', ' ', $b->online_provider)) }}</span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <button wire:click="sendBack({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                            class="{{ $btn }} bg-amber-600 text-white hover:bg-amber-700">
                                            <span wire:loading.remove wire:target="sendBack({{ $b->bookingroom_id }})">Send
                                                Back (Pending)</span>
                                            <span wire:loading
                                                wire:target="sendBack({{ $b->bookingroom_id }})">Processing…</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 text-sm">Belum ada yang On Going.</div>
                    @endforelse

                    {{-- Pagination for On Going --}}
                    <div class="mt-4">
                        {{ $onGoing->links() }}
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>