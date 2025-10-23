{{-- resources/views/livewire/pages/superadmin/vehicle.blade.php --}}
<div class="bg-gray-50">
    @php
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $label = 'block text-sm font-semibold text-gray-700 mb-2';
        $input = 'w-full px-4 py-3 rounded-xl border-2 border-gray-200 text-gray-700 focus:border-black focus:ring-4 focus:ring-black/10 bg-gray-50 focus:bg-white transition';
        $btnBlk = 'px-4 py-2 text-sm rounded-xl bg-black text-white hover:bg-gray-800 disabled:opacity-60 font-semibold shadow-lg hover:shadow-xl transition';
        $btnRed = 'px-4 py-2 text-sm rounded-xl bg-red-600 text-white hover:bg-red-700 disabled:opacity-60 font-semibold shadow-lg hover:shadow-xl transition';
        $btnLite = 'px-4 py-2 text-sm rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 disabled:opacity-60 font-semibold transition';
        $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
        $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
        $ico = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">
        {{-- HEADER --}}
        <div
            class="rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <x-heroicon-o-truck class="w-6 h-6 text-white" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg sm:text-xl font-semibold">Manage Vehicles</h2>
                        <p class="text-sm text-white/80">
                            Company: {{ optional(Auth::user()->company)->company_name ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM + LIST --}}
        <section class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-gray-900">Add New Vehicle</h3>
                <div class="w-full sm:w-72">
                    <input type="text" wire:model.live="search" class="{{ $input }} h-10"
                        placeholder="Search by name or plate number…">
                </div>
            </div>

            {{-- FORM --}}
            <form class="p-5" wire:submit.prevent="create">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="{{ $label }}">Name</label>
                        <input type="text" wire:model.defer="name" class="{{ $input }}" placeholder="e.g. Avanza">
                        @error('name') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Category</label>
                        <input type="text" wire:model.defer="category" class="{{ $input }}"
                            placeholder="car, pickup, motorcycle">
                        @error('category') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Plate Number</label>
                        <input type="text" wire:model.defer="plate_number" class="{{ $input }}"
                            placeholder="e.g. B 1234 ABC">
                        @error('plate_number') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Year</label>
                        <input type="text" wire:model.defer="year" class="{{ $input }}" placeholder="e.g. 2023">
                        @error('year') <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Notes</label>
                        <input type="text" wire:model.defer="notes" class="{{ $input }}" placeholder="Optional notes">
                    </div>
                    <div class="flex items-center gap-3 pt-7">
                        <input id="is_active" type="checkbox" wire:model.defer="is_active"
                            class="h-4 w-4 rounded border-gray-300">
                        <label for="is_active" class="text-sm text-gray-700">Active</label>
                    </div>
                </div>

                <div class="pt-5">
                    <button type="submit" class="{{ $btnBlk }}">Save</button>
                </div>
            </form>

            {{-- LIST --}}
            <div class="divide-y divide-gray-200">
                @forelse ($rows as $row)
                    <div class="px-5 py-5 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="{{ $ico }}">
                                    <x-heroicon-o-truck class="w-5 h-5 text-white" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-semibold text-gray-900">{{ $row->name }}</h4>
                                        <span class="{{ $mono }}">{{ $row->plate_number }}</span>
                                        @if($row->deleted_at)
                                            <span class="{{ $chip }}"><span
                                                    class="w-2 h-2 bg-rose-500 rounded-full"></span>Trashed</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <span class="{{ $chip }}">Category: {{ $row->category }}</span>
                                        <span class="{{ $chip }}">Year: {{ $row->year }}</span>
                                        @if ($row->is_active)
                                            <span class="{{ $chip }}"><span
                                                    class="w-2 h-2 bg-emerald-500 rounded-full"></span>Active</span>
                                        @else
                                            <span class="{{ $chip }}"><span
                                                    class="w-2 h-2 bg-gray-400 rounded-full"></span>Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="text-right shrink-0 space-y-2">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    @if(!$row->deleted_at)
                                        <button class="{{ $btnLite }}"
                                            wire:click="openEdit({{ $row->vehicle_id }})">Edit</button>
                                        <button class="{{ $btnRed }}" wire:click="delete({{ $row->vehicle_id }})"
                                            onclick="return confirm('Move to trash?')">Delete</button>
                                    @else
                                        <button class="{{ $btnLite }}"
                                            wire:click="restore({{ $row->vehicle_id }})">Restore</button>
                                        <button class="{{ $btnRed }}" wire:click="forceDelete({{ $row->vehicle_id }})"
                                            onclick="return confirm('Permanently delete?')">Delete Permanently</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-gray-500">No vehicles found.</div>
                @endforelse
            </div>

            @if($rows->hasPages())
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $rows->links() }}
                    </div>
                </div>
            @endif
        </section>

        {{-- EDIT MODAL --}}
        @if($modalEdit)
            <div class="fixed inset-0 z-50 flex items-center justify-center" role="dialog" aria-modal="true">
                <button type="button" class="absolute inset-0 bg-black/50" wire:click="$set('modalEdit', false)"></button>
                <div class="relative w-full max-w-xl mx-4 {{ $card }}">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Edit Vehicle</h3>
                        <button type="button" wire:click="$set('modalEdit', false)">
                            <x-heroicon-o-x-mark class="w-5 h-5 text-gray-600" />
                        </button>
                    </div>
                    <form class="p-5 space-y-5" wire:submit.prevent="update">
                        <div>
                            <label class="{{ $label }}">Name</label>
                            <input type="text" wire:model.defer="edit_name" class="{{ $input }}">
                        </div>
                        <div>
                            <label class="{{ $label }}">Category</label>
                            <input type="text" wire:model.defer="edit_category" class="{{ $input }}">
                        </div>
                        <div>
                            <label class="{{ $label }}">Plate Number</label>
                            <input type="text" wire:model.defer="edit_plate_number" class="{{ $input }}">
                        </div>
                        <div>
                            <label class="{{ $label }}">Year</label>
                            <input type="text" wire:model.defer="edit_year" class="{{ $input }}">
                        </div>
                        <div>
                            <label class="{{ $label }}">Notes</label>
                            <input type="text" wire:model.defer="edit_notes" class="{{ $input }}">
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" wire:model.defer="edit_is_active"
                                class="h-4 w-4 border-gray-300 rounded">
                            <label class="text-sm text-gray-700">Active</label>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                            <button type="button" class="{{ $btnLite }}"
                                wire:click="$set('modalEdit', false)">Cancel</button>
                            <button type="submit" class="{{ $btnBlk }}">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </main>
</div>