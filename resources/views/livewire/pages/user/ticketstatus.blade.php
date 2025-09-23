<div class="space-y-5">
    <div class="space-y-4">
        @foreach ($tickets as $t)
            <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-6">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $t['subject'] }}</h3>
                        <div class="flex items-center gap-3 text-xs text-gray-500 mb-1">
                            <span class="font-mono font-medium">#{{ $t['ticket_id'] }}</span>
                            <span>•</span>

                            @php $priority = $t['priority'] ?? 'LOW'; @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium
                                @if($priority === 'URGENT') bg-red-50 text-red-700 border-2 border-red-400
                                @elseif($priority === 'HIGH') bg-orange-50 text-orange-700 border-2 border-orange-400
                                @elseif($priority === 'MEDIUM') bg-yellow-50 text-yellow-700 border-2 border-yellow-400
                                @else bg-gray-50 text-gray-700 border-2 border-gray-400 @endif">
                                <div class="flex items-center gap-1">
                                    <div class="w-1.5 h-1.5 rounded-full
                                        @if($priority === 'URGENT') bg-red-600
                                        @elseif($priority === 'HIGH') bg-orange-600
                                        @elseif($priority === 'MEDIUM') bg-yellow-600
                                        @else bg-gray-600 @endif"></div>
                                    {{ $priority }}
                                </div>
                            </span>
                        </div>
                    </div>

                    @php $status = $t['status'] ?? 'pending'; @endphp
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                        @if($status === 'pending') bg-yellow-50 text-yellow-700 border-2 border-yellow-500
                        @elseif($status === 'process') bg-blue-50 text-blue-700 border-2 border-blue-500
                        @elseif($status === 'complete') bg-green-50 text-green-700 border-2 border-green-500
                        @else bg-gray-50 text-gray-700 border-2 border-gray-500 @endif">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full
                                @if($status === 'pending') bg-yellow-500 animate-pulse
                                @elseif($status === 'process') bg-blue-500 animate-pulse
                                @elseif($status === 'complete') bg-green-600
                                @else bg-gray-600 @endif"></div>
                            {{ ucfirst($status) }}
                        </div>
                    </span>
                </div>

                <p class="text-sm text-gray-600 leading-relaxed">{{ $t['description'] }}</p>
            </div>
        @endforeach
    </div>

    @if (($ticket['status'] ?? null) === 'process')
        <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">Konfirmasi Penyelesaian</h3>
                    <p class="text-xs text-gray-500">Klik tombol di bawah untuk menandai tiket sebagai selesai</p>
                </div>
                <button wire:click="markComplete"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Tiket diterima
                </button>
            </div>
        </div>
    @endif
</div>