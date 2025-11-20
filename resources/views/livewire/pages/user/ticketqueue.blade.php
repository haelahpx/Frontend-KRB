<div class="max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm border-2 border-black p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Support Ticket Queue</h1>

            {{-- Navigation Tabs --}}
            <div class="inline-flex rounded-lg overflow-hidden bg-gray-100 border border-gray-200 self-start md:self-center">
                <button
                    type="button"
                    wire:click="$set('tab','queue')"
                    @class([ 'px-3 md:px-4 py-2 text-sm font-medium transition-colors border-r border-gray-200' , 'bg-gray-900 text-white'=> $tab === 'queue',
                    'text-gray-700 hover:text-gray-900 hover:bg-gray-50' => $tab !== 'queue',
                    ])>
                    Ticket Queue
                </button>
                <button
                    type="button"
                    wire:click="$set('tab','claims')"
                    @class([ 'px-3 md:px-4 py-2 text-sm font-medium transition-colors' , 'bg-gray-900 text-white'=> $tab === 'claims',
                    'text-gray-700 hover:text-gray-900 hover:bg-gray-50' => $tab !== 'claims',
                    ])>
                    My Claims
                </button>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="bg-white rounded-xl shadow-sm border-2 border-black p-4 md:p-5 min-h-[600px]">
        {{-- QUEUE TAB --}}
        @if ($tab === 'queue')
        <div wire:key="queue-tab-container">
            <div class="flex flex-col md:flex-row md:items-center gap-4 pb-4 mb-4 border-b border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full">
                    {{-- Search --}}
                    <div class="md:col-span-2">
                        <div class="relative">
                            <input
                                type="text"
                                wire:model.debounce.400ms="search"
                                placeholder="Search subject or description..."
                                class="w-full pl-9 px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <select wire:model.live="status" class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="OPEN">Open</option>
                        <option value="IN_PROGRESS">In Progress</option>
                        <option value="RESOLVED">Resolved</option>
                        <option value="CLOSED">Closed</option>
                    </select>

                    {{-- Priority Filter --}}
                    <select wire:model.live="priority" class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        <option value="">All Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>

            @if(!$tickets || $tickets->isEmpty())
            <div class="rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No tickets found</h3>
                <p class="mt-1 text-sm text-gray-500">Tidak ada tiket yang cocok dengan filter Anda.</p>
            </div>
            @else
            <div class="space-y-4">
                @foreach ($tickets as $t)
                @php
                $priority = strtolower($t->priority ?? '');
                $statusUp = strtoupper($t->status ?? 'OPEN');
                $statusLabel = ucfirst(strtolower(str_replace('_',' ',$statusUp)));

                $isHigh = $priority === 'high';
                $isMedium = $priority === 'medium';

                // Badge Styles
                $priorityBadge = $isHigh
                ? 'bg-orange-50 text-orange-800 border-orange-200'
                : ($isMedium
                ? 'bg-yellow-50 text-yellow-800 border-yellow-200'
                : 'bg-gray-50 text-gray-700 border-gray-200');

                $statusBadge = match(true){
                $statusUp === 'OPEN' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                in_array($statusUp, ['ASSIGNED','IN_PROGRESS']) => 'bg-blue-100 text-blue-800 border-blue-200',
                in_array($statusUp, ['RESOLVED','CLOSED']) => 'bg-green-100 text-green-800 border-green-200',
                default => 'bg-gray-100 text-gray-700 border-gray-200'
                };
                @endphp

                {{-- Card is now clickable using window.location.href and ULID --}}
                <div 
                    onclick="window.location.href='{{ route('user.ticket.show', $t->ulid) }}'"
                    class="group relative bg-white rounded-xl border-2 border-black p-5 hover:shadow-md transition-all duration-200 cursor-pointer"
                >
                    {{-- Header --}}
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="text-xs font-mono font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded">#{{ $t->ticket_id }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-md uppercase font-bold tracking-wide border {{ $priorityBadge }}">
                                    {{ $priority ? ucfirst($priority) : 'Low' }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 truncate">
                                {{ $t->subject }}
                            </h3>

                            @if($t->requester)
                            <div class="mt-1 flex items-center gap-1 text-xs text-gray-600">
                                <span class="text-gray-400">From:</span>
                                <span class="font-medium text-gray-900 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-200">
                                    {{ $t->requester->full_name ?? $t->requester->email }}
                                </span>
                            </div>
                            @endif
                        </div>

                        <span class="shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border {{ $statusBadge }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    {{-- Description --}}
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-3 mb-4">
                        {{ $t->description }}
                    </p>

                    {{-- Footer Info --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-4 border-t border-gray-100">
                        <div class="text-[11px] text-gray-500 flex flex-col gap-1">
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Created {{ \Carbon\Carbon::parse($t->created_at)->diffForHumans() }}</span>
                            </div>
                            @if($t->updated_at != $t->created_at)
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span>Updated {{ optional($t->updated_at)->diffForHumans() }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="w-full sm:w-auto flex flex-col sm:flex-row items-end sm:items-center gap-3">
                            {{-- Attachments --}}
                            @if($t->attachments->count())
                            <div class="flex -space-x-2 overflow-hidden p-1">
                                @foreach($t->attachments->take(3) as $a)
                                @php $isImage = str_starts_with(strtolower($a->file_type ?? ''), 'image/'); @endphp
                                <a 
                                    href="{{ $a->file_url }}" 
                                    target="_blank" 
                                    onclick="event.stopPropagation()"
                                    class="inline-block h-6 w-6 rounded-full ring-2 ring-white bg-gray-100 flex items-center justify-center overflow-hidden text-[8px] text-gray-500 hover:scale-110 transition-transform" 
                                    title="{{ $a->original_filename }}">
                                    @if($isImage)
                                    <img src="{{ $a->file_url }}" class="h-full w-full object-cover">
                                    @else
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    @endif
                                </a>
                                @endforeach
                                @if($t->attachments->count() > 3)
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full ring-2 ring-white bg-gray-100 text-[8px] font-medium text-gray-600">
                                    +{{ $t->attachments->count() - 3 }}
                                </span>
                                @endif
                            </div>
                            @endif

                            {{-- Claim Button --}}
                            <button
                                onclick="event.stopPropagation()"
                                wire:click="claim({{ $t->ticket_id }})"
                                wire:loading.attr="disabled"
                                wire:target="claim"
                                type="button"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75A2.25 2.25 0 0014.25 4.5h-6A2.25 2.25 0 006 6.75v10.5A2.25 2.25 0 008.25 19.5h7.5A2.25 2.25 0 0018 17.25V12M12 12l9-9m0 0v6m0-6h-6"></path>
                                </svg>
                                <span wire:loading.remove wire:target="claim">Claim Ticket</span>
                                <span wire:loading wire:target="claim">Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $tickets->onEachSide(1)->links() }}
            </div>
            @endif
        </div>
        @endif

        {{-- CLAIMS TAB (KANBAN) --}}
        @if ($tab === 'claims')
        <div wire:key="claims-tab-container"
            x-data="{
                    draggingId: null,
                    draggingFrom: null,
                    dragStart(id, fromStatus) {
                        this.draggingId = id;
                        this.draggingFrom = fromStatus;
                    },
                    drop(toStatus) {
                        if (!this.draggingId || toStatus === this.draggingFrom) {
                            this.draggingId = null;
                            this.draggingFrom = null;
                            return;
                        }
                        $wire.moveClaim(this.draggingId, toStatus);
                        this.draggingId = null;
                        this.draggingFrom = null;
                    }
                }"
            class="h-full">
            {{-- Filter bar --}}
            <div class="flex flex-col md:flex-row md:items-center gap-4 pb-4 mb-4 border-b border-gray-100">
                <div class="w-full md:w-1/4">
                    <select wire:model.live="claimPriority" class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        <option value="">All Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>

            @if(!$claims || $claims->isEmpty())
            <div class="rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No claims yet</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum meng-claim tiket apapun.</p>
            </div>
            @else
            @php
            // Group claims by status for Kanban columns
            $groupedClaims = $claims->groupBy(function ($assignment) {
            $status = strtoupper(optional($assignment->ticket)->status ?? 'OPEN');

            return match (true) {
            in_array($status, ['ASSIGNED', 'IN_PROGRESS']) => 'IN_PROGRESS',
            in_array($status, ['RESOLVED']) => 'RESOLVED',
            default => $status,
            };
            });
            @endphp

            {{-- Layout: 2 Columns --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 h-full items-start">
                @foreach ($kanbanColumns as $statusKey => $label)
                <div
                    class="flex flex-col rounded-xl border-2 border-gray-200 bg-gray-50 p-3 h-full min-h-[500px]"
                    x-on:dragover.prevent
                    x-on:drop.prevent="drop('{{ $statusKey }}')">
                    {{-- Column Header --}}
                    <div class="flex items-center justify-between mb-3 px-1">
                        <h3 class="text-xs font-bold tracking-wider uppercase text-gray-600">
                            {{ $label }}
                        </h3>
                        <span class="bg-gray-200 text-gray-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                            {{ ($groupedClaims[$statusKey] ?? collect())->count() }}
                        </span>
                    </div>

                    {{-- Column Body --}}
                    <div class="space-y-3 flex-1">
                        @forelse ($groupedClaims[$statusKey] ?? [] as $asgn)
                        @php
                        $t = $asgn->ticket;
                        if (!$t) continue;

                        $prio = strtolower($t->priority ?? '');
                        $cardBorderClass = match($prio) {
                        'high' => 'border-l-4 border-l-orange-500 border-y border-r border-gray-200',
                        'medium' => 'border-l-4 border-l-yellow-400 border-y border-r border-gray-200',
                        default => 'border border-gray-200',
                        };
                        @endphp

                        <div
                            class="relative bg-white rounded-lg p-3 shadow-sm hover:shadow-md transition-all cursor-grab active:cursor-grabbing {{ $cardBorderClass }}"
                            draggable="true"
                            x-on:dragstart="dragStart({{ $t->ticket_id }}, '{{ strtoupper($t->status ?? 'OPEN') }}')"
                            x-on:dragend="draggingId = null; draggingFrom = null">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <span class="text-[10px] font-mono text-gray-500">#{{ $t->ticket_id }}</span>
                                @if($prio === 'high')
                                <span class="text-[9px] font-bold text-orange-700 bg-orange-50 px-1.5 py-0.5 rounded border border-orange-100">HIGH</span>
                                @elseif($prio === 'medium')
                                <span class="text-[9px] font-bold text-yellow-700 bg-yellow-50 px-1.5 py-0.5 rounded border border-yellow-100">MED</span>
                                @endif
                            </div>

                            <a href="{{ route('user.ticket.show', $t) }}" class="block text-sm font-semibold text-gray-900 line-clamp-2 hover:text-blue-600 mb-1.5">
                                {{ $t->subject }}
                            </a>

                            <div class="flex items-center justify-between text-[10px] text-gray-500 border-t border-gray-50 pt-2 mt-2">
                                <div class="flex items-center gap-1" title="Claimed at">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($asgn->created_at)->diffForHumans(null, true) }}
                                </div>
                                @if($t->requester)
                                <div class="flex items-center gap-1 truncate max-w-[80px]" title="{{ $t->requester->full_name }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="truncate">{{ explode(' ', $t->requester->full_name)[0] }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="flex flex-col items-center justify-center h-24 border-2 border-dashed border-gray-200 rounded-lg text-gray-400">
                            <span class="text-xs italic">Empty</span>
                        </div>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif
    </div>
</div>