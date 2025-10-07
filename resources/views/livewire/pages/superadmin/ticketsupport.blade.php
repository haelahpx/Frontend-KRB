<div class="bg-gray-50">
    @php
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk= 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnRed= 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
    $btnLt = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition';
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-[11px]';
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $ico = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">
        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Ticket Support</h2>
                        <p class="text-sm text-white/80">Scope: <span class="font-semibold">Company</span></p>
                    </div>
                    <div class="ml-auto">
                        <a href="#" class="{{ $btnLt }}">Go to Agents</a>
                    </div>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl px-4 py-3 text-sm text-gray-800">
            {{ session('success') }}
        </div>
        @endif

        {{-- TOOLBAR --}}
        <div class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="relative flex-1">
                        <input type="text" wire:model.live="search" placeholder="Search subject, description or user..."
                            class="{{ $input }} pl-10 w-full placeholder:text-gray-400">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                        </svg>
                    </div>

                    <div class="relative">
                        <select wire:model.live="departmentFilter" class="{{ $input }} pl-10 w-full lg:w-60">
                            <option value="">All Departments</option>
                            @foreach ($deptLookup as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative">
                        <select wire:model.live="priorityFilter" class="{{ $input }} pl-10 w-full lg:w-40">
                            <option value="">All Priorities</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <div class="relative">
                        <select wire:model.live="perPage" class="{{ $input }} pl-10 w-full lg:w-40">
                            <option value="10">10 / page</option>
                            <option value="20">20 / page</option>
                            <option value="50">50 / page</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- LIST --}}
            <div class="divide-y divide-gray-200">
                @forelse ($tickets as $t)
                <div class="px-5 py-5 hover:bg-gray-50 transition-colors" wire:key="ticket-{{ $t->ticket_id }}">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1">
                            <div class="{{ $ico }}">
                                {{ strtoupper(substr($t->subject ?? 'T',0,1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">
                                        {{ $t->subject }}
                                    </h4>

                                    @php
                                        $p = strtolower($t->priority ?? 'low');
                                        $priorityBadge = match($p) {
                                            'urgent' => 'bg-rose-600 text-white',
                                            'high' => 'bg-amber-500 text-white',
                                            'medium' => 'bg-yellow-100 text-gray-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="text-[11px] px-2 py-0.5 rounded-md font-medium {{ $priorityBadge }}">{{ ucfirst($p) }}</span>
                                </div>

                                <p class="text-[12px] text-gray-500 mt-1">
                                    By: {{ $t->user->full_name ?? '—' }}
                                </p>

                                <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $t->description }}</p>

                                {{-- attachments --}}
                                @if($t->attachments && $t->attachments->count())
                                    <div class="mt-3 flex flex-wrap items-center gap-3">
                                        @foreach($t->attachments as $att)
                                            @php
                                                $url = $att->file_url;
                                                $name = $att->original_filename ?? basename($url);
                                                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                                $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                                            @endphp

                                            <div class="flex items-center gap-2 bg-white p-2 rounded-md border">
                                                @if($isImage)
                                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="block">
                                                        <img src="{{ $url }}" alt="{{ $name }}" class="h-20 w-auto rounded-md object-cover"/>
                                                    </a>
                                                @else
                                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-xs px-2 py-1 border rounded">
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v8l4-4m6 16H6a2 2 0 01-2-2V6a2 2 0 012-2h7"/></svg>
                                                        {{ $name }}
                                                    </a>
                                                @endif

                                                <a href="{{ $url }}" download class="text-xs text-gray-500 ml-2">Download</a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="{{ $chip }}"><span class="text-gray-500">Dept:</span><span class="font-medium text-gray-700">{{ $t->department->department_name ?? ($deptLookup[$t->department_id] ?? '—') }}</span></span>
                                    <span class="{{ $chip }}"><span class="text-gray-500">Created:</span><span class="font-medium text-gray-700">{{ \Illuminate\Support\Carbon::parse($t->created_at)->format('d M Y H:i') }}</span></span>
                                    <span class="{{ $chip }}"><span class="text-gray-500">Status:</span><span class="font-medium text-gray-700">{{ ucfirst(str_replace('_',' ',$t->status)) }}</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right shrink-0 space-y-2">
                            <div class="{{ $mono }}">#{{ $t->ticket_id }}</div>
                            <div class="flex flex-wrap gap-2 justify-end pt-1">
                                <button class="{{ $btnBlk }}"
                                    wire:click="openEdit({{ $t->ticket_id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="openEdit({{ $t->ticket_id }})">
                                    <span wire:loading.remove wire:target="openEdit({{ $t->ticket_id }})">Edit</span>
                                    <span wire:loading wire:target="openEdit({{ $t->ticket_id }})">Loading…</span>
                                </button>
                                <button class="{{ $btnRed }}"
                                    wire:click="delete({{ $t->ticket_id }})"
                                    onclick="return confirm('Delete this ticket?')"
                                    wire:loading.attr="disabled"
                                    wire:target="delete({{ $t->ticket_id }})">
                                    <span wire:loading.remove wire:target="delete({{ $t->ticket_id }})">Delete</span>
                                    <span wire:loading wire:target="delete({{ $t->ticket_id }})">Deleting…</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-14 text-center text-gray-500 text-sm">No tickets found.</div>
                @endforelse
            </div>

            @if($tickets->hasPages())
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-center">
                    {{ $tickets->withQueryString()->links() }}
                </div>
            </div>
            @endif
        </div>

        {{-- MODAL: EDIT --}}
        @if($modal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center" role="dialog" aria-modal="true" wire:key="modal-ticket" wire:keydown.escape.window="closeModal">
            <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay" wire:click="closeModal"></button>

            <div class="relative w-full max-w-2xl mx-4 {{ $card }} focus:outline-none" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Edit Ticket</h3>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeModal" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form class="p-5" wire:submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="{{ $label }}">Department</label>
                            <select class="{{ $input }}" wire:model.defer="department_id">
                                <option value="">Choose department</option>
                                @foreach ($deptLookup as $did => $dname)
                                <option value="{{ $did }}">{{ $dname }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Priority</label>
                            <select class="{{ $input }}" wire:model.defer="priority">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            @error('priority') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="{{ $label }}">Subject</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="subject">
                            @error('subject') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="{{ $label }}">Description</label>
                            <textarea class="{{ $input }} h-28" wire:model.defer="description"></textarea>
                            @error('description') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Status</label>
                            <select class="{{ $input }}" wire:model.defer="status">
                                <option value="">-- choose --</option>
                                <option value="OPEN">Open</option>
                                <option value="IN_PROGRESS">In Progress</option>
                                <option value="RESOLVED">Resolved</option>
                                <option value="CLOSED">Closed</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button" class="{{ $btnLt }}" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="{{ $btnBlk }}" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update">Update</span>
                            <span class="inline-flex items-center gap-2" wire:loading wire:target="update">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                                </svg>
                                Processing…
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </main>
</div>
