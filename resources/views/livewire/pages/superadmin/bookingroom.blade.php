<div class="bg-gray-50">
    {{-- Style Variables (samakan dengan Announcement page) --}}
    @php
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $head = 'bg-gradient-to-r from-black to-gray-800';
    $hpad = 'px-8 py-6';
    $tag  = 'w-2 h-8 bg-white rounded-full';
    $label = 'block text-sm font-semibold text-gray-700 mb-2';
    $input = 'w-full px-4 py-3 rounded-xl border-2 border-gray-200 text-gray-700 focus:border-black focus:ring-4 focus:ring-black/10 bg-gray-50 focus:bg-white transition';
    $btnBlk = 'px-4 py-2 text-sm rounded-xl bg-black text-white hover:bg-gray-800 disabled:opacity-60 font-semibold shadow-lg hover:shadow-xl transition';
    $btnRed = 'px-4 py-2 text-sm rounded-xl bg-red-600 text-white hover:bg-red-700 disabled:opacity-60 font-semibold shadow-lg hover:shadow-xl transition';
    $btnLite = 'px-4 py-2 text-sm rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 disabled:opacity-60 font-semibold transition';
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $ico  = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">
        {{-- HERO (sama pola) --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Rooms & Requirements Management</h2>
                        <p class="text-sm text-white/80">
                            Company: <span class="font-semibold">{{ optional(Auth::user()->company)->company_name ?? '-' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOAST session (sama gaya tone card putih) --}}
        @if (session()->has('success'))
            <div class="bg-white border border-gray-200 shadow-lg rounded-xl px-4 py-3 text-sm text-gray-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- ===================== ROOMS ===================== --}}
        <section class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Add New Room</h3>
            </div>

            {{-- Create Form (selalu tampil, mirip Announcement create) --}}
            <form class="p-5" wire:submit.prevent="roomStore">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="md:col-span-1">
                        <label class="{{ $label }}">Company</label>
                        <input type="text" class="{{ $input }}" value="{{ optional(Auth::user()->company)->company_name ?? '-' }}" readonly>
                    </div>
                    <div class="md:col-span-2">
                        <label class="{{ $label }}">Room Number</label>
                        <input type="text" wire:model.defer="room_number" class="{{ $input }}" placeholder="e.g. B-203">
                        @error('room_number') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="pt-5">
                    <button type="submit" wire:loading.attr="disabled" wire:target="roomStore" class="{{ $btnBlk }} relative overflow-hidden">
                        <span wire:loading.remove wire:target="roomStore">Save Room</span>
                        <span class="inline-flex items-center gap-2" wire:loading wire:target="roomStore">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>

            {{-- ROOM LIST (pola list seperti Announcement) --}}
            <div class="divide-y divide-gray-200">
                @forelse ($rooms as $room)
                    <div class="px-5 py-5 hover:bg-gray-50 transition-colors" wire:key="room-{{ $room->room_id }}">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="{{ $ico }}">{{ substr(optional(Auth::user()->company)->company_name ?? 'C', 0, 1) }}</div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base">Room {{ $room->room_number }}</h4>
                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <span class="{{ $chip }}">
                                            <span class="text-gray-500">Created:</span>
                                            <span class="font-medium text-gray-700">{{ $room->created_at?->format('d M Y, H:i') }}</span>
                                        </span>
                                        <span class="{{ $chip }}">
                                            <span class="text-gray-500">Updated:</span>
                                            <span class="font-medium text-gray-700">{{ $room->updated_at?->format('d M Y, H:i') }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0 space-y-2">
                                <p class="{{ $mono }} mt-1">#{{ $room->room_id }}</p>
                                <div class="flex flex-wrap gap-2 justify-end pt-1">
                                    <button
                                        wire:click="roomOpenEdit({{ $room->room_id }})"
                                        class="{{ $btnBlk }}"
                                        wire:loading.attr="disabled"
                                        wire:target="roomOpenEdit({{ $room->room_id }})"
                                        wire:key="btn-edit-room-{{ $room->room_id }}">
                                        <span wire:loading.remove wire:target="roomOpenEdit({{ $room->room_id }})">Edit</span>
                                        <span wire:loading wire:target="roomOpenEdit({{ $room->room_id }})">Loading…</span>
                                    </button>

                                    <button
                                        wire:click="roomDelete({{ $room->room_id }})"
                                        onclick="return confirm('Hapus room ini?')"
                                        class="{{ $btnRed }}"
                                        wire:loading.attr="disabled"
                                        wire:target="roomDelete({{ $room->room_id }})"
                                        wire:key="btn-del-room-{{ $room->room_id }}">
                                        <span wire:loading.remove wire:target="roomDelete({{ $room->room_id }})">Delete</span>
                                        <span wire:loading wire:target="roomDelete({{ $room->room_id }})">Deleting…</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-14 text-center text-gray-500 text-sm">No rooms found.</div>
                @endforelse
            </div>

            @if($rooms->hasPages())
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $rooms->links() }}
                    </div>
                </div>
            @endif
        </section>

        {{-- ===================== REQUIREMENTS ===================== --}}
        <section class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Add New Requirement</h3>
            </div>

            {{-- Create Form --}}
            <form class="p-5" wire:submit.prevent="reqStore">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="md:col-span-1">
                        <label class="{{ $label }}">Scope</label>
                        <input type="text" class="{{ $input }}" value="Global (no company binding)" readonly>
                    </div>
                    <div class="md:col-span-2">
                        <label class="{{ $label }}">Requirement Name</label>
                        <input type="text" wire:model.defer="req_name" class="{{ $input }}" placeholder="e.g. Projector, Whiteboard">
                        @error('req_name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="pt-5">
                    <button type="submit" wire:loading.attr="disabled" wire:target="reqStore" class="{{ $btnBlk }}">
                        <span wire:loading.remove wire:target="reqStore">Save Requirement</span>
                        <span class="inline-flex items-center gap-2" wire:loading wire:target="reqStore">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a 8 8 0 018-8V0A12 12 0 000 12h4z" />
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>

            {{-- REQUIREMENT LIST (pola sama) --}}
            <div class="divide-y divide-gray-200">
                @forelse ($requirements as $req)
                    <div class="px-5 py-5 hover:bg-gray-50 transition-colors" wire:key="req-{{ $req->requirement_id }}">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="{{ $ico }}">R</div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base">{{ $req->name }}</h4>
                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <span class="{{ $chip }}">
                                            <span class="text-gray-500">Created:</span>
                                            <span class="font-medium text-gray-700">{{ $req->created_at?->format('d M Y, H:i') }}</span>
                                        </span>
                                        <span class="{{ $chip }}">
                                            <span class="text-gray-500">Updated:</span>
                                            <span class="font-medium text-gray-700">{{ $req->updated_at?->format('d M Y, H:i') }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0 space-y-2">
                                <p class="{{ $mono }} mt-1">#{{ $req->requirement_id }}</p>
                                <div class="flex flex-wrap gap-2 justify-end pt-1">
                                    <button
                                        wire:click="reqOpenEdit({{ $req->requirement_id }})"
                                        class="{{ $btnBlk }}"
                                        wire:loading.attr="disabled"
                                        wire:target="reqOpenEdit({{ $req->requirement_id }})"
                                        wire:key="btn-edit-req-{{ $req->requirement_id }}">
                                        <span wire:loading.remove wire:target="reqOpenEdit({{ $req->requirement_id }})">Edit</span>
                                        <span wire:loading wire:target="reqOpenEdit({{ $req->requirement_id }})">Loading…</span>
                                    </button>

                                    <button
                                        wire:click="reqDelete({{ $req->requirement_id }})"
                                        onclick="return confirm('Hapus requirement ini?')"
                                        class="{{ $btnRed }}"
                                        wire:loading.attr="disabled"
                                        wire:target="reqDelete({{ $req->requirement_id }})"
                                        wire:key="btn-del-req-{{ $req->requirement_id }}">
                                        <span wire:loading.remove wire:target="reqDelete({{ $req->requirement_id }})">Delete</span>
                                        <span wire:loading wire:target="reqDelete({{ $req->requirement_id }})">Deleting…</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-14 text-center text-gray-500 text-sm">No requirements found.</div>
                @endforelse
            </div>

            @if($requirements->hasPages())
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $requirements->links() }}
                    </div>
                </div>
            @endif
        </section>

        {{-- ===================== MODALS ===================== --}}

        {{-- ROOM Edit Modal (persis gaya Announcement edit modal) --}}
        @if($roomModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center" role="dialog" aria-modal="true" wire:key="modal-edit-room" wire:keydown.escape.window="roomCloseEdit">
            <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay" wire:click="roomCloseEdit"></button>
            <div class="relative w-full max-w-xl mx-4 {{ $card }} focus:outline-none" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Edit Room</h3>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="roomCloseEdit" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form class="p-5" wire:submit.prevent="roomUpdate">
                    <div class="space-y-5">
                        <div>
                            <label class="{{ $label }}">Room Number</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="room_edit_number" autofocus>
                            @error('room_edit_number') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button" class="{{ $btnLite }}" wire:click="roomCloseEdit">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="roomUpdate" class="{{ $btnBlk }}">
                            <span wire:loading.remove wire:target="roomUpdate">Save Changes</span>
                            <span class="inline-flex items-center gap-2" wire:loading wire:target="roomUpdate">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- REQUIREMENT Edit Modal --}}
        @if($reqModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center" role="dialog" aria-modal="true" wire:key="modal-edit-req" wire:keydown.escape.window="reqCloseEdit">
            <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay" wire:click="reqCloseEdit"></button>
            <div class="relative w-full max-w-xl mx-4 {{ $card }} focus:outline-none" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Edit Requirement</h3>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="reqCloseEdit" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form class="p-5" wire:submit.prevent="reqUpdate">
                    <div class="space-y-5">
                        <div>
                            <label class="{{ $label }}">Requirement Name</label>
                            <input type="text" class="{{ $input }}" wire:model.defer="req_edit_name" autofocus>
                            @error('req_edit_name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button" class="{{ $btnLite }}" wire:click="reqCloseEdit">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="reqUpdate" class="{{ $btnBlk }}">
                            <span wire:loading.remove wire:target="reqUpdate">Save Changes</span>
                            <span class="inline-flex items-center gap-2" wire:loading wire:target="reqUpdate">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </main>
</div>
