<div class="p-6 bg-gray-50 min-h-screen">
    <h2 class="text-xl font-bold mb-4">Manage Rooms</h2>

    {{-- Form --}}
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}" class="mb-6">
        <input type="text" wire:model="room_number" placeholder="Room Number"
            class="border rounded px-3 py-2 w-64">
        @error('room_number') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

        <button type="submit"
            class="px-4 py-2 rounded bg-black text-white ml-2">
            {{ $isEdit ? 'Update' : 'Add' }} Room
        </button>

        @if($isEdit)
            <button type="button" wire:click="resetInput"
                class="px-4 py-2 rounded bg-gray-400 text-white ml-2">
                Cancel
            </button>
        @endif
    </form>

    {{-- Messages --}}
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <table class="w-full border-collapse bg-white shadow rounded">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-2">ID</th>
                <th class="p-2">Room Number</th>
                <th class="p-2">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rooms as $room)
                <tr class="border-t">
                    <td class="p-2">{{ $room->room_id }}</td>
                    <td class="p-2">{{ $room->room_number }}</td>
                    <td class="p-2">
                        <button wire:click="edit({{ $room->room_id }})"
                            class="px-3 py-1 bg-blue-600 text-white rounded">Edit</button>
                        <button wire:click="delete({{ $room->room_id }})"
                            class="px-3 py-1 bg-red-600 text-white rounded"
                            onclick="return confirm('Are you sure?')">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $rooms->links() }}
    </div>
</div>
