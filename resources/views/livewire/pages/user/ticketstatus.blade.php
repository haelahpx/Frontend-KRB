{{-- resources/views/livewire/pages/user/ticketstatus.blade.php --}}
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
        <div class="flex items-center justify-start gap-3">
            <h1 class="text-3xl font-bold text-gray-900">Support Ticket System</h1>

            @php
            $isCreate = request()->routeIs('create-ticket') || request()->is('create-ticket');
            $isStatus = request()->routeIs('ticketstatus') || request()->is('ticketstatus');
            @endphp

            {{-- Tabs (icons removed as requested) --}}
            <div class="ml-auto inline-flex rounded-md overflow-hidden bg-gray-100 border border-gray-200">
                <a href="{{ route('create-ticket') }}"
                    @class([ 'px-4 py-2 text-sm font-medium transition-colors inline-flex items-center' , 'bg-gray-900 text-white'=> $isCreate,
                    'text-gray-700 hover:text-gray-900' => !$isCreate,
                    ])>
                    Create Ticket
                </a>
                <a href="{{ route('ticketstatus') }}"
                    @class([ 'px-4 py-2 text-sm font-medium transition-colors border-l border-gray-200 inline-flex items-center' , 'bg-gray-900 text-white'=> $isStatus,
                    'text-gray-700 hover:text-gray-900' => !$isStatus,
                    ])>
                    Ticket Status
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4">
        <div class="flex flex-col md:flex-row md:items-center gap-6 pb-4 mb-4 border-b border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 w-full md:w-auto">
                <select wire:model.live="statusFilter" class="px-3 py-2 border border-gray-300 rounded-md text-gray-900 w-full">
                    <option value="">All Status</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>

                <select wire:model.live="priorityFilter" class="px-3 py-2 border border-gray-300 rounded-md text-gray-900 w-full">
                    <option value="">All Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>

                <select wire:model.live="departmentFilter" class="px-3 py-2 border border-gray-300 rounded-md text-gray-900 w-full">
                    <option value="">All Departments</option>
                    @foreach ($departments as $dept)
                    <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="sortFilter" class="px-3 py-2 border border-gray-300 rounded-md text-gray-900 w-full">
                    <option value="recent">Recent first</option>
                    <option value="oldest">Oldest first</option>
                    <option value="due">Nearest due</option>
                </select>
            </div>
        </div>

        {{-- Cards --}}
        <div class="space-y-5">
            @forelse ($tickets as $t)
            @php
            $priority = strtolower($t->priority ?? '');
            $statusUp = strtoupper($t->status ?? 'OPEN');
            $statusLabel = ucfirst(strtolower(str_replace('_',' ',$statusUp)));
            $userName = $t->user->full_name ?? $t->user->name ?? 'User';
            $initial = strtoupper(mb_substr($userName, 0, 1));
            $isOpen = $statusUp === 'OPEN';
            $isAssignedOrProgress = in_array($statusUp, ['ASSIGNED','IN_PROGRESS'], true);
            $isResolvedOrClosed = in_array($statusUp, ['RESOLVED','CLOSED'], true);
            $hasAgent = (int)($t->agent_count ?? 0) > 0;
            $agents = collect($t->assignments ?? [])->pluck('user.full_name')->filter()->unique()->values();
            @endphp

            <div class="relative bg-white rounded-xl border-2 border-black/80 shadow-md p-6 hover:shadow-lg hover:-translate-y-0.5 transition">
                <a href="{{ route('user.ticket.show', $t) }}" class="absolute inset-0 z-20" aria-label="Open ticket #{{ $t->ticket_id }}"></a>

                <div class="flex items-start justify-between gap-4 mb-3 relative z-0 pointer-events-none">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate">{{ $t->subject }}</h3>

                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="font-mono font-medium text-gray-800 inline-flex items-center gap-1">
                                <x-heroicon-o-hashtag class="w-3.5 h-3.5" /> {{ $t->ticket_id }}
                            </span>
                            <span class="text-gray-300">•</span>

                            @php
                            $isHigh = $priority === 'high';
                            $isMedium = $priority === 'medium';
                            $isLow = $priority === 'low' || $priority === '';
                            @endphp
                            <span @class([ 'inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium' , 'bg-orange-50 text-orange-700 border-2 border-orange-400'=> $isHigh,
                                'bg-yellow-50 text-yellow-700 border-2 border-yellow-400' => $isMedium,
                                'bg-gray-50 text-gray-700 border-2 border-gray-400' => $isLow,
                                ])>
                                <x-heroicon-o-bolt class="w-3.5 h-3.5" />
                                {{ $priority ? ucfirst($priority) : 'Low' }}
                            </span>

                            @if ($t->department)
                            <span class="text-gray-300">•</span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border-2 border-gray-400 bg-gray-50 text-gray-700">
                                <x-heroicon-o-building-office-2 class="w-3.5 h-3.5" />
                                <span class="font-medium">{{ $t->department->department_name }}</span>
                            </span>
                            @endif

                            <span class="text-gray-300">•</span>
                            <span class="inline-flex items-center gap-2 px-2 py-1 rounded-lg border-2 border-gray-400 bg-gray-50 text-gray-700">
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-gray-400 text-[10px] leading-none">
                                    {{ $initial }}
                                </span>
                                <x-heroicon-o-user class="w-3.5 h-3.5" />
                                <span class="font-medium">{{ $userName }}</span>
                            </span>

                            <span class="text-gray-300">•</span>
                            <span @class([ 'inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium' , 'border-2' ,
                                $hasAgent ? 'bg-emerald-50 text-emerald-700 border-emerald-400' : 'bg-red-50 text-red-700 border-red-400'
                                ])>
                                <x-heroicon-o-user-circle class="w-3.5 h-3.5" />
                                {{ $hasAgent ? 'Agent assigned' : 'No agent yet' }}
                            </span>

                            @if($agents->isNotEmpty())
                            <span class="text-gray-300">•</span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border-2 border-blue-400 bg-blue-50 text-blue-700">
                                <x-heroicon-o-users class="w-3.5 h-3.5" />
                                <span class="font-medium truncate max-w-[220px]">
                                    {{ $agents->join(', ') }}
                                </span>
                            </span>
                            @endif
                        </div>
                    </div>

                    <span @class([ 'inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium' , 'bg-yellow-50 text-yellow-700 border-2 border-yellow-500'=> $isOpen,
                        'bg-blue-50 text-blue-700 border-2 border-blue-500' => $isAssignedOrProgress,
                        'bg-green-50 text-green-700 border-2 border-green-500' => $isResolvedOrClosed,
                        'bg-gray-50 text-gray-700 border-2 border-gray-500' => (!$isOpen && !$isAssignedOrProgress && !$isResolvedOrClosed),
                        ])>
                        <x-heroicon-o-check-badge class="w-4 h-4" />
                        {{ $statusLabel }}
                    </span>
                </div>

                <p class="text-sm text-gray-600 leading-relaxed relative z-0 pointer-events-none">
                    {{ $t->description }}
                </p>

                <div class="mt-3 text-[11px] text-gray-500 relative z-0 pointer-events-none inline-flex items-center gap-2">
                    <x-heroicon-o-clock class="w-3.5 h-3.5" />
                    <span>Created: {{ optional($t->created_at)->format('Y-m-d H:i') }}</span>
                    <span class="mx-2">•</span>
                    <x-heroicon-o-arrow-path class="w-3.5 h-3.5" />
                    <span>Updated: {{ optional($t->updated_at)->format('Y-m-d H:i') }}</span>
                </div>

                @if (in_array($statusUp, ['OPEN','ASSIGNED','IN_PROGRESS','RESOLVED','CLOSED'], true))
                <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-end gap-2 relative z-30 pointer-events-auto">
                    @if (in_array($statusUp, ['OPEN','ASSIGNED','IN_PROGRESS'], true))
                    <button
                        @disabled(!$hasAgent)
                        @class([ 'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2' ,
                        $hasAgent
                        ? 'text-white bg-black hover:bg-gray-800 focus:ring-gray-500'
                        : 'text-gray-400 bg-gray-200 cursor-not-allowed'
                        ])
                        @if($hasAgent)
                        wire:click.stop="markComplete({{ (int) $t->ticket_id }})"
                        @endif
                        wire:loading.attr="disabled"
                        wire:target="markComplete"
                        type="button"
                        title="{{ $hasAgent ? 'Mark this ticket as Resolved' : 'Cannot resolve: no agent assigned' }}">
                        <x-heroicon-o-check-circle class="w-4 h-4" />
                        Mark as Resolved
                    </button>
                    @elseif ($statusUp === 'RESOLVED')
                    <button
                        wire:click.stop="markComplete({{ (int) $t->ticket_id }})"
                        wire:loading.attr="disabled"
                        wire:target="markComplete"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200"
                        title="Close this ticket">
                        <x-heroicon-o-lock-closed class="w-4 h-4" />
                        Close Ticket
                    </button>
                    @elseif ($statusUp === 'CLOSED')
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 rounded-lg">
                        <x-heroicon-o-lock-closed class="w-4 h-4" />
                        Closed
                    </span>
                    @endif
                </div>
                @endif
            </div>
            @empty
            <div class="rounded-lg border-2 border-dashed border-gray-300 p-10 text-center text-gray-600">
                No tickets found. Try adjusting filters above.
            </div>
            @endforelse
        </div>

        @if(method_exists($tickets, 'links'))
        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>