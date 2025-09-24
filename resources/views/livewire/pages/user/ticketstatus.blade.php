<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
        <div class="flex items-center justify-start gap-3">
            <h1 class="text-3xl font-bold text-gray-900">Support Ticket System</h1>

            @php
                $isCreate = request()->routeIs('create-ticket') || request()->is('create-ticket');
                $isStatus = request()->routeIs('ticketstatus') || request()->is('ticketstatus');
            @endphp

            <div class="ml-auto inline-flex rounded-md overflow-hidden bg-gray-100 border border-gray-200">
                <a href="{{ route('create-ticket') }}" @class([
                    'px-4 py-2 text-sm font-medium transition-colors',
                    'bg-gray-900 text-white' => $isCreate,
                    'text-gray-700 hover:text-gray-900' => !$isCreate,
                ])>
            Create Ticket
                </a>
                <a href="{{ route('ticketstatus') }}" @class([
                    'px-4 py-2 text-sm font-medium transition-colors border-l border-gray-200',
                    'bg-gray-900 text-white' => $isStatus,
                    'text-gray-700 hover:text-gray-900' => !$isStatus,
                ])>
                    Ticket Status
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4">
        <div class="flex flex-col md:flex-row md:items-center gap-6 pb-4 mb-4 border-b border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 w-full md:w-auto">
                <input type="text" placeholder="Requester (e.g. Finance, HR)"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-900">
                <select class="px-3 py-2 border border-gray-300 rounded-md text-gray-900">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="process">In Process</option>
                    <option value="complete">Complete</option>
                </select>
                <select class="px-3 py-2 border border-gray-300 rounded-md text-gray-900">
                    <option value="">All Priority</option>
                    <option value="LOW">Low</option>
                    <option value="MEDIUM">Medium</option>
                    <option value="HIGH">High</option>
                    <option value="URGENT">Urgent</option>
                </select>
                <select class="px-3 py-2 border border-gray-300 rounded-md text-gray-900">
                    <option value="">All Departments</option>
                    <option value="IT">IT</option>
                    <option value="HR">HR</option>
                    <option value="Finance">Finance</option>
                    <option value="Humas">Humas</option>
                </select>
                <select class="px-3 py-2 border border-gray-300 rounded-md text-gray-900">
                    <option value="">Recent first</option>
                    <option value="oldest">Oldest first</option>
                    <option value="due">Nearest due</option>
                </select>
            </div>
        </div>
        <div class="space-y-5">
            @foreach ($tickets as $t)
                @php
                    $priority = $t['priority'] ?? 'LOW';
                    $status = $t['status'] ?? 'pending';
                    $dept = $t['departement_id'] ?? $t['department_id'] ?? null;  // supports both keys
                    $req = $t['requester_id'] ?? null;
                    $userName = $t['user']['name'] ?? null;
                    $initials = function (?string $name) {
                        if (!$name)
                            return '👤';
                        $parts = preg_split('/\s+/', trim($name));
                        $chars = array_map(fn($p) => mb_substr($p, 0, 1), array_slice($parts, 0, 2));
                        return strtoupper(implode('', $chars));
                    };
                @endphp

                <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-6">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate">{{ $t['subject'] }}</h3>
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="font-mono font-medium text-gray-800">#{{ $t['ticket_id'] }}</span>
                                <span class="text-gray-300">•</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium
                                    @if($priority === 'URGENT') bg-red-50 text-red-700 border-2 border-red-400
                                    @elseif($priority === 'HIGH') bg-orange-50 text-orange-700 border-2 border-orange-400
                                    @elseif($priority === 'MEDIUM') bg-yellow-50 text-yellow-700 border-2 border-yellow-400
                                    @else bg-gray-50 text-gray-700 border-2 border-gray-400 @endif">
                                    {{ $priority }}
                                </span>

                                @if($dept)
                                    <span class="text-gray-300">•</span>
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border-2 border-gray-400 bg-gray-50 text-gray-700">
                                        🏷️ <span class="font-medium">{{ $dept }}</span>
                                    </span>
                                @endif

                                @if($req || $userName)
                                    <span class="text-gray-300">•</span>
                                    <span
                                        class="inline-flex items-center gap-2 px-2 py-1 rounded-lg border-2 border-gray-400 bg-gray-50 text-gray-700">
                                        <span
                                            class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-gray-400 text-[10px] leading-none">
                                            {{ $initials($userName) }}
                                        </span>
                                        <span class="font-medium">
                                            {{ $userName ?: 'Requester' }}
                                        </span>
                                        @if($req)
                                            <span class="text-gray-500">({{ $req }})</span>
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                            @if($status === 'pending') bg-yellow-50 text-yellow-700 border-2 border-yellow-500
                            @elseif($status === 'process') bg-blue-50 text-blue-700 border-2 border-blue-500
                            @elseif($status === 'complete') bg-green-50 text-green-700 border-2 border-green-500
                            @else bg-gray-50 text-gray-700 border-2 border-gray-500 @endif">
                            {{ ucfirst($status) }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 leading-relaxed">{{ $t['description'] }}</p>
                    <div class="mt-3 text-[11px] text-gray-500">
                        <span>Created: {{ $t['created_at'] }}</span>
                        <span class="mx-2">•</span>
                        <span>Updated: {{ $t['updated_at'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
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