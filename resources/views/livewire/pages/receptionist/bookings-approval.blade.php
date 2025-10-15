<div class="p-6 space-y-6">
    {{-- Header / Filters --}}
    <div class="bg-white rounded-2xl border-2 border-black p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-bold text-gray-900">Bookings Approval</h1>
            <div class="flex items-center gap-2">
                <input type="text" wire:model.live="q" placeholder="Search title..."
                    class="h-10 px-3 rounded-lg border border-gray-300 text-gray-800 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10" />
                <select wire:model.live="filter"
                    class="h-10 px-3 rounded-lg border border-gray-300 text-gray-800 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="all">All</option>
                </select>
            </div>
        </div>
    </div>

    @if(!$this->googleConnected)
        <div class="bg-yellow-50 border border-yellow-300 text-yellow-900 rounded-xl p-3">
            <div class="flex items-center justify-between gap-3">
                <div><strong>Google not connected.</strong> Connect to enable automatic Google Meet creation.</div>
                <a href="{{ route('google.connect') }}" class="px-3 py-2 rounded-lg border border-gray-300">Connect
                    Google</a>
            </div>
        </div>
    @endif


    {{-- List --}}
    <div class="bg-white rounded-2xl border-2 border-black p-4 md:p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4">
            @forelse($rows as $b)
                    @php
                        $status = strtolower(trim((string) $b->status));
                    @endphp

                    <div class="border border-gray-200 rounded-xl p-4">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                            {{-- Left info --}}
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-900">
                                        #{{ $b->bookingroom_id }} — {{ $b->meeting_title }}
                                    </span>

                                    <span class="text-xs px-2 py-0.5 rounded-full border
                                                   {{ $b->booking_type === 'online_meeting'
                ? 'border-blue-300 text-blue-700 bg-blue-50'
                : 'border-gray-300 text-gray-700 bg-gray-50' }}">
                                        {{ strtoupper($b->booking_type) }}
                                    </span>

                                    <span class="text-xs px-2 py-0.5 rounded-full
                                                   {{ $status === 'pending'
                ? 'bg-yellow-100 text-yellow-800'
                : ($status === 'approved'
                    ? 'bg-green-100 text-green-800'
                    : 'bg-rose-100 text-rose-800') }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </div>

                                <div class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($b->date)->format('d M Y') }}
                                    •
                                    {{ \Carbon\Carbon::parse($b->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($b->end_time)->format('H:i') }}
                                    @if($b->booking_type === 'online_meeting' && $b->online_provider)
                                        • Provider: {{ ucfirst(str_replace('_', ' ', $b->online_provider)) }}
                                    @endif
                                </div>

                                {{-- Show link if already approved & online --}}
                                @if($b->booking_type === 'online_meeting' && $status === 'approved' && $b->online_meeting_url)
                                    <div class="text-sm">
                                        <a href="{{ $b->online_meeting_url }}" target="_blank"
                                            class="text-blue-600 underline">Meeting Link</a>
                                        @if($b->online_meeting_code)
                                            <span class="text-gray-600 ml-2">Code: <span
                                                    class="font-medium">{{ $b->online_meeting_code }}</span></span>
                                        @endif
                                        @if($b->online_meeting_password)
                                            <span class="text-gray-600 ml-2">Password: <span
                                                    class="font-medium">{{ $b->online_meeting_password }}</span></span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Actions (only INSIDE the row) --}}
                            <div class="flex items-center gap-2">
                                @if($status === 'pending')
                                    <button wire:click="approve({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                        class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                                        Approve
                                    </button>
                                    <button wire:click="reject({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                        class="px-3 py-2 bg-rose-600 text-white text-sm rounded-lg hover:bg-rose-700">
                                        Reject
                                    </button>
                                @elseif($status === 'approved')
                                    <button wire:click="reject({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                        title="Change to Rejected"
                                        class="px-3 py-2 bg-rose-600 text-white text-sm rounded-lg hover:bg-rose-700">
                                        Reject
                                    </button>
                                @elseif($status === 'rejected')
                                    <button wire:click="approve({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                        title="Change to Approved"
                                        class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                                        Approve
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
            @empty
                <p class="text-sm text-gray-500">Tidak ada data.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $rows->links() }}
        </div>
    </div>
</div>