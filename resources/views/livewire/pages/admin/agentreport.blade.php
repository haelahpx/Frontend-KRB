<div class="bg-gray-50">

    @php
        // reusable design classes
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    @endphp

    <script>
        // Function to set bar widths after page loads and after Livewire updates
        function applyFlexBasis() {
            document.querySelectorAll('[data-flex-basis]').forEach(el => {
                el.style.flex = '0 0 ' + el.dataset.flexBasis + '%';
            });
        }

        document.addEventListener('DOMContentLoaded', applyFlexBasis);
        window.addEventListener('livewire:updated', applyFlexBasis);
    </script>

    <main class="px-4 sm:px-6 py-6 space-y-5">

        {{-- Header --}}
        <header class="rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-xl px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/20 shrink-0">
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
                <h3 class="text-base font-semibold text-gray-900 mb-4">Agents by Ticket Count</h3>

                @if($topAgents->isEmpty())
                    <p class="text-gray-500 text-sm">No agents found.</p>
                @else
                    @php
                        // compute max total among top agents to scale bars
                        $max = $topAgents->map(fn($a) => $allTicketStatsDetailed[$a->user_id]['total'] ?? 0)->max() ?: 1;
                    @endphp

                    <div class="space-y-4">
                        @foreach($topAgents as $agent)
                            @php
                                $stats = $allTicketStatsDetailed[$agent->user_id] ?? [];
                                $total = $stats['total'] ?? 0;
                                $open = $stats['Open'] ?? 0;
                                $resolved = $stats['Resolved'] ?? 0;
                                $progress = $stats['IN_PROGRESS'] ?? 0;
                                $closed = $stats['Closed'] ?? 0;

                                // Fallback: if no stats, recalculate from all tickets
                                if ($total === 0) {
                                    $agentTickets = $allTickets->where('user_id', $agent->user_id);
                                    $total = $agentTickets->count();
                                    $progress = $agentTickets->where('status', 'IN_PROGRESS')->count();
                                    $open = $agentTickets->where('status', 'OPEN')->count();
                                    $closed = $agentTickets->where('status', 'CLOSED')->count();
                                    $resolved = $agentTickets->where('status', 'RESOLVED')->count();
                                }

                                // width relative to max
                                $barWidth = $max > 0 ? ($total / $max) * 100 : 0;
                            @endphp

                            <div class="space-y-1">

                                {{-- Agent name + total --}}
                                <div class="flex items-center justify-between">
                                    <span class="font-medium">{{ $agent->full_name }}</span>
                                    <span class="text-sm font-semibold">{{ $total }} tickets</span>
                                </div>

                                {{-- Single bar measuring total tickets --}}
                                <div class="w-full h-4 bg-gray-200 rounded overflow-hidden">
                                    <div x-data="{ width: {{ $barWidth }} }" :style="'width: ' + width + '%'"
                                         class="bg-gradient-to-r from-gray-900 to-black h-4">
                                    </div>
                                </div>

                                {{-- Status counts below the bar --}}
                                <div class="flex flex-wrap gap-4 text-xs text-gray-600 mt-2">
                                    <div>Open: <span class="font-semibold">{{ $open }}</span></div>
                                    <div>In Progress: <span class="font-semibold">{{ $progress }}</span></div>
                                    <div>Resolved: <span class="font-semibold">{{ $resolved }}</span></div>
                                    <div>Closed: <span class="font-semibold">{{ $closed }}</span></div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Main Agent Card --}}
        <section class="{{ $card }}">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">

                <h3 class="text-base font-semibold text-gray-900">Agent List</h3>

                {{-- Right side: search + download --}}
                <div class="flex items-center gap-3">

                    {{-- Search --}}
                    <input type="text"
                        wire:model.live.debounce.100ms="search"
                        placeholder="Search agent by name or ID..."
                        class="w-64 rounded-lg bg-white/50 border border-gray-200 px-3 py-2
                                focus:ring-2 focus:ring-gray-900/10 focus:outline-none">

                    {{-- Download Button --}}
                    <button wire:click="downloadReport"
                            class="px-3 py-2 rounded-lg bg-gradient-to-r from-gray-900 to-black text-white text-sm shadow cursor-pointer">
                        Download Report
                    </button>

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
                                    <div class="flex-shrink-0 px-3 py-1 bg-white rounded shadow text-gray-700 font-semibold text-sm">
                                        {{ $loop->iteration }}
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
                                    <div class="ml-2 transform transition-transform duration-200 {{ $isOpen ? 'rotate-90' : '' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>

                            </div>

                            {{-- Expanded Section: Tickets for this Agent --}}
                            @if ($isOpen)
                                @if (!empty($agent->tickets) && $agent->tickets->isNotEmpty())
                                    <div class="mt-2 space-y-2">
                                        @foreach ($agent->tickets as $ticket)
                                            @php
                                                // SLA data is already calculated in PHP backend
                                                $slaData = $ticket->sla_state ?? [];
                                                $slaState = $slaData['state'] ?? null;
                                                $slaLabel = $slaData['label'] ?? null;
                                                $slaClasses = $slaData['classes'] ?? '';
                                                $hoursElapsed = $slaData['hours_elapsed'] ?? 0;
                                                $createdAt = $ticket->created_at;
                                                $updatedAt = $ticket->updated_at;
                                                $ticketStatus = ucwords(strtolower(str_replace('_', ' ', trim((string) ($ticket->status ?? '')))));
                                                $ticketPriority = ucwords(strtolower(str_replace('_', ' ', trim((string) ($ticket->priority ?? '')))));
                                            @endphp

                                            <a href="{{ url('/admin/tickets/' . $ticket->ulid) }}" class="block">
                                                <div class="px-3 py-2 bg-white rounded shadow flex flex-wrap items-center justify-between text-sm text-gray-700 hover:bg-gray-100 transition">

                                                    {{-- Left side: Ticket ID, Subject, Status, Priority --}}
                                                    <div class="flex items-center flex-wrap gap-2 min-w-0">
                                                        <span>
                                                            <div class="flex-shrink-0 px-3 py-1 bg-white rounded shadow text-gray-700 font-semibold text-sm font-mono font-semibold">
                                                                #{{ $ticket->ticket_id }}
                                                            </div>
                                                        </span>

                                                        <span class="font-semibold truncate max-w-[40ch]">{{ $ticket->subject ?? 'No Subject' }}</span>

                                                        {{-- Status Badge --}}
                                                        @if(!empty($ticket->status))
                                                            <span class="px-1.5 py-0.5 text-[10px] text-white font-semibold rounded bg-gradient-to-r from-gray-900 to-black">
                                                                {{ $ticketStatus }}
                                                            </span>
                                                        @endif

                                                        {{-- Priority Badge --}}
                                                        @if(!empty($ticket->priority))
                                                            <span class="px-1.5 py-0.5 text-[10px] text-white font-semibold rounded bg-gradient-to-r from-gray-900 to-black">
                                                                {{ $ticketPriority }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Right side: Created date + Time elapsed + SLA (inline) --}}
                                                    <div class="ml-auto flex flex-col items-end gap-1">
                                                        {{-- Time elapsed + SLA in one row --}}
                                                        <div class="flex items-center gap-2">
                                                            {{-- Time Elapsed Box --}}
                                                            <div class="px-2 py-0.5 bg-white rounded-lg shadow text-[10px] text-gray-700 border border-gray-100">
                                                                @php
                                                                    $h = $hoursElapsed ?? 0;
                                                                @endphp
                                                                @if ($h > 0)
                                                                    @if ($h < 1)
                                                                        Updated: {{ floor($h * 60) }}m ago
                                                                    @elseif ($h < 24)
                                                                        Updated: {{ floor($h) }}h ago
                                                                    @elseif ($h < 24 * 30)
                                                                        Updated: {{ floor($h / 24) }}d ago
                                                                    @elseif ($h < 24 * 30 * 12)
                                                                        Updated: {{ floor($h / (24 * 30)) }}mo ago
                                                                    @else
                                                                        Updated: {{ floor($h / (24 * 365)) }}y ago
                                                                    @endif
                                                                @endif
                                                            </div>

                                                            {{-- SLA Solid Gradient Badge (no icon) --}}
                                                            @if($slaState)
                                                                <div class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $slaClasses }}">
                                                                    {{ $slaLabel }}
                                                                </div>
                                                            @endif
                                                        </div>
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
