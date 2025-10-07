<div class="bg-gray-50"  wire:poll.800ms="tick" wire:poll.keep-alive.2s="tick">

    @php
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $label = 'block text-sm font-medium text-gray-700 mb-2';
        $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
        $btnBlu = 'px-3 py-2 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 disabled:opacity-60 transition';
        $btnGrn = 'px-3 py-2 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition';
        $btnOrn = 'px-3 py-2 text-xs font-medium rounded-lg bg-amber-600 text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-600/20 disabled:opacity-60 transition';
        $btnRed = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
        $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
        $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
        $ico = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
        $statusDot = [
            'open' => 'bg-gray-500',
            'in_progress' => 'bg-amber-500',
            'resolved' => 'bg-emerald-600',
            'closed' => 'bg-slate-500',
            'deleted' => 'bg-rose-600',
        ];
        $statusPill = [
            'open' => 'inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-200 text-gray-800 text-xs',
            'in_progress' => 'inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 text-xs',
            'resolved' => 'inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-900 text-xs',
            'closed' => 'inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-900 text-xs',
            'deleted' => 'inline-flex items-center px-2.5 py-1 rounded-lg bg-rose-100 text-rose-900 text-xs',
        ];

        /** Render up to 4 image attachments */
        $renderImages = function($ticket) {
            $imgs = collect($ticket->attachments ?? [])
                ->filter(function($a) {
                    $type = $a->file_type ?? '';
                    return is_string($type) && str_starts_with(strtolower($type), 'image');
                })
                ->take(4);

            if ($imgs->isEmpty()) return '';

            $html = '<div class="mt-2 flex flex-wrap gap-2">';
            foreach ($imgs as $att) {
                $url = e($att->file_url ?? '#');
                $alt = e($att->original_filename ?? 'attachment');
                $html .= '<a href="'.$url.'" target="_blank" class="block"><img src="'.$url.'" alt="'.$alt.'" class="h-14 w-14 rounded-md object-cover border border-gray-200"/></a>';
            }
            $html .= '</div>';
            return $html;
        };
    @endphp

    <main class="px-4 sm:px-6 py-6">
        <div class="space-y-8">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
                <div class="pointer-events-none absolute inset-0 opacity-10">
                    <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                    <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
                </div>
                <div class="relative z-10 p-6 sm:p-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-9 4h6M7 8h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Ticket Management</h2>
                            <p class="text-sm text-white/80">Kelola tiket berdasarkan status dan penugasan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <section class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Filter</h3>
                    <p class="text-sm text-gray-500">Cari dan saring tiket.</p>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-4 gap-5">
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
                            <option value="deleted">Deleted</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Catatan: status <b>Deleted</b> tidak tampil di tampilan default. Pilih “Deleted” untuk melihat trash.</p>
                    </div>
                </div>
            </section>

            @if (session()->has('message'))
                <div class="p-3 rounded-lg bg-emerald-600 text-white">{{ session('message') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="p-3 rounded-lg bg-rose-600 text-white">{{ session('error') }}</div>
            @endif

            {{-- Deleted bucket --}}
            @if ($status === 'deleted')
                <section class="{{ $card }}">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 {{ $statusDot['deleted'] }} rounded-full"></div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">Trash (Deleted)</h3>
                                <p class="text-sm text-gray-500">Tiket yang ditandai <b>Deleted</b>.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 space-y-3">
                        @forelse ($deleted ?? collect() as $t)
                            <div class="p-3 rounded-lg bg-rose-50 border border-rose-200" wire:key="del-{{ $t->ticket_id }}">
                                <div class="flex items-start gap-3">
                                    <div class="{{ $ico }}">{{ strtoupper(substr(($t->subject ?? 'T'), 0, 1)) }}</div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <div class="font-semibold text-gray-900 text-sm">#{{ $t->ticket_id }} — {{ $t->subject }}</div>
                                            <span class="{{ $chip }} capitalize">Priority: {{ $t->priority }}</span>
                                            <span class="{{ $statusPill['deleted'] }}">Deleted</span>
                                        </div>
                                        <div class="mt-1 text-[12px] text-gray-600 line-clamp-3">{{ $t->description }}</div>
                                        {!! $renderImages($t) !!}
                                    </div>
                                    <div class="flex items-center gap-2 {{ $mono }}">DELETED</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500 text-sm">Trash kosong.</div>
                        @endforelse
                    </div>
                </section>
            @endif

            @if ($status !== 'deleted')
                <div class="space-y-6">

                    {{-- OPEN --}}
                    <section class="{{ $card }}">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 {{ $statusDot['open'] }} rounded-full"></div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Open</h3>
                                    <p class="text-sm text-gray-500">Tiket baru / belum dikerjakan.</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-3">
                            @forelse ($open as $t)
                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-200" wire:key="open-{{ $t->ticket_id }}">
                                    <div class="flex items-start gap-3">
                                        <div class="{{ $ico }}">{{ strtoupper(substr(($t->subject ?? 'T'), 0, 1)) }}</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <div class="font-semibold text-gray-900 text-sm">#{{ $t->ticket_id }} — {{ $t->subject }}</div>
                                                <span class="{{ $chip }} capitalize">Priority: {{ $t->priority }}</span>
                                            </div>
                                            <div class="mt-1 text-[12px] text-gray-600 line-clamp-3">{{ $t->description }}</div>
                                            {!! $renderImages($t) !!}
                                        </div>
                                        <div class="flex items-center gap-2 {{ $mono }}">
                                            <button class="{{ $btnBlk }}" wire:click="openEdit({{ $t->ticket_id }})" wire:loading.attr="disabled" wire:target="openEdit({{ $t->ticket_id }})">Edit</button>
                                            <button class="{{ $btnRed }}" wire:click="deleteTicket({{ $t->ticket_id }})" wire:loading.attr="disabled" wire:target="deleteTicket({{ $t->ticket_id }})">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 text-sm">Belum ada item open</div>
                            @endforelse
                        </div>
                    </section>

                    {{-- IN PROGRESS --}}
                    <section class="{{ $card }}">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 {{ $statusDot['in_progress'] }} rounded-full"></div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">In Progress</h3>
                                    <p class="text-sm text-gray-500">Tiket sedang dikerjakan (agent ditetapkan).</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-3">
                            @forelse ($inProgress as $t)
                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-200" wire:key="prog-{{ $t->ticket_id }}">
                                    <div class="flex items-start gap-3">
                                        <div class="{{ $ico }}">{{ strtoupper(substr(($t->subject ?? 'T'), 0, 1)) }}</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <div class="font-semibold text-gray-900 text-sm">#{{ $t->ticket_id }} — {{ $t->subject }}</div>
                                                <span class="{{ $chip }}">Agent: {{ $t->assignment?->agent?->full_name ?? '—' }}</span>
                                                <span class="{{ $chip }} capitalize">Priority: {{ $t->priority }}</span>
                                            </div>
                                            <div class="mt-1 text-[12px] text-gray-600 line-clamp-3">{{ $t->description }}</div>
                                            {!! $renderImages($t) !!}
                                        </div>
                                        <div class="flex items-center gap-2 {{ $mono }}">
                                            <button class="{{ $btnBlk }}" wire:click="openEdit({{ $t->ticket_id }})" wire:loading.attr="disabled" wire:target="openEdit({{ $t->ticket_id }})">Edit</button>
                                            <button class="{{ $btnRed }}" wire:click="deleteTicket({{ $t->ticket_id }})" wire:loading.attr="disabled" wire:target="deleteTicket({{ $t->ticket_id }})">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 text-sm">Belum ada item in progress</div>
                            @endforelse
                        </div>
                    </section>

                    {{-- RESOLVED --}}
                    <section class="{{ $card }}">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 {{ $statusDot['resolved'] }} rounded-full"></div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Resolved</h3>
                                    <p class="text-sm text-gray-500">Tiket sudah diatasi, menunggu close.</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-3">
                            @forelse ($resolved as $t)
                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-200" wire:key="res-{{ $t->ticket_id }}">
                                    <div class="flex items-start gap-3">
                                        <div class="{{ $ico }}">{{ strtoupper(substr(($t->subject ?? 'T'), 0, 1)) }}</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <div class="font-semibold text-gray-900 text-sm">#{{ $t->ticket_id }} — {{ $t->subject }}</div>
                                                <span class="{{ $chip }}">Agent: {{ $t->assignment?->agent?->full_name ?? '—' }}</span>
                                                <span class="{{ $chip }} capitalize">Priority: {{ $t->priority }}</span>
                                            </div>
                                            <div class="mt-1 text-[12px] text-gray-600 line-clamp-3">{{ $t->description }}</div>
                                            {!! $renderImages($t) !!}
                                        </div>
                                        <div class="flex items-center gap-2 {{ $mono }}">
                                            <button class="{{ $btnBlk }}" wire:click="openEdit({{ $t->ticket_id }})" wire:loading.attr="disabled" wire:target="openEdit({{ $t->ticket_id }})">Edit</button>
                                            <button class="{{ $btnRed }}" wire:click="deleteTicket({{ $t->ticket_id }})" wire:loading.attr="disabled" wire:target="deleteTicket({{ $t->ticket_id }})">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 text-sm">Belum ada item resolved</div>
                            @endforelse
                        </div>
                    </section>

                    {{-- CLOSED --}}
                    <section class="{{ $card }}">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 {{ $statusDot['closed'] }} rounded-full"></div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Closed</h3>
                                    <p class="text-sm text-gray-500">Tiket ditutup dan tidak bisa diedit.</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-3">
                            @forelse ($closed as $t)
                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 opacity-80" wire:key="cls-{{ $t->ticket_id }}">
                                    <div class="flex items-start gap-3">
                                        <div class="{{ $ico }}">{{ strtoupper(substr(($t->subject ?? 'T'), 0, 1)) }}</div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <div class="font-semibold text-gray-900 text-sm">#{{ $t->ticket_id }} — {{ $t->subject }}</div>
                                                <span class="{{ $chip }}">Agent: {{ $t->assignment?->agent?->full_name ?? '—' }}</span>
                                                <span class="{{ $chip }} capitalize">Priority: {{ $t->priority }}</span>
                                            </div>
                                            <div class="mt-1 text-[12px] text-gray-600 line-clamp-3">{{ $t->description }}</div>
                                            {!! $renderImages($t) !!}
                                        </div>
                                        <div class="{{ $mono }}">CLOSED</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 text-sm">Belum ada item closed</div>
                            @endforelse
                        </div>
                    </section>

                    <div>
                        {{ $tickets->links() }}
                    </div>
                </div>
            @endif
        </div>
    </main>

    @if($modalEdit)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4" wire:key="modal-edit-ticket" wire:keydown.escape.window="closeEdit">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeEdit"></div>

            <div class="relative w-full max-w-xl bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden max-h-[90vh] flex flex-col">
                <div class="bg-gradient-to-r from-gray-900 to-black p-5 text-white relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 pointer-events-none">
                        <div class="absolute top-0 -right-6 w-24 h-24 bg-white rounded-full blur-2xl"></div>
                        <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-xl"></div>
                    </div>
                    <div class="relative z-10 flex items-center justify-between">
                        <h3 class="text-lg font-semibold tracking-tight">
                            Edit Ticket @if($editId)#{{ $editId }}@endif
                        </h3>
                        <button class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 flex items-center justify-center" wire:click="closeEdit">
                            <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <form class="p-5 space-y-4 overflow-y-auto flex-1" wire:submit.prevent="saveEdit">
                    <div>
                        <label class="{{ $label }}">👤 Assign Agent</label>
                        <select class="{{ $input }}" wire:model.defer="edit_agent_id">
                            <option value="">— No Agent —</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->user_id }}">{{ $agent->full_name }}</option>
                            @endforeach
                        </select>
                        @error('edit_agent_id')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Tetapkan agent untuk menandai tiket sebagai <b>In Progress</b>.</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">📌 Status</label>
                        <select class="{{ $input }}" wire:model.defer="edit_status">
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed" disabled>Closed (non-editable)</option>
                            <option value="deleted">Deleted</option>
                        </select>
                        @error('edit_status')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Status disimpan sebagai ENUM UPPERCASE di DB.</p>
                    </div>

                    <div class="bg-gray-50 border-t border-gray-200 mt-4 -mx-5 px-5 py-4 flex items-center justify-end gap-2.5">
                        <button type="button" class="px-4 h-10 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 hover:border-gray-400 transition" wire:click="closeEdit">
                            Batal
                        </button>
                        <button type="submit" class="px-5 h-10 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
