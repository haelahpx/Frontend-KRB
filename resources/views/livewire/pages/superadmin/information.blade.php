<div class="p-6 bg-white">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Information Management</h1>
        <p class="text-gray-600 mt-2">Manage your company information and events</p>
    </div>

    {{-- Flash messages --}}
    @if (session()->has('message'))
    <div class="mb-6 bg-gray-900 text-white px-6 py-4 rounded-lg">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
    <div class="mb-6 bg-red-600 text-white px-6 py-4 rounded-lg">{{ session('error') }}</div>
    @endif

    {{-- Controls --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-3 sm:gap-0 sm:items-center sm:justify-between">
        <input
            wire:model.live="search"
            type="text"
            placeholder="Search..."
            class="w-full sm:w-1/3 px-3 py-2 border rounded-md" />

        <button
            wire:click="toggleForm"
            class="bg-gray-900 text-white px-6 py-2 rounded-lg hover:bg-gray-800">
            {{ $editMode ? 'Edit Information' : ($formVisible ? 'Close Form' : '+ Add Information') }}
        </button>
    </div>

    {{-- Inline Create/Edit Form --}}
    @if ($formVisible)
    <div class="mb-6 border rounded-lg shadow-sm">
        <form wire:submit.prevent="save" class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">
                    {{ $editMode ? 'Edit Information' : 'Create Information' }}
                </h2>
                @if ($editMode && $informationId)
                <span class="text-sm text-gray-500">#{{ $informationId }}</span>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Company (readonly display) --}}
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Company</label>
                    @php
                    $companyName = optional(auth()->user()?->company)->company_name ?? '-';
                    @endphp
                    <input
                        type="text"
                        class="mt-1 w-full px-3 py-2 border rounded-md bg-gray-50"
                        value="{{ $companyName }}"
                        readonly>
                    @error('company_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <input
                        type="text"
                        wire:model.defer="description"
                        class="mt-1 w-full px-3 py-2 border rounded-md"
                        placeholder="Short information..." />
                    @error('description')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Event date (optional) --}}
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Event Date (optional)</label>
                    <input
                        type="datetime-local"
                        wire:model.defer="event_at"
                        class="mt-1 w-full px-3 py-2 border rounded-md" />
                    @error('event_at')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button
                    type="submit"
                    class="bg-gray-900 text-white px-6 py-2 rounded-lg hover:bg-gray-800">
                    {{ $editMode ? 'Save Changes' : 'Create' }}
                </button>

                <button
                    type="button"
                    wire:click="cancelForm"
                    class="px-6 py-2 rounded-lg border hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('information_id')" class="px-6 py-3 text-left cursor-pointer">ID</th>
                        <th wire:click="sortBy('company_id')" class="px-6 py-3 text-left cursor-pointer">Company Name</th>
                        <th class="px-6 py-3 text-left">Description</th>
                        <th wire:click="sortBy('event_at')" class="px-6 py-3 text-left cursor-pointer">Event Date</th>
                        <th wire:click="sortBy('created_at')" class="px-6 py-3 text-left cursor-pointer">Created</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($information as $info)
                    <tr>
                        <td class="px-6 py-4">{{ $info->information_id }}</td>
                        <td class="px-6 py-4">{{ optional(Auth::user()->company)->company_name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $info->description }}</td>
                        <td class="px-6 py-4">
                            {{ $info->formatted_event_date ?? 'No date' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $info->formatted_created_date ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <button
                                wire:click="startEdit({{ $info->information_id }})"
                                class="text-gray-600 hover:text-gray-900">
                                Edit
                            </button>
                            <button
                                wire:click="confirmDelete({{ $info->information_id }})"
                                class="text-red-600 hover:text-red-900">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No information found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($information->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $information->links() }}
        </div>
        @endif
    </div>

    {{-- Inline delete confirmation --}}
    @if ($showDeleteConfirm)
    <div class="mt-6 p-4 border rounded-lg bg-red-50">
        <p class="text-red-700">
            Are you sure you want to delete information
            <strong>#{{ $informationId }}</strong>?
        </p>
        <div class="mt-3 flex gap-3">
            <button wire:click="delete" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Yes, Delete
            </button>
            <button wire:click="cancelDelete" class="px-4 py-2 rounded border hover:bg-white">
                Cancel
            </button>
        </div>
    </div>
    @endif
</div>