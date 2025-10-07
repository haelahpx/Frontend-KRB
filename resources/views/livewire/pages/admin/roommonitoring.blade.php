<div class="bg-gray-50">
@php
    $card   = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label  = 'block text-sm font-medium text-gray-700 mb-2';
    $input  = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnLt  = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition';
    $chip   = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-[11px]';
    $mono   = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $ico    = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';

    $statusPill = [
        'planned' => 'inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-100 text-blue-900 text-[11px]',
        'ongoing' => 'inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 text-[11px]',
        'done'    => 'inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-900 text-[11px]',
    ];
@endphp

<main class="px-4 sm:px-6 py-6 space-y-8">
    {{-- HERO --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
        <div class="relative z-10 p-6 sm:p-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-semibold">Room Monitoring</h2>
                    <p class="text-sm text-white/80">
                        Department: <span class="font-semibold">{{ $lockedDeptName }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="{{ $card }}">
        <div class="px-5 py-4 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                {{-- Search --}}
                <div class="relative flex-1">
                    <input type="text" wire:model.live="q" placeholder="Search meeting title or notes..."
                           class="{{ $input }} pl-10 w-full placeholder:text-gray-400">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                        </svg>
                    </span>
                </div>

                {{-- Room --}}
                <div class="relative">
                    <select wire:model.live="room_id" class="{{ $input }} pl-10 w-full lg:w-56">
                        <option value="">All Rooms</option>
                        @foreach($rooms as $r)
                            <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Department --}}
                <div class="relative">
                    <select wire:model.live="department_id" class="{{ $input }} pl-10 w-full lg:w-56">
                        <option value="">All Departments</option>
                        @foreach($departments as $d)
                            <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="relative">
                    <select wire:model.live="status" class="{{ $input }} pl-10 w-full lg:w-44">
                        <option value="">All Status</option>
                        <option value="planned">Planned</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="done">Done</option>
                    </select>
                </div>

                {{-- Date From (dd/mm/yyyy) --}}
                <div>
                    <input type="text" wire:model.live="date_from_ui" placeholder="dd/mm/yyyy" class="{{ $input }} w-40" inputmode="numeric">
                    @error('date_from_ui') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Date To (dd/mm/yyyy) --}}
                <div>
                    <input type="text" wire:model.live="date_to_ui" placeholder="dd/mm/yyyy" class="{{ $input }} w-40" inputmode="numeric">
                    @error('date_to_ui') <div class="text-xs text-rose-600 mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Per page --}}
                <div class="relative">
                    <select wire:model.live="perPage" class="{{ $input }} pl-10 w-full lg:w-40">
                        <option value="10">10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- QUICK RANGE --}}
        <div class="px-5 py-4">
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="{{ $btnLt }}"  wire:click.prevent="setQuickRange('all')"   wire:loading.attr="disabled">All Dates</button>
                <button type="button" class="{{ $btnBlk }}" wire:click.prevent="setQuickRange('today')" wire:loading.attr="disabled">Today</button>
                <button type="button" class="{{ $btnLt }}"  wire:click.prevent="setQuickRange('week')"  wire:loading.attr="disabled">This Week</button>
            </div>
        </div>

        {{-- LIST --}}
        <div class="divide-y divide-gray-200" wire:key="rm-list">
            @forelse ($rows as $r)
                <div class="px-5 py-5 hover:bg-gray-50 transition-colors" wire:key="rm-{{ $r['id'] }}">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1">
                            <div class="{{ $ico }}">{{ strtoupper(substr($r['meeting_title'] ?? 'M', 0, 1)) }}</div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $r['meeting_title'] ?? '—' }}</h4>
                                    <span class="{{ $statusPill[$r['status']] ?? $statusPill['planned'] }}">{{ ucfirst($r['status'] ?? 'planned') }}</span>
                                </div>
                                <p class="text-[12px] text-gray-500">By: {{ $r['requested_by'] ?? '—' }}</p>
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="{{ $chip }}"><span class="text-gray-500">Room:</span><span class="font-medium text-gray-700">{{ $r['room'] ?? '—' }}</span></span>
                                    <span class="{{ $chip }}"><span class="text-gray-500">Dept:</span><span class="font-medium text-gray-700">{{ $r['department'] ?? '—' }}</span></span>
                                    <span class="{{ $chip }}"><span class="text-gray-500">Date:</span><span class="font-medium text-gray-700">{{ $r['date'] ? \Illuminate\Support\Carbon::parse($r['date'])->format('d M Y') : '—' }}</span></span>
                                    <span class="{{ $chip }}"><span class="text-gray-500">Time:</span><span class="font-medium text-gray-700">{{ ($r['start'] ?? '—') }}–{{ ($r['end'] ?? '—') }}</span></span>
                                    <span class="{{ $chip }}"><span class="text-gray-500">Att:</span><span class="font-medium text-gray-700">{{ $r['attendees'] ?? '—' }}</span></span>
                                </div>
                                @if(!empty($r['notes']))
                                    <div class="mt-2 text-[12px] text-gray-600 line-clamp-2" title="{{ $r['notes'] }}">{{ $r['notes'] }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="text-right shrink-0 space-y-2">
                            <div class="{{ $mono }}">#{{ $r['id'] }}</div>
                            <button type="button" class="{{ $btnLt }}" wire:click.prevent="openDetail({{ $r['id'] }})" wire:loading.attr="disabled">Detail</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-14 text-center text-gray-500 text-sm">No bookings found.</div>
            @endforelse
        </div>

        @if($rows->hasPages())
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-center">
                    {{ $rows->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- DETAIL MODAL (Livewire only) --}}
    @if ($showDetail && $detail)
        <div wire:key="rm-modal-{{ $detail['id'] ?? 'x' }}" class="fixed inset-0 z-[60] flex items-center justify-center" role="dialog" aria-modal="true">
            <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay" wire:click.prevent="closeDetail"></button>

            <div class="relative w-full max-w-3xl mx-4 {{ $card }}" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Detail Booking</h3>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click.prevent="closeDetail" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><div class="text-gray-500 text-xs">Meeting Title</div><div class="font-medium">{{ $detail['meeting_title'] ?? '—' }}</div></div>
                    <div><div class="text-gray-500 text-xs">Status</div><span class="{{ $statusPill[$detail['status'] ?? 'planned'] ?? $statusPill['planned'] }}">{{ ucfirst($detail['status'] ?? 'planned') }}</span></div>

                    <div>
                        <div class="text-gray-500 text-xs">Room</div>
                        <div class="font-medium">{{ $detail['room'] ?? '—' }}</div>
                        @if(!empty($detail['room_meta']))
                            <div class="text-[11px] text-gray-500 mt-1">{{ $detail['room_meta'] }}</div>
                        @endif
                    </div>
                    <div><div class="text-gray-500 text-xs">Department</div><div class="font-medium">{{ $detail['department'] ?? '—' }}</div></div>

                    <div><div class="text-gray-500 text-xs">Date</div><div class="font-medium">{{ $detail['date'] ?? '—' }}</div></div>
                    <div><div class="text-gray-500 text-xs">Time</div><div class="font-medium">{{ ($detail['start'] ?? '—') . '–' . ($detail['end'] ?? '—') }}</div></div>

                    <div><div class="text-gray-500 text-xs">Requested By</div><div class="font-medium">{{ $detail['requested_by'] ?? '—' }}</div></div>
                    <div><div class="text-gray-500 text-xs">Attendees</div><div class="font-medium">{{ $detail['attendees'] ?? '—' }}</div></div>

                    <div class="sm:col-span-2">
                        <div class="text-gray-500 text-xs">Requirements</div>
                        @if(!empty($detail['requirements']) && count($detail['requirements']))
                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach($detail['requirements'] as $req)
                                    <span class="{{ $chip }}">{{ $req }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="font-medium">—</div>
                        @endif
                    </div>

                    <div class="sm:col-span-2">
                        <div class="text-gray-500 text-xs">Notes</div>
                        <div class="font-medium whitespace-pre-line">{{ $detail['notes'] ?: '—' }}</div>
                    </div>

                    <div class="sm:col-span-2">
                        <div class="text-gray-500 text-xs mb-1">Summary (select & copy)</div>
                        <textarea class="w-full h-28 p-3 border border-gray-300 rounded-lg text-xs" readonly>@foreach(($detail['summary_lines'] ?? []) as $line)
{{ $line }}
@endforeach</textarea>
                    </div>
                </div>

                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                    <button type="button" class="{{ $btnLt }}" wire:click.prevent="closeDetail">Close</button>
                </div>
            </div>
        </div>
    @endif
</main>
</div>
