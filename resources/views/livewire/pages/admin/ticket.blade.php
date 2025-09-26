<div class="p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-4">Ticket Management</h1>

    @if (session()->has('message'))
    <div class="mb-4 p-3 rounded bg-green-600 text-white">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
    <div class="mb-4 p-3 rounded bg-red-600 text-white">{{ session('error') }}</div>
    @endif

    <table class="w-full border border-gray-300 rounded">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Subject</th>
                <th class="px-4 py-2">Description</th>
                <th class="px-4 py-2">Priority</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Assign Agent</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tickets as $ticket)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $ticket->ticket_id }}</td>
                <td class="px-4 py-2">{{ $ticket->subject }}</td>
                <td class="px-4 py-2">{{ $ticket->description }}</td>
                <td class="px-4 py-2">{{ ucfirst($ticket->priority) }}</td>
                <td class="px-4 py-2">{{ $ticket->status }}</td>
                <td class="px-4 py-2">
                    <form wire:submit.prevent="assignAgent({{ $ticket->ticket_id }})" class="flex items-center gap-2">
                        <select wire:model="selectedAgentId" class="border rounded px-2 py-1">
                            <option value="">-- Select Agent (role: user) --</option>
                            @foreach ($agents as $agent)
                            <option value="{{ $agent->user_id }}">{{ $agent->full_name }}</option>
                            @endforeach
                        </select>
                        @error('selectedAgentId') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">
                            Assign
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $tickets->links() }}
    </div>
</div>