{{-- resources/views/livewire/pages/user/ticketshow.blade.php --}}
@php
// Two-letter initials from full name (first + last)
$initials = function (?string $fullName): string {
    $fullName = trim($fullName ?? '');
    if ($fullName === '') return 'US';
    $parts = preg_split('/\s+/', $fullName);
    $first = strtoupper(mb_substr($parts[0] ?? 'U', 0, 1));
    $last = strtoupper(mb_substr($parts[count($parts)-1] ?? $parts[0] ?? 'S', 0, 1));
    return $first.$last;
};

$isCreate = request()->routeIs('create-ticket') || request()->is('create-ticket');
$isStatus = request()->routeIs('ticketstatus') || request()->is('ticketstatus');

$priority = strtolower($ticket->priority ?? '');
$status = strtolower($ticket->status ?? 'open');
@endphp

<div class="max-w-6xl mx-auto">
    {{-- Header / Tabs (same as ticketstatus) --}}
    <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
        <div class="flex items-center justify-start gap-3">
            <h1 class="text-3xl font-bold text-gray-900">Support Ticket System</h1>

            <div class="ml-auto inline-flex rounded-md overflow-hidden bg-gray-100 border border-gray-200">
                <a href="{{ route('create-ticket') }}"
                    @class([
                        'px-4 py-2 text-sm font-medium transition-colors',
                        'bg-gray-900 text-white' => $isCreate,
                        'text-gray-700 hover:text-gray-900' => ! $isCreate,
                    ])>
                    Create Ticket
                </a>

                <a href="{{ route('ticketstatus') }}"
                    @class([
                        'px-4 py-2 text-sm font-medium transition-colors border-l border-gray-200',
                        // On detail page we still highlight the "Ticket Status" tab to match list context
                        'bg-gray-900 text-white' => true,
                    ])>
                    Ticket Status
                </a>
            </div>
        </div>
    </div>

    {{-- Content Card (details + chat), mirroring ticketstatus card styles --}}
    <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4 space-y-6">
        {{-- Ticket Head --}}
        <div class="relative bg-white rounded-xl border-2 border-black/80 shadow p-6">
            <div class="flex items-start justify-between gap-4 mb-2">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2 break-words">{{ $ticket->subject }}</h2>

                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        {{-- ID chip --}}
                        <span class="font-mono font-medium text-gray-800">#{{ $ticket->ticket_id }}</span>

                        {{-- Priority chip (same tone as list) --}}
                        <span class="text-gray-300">•</span>

                        @php
                            $isHigh = $priority === 'high';
                            $isMedium = $priority === 'medium';
                            $isLow = $priority === 'low' || $priority === '';
                        @endphp

                        <span
                            @class([
                                'inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium',
                                'bg-orange-50 text-orange-700 border-2 border-orange-400' => $isHigh,
                                'bg-yellow-50 text-yellow-700 border-2 border-yellow-400' => $isMedium,
                                'bg-gray-50 text-gray-700 border-2 border-gray-400' => $isLow,
                            ])>
                            {{ $priority ? ucfirst($priority) : 'Low' }}
                        </span>

                        {{-- Department chip --}}
                        @if ($ticket->department)
                        <span class="text-gray-300">•</span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border-2 border-gray-400 bg-gray-50 text-gray-700">
                            🏷 <span class="font-medium">{{ $ticket->department->department_name }}</span>
                        </span>
                        @endif

                        {{-- Status chip (same mapping as list) --}}
                        <span class="text-gray-300">•</span>

                        @php
                            $isOpen = $status === 'open';
                            $isAssignedOrProgress = in_array($status, ['assigned','in_progress','process'], true);
                            $isResolved = in_array($status, ['resolved','closed','complete'], true);
                        @endphp

                        <span
                            @class([
                                'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium',
                                'bg-yellow-50 text-yellow-700 border-2 border-yellow-500' => $isOpen,
                                'bg-blue-50 text-blue-700 border-2 border-blue-500' => $isAssignedOrProgress,
                                'bg-green-50 text-green-700 border-2 border-green-500' => $isResolved,
                                'bg-gray-50 text-gray-700 border-2 border-gray-500' => (! $isOpen && ! $isAssignedOrProgress && ! $isResolved),
                            ])>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <p class="text-sm text-gray-700 leading-relaxed">
                {{ $ticket->description }}
            </p>

            <div class="mt-3 text-[11px] text-gray-500">
                <span>Created: {{ optional($ticket->created_at)->format('Y-m-d H:i') }}</span>
                <span class="mx-2">•</span>
                <span>Updated: {{ optional($ticket->updated_at)->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        {{-- Discussion --}}
        <div class="bg-white rounded-xl border-2 border-black/80 shadow p-6">
            <h3 class="text-xl font-bold mb-4">Discussion 💬</h3>

            {{-- Composer --}}
            <form wire:submit.prevent="addComment" class="mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        @php $meInitials = $initials(auth()->user()->full_name ?? auth()->user()->name ?? 'User'); @endphp
                        <span class="inline-flex h-10 w-10 rounded-full bg-black text-white items-center justify-center text-xs font-bold">
                            {{ $meInitials }}
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <textarea
                            wire:model.defer="newComment"
                            rows="3"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 transition"
                            placeholder="Add your comment..."></textarea>
                        @error('newComment') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror

                        <div class="mt-3 flex items-center justify-end">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                Post Comment
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Chat list (mine left, others right to “look like chat”) --}}
            <div class="space-y-5">
                @forelse ($ticket->comments as $comment)
                @php
                    $isMine = $comment->user_id === auth()->id();
                    $name = $comment->user->full_name ?? $comment->user->name ?? 'User';
                    $init = $initials($name);
                @endphp

                <div class="flex {{ $isMine ? 'flex-row' : 'flex-row-reverse' }} items-start gap-3">
                    {{-- Avatar --}}
                    <div class="flex-shrink-0">
                        <span
                            @class([
                                'inline-flex h-9 w-9 rounded-full items-center justify-center text-[11px] font-bold',
                                'bg-black text-white' => $isMine,
                                'bg-gray-200 text-gray-800' => ! $isMine,
                            ])>
                            {{ $init }}
                        </span>
                    </div>

                    {{-- Bubble --}}
                    <div class="max-w-[80%]">
                        <div class="flex items-center {{ $isMine ? 'justify-between' : 'flex-row-reverse justify-between' }} gap-3">
                            <p class="text-xs font-semibold text-gray-700 truncate">{{ $name }}</p>
                            <p class="text-[11px] text-gray-500" title="{{ $comment->created_at->format('Y-m-d H:i') }}">
                                {{ $comment->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <div
                            @class([
                                'mt-1 rounded-xl px-4 py-3 shadow-sm',
                                'bg-black text-white border-2 border-black' => $isMine,
                                'bg-gray-50 text-gray-900 border-2 border-black/20' => ! $isMine,
                            ])>
                            <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ $comment->comment_text }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center text-gray-600">
                    No comments yet. Be the first to reply!
                </div>
                @endforelse
            </div>

            <div class="mt-6">
                <a href="{{ route('ticketstatus') }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg border-2 border-black bg-white hover:bg-gray-50 font-medium">
                    ← Back to list
                </a>
            </div>
        </div>
    </div>
</div>
