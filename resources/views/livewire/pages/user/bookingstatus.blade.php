<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-6">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Booking History</h1>
                <p class="text-gray-600">Riwayat & pengelolaan booking ruanganmu.</p>
            </div>
            <div class="flex gap-2">
                <button type="button" wire:click="openQuickBook"
                    class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800">+ Booking</button>
                <a href="{{ route('book-room') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Open Calendar</a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        {{-- Tabs --}}
        <div class="flex flex-wrap items-center justify-between gap-3 p-4 border-b">
            <div class="flex gap-2">
                <button wire:click="setTab('upcoming')"
                    class="px-3 py-1.5 rounded-md border text-sm {{ $tab==='upcoming' ? 'bg-black text-white border-black' : 'border-gray-300 hover:bg-gray-50' }}">Upcoming</button>
                <button wire:click="setTab('ongoing')"
                    class="px-3 py-1.5 rounded-md border text-sm {{ $tab==='ongoing' ? 'bg-black text-white border-black' : 'border-gray-300 hover:bg-gray-50' }}">Ongoing</button>
                <button wire:click="setTab('past')"
                    class="px-3 py-1.5 rounded-md border text-sm {{ $tab==='past' ? 'bg-black text-white border-black' : 'border-gray-300 hover:bg-gray-50' }}">Past</button>
                <button wire:click="setTab('all')"
                    class="px-3 py-1.5 rounded-md border text-sm {{ $tab==='all' ? 'bg-black text-white border-black' : 'border-gray-300 hover:bg-gray-50' }}">All</button>
            </div>

            {{-- Filters --}}
            <div class="flex flex-wrap gap-2">
                <input type="text" wire:model.debounce.400ms="q" placeholder="Search title/room…"
                       class="px-3 py-2 border border-gray-300 rounded-md text-sm w-56">
                <input type="date" wire:model="dateFrom" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                <input type="date" wire:model="dateTo"   class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                <select wire:model="roomFilter" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">All rooms</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->room_id }}">{{ $r->room_number }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- List --}}
        @if($bookings->isEmpty())
            <div class="p-8 text-center text-gray-500">
                Belum ada data pada filter ini.
            </div>
        @else
            <div class="divide-y">
                @foreach($bookings as $b)
                    @php
                        $roomName = $roomMap[$b->room_id] ?? 'Unknown';
                    @endphp
                    <div class="p-4 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-medium text-gray-900 truncate">{{ $b->meeting_title }}</div>
                            <div class="text-sm text-gray-600">
                                Room {{ $roomName }} •
                                {{ \Carbon\Carbon::parse($b->date)->format('D, M j, Y') }} •
                                {{ \Carbon\Carbon::parse($b->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($b->end_time)->format('H:i') }}
                            </div>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button wire:click="rebook({{ $b->bookingroom_id }})"
                                class="px-3 py-1.5 text-sm border rounded-md hover:bg-gray-100">Rebook</button>
                            <button wire:click="cancelBooking({{ $b->bookingroom_id }})"
                                class="px-3 py-1.5 text-sm border rounded-md hover:bg-gray-100">Cancel</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>

    {{-- Reuse modal quick-book --}}
    <livewire:booking.quick-book-modal />
</div>
