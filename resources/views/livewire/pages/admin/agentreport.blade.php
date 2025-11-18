<div class="bg-gray-50">

    @php
        // reusable design classes
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-5">

        {{-- Header --}}
        <header
            class="rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-xl px-5 py-4 flex items-center gap-4">
            <div
                class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/20 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6V4m0 16v-2m0-10v2m0 6v2M6 12H4m16 0h-2m-10 0h2m6 0h2M9 17l-2 2M15 7l2-2M7 7l-2-2M17 17l2 2" />
                </svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-base sm:text-lg font-semibold truncate">Agent Report</h2>
                <p class="text-xs text-white/80 truncate">Overview of Agents and Their Assigned Support Tickets.</p>
            </div>
        </header>

        {{-- Statistics Card --}}
        <section class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Agents Assigned Tickets</h3>

                @if($ticketStats->isEmpty())
                    <p class="text-gray-500 text-sm">No tickets found.</p>
                @else
                    @php
                        // Sort agents by ticket count descending and take top 5
                        $sortedAgents = $agents->sortByDesc(fn($a) => $ticketStats[$a->user_id] ?? 0)->take(5);
                        $maxCount = $ticketStats->max() ?: 1; // for percentage
                    @endphp

                    <div class="space-y-3">
                        @foreach($sortedAgents as $agent)
                            @php
                                $count = $ticketStats[$agent->user_id] ?? 0;
                                $widthPercent = ($count / $maxCount) * 100;
                            @endphp

                            <div class="flex items-center gap-3">
                                <span class="w-32 truncate font-medium">{{ $agent->full_name }}</span>
                                <div class="flex-1 bg-gray-200 h-4 rounded overflow-hidden">
                                    <div x-data="{ w: {{ $widthPercent }} }" class="bg-gradient-to-r from-gray-900 to-black h-4"
                                        :style="'width: ' + w + '%'">
                                    </div>
                                </div>
                                <span class="w-8 text-right text-sm font-medium">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Main Agent Card --}}
        <section class="{{ $card }}">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-4">
                <h3 class="text-base font-semibold text-gray-900 flex-1">Agent List</h3>

                {{-- Search --}}
                <div class="w-full max-w-md">
                    <input type="text" wire:model.debounce.400ms="search" placeholder="Search agent by name or ID..."
                        class="w-full rounded-lg bg-white/50 border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-gray-900/10 focus:outline-none">
                </div>
            </div>

            {{-- Search Chip --}}
            @if(!empty($search))
                <div class="px-5 py-2 bg-white border-b border-gray-200 flex items-center gap-2">

                    <span class="text-sm">
                        Search:
                        <span class="px-2 py-1 bg-gray-100 rounded-lg text-gray-800 font-medium">
                            {{ $search }}
                        </span>
                    </span>
                </div>
            @endif

            {{-- Agent List --}}
            <div class="divide-y divide-gray-100">
                @forelse ($agents as $agent)
                    @php
                        $isOpen = (string) $openAgent === (string) $agent->user_id;
                    @endphp

                    <div class="px-5 py-4" wire:key="agent-{{ $agent->user_id }}">
                        <div class="space-y-2">

                            {{-- Compact Bar --}}
                            <div wire:click="toggleAgent('{{ $agent->user_id }}')"
                                class="flex items-center justify-between gap-3 rounded-lg px-3 py-2 cursor-pointer transition-shadow duration-150
                                            {{ $isOpen ? 'bg-gradient-to-r from-gray-900 to-black text-white shadow-lg' : 'bg-white shadow-sm hover:shadow-md' }}">

                                {{-- LEFT SIDE --}}
                                <div class="flex items-center gap-3 truncate min-w-0">

                                    {{-- Slim White Agent ID Box --}}
                                    <div
                                        class="flex-shrink-0 px-3 py-1 bg-white rounded shadow text-gray-700 font-semibold text-sm">
                                        #{{ $agent->user_id }}
                                    </div>

                                    {{-- Agent Name --}}
                                    <div class="truncate font-semibold text-sm max-w-xl">
                                        {{ $agent->full_name }}
                                        <span class="{{ $isOpen ? 'text-white/90' : 'text-gray-500' }} text-xs">
                                            - {{ $agent->company_name ?? '-' }} | {{ $agent->department_name ?? '-' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- RIGHT SIDE: Arrow --}}
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <div
                                        class="ml-2 transform transition-transform duration-200 {{ $isOpen ? 'rotate-90' : '' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>

                            </div>

                            {{-- Expanded Section: Tickets for this Agent --}}
                            @if ($isOpen)
                                @if (!empty($agent->tickets))
                                    <div class="mt-2 space-y-2">
                                        @foreach ($agent->tickets as $ticket)
                                            <a href="{{ url('/admin/tickets/' . $ticket->ulid) }}">
                                                <div
                                                    class="px-3 py-2 bg-white rounded shadow flex flex-wrap items-center justify-between text-sm text-gray-700 hover:bg-gray-100 transition">

                                                    {{-- Left side: Ticket ID, Subject, Status, Priority --}}
                                                    <div class="flex items-center flex-wrap gap-2">
                                                        <span class="font-mono font-semibold">#{{ $ticket->ticket_id }}</span>
                                                        <span class="font-semibold">{{ $ticket->subject ?? 'No Subject' }}</span>

                                                        {{-- Status Badge --}}
                                                        @if(!empty($ticket->status))
                                                            <span
                                                                class="px-2 py-0.5 text-xs font-medium rounded {{ $ticket->status == 'Open' ? 'bg-green-100 text-green-800' : ($ticket->status == 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                                {{ $ticket->status }}
                                                            </span>
                                                        @endif

                                                        {{-- Priority Badge --}}
                                                        @if(!empty($ticket->priority))
                                                            <span
                                                                class="px-2 py-0.5 text-xs font-medium rounded {{ $ticket->priority == 'High' ? 'bg-red-100 text-red-800' : ($ticket->priority == 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                                {{ $ticket->priority }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Right side: Created date --}}
                                                    <div class="text-xs text-gray-500 ml-auto">
                                                        created: {{ optional($ticket->created_at)->format('d M Y, H:i') }}
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="mt-2 p-2 text-gray-500 text-sm">
                                        No tickets assigned to this agent.
                                    </div>
                                @endif
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="px-5 py-4 text-gray-500 text-sm">
                        No agents found.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($agents->hasPages())
                <div class="px-5 py-4 bg-white border-t border-gray-100">
                    <div class="flex justify-center">
                        {{ $agents->links() }}
                    </div>
                </div>
            @endif

        </section>
    </main>
</div>