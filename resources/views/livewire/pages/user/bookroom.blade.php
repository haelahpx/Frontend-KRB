<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h1 class="text-3xl font-bold text-gray-900">Room Booking System</h1>

            <div class="flex items-center gap-3">
                <div class="flex bg-gray-100 rounded-md p-1">
                    <a href="{{ route('book-room') }}"
                       class="px-4 py-2 text-sm font-medium rounded transition-colors
                            {{ request()->routeIs('book-room') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:text-gray-900' }}">
                        Offline (Room)
                    </a>
                    <a href="{{ route('user.meetonline') }}"
                       class="px-4 py-2 text-sm font-medium rounded transition-colors
                              {{ request()->routeIs('user.meetonline') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:text-gray-900' }}">
                        Online Meeting
                    </a>
                </div>

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
    </div>

    <div id="global-toast-container" class="fixed top-6 right-6 z-50 flex flex-col gap-2"></div>

    @if ($view === 'form')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Book a Meeting Room</h2>
                    <p class="text-gray-600 mb-6">Fill out the form below to request a room booking</p>

                    <div class="bg-blue-50 mb-2 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                        <h4 class="font-semibold mb-2">⏰ Booking Rules</h4>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Slot 30 menit.</li>
                            <li>Judul meeting jelas.</li>
                            <li>Minimal mulai 15 menit dari sekarang.</li>
                            <li>Jam yang lewat otomatis digeser ke slot berikutnya.</li>
                            <li>Tidak bisa pesan ke jam yang sudah lewat.</li>
                        </ul>
                    </div>

                    <form wire:submit.prevent="submitBooking" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Meeting Title</label>
                                <input type="text" wire:model.defer="meeting_title" placeholder="Enter meeting title"
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
                                <input type="date" wire:model.live="date" wire:change="selectDate($event.target.value)"
                                    class="w-full px-3 text-gray-900 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                @error('date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Number of Attendees</label>
                                <input type="number" wire:model.defer="number_of_attendees" placeholder="0" min="1"
                                    class="w-full px-3 text-gray-900 placeholder:text-gray-400 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                @error('number_of_attendees') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Start Time</label>
                                <input type="time" wire:model.live="start_time" min="{{ $minStart }}"
                                    class="w-full px-3 py-2 border text-gray-900 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">End Time</label>
                                <input type="time" wire:model.live="end_time" min="{{ $start_time ?: $minStart }}"
                                    class="w-full px-3 py-2 border text-gray-900 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-3">Additional Requirements</label>
                            <div class="grid grid-cols-2 gap-4">
                                @forelse ($requirementsMaster as $reqName)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" wire:model.live="requirements" value="{{ $reqName }}"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span class="text-sm text-gray-900">{{ $reqName }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 col-span-2">Belum ada requirements untuk perusahaan Anda.</p>
                                @endforelse

                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" wire:model.live="requirements" value="other"
                                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                    <span class="text-sm text-gray-900">Other</span>
                                </label>
                            </div>
                        </div>

                        @if (in_array('other', $requirements ?? [], true))
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-900 mb-2">Special Notes</label>
                                <textarea wire:model.defer="special_notes" rows="4" placeholder="Please specify your other requirement…"
                                    class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none"></textarea>
                                @error('special_notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        {{-- NEW: Satu checkbox untuk inform ke Information Dept (simpan 'request' jika dicentang) --}}
                        <div class="pt-2">
                            <label class="inline-flex items-center gap-3">
                                <input type="checkbox" wire:model.live="informInfo"
                                       class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                <span class="text-sm text-gray-900">
                                    Minta Information Dept menginformasikan meeting ini (akan disimpan sebagai <span class="font-semibold">request</span>)
                                </span>
                            </label>
                        </div>

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

            <div class="space-y-6">
                <div wire:poll.60s class="bg-white rounded-lg shadow-sm border-2 border-black p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Room Availability</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        For {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                        @if($start_time && $end_time) — {{ $start_time }}–{{ $end_time }} @endif
                    </p>
                    <div class="space-y-3">
                        @foreach($rooms as $room)
                            <div class="flex items-center justify-between p-3 {{ $room['available_req'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} border rounded-md">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 {{ $room['available_req'] ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></div>
                                    <span class="font-medium text-gray-900">{{ $room['name'] }}</span>
                                </div>
                                <span class="text-sm font-medium {{ $room['available_req'] ? 'text-green-700' : 'text-red-700' }}">
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
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-medium text-gray-900">{{ $booking['meeting_title'] }}</h4>
                                        @if(isset($booking['status']))
                                            @if($booking['status'] === 'approved')
                                                <span class="text-xs font-semibold text-green-800 bg-green-100 px-2 py-0.5 rounded">Approved</span>
                                            @elseif($booking['status'] === 'pending')
                                                <span class="text-xs font-semibold text-yellow-800 bg-yellow-100 px-2 py-0.5 rounded">Pending</span>
                                            @elseif($booking['status'] === 'rejected')
                                                <span class="text-xs font-semibold text-red-800 bg-red-100 px-2 py-0.5 rounded">Rejected</span>
                                            @else
                                                <span class="text-xs font-semibold text-green-800 bg-green-100 px-2 py-0.5 rounded">Completed</span> 
                                            @endif   
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($booking['date'])->format('M j') }},
                                        {{ \Carbon\Carbon::parse($booking['start_time'])->format('g:i A') }} •
                                        {{ collect($rooms)->firstWhere('id', $booking['room_id'])['name'] ?? 'Unknown Room' }}
                                        @if(!empty($booking['requestinformation']))
                                            <span class="ml-2 text-xs text-gray-500">• Info: {{ ucfirst($booking['requestinformation']) }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    @else
        <div wire:poll.60s class="bg-white rounded-lg shadow-sm border-2 border-black overflow-hidden">
            <div class="bg-gray-50 border-b-2 border-black p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Room Schedule</h2>
                        <p class="text-gray-600 mt-1">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="previousMonth" class="hidden md:inline px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">« Month</button>
                        <button wire:click="previousWeek" class="hidden md:inline px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">‹ Week</button>
                        <input type="date" wire:model.live="date" wire:change="selectDate($event.target.value)" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-gray-900" />
                        <button wire:click="nextWeek" class="hidden md:inline px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Week ›</button>
                        <button wire:click="nextMonth" class="hidden md:inline px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Month »</button>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing rooms {{ ($roomsPage - 1) * $roomsPerPage + 1 }} –
                        {{ min($roomsPage * $roomsPerPage, count($rooms)) }}
                        of {{ count($rooms) }}
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="prevRoomPage" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm hover:bg-gray-50 disabled:opacity-50" {{ $roomsPage <= 1 ? 'disabled' : '' }}>‹ Rooms</button>
                        <button wire:click="nextRoomPage" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm hover:bg-gray-50 disabled:opacity-50" {{ $roomsPage >= $roomsTotalPages ? 'disabled' : '' }}>Rooms ›</button>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="flex">
                    <div class="w-20 shrink-0 border-r border-gray-200 bg-gray-50 sticky left-0 z-10">
                        <div class="h-10 border-b border-gray-200"></div>
                        @foreach($timeSlots as $t)
                            <div class="h-7 md:h-8 text-[10px] md:text-xs text-gray-600 flex items-center justify-center border-b border-gray-100">{{ $t }}</div>
                        @endforeach
                    </div>

                    <div class="overflow-x-auto">
                        <div class="min-w-[640px]">
                            <div class="grid" style="grid-template-columns: repeat({{ count($visibleRooms) }}, minmax(160px,1fr));">
                                @foreach($visibleRooms as $room)
                                    <div class="h-10 bg-gray-50 border-b border-r border-gray-200 px-3 flex items-center">
                                        <div class="w-2 h-2 rounded-full {{ $room['available_req'] ? 'bg-green-500' : 'bg-red-500' }} mr-2"></div>
                                        <span class="text-sm font-medium text-gray-900 truncate">{{ $room['name'] }}</span>
                                    </div>
                                @endforeach
                            </div>

                            @foreach($timeSlots as $t)
                                <div class="grid border-b border-gray-100" style="grid-template-columns: repeat({{ count($visibleRooms) }}, minmax(160px,1fr));">
                                    @foreach($visibleRooms as $room)
                                        @php $slotBooking = $this->getBookingForSlot($room['id'], $date, $t); @endphp
                                        @if($slotBooking)
                                            <div class="h-7 md:h-8 relative border-r border-gray-100">
                                                <div class="absolute inset-1 bg-red-100 border border-red-200 rounded px-2 flex items-center justify-between">
                                                    <div class="truncate text-[10px] md:text-xs text-red-800">
                                                        {{ $slotBooking['meeting_title'] }}
                                                        ({{ \Carbon\Carbon::parse($slotBooking['start_time'])->format('H:i') }}–{{ \Carbon\Carbon::parse($slotBooking['end_time'])->format('H:i') }})
                                                    </div>
                                                    <div class="ml-2 flex-shrink-0">
                                                        @if(isset($slotBooking['status']) && $slotBooking['status'] === 'approved')
                                                            <span class="text-xs font-semibold text-green-800 bg-green-100 px-2 py-0.5 rounded">Approved</span>
                                                        @elseif(isset($slotBooking['status']) && $slotBooking['status'] === 'pending')
                                                            <span class="text-xs font-semibold text-yellow-800 bg-yellow-100 px-2 py-0.5 rounded">Pending</span>
                                                        @else
                                                            <span class="text-xs font-semibold text-red-800 bg-red-100 px-2 py-0.5 rounded">Rejected</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <button
                                                wire:click="selectCalendarSlot({{ $room['id'] }}, '{{ $date }}', '{{ $t }}')"
                                                class="h-7 md:h-8 w-full border-r border-gray-100 hover:bg-green-50 transition-colors"
                                                title="Book {{ $room['name'] }} at {{ $t }}">
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 border-t border-gray-200 p-3 md:p-4">
                <div class="flex items-center gap-4 text-xs md:text-sm">
                    <span class="inline-flex items-center gap-2"><span class="w-3 h-3 bg-red-100 border border-red-200 rounded inline-block"></span> Booked</span>
                    <span class="inline-flex items-center gap-2"><span class="w-3 h-3 bg-white border border-gray-200 rounded inline-block"></span> Available</span>
                    <span class="inline-flex items-center gap-2"><span class="w-3 h-3 bg-green-50 border border-green-200 rounded inline-block"></span> Hover to book</span>
                </div>
            </div>
        </div>
    @endif

    <livewire:booking.quick-book-modal />
</div>

<script>
    (function(){
        const container = document.getElementById('global-toast-container');
        function showToast(type, message) {
            if (!message) return;
            const el = document.createElement('div');
            el.className = ['px-4','py-2','rounded','shadow','max-w-sm','text-sm','flex','items-center','justify-between','border'].join(' ');
            if (type === 'success') { el.classList.add('bg-green-50','text-green-800','border-green-200'); }
            else if (type === 'error') { el.classList.add('bg-red-50','text-red-800','border-red-200'); }
            else { el.classList.add('bg-blue-50','text-blue-800','border-blue-200'); }
            el.innerHTML = `<div class="truncate pr-3">${message}</div><button class="ml-3 text-sm font-semibold">OK</button>`;
            el.querySelector('button').addEventListener('click', () => el.remove());
            container.appendChild(el);
            setTimeout(()=> el.remove(), 5000);
        }
        window.addEventListener('toast', e => {
            const d = e.detail || {};
            showToast(d.type || 'info', d.message || '');
        });
    })();
</script>
