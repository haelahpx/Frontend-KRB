<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">

    {{-- Style Variables --}}
    @php
    $card = 'bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden';
    $head = 'bg-gradient-to-r from-black to-gray-800';
    $hpad = 'px-8 py-6';
    $tag = 'w-2 h-8 bg-white rounded-full';
    $label = 'block text-sm font-semibold text-gray-700 mb-2';
    $input = 'w-full px-4 py-3 rounded-xl border-2 border-gray-200 text-gray-700 focus:border-black focus:ring-4 focus:ring-black/10 bg-gray-50 focus:bg-white transition';
    $btnBlk = 'px-4 py-2 text-sm rounded-xl bg-black text-white hover:bg-gray-800 disabled:opacity-60 font-semibold shadow-lg hover:shadow-xl transition';
    $btnRed = 'px-4 py-2 text-sm rounded-xl bg-red-600 text-white hover:bg-red-700 disabled:opacity-60 font-semibold shadow-lg hover:shadow-xl transition';
    $chip = 'inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-100 text-sm';
    $mono = 'text-xs font-mono text-gray-400 bg-gray-100 px-3 py-1 rounded-lg';
    $icoAvatar = 'w-12 h-12 bg-black rounded-2xl flex items-center justify-center text-white font-bold text-lg shrink-0';
    @endphp

    <div class="space-y-8">
        <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Announcement Management</h1>
                <p class="text-gray-600 mt-1">Manage your company announcements and events</p>
            </div>
            <button wire:click="toggleForm" class="{{ $btnBlk }} inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>{{ $formVisible ? 'Close Form' : 'Add Announcement' }}</span>
            </button>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('message'))
        <div class="bg-gray-900 text-white px-6 py-4 font-semibold rounded-2xl shadow-lg">{{ session('message') }}</div>
        @endif
        @if (session()->has('error'))
        <div class="bg-red-600 text-white px-6 py-4 font-semibold rounded-2xl shadow-lg">{{ session('error') }}</div>
        @endif

        @if($formVisible && !$editMode)
        <div class="{{ $card }}">
            <div class="{{ $head }} {{ $hpad }}">
                <div class="flex items-center gap-4">
                    <div class="{{ $tag }}"></div>
                    <div>
                        <h2 class="text-xl font-semibold text-white">Create New Announcement</h2>
                        <p class="text-gray-300 text-sm">Fill in the details for the new announcement.</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="save" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label class="{{ $label }}">🏢 Company</label>
                        <input type="text" wire:model.defer="company_name" class="{{ $input }}" value="{{ optional(Auth::user()->company)->company_name ?? '-' }}" readonly>
                        @error('company_id') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="{{ $label }}">📝 Description</label>
                        <input type="text" wire:model.defer="description" class="{{ $input }}" placeholder="Enter a short announcement or event detail...">
                        @error('description') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-1">
                        <label class="{{ $label }}">🗓️ Event Date (Optional)</label>
                        <input type="datetime-local" wire:model.defer="event_at" class="{{ $input }}">
                        @error('event_at') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="pt-4 flex items-center gap-4">
                    <button type="submit" class="{{ $btnBlk }} px-8 py-3">Create Announcement</button>
                    <button type="button" wire:click="cancelForm" class="px-8 py-3 rounded-xl border-2 font-semibold hover:bg-gray-100 transition">Cancel</button>
                </div>
            </form>
        </div>
        @endif

        <div class="{{ $card }}">
            <div class="{{ $head }} {{ $hpad }}">
                <div class="flex items-center gap-4">
                    <div class="{{ $tag }}"></div>
                    <div>
                        <h2 class="text-xl font-semibold text-white">Announcement History</h2>
                        <p class="text-gray-300 text-sm">A log of all past and upcoming announcements.</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-6 bg-gray-50/70 border-b border-gray-100">
                <div class="relative flex-1">
                    <input type="text" wire:model.live="search" placeholder="Search by description or event details..." class="{{ $input }} pl-12 w-full placeholder:text-gray-400">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                    </svg>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($announcements as $announcement)
                <div class="px-8 py-6 hover:bg-gray-50/70 transition-colors" wire:key="announcement-{{ $announcement->announcements_id }}">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="{{ $icoAvatar }}">{{ substr(optional(Auth::user()->company)->company_name ?? 'C', 0, 1) }}</div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-semibold text-gray-800 text-lg mb-2">{{ $announcement->description }}</h4>
                                <div class="flex flex-wrap gap-2">
                                    <span class="{{ $chip }}">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="font-medium text-gray-700">{{ optional(Auth::user()->company)->company_name ?? '-' }}</span>
                                    </span>
                                    @if($announcement->event_at)
                                    <span class="{{ $chip }}">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium text-gray-700">{{ $announcement->formatted_event_date }}</span>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 space-y-2">
                            <div class="{{ $mono }}">#{{ $announcement->announcements_id }}</div>
                            <div class="text-xs text-gray-500">Created: {{ $announcement->formatted_created_date ?? '-' }}</div>
                            <div class="flex flex-wrap gap-2 justify-end pt-2">
                                <button wire:click="startEdit({{ $announcement->announcements_id }})" class="{{ $btnBlk }}">Edit</button>
                                <button wire:click="confirmDelete({{ $announcement->announcements_id }})" class="{{ $btnRed }}">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-8 py-16 text-center text-gray-500">No announcements found. Try adding one!</div>
                @endforelse
            </div>

            @if($announcements->hasPages())
            <div class="px-8 py-6 bg-gray-50/70 border-t border-gray-100">
                <div class="flex justify-center">
                    {{ $announcements->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation --}}
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cancelDelete"></div>
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 text-center">
            <h3 class="text-xl font-bold text-gray-900">Confirm Deletion</h3>
            <p class="mt-2 text-gray-600">
                Are you sure you want to delete announcement <strong>#{{ $announcementId }}</strong>? This action cannot be undone.
            </p>
            <div class="mt-6 flex justify-center gap-4">
                <button wire:click="delete" class="{{ $btnRed }} px-8 py-3">Yes, Delete</button>
                <button wire:click="cancelDelete" class="px-8 py-3 rounded-xl border-2 font-semibold hover:bg-gray-100 transition">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Edit Modal --}}
    @if($editMode)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cancelForm"></div>
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-100 max-h-[90vh] flex flex-col">
            <div class="bg-gradient-to-r from-gray-900 to-black text-white p-6 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold">Edit Announcement</h3>
                    <p class="text-sm text-gray-300 font-mono">ID #{{ $announcementId }}</p>
                </div>
                <button wire:click="cancelForm" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form wire:submit.prevent="save">
                <div class="p-6 space-y-5 overflow-y-auto">
                    <div>
                        <label class="{{ $label }}">🏢 Company</label>
                        <input type="text" class="{{ $input }}" value="{{ optional(Auth::user()->company)->company_name ?? '-' }}" readonly>
                    </div>
                    <div>
                        <label class="{{ $label }}">📝 Description</label>
                        <input type="text" wire:model.defer="description" class="{{ $input }}" placeholder="Short announcement...">
                        @error('description') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">🗓️ Event Date (Optional)</label>
                        <input type="datetime-local" wire:model.defer="event_at" class="{{ $input }}">
                        @error('event_at') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="bg-gray-50 border-t p-6 flex justify-end gap-4">
                    <button type="button" wire:click="cancelForm" class="px-6 py-3 rounded-xl border-2 font-semibold hover:bg-gray-100 transition">Cancel</button>
                    <button type="submit" class="{{ $btnBlk }} px-6 py-3">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>