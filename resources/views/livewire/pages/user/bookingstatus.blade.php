<<div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4">
        <div class="flex flex-col md:flex-row md:items-center gap-6 pb-4 mb-4 border-b border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 w-full md:w-auto">
                <input type="text" placeholder="Requester (e.g. Finance, HR)"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-900">
                <select class="px-3 py-2 border border-gray-300 rounded-md text-gray-900">
                    <option value="">All Room</option>
                    <option value="room_A">Room A</option>
                    <option value="room_B">Room B</option>
                    <option value="room_C">Room C</option>
                </select>
                <select class="px-3 py-2 border border-gray-300 rounded-md text-gray-900">
                    <option value="">All Date</option>
                    <option value="recent">Recent first</option>
                    <option value="oldest">Oldest first</option>
                    <option value="due">Nearest due</option>
                </select>
            </div>
        </div>

    <div class="bg-white rounded-xl border-2 border-black/80 shadow-md divide-y">
        @forelse ($this->booked as $b)
            <div class="p-4 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md border-2 border-emerald-500/70 bg-emerald-50 text-emerald-700 font-medium">
                            Booked
                        </span>
                        <span>•</span>
                        <span class="font-mono text-gray-700">
                            {{ \Carbon\Carbon::parse($b['start_time'])->format('D, d M Y') }}
                        </span>
                    </div>

                    <h3 class="mt-1 text-base md:text-lg font-semibold text-gray-900 truncate">
                        {{ $b['title'] }}
                    </h3>

                    <div class="mt-1 text-sm text-gray-700 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span>🕒 {{ \Carbon\Carbon::parse($b['start_time'])->format('H:i') }}–{{ \Carbon\Carbon::parse($b['end_time'])->format('H:i') }}</span>
                        <span class="text-gray-300">•</span>
                        <span>🏠 {{ $b['room_name'] }}</span>
                    </div>
                </div>

                <div class="text-right text-xs text-gray-500">
                    <div>{{ \Carbon\Carbon::parse($b['start_time'])->format('d M Y H:i') }}</div>
                    <div>→ {{ \Carbon\Carbon::parse($b['end_time'])->format('d M Y H:i') }}</div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-500">
                No booked rooms found in this range.
            </div>
        @endforelse
    </div>
</div>
