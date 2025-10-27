<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h1 class="text-3xl font-bold text-gray-900">Online Meeting Booking</h1>

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
                        Form
                    </button>
                    <button wire:click="switchView('calendar')"
                        class="px-4 py-2 text-sm font-medium rounded transition-colors {{ $view === 'calendar' ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                        Calendar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($view === 'form')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Book an Online Meeting</h2>
                    <p class="text-gray-600 mb-6">Pilih Zoom atau Google Meet. Link muncul setelah disetujui receptionist.</p>

                    <div class="bg-blue-50 mb-4 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                        <h4 class="font-semibold mb-2">⏰ Booking Rules</h4>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Mulai minimal <strong>15 menit dari sekarang</strong>.</li>
                            <li>Isi <strong>judul meeting</strong> yang jelas.</li>
                            <li>Link meeting tersedia setelah <strong>approved</strong>.</li>
                        </ul>
                    </div>

                    <form wire:submit="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Meeting Title</label>
                            <input type="text" wire:model="meeting_title"
                                class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
                            @error('meeting_title') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Platform</label>
                                <select wire:model="online_provider"
                                        class="w-full px-3 py-2 border text-gray-900 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Pilih platform</option>
                                    <option value="zoom">Zoom</option>
                                    <option value="google_meet">Google Meet</option>
                                </select>
                                @error('online_provider') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Tanggal</label>
                                <input type="date" wire:model="date"
                                    class="w-full px-3 py-2 border-2 text-gray-900 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
                                @error('date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="hidden md:block"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Start Time</label>
                                <input type="time" wire:model="start_time"
                                    class="w-full px-3 py-2 border text-gray-900 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
                                @error('start_time') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">End Time</label>
                                <input type="time" wire:model="end_time"
                                    class="w-full px-3 py-2 border text-gray-900 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
                                @error('end_time') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex gap-4 pt-2">
                            <button type="reset"
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
                <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Platform Tips</h3>
                    <ul class="text-sm text-gray-700 list-disc pl-5 space-y-1">
                        <li>Zoom cocok untuk webinar / breakout rooms.</li>
                        <li>Google Meet praktis untuk Google Workspace.</li>
                        <li>Host sebaiknya hadir 5 menit lebih awal.</li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Booking Saya</h3>
                    <div class="space-y-3">
                        @forelse($bookings as $b)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $b->meeting_title }}</h4>
                                        <p class="text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($b->date)->format('d M Y') }},
                                            {{ \Carbon\Carbon::parse($b->start_time)->timezone('Asia/Jakarta')->format('H:i') }}–{{ \Carbon\Carbon::parse($b->end_time)->timezone('Asia/Jakarta')->format('H:i') }}
                                            • {{ ucfirst(str_replace('_', ' ', $b->online_provider)) }}
                                        </p>

                                        {{-- Visible link + copy button under details (approved only) --}}
                                        @if(($b->status ?? null) === 'approved' && ($b->online_meeting_url ?? null))
                                            <div x-data="{ copied:false }" class="mt-1 flex items-center gap-2">
                                                <a href="{{ $b->online_meeting_url }}" target="_blank"
                                                   class="text-blue-600 underline text-sm inline-block">
                                                    Join Meeting
                                                </a>
                                                <button type="button"
                                                        class="text-xs px-2 py-1 border border-gray-300 rounded hover:bg-gray-50"
                                                        @click="navigator.clipboard.writeText('{{ $b->online_meeting_url }}'); copied=true; setTimeout(()=>copied=false,1500)">
                                                    Copy link
                                                </button>
                                                <span x-show="copied" x-cloak class="text-xs text-green-600">Copied!</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Status pill with inline Join link when approved --}}
                                    <span class="px-3 py-1 text-xs md:text-sm font-medium rounded-full
                                        {{ ($b->status ?? '') === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                        (($b->status ?? '') === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($b->status ?? 'pending') }}
                                        @if(($b->status ?? null) === 'approved' && ($b->online_meeting_url ?? null))
                                            • <a href="{{ $b->online_meeting_url }}" target="_blank" class="underline">Join</a>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada booking.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @else
        <div wire:poll.60s class="bg-white rounded-lg shadow-sm border-2 border-black overflow-hidden">
            <div class="bg-gray-50 border-b-2 border-black p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Online Meeting Schedule</h2>
                        <p class="text-gray-600 mt-1">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="previousMonth"
                                class="hidden md:inline px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">« Month</button>
                        <button wire:click="previousWeek"
                                class="hidden md:inline px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">‹ Week</button>
                        <input type="date" wire:model.live="date" wire:change="selectDate($event.target.value)"
                            class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-gray-900" />

                        <button wire:click="nextWeek"
                                class="hidden md:inline px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Week ›</button>
                        <button wire:click="nextMonth"
                                class="hidden md:inline px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Month »</button>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="flex">
                    <div class="w-24 shrink-0 border-r border-gray-200 bg-gray-50 sticky left-0 z-10">
                        <div class="h-10 border-b border-gray-200"></div>
                        @foreach($timeSlots as $t)
                            <div class="h-7 md:h-8 text-[10px] md:text-xs text-gray-600 flex items-center justify-center border-b border-gray-100">
                                {{ $t }}
                            </div>
                        @endforeach
                    </div>

                    <div class="overflow-x-auto">
                        <div class="min-w-[480px]">
                            <div class="grid" style="grid-template-columns: repeat({{ count($providers) }}, minmax(200px,1fr));">
                                @foreach($providers as $p)
                                    <div class="h-10 bg-gray-50 border-b border-r border-gray-200 px-3 flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-gray-600 mr-2"></div>
                                        <span class="text-sm font-medium text-gray-900 truncate">{{ $p['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>

                            @foreach($timeSlots as $t)
                                <div class="grid border-b border-gray-100"
                                     style="grid-template-columns: repeat({{ count($providers) }}, minmax(200px,1fr));">
                                    @foreach($providers as $p)
                                        @php $slotBooking = $this->getOnlineBookingForSlot($p['key'], $date, $t); @endphp

                                        @if($slotBooking)
                                            <div class="h-7 md:h-8 relative border-r border-gray-100">
                                                <div class="absolute inset-1 bg-red-100 border border-red-200 rounded px-2 flex items-center">
                                                    <span class="text-[10px] md:text-xs text-red-800 truncate">
                                                        {{ $slotBooking['meeting_title'] }}
                                                        ({{ \Carbon\Carbon::parse($slotBooking['start_time'])->timezone('Asia/Jakarta')->format('H:i') }}–{{ \Carbon\Carbon::parse($slotBooking['end_time'])->timezone('Asia/Jakarta')->format('H:i') }})
                                                        @if(($slotBooking['status'] ?? null) === 'approved' && ($slotBooking['online_meeting_url'] ?? null))
                                                            • <a class="underline" target="_blank" href="{{ $slotBooking['online_meeting_url'] }}">Join</a>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <button
                                                wire:click="selectCalendarSlot('{{ $p['key'] }}', '{{ $date }}', '{{ $t }}')"
                                                class="h-7 md:h-8 w-full border-r border-gray-100 hover:bg-green-50 transition-colors">
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
                    <span class="inline-flex items-center gap-2">
                        <span class="w-3 h-3 bg-red-100 border border-red-200 rounded inline-block"></span> Booked
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span class="w-3 h-3 bg-white border border-gray-200 rounded inline-block"></span> Available
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span class="w-3 h-3 bg-green-50 border border-green-200 rounded inline-block"></span> Hover to book
                    </span>
                </div>
            </div>
        </div>
    @endif
</div>
