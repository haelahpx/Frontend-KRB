<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border-2 border-black p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Room Booking</h1>

            <div class="ml-auto flex items-center gap-3">
                {{-- Mode switcher --}}
                <div class="inline-flex rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                    <button wire:click="setMode('meeting')"
                        @class([
                            'px-3 md:px-4 py-2 text-sm font-medium transition-colors border-r border-gray-200 inline-flex items-center gap-2',
                            'bg-gray-900 text-white' => $mode === 'meeting',
                            'text-gray-700 hover:text-gray-900' => $mode !== 'meeting',
                        ])>
                        <x-heroicon-o-building-office-2 class="w-4 h-4"/>
                        Meeting
                    </button>
                    <button wire:click="setMode('online')"
                        @class([
                            'px-3 md:px-4 py-2 text-sm font-medium transition-colors inline-flex items-center gap-2',
                            'bg-gray-900 text-white' => $mode === 'online',
                            'text-gray-700 hover:text-gray-900' => $mode !== 'online',
                        ])>
                        <x-heroicon-o-video-camera class="w-4 h-4"/>
                        Online
                    </button>
                </div>

                {{-- Top tabs --}}
                <div class="inline-flex rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                    <a href="{{ route('bookingstatus') }}"
                        @class([
                            'px-3 md:px-4 py-2 text-sm font-medium transition-colors inline-flex items-center gap-2',
                            'bg-gray-900 text-white' => true,
                        ])>
                        <x-heroicon-o-calendar-days class="w-4 h-4"/>
                        Room Booking
                    </a>
                    <a href="{{ route('vehiclestatus') }}"
                        class="px-3 md:px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors border-l border-gray-200 inline-flex items-center gap-2">
                        <x-heroicon-o-truck class="w-4 h-4"/>
                        Vehicle Booking
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4 md:p-5">
        {{-- Search / Filters header --}}
        <div class="flex flex-col gap-3 md:gap-4 md:flex-row md:items-center md:justify-between pb-3 mb-3 border-b border-gray-200">
            <div class="w-full md:w-80">
                <div class="relative">
                    <input type="text"
                        wire:model.live.debounce.400ms="q"
                        placeholder="Search title / room…"
                        class="w-full h-10 pl-9 pr-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-black/20">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"/>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 mb-3">
            @if($mode === 'meeting')
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Room</label>
                    <select wire:model.live="roomFilter"
                        class="w-full h-10 px-3 border border-gray-300 rounded-md text-sm bg-white focus:outline-none focus:ring-2 focus:ring-black/20">
                        <option value="">All rooms</option>
                        @foreach($rooms as $r)
                            <option value="{{ $r->room_id }}">{{ $r->room_number }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div @class([$mode==='meeting' ? '' : 'md:col-span-2' ])>
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
                    <option value="rejected">Rejected</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>

        <div class="space-y-5">
            @forelse($bookings as $b)
                @php
                    $start = \Carbon\Carbon::parse($b->start_time, 'Asia/Jakarta');
                    $end = \Carbon\Carbon::parse($b->end_time, 'Asia/Jakarta');
                    $dateStr = $start->format('D, M j, Y');
                    $timeStr = $start->format('H:i').'–'.$end->format('H:i');
                    $roomName = $roomMap[$b->room_id] ?? 'Unknown';
                    $statusColor = [
                        'pending'   => 'bg-amber-50 text-amber-700 border-2 border-amber-500',
                        'approved'  => 'bg-emerald-50 text-emerald-700 border-2 border-emerald-500',
                        'rejected'  => 'bg-rose-50 text-rose-700 border-2 border-rose-500',
                        'completed' => 'bg-blue-50 text-blue-700 border-2 border-blue-500',
                    ][$b->status] ?? 'bg-slate-50 text-slate-700 border-2 border-slate-400';
                @endphp

                <div class="relative bg-white rounded-xl border-2 border-black/80 shadow-md p-4 md:p-5 hover:shadow-lg hover:-translate-y-0.5 transition">
                    <div class="flex items-start justify-between gap-4 mb-2">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base md:text-lg font-semibold text-gray-900 truncate">
                                {{ $b->meeting_title ?? 'Untitled' }}
                            </h3>
                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                                <span class="font-mono font-medium text-gray-800 inline-flex items-center gap-1">
                                    <x-heroicon-o-hashtag class="w-3.5 h-3.5"/> {{ $b->bookingroom_id }}
                                </span>

                                @if($mode === 'meeting' && $b->room_id)
                                    <span class="text-gray-300">•</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 border-2 border-gray-400">
                                        <x-heroicon-o-building-office-2 class="w-3.5 h-3.5"/>
                                        Room {{ $roomName }}
                                    </span>
                                @endif

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

                                @if($mode === 'online' && !empty($b->online_provider))
                                    <span class="text-gray-300">•</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 border-2 border-gray-400">
                                        <x-heroicon-o-video-camera class="w-3.5 h-3.5"/>
                                        {{ ucfirst($b->online_provider) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium {{ $statusColor }}">
                                <x-heroicon-o-check-badge class="w-4 h-4"/>
                                {{ ucfirst($b->status) }}
                            </span>
                        </div>
                    </div>

                    @if(!empty($b->special_notes))
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ $b->special_notes }}
                        </p>
                    @endif

                    @if($b->status === 'rejected' && !empty($b->book_reject))
                        <div class="mt-3 rounded-lg border-2 border-rose-400 bg-rose-50 p-3">
                            <div class="text-xs font-semibold text-rose-700 inline-flex items-center gap-1">
                                <x-heroicon-o-no-symbol class="w-4 h-4"/>
                                Reject / Cancel Reason
                            </div>
                            <div class="mt-1 text-sm text-rose-800">{{ $b->book_reject }}</div>
                        </div>
                    @elseif(!empty($b->book_reject))
                        <div class="mt-3 rounded-lg border-2 border-amber-400 bg-amber-50 p-3">
                            <div class="text-xs font-semibold text-amber-700 inline-flex items-center gap-1">
                                <x-heroicon-o-information-circle class="w-4 h-4"/>
                                Note
                            </div>
                            <div class="mt-1 text-sm text-amber-800">{{ $b->book_reject }}</div>
                        </div>
                    @endif

                    @if($mode === 'online')
                        <div class="mt-3 text-xs text-gray-600 space-y-1">
                            @if(!empty($b->online_provider))
                                <div class="inline-flex items-center gap-1">
                                    <x-heroicon-o-video-camera class="w-4 h-4"/>
                                    <span class="font-semibold">Provider:</span> {{ ucfirst($b->online_provider) }}
                                </div>
                            @endif
                            @if(!empty($b->online_meeting_url))
                                <div class="truncate inline-flex items-center gap-1">
                                    <x-heroicon-o-link class="w-4 h-4"/>
                                    <span class="font-semibold">URL:</span>
                                    <a class="underline text-blue-600" href="{{ $b->online_meeting_url }}" target="_blank" rel="noopener">Open link</a>
                                </div>
                            @endif
                            @if(!empty($b->online_meeting_code))
                                <div class="inline-flex items-center gap-1">
                                    <x-heroicon-o-hashtag class="w-4 h-4"/>
                                    <span class="font-semibold">Code:</span> {{ $b->online_meeting_code }}
                                </div>
                            @endif
                            @if(!empty($b->online_meeting_password))
                                <div class="inline-flex items-center gap-1">
                                    <x-heroicon-o-key class="w-4 h-4"/>
                                    <span class="font-semibold">Password:</span> {{ $b->online_meeting_password }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="mt-3 text-[11px] text-gray-500 inline-flex items-center gap-2">
                        <x-heroicon-o-calendar class="w-3.5 h-3.5"/>
                        <span>Created: {{ optional($b->created_at)->format('Y-m-d H:i') }}</span>
                        <span class="mx-2">•</span>
                        <x-heroicon-o-arrow-path class="w-3.5 h-3.5"/>
                        <span>Updated: {{ optional($b->updated_at)->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
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
