<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Room Booking System</h1>
            <div class="flex bg-gray-100 rounded-md p-1">
                <button wire:click="switchView('form')"
                    class="px-4 py-2 text-sm font-medium rounded transition-colors {{ $view === 'form' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                    Book Room
                </button>
                <button wire:click="switchView('calendar')"
                    class="px-4 py-2 text-sm font-medium rounded transition-colors {{ $view === 'calendar' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                    Calendar View
                </button>
            </div>
        </div>
    </div>

    @if ($view === 'form')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- FORM --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Book a Meeting Room</h2>
                    <p class="text-gray-600 mb-6">Fill out the form below to request a room booking</p>

                    {{-- Booking Rules --}}
                    <div class="bg-blue-50 mb-2 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                        <h4 class="font-semibold mb-2">⏰ Booking Rules</h4>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Waktu dibagi dalam <strong>slot 30 menit</strong> (misalnya 09:00–09:30, 09:30–10:00).</li>
                            <li>Booking harus dimulai minimal <strong>15 menit dari sekarang</strong> (lead time).</li>
                            <li>Jika waktu yang kamu pilih terlewat saat mengisi form, sistem akan otomatis
                                <strong>menggeser ke slot berikutnya</strong> dan menampilkan pesan pemberitahuan.
                            </li>
                            <li>Kamu tidak bisa booking ke jam yang sudah lewat.</li>
                            <li>Untuk tanggal di masa depan, kamu bebas memilih jam berapapun.</li>
                        </ul>
                    </div>

                    <form wire:submit="submitBooking" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Meeting Title</label>
                                <input type="text" wire:model="meeting_title" placeholder="Enter meeting title"
                                    class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                @error('meeting_title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Room</label>
                                <select wire:model="room_id"
                                    class="w-full px-3 py-2 border text-gray-900 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Select room</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room['id'] }}" {{ !$room['available_req'] ? 'disabled' : '' }}>
                                            {{ $room['name'] }} {{ !$room['available_req'] ? '(Occupied)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Date</label>
                                <input type="date" wire:model="date" wire:change="selectDate($event.target.value)"
                                    class="w-full px-3 text-gray-900 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                @error('date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Number of Attendees</label>
                                <input type="number" wire:model="number_of_attendees" placeholder="0" min="1"
                                    class="w-full px-3 text-gray-900 placeholder:text-gray-400 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                @error('number_of_attendees') <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Start Time</label>
                                <input type="time" wire:model="start_time" min="{{ $minStart }}"
                                    class="w-full px-3 py-2 border text-gray-900 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">End Time</label>
                                <input type="time" wire:model="end_time" min="{{ $start_time ?: $minStart }}"
                                    class="w-full px-3 py-2 border text-gray-900 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-3">Additional Requirements</label>
                            <div class="grid grid-cols-2 gap-4">
                                @foreach (['projector', 'whiteboard', 'video_conference', 'catering', 'other'] as $req)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" wire:model.live="requirements" value="{{ $req }}"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span class="text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $req)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        @if (in_array('other', $requirements ?? [], true))
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-900 mb-2">Special Notes</label>
                                <textarea wire:model.defer="special_notes" placeholder="Please specify your other requirement…"
                                    rows="4"
                                    class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none"></textarea>
                                @error('special_notes')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="flex space-x-4 pt-4">
                            <button type="button" wire:click="$refresh"
                                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                                Clear Form
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 transition-colors">
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SIDEBAR --}}
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Room Availability</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        For {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                        @if($start_time && $end_time)
                            — {{ $start_time }}–{{ $end_time }}
                        @endif
                    </p>
                    <div class="space-y-3">
                        @foreach($rooms as $room)
                            <div
                                class="flex items-center justify-between p-3 {{ $room['available_req'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} border rounded-md">
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="w-2 h-2 {{ $room['available_req'] ? 'bg-green-500' : 'bg-red-500' }} rounded-full">
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $room['name'] }}</span>
                                </div>
                                <span
                                    class="text-sm font-medium {{ $room['available_req'] ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $room['available_req'] ? 'Available' : 'Occupied' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Bookings</h3>
                    <div class="space-y-4">
                        @foreach(array_slice($bookings, 0, 3) as $booking)
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-md flex items-center justify-center flex-shrink-0">
                                    <div class="w-2 h-2 bg-gray-600 rounded-full"></div>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $booking['meeting_title'] }}</h4>
                                    <p class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($booking['date'])->format('M j') }},
                                        {{ \Carbon\Carbon::parse($booking['start_time'])->format('g:i A') }} •
                                        {{ collect($rooms)->firstWhere('id', $booking['room_id'])['name'] ?? 'Unknown Room' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- CALENDAR --}}
        <div class="bg-white rounded-lg shadow-sm border-2 border-black overflow-hidden">
            <div class="bg-gray-50 border-b-2 border-black p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Room Schedule</h2>
                        <p class="text-gray-600 mt-1">
                            Week of {{ $currentWeek->format('M j') }} -
                            {{ $currentWeek->copy()->addDays(6)->format('M j, Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="previousMonth"
                            class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">«
                            Month</button>
                        <button wire:click="previousWeek"
                            class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">‹
                            Week</button>
                        <button wire:click="nextWeek"
                            class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Week
                            ›</button>
                        <button wire:click="nextMonth"
                            class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Month
                            »</button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <div class="min-w-full">
                    <div class="grid grid-cols-8 border-b border-gray-200">
                        <div class="p-4 border-r border-gray-200 bg-gray-50">
                            <span class="text-sm font-medium text-gray-600">Time / Room</span>
                        </div>
                        @foreach($weekDays as $day)
                            <div class="p-4 text-center border-r border-gray-200 bg-gray-50">
                                <div class="font-medium text-gray-900">{{ $day->format('D') }}</div>
                                <div class="text-sm text-gray-600">{{ $day->format('M j') }}</div>
                            </div>
                        @endforeach
                    </div>

                    @foreach($rooms as $room)
                        <div class="border-b border-gray-200">
                            <div class="grid grid-cols-8 bg-gray-50 border-b border-gray-100">
                                <div class="col-span-8 p-3 border-r border-gray-200">
                                    <div class="flex items-center space-x-2">
                                        <div
                                            class="w-3 h-3 {{ $room['available_req'] ? 'bg-green-500' : 'bg-red-500' }} rounded-full">
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $room['name'] }}</span>
                                    </div>
                                </div>
                            </div>

                            @foreach(array_chunk($timeSlots, 2) as $hourSlots)
                                <div class="grid grid-cols-8 border-b border-gray-100">
                                    <div class="p-2 text-xs text-gray-600 text-center border-r border-gray-200 bg-gray-50">
                                        {{ $hourSlots[0] }}
                                    </div>
                                    @foreach($weekDays as $day)
                                        <div class="border-r border-gray-200 relative h-16">
                                            @php
                                                $booking = null;
                                                foreach ($hourSlots as $timeSlot) {
                                                    $slotBooking = $this->getBookingForSlot($room['id'], $day->format('Y-m-d'), $timeSlot);
                                                    if ($slotBooking) {
                                                        $booking = $slotBooking;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            @if($booking)
                                                <div class="absolute inset-1 bg-red-100 border border-red-200 rounded p-1 text-xs">
                                                    <div class="font-medium text-red-800 truncate">{{ $booking['meeting_title'] }}</div>
                                                    <div class="text-red-600 text-xs">
                                                        {{ \Carbon\Carbon::parse($booking['start_time'])->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($booking['end_time'])->format('H:i') }}
                                                    </div>
                                                </div>
                                            @else
                                                <button wire:click="selectDate('{{ $day->format('Y-m-d') }}')"
                                                    class="w-full h-full hover:bg-green-50 transition-colors cursor-pointer group"
                                                    title="Book this time slot">
                                                    <div class="hidden group-hover:block text-xs text-green-600 text-center">
                                                        Click to book
                                                    </div>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-gray-50 border-t border-gray-200 p-4">
                <div class="flex items-center space-x-6 text-sm">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-100 border border-red-200 rounded"></div>
                        <span class="text-gray-600">Booked</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-white border border-gray-200 rounded"></div>
                        <span class="text-gray-600">Available</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-50 border border-green-200 rounded"></div>
                        <span class="text-gray-600">Hover to book</span>
                    </div>
                </div>
            </div>
        </div>

        @if($selectedDate)
            <div class="mt-8 bg-white rounded-lg shadow-sm border-2 border-black p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Quick Book for {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                </h3>
                <button wire:click="switchView('form')"
                    class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 transition-colors">
                    Go to Booking Form
                </button>
            </div>
        @endif
    @endif
</div>