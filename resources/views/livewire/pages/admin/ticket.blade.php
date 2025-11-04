<div class="min-h-screen bg-gray-50" wire:poll.800ms="tick" wire:poll.keep-alive.2s="tick">
    @php
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $label = 'block text-sm font-medium text-gray-700 mb-2';
        $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
        $btnRed = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
        $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">
        <div class="space-y-6">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
                <div class="relative z-10 p-6 sm:p-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-9 4h6M7 8h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h1 class="text-xl sm:text-2xl font-semibold">Ticket Management</h1>
                        </div>
                    </div>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="p-4 rounded-lg bg-emerald-600 text-white shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        {{ session('message') }}
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-4 rounded-lg bg-rose-600 text-white shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <section class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200">
                    <div class="flex flex-col gap-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <label class="{{ $label }}">Search</label>
                                <input type="text" wire:model.debounce.500ms="search" class="{{ $input }}" placeholder="Subject / description">
                            </div>
                            <div>
                                <label class="{{ $label }}">Priority</label>
                                <select wire:model="priority" class="{{ $input }}">
                                    <option value="">All priorities</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div>
                                <label class="{{ $label }}">Status</label>
                                <select wire:model="status" class="{{ $input }}">
                                    <option value="">All status</option>
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            @if($search)
                                <span class="{{ $chip }}"><span class="text-gray-600">Search:</span><span class="font-medium text-gray-900">{{ $search }}</span></span>
                            @endif
                            @if($priority)
                                <span class="{{ $chip }}"><span class="text-gray-600">Priority:</span><span class="font-medium text-gray-900 capitalize">{{ $priority }}</span></span>
                            @endif
                            @if($status)
                                <span class="{{ $chip }}"><span class="text-gray-600">Status:</span><span class="font-medium text-gray-900 capitalize">{{ str_replace('_',' ',$status) }}</span></span>
                            @endif
                            @if($search || $priority || $status)
                                <button wire:click="resetFilters" type="button" class="text-xs underline text-gray-600 hover:text-gray-900 ml-1">Reset filters</button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-5">
                    @if($tickets->count())
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($tickets as $t)
                                @php
                                    $initial = strtoupper(substr(($t->subject ?? "T"), 0, 1));
                                @endphp

                                <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 hover:border-gray-300 transition"
                                     wire:key="t-{{ $t->ticket_id }}">
                                    <div class="flex items-start gap-4">
                                        <div class="w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0">
                                            {{ $initial }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <h4 class="font-semibold text-gray-900 text-sm truncate">
                                                    #{{ $t->ticket_id }} — {{ $t->subject }}
                                                </h4>

                                                <span class="{{ $chip }} capitalize font-medium">
                                                    <span class="text-gray-600">Priority:</span>
                                                    <span class="text-gray-900">{{ $t->priority }}</span>
                                                </span>

                                                <span class="{{ $chip }} capitalize font-medium">
                                                    <span class="text-gray-600">Status:</span>
                                                    <span class="text-gray-900">{{ strtolower($t->status) }}</span>
                                                </span>
                                            </div>

                                            <p class="text-sm text-gray-600 line-clamp-2">{{ $t->description }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex justify-end gap-2">
                                        {{-- IMPORTANT: pass the MODEL -> URL becomes /tickets/{ulid} --}}
                                        <a href="{{ route('admin.ticket.show', $t) }}" class="{{ $btnBlk }}">Open</a>

                                        <button wire:click.stop="deleteTicket({{ $t->ticket_id }})" class="{{ $btnRed }}" title="Move to Trash">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500 text-sm">Tidak ada tiket.</div>
                    @endif
                </div>

                <div class="px-5 py-4 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $tickets->links() }}
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>
