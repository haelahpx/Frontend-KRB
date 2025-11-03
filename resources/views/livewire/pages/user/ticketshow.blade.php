{{-- resources/views/livewire/pages/user/ticketshow.blade.php --}}
@php
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

$agents = collect($ticket->assignments ?? [])
    ->pluck('user.full_name')
    ->filter()
    ->unique()
    ->values();
$hasAgent = $agents->isNotEmpty();
@endphp

<div class="max-w-6xl mx-auto">
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
                        'bg-gray-900 text-white' => true,
                    ])>
                    Ticket Status
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4 space-y-6">
        <div class="relative bg-white rounded-xl border-2 border-black/80 shadow p-6">
            <div class="flex items-start justify-between gap-4 mb-2">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2 break-words">{{ $ticket->subject }}</h2>

                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="font-mono font-medium text-gray-800 inline-flex items-center gap-1">
                            <x-heroicon-o-hashtag class="w-3.5 h-3.5"/> {{ $ticket->ticket_id }}
                        </span>

                        <span class="text-gray-300">•</span>

                        @php
                            $isHigh = $priority === 'high';
                            $isMedium = $priority === 'medium';
                            $isLow = $priority === 'low' || $priority === '';
                        @endphp
                        <span
                            @class([
                                'inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium',
                                'bg-orange-50 text-orange-700 border-2 border-orange-400' => $isHigh,
                                'bg-yellow-50 text-yellow-700 border-2 border-yellow-400' => $isMedium,
                                'bg-gray-50 text-gray-700 border-2 border-gray-400' => $isLow,
                            ])>
                            <x-heroicon-o-bolt class="w-3.5 h-3.5"/>
                            {{ $priority ? ucfirst($priority) : 'Low' }}
                        </span>

                        @if ($ticket->department)
                            <span class="text-gray-300">•</span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border-2 border-gray-400 bg-gray-50 text-gray-700">
                                <x-heroicon-o-building-office-2 class="w-3.5 h-3.5"/>
                                <span class="font-medium">{{ $ticket->department->department_name }}</span>
                            </span>
                        @endif

                        <span class="text-gray-300">•</span>

                        @php
                            $isOpen = $status === 'open';
                            $isAssignedOrProgress = in_array($status, ['assigned','in_progress','process'], true);
                            $isResolved = in_array($status, ['resolved','closed','complete'], true);
                        @endphp
                        <span
                            @class([
                                'inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium',
                                'bg-yellow-50 text-yellow-700 border-2 border-yellow-500' => $isOpen,
                                'bg-blue-50 text-blue-700 border-2 border-blue-500' => $isAssignedOrProgress,
                                'bg-green-50 text-green-700 border-2 border-green-500' => $isResolved,
                                'bg-gray-50 text-gray-700 border-2 border-gray-500' => (! $isOpen && ! $isAssignedOrProgress && ! $isResolved),
                            ])>
                            <x-heroicon-o-check-badge class="w-4 h-4"/>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>

                        <span class="text-gray-300">•</span>
                        <span @class([
                            'inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium border-2',
                            $hasAgent ? 'bg-emerald-50 text-emerald-700 border-emerald-400' : 'bg-red-50 text-red-700 border-red-400'
                        ])>
                            <x-heroicon-o-user-circle class="w-3.5 h-3.5"/>
                            {{ $hasAgent ? 'Agent assigned' : 'No agent yet' }}
                        </span>

                        @if($agents->isNotEmpty())
                            <span class="text-gray-300">•</span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border-2 border-blue-400 bg-blue-50 text-blue-700">
                                <x-heroicon-o-users class="w-3.5 h-3.5"/>
                                <span class="font-medium truncate max-w-[260px]">{{ $agents->join(', ') }}</span>
                            </span>
                        @endif
                    </div>

                    @if($canEditStatus ?? false)
                        <div class="mt-3">
                            <form wire:submit.prevent="updateStatus" class="flex items-center gap-2">
                                <select
                                    wire:model="statusEdit"
                                    class="rounded-lg border-2 border-black/40 bg-white text-gray-900 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                                    @foreach (($allowedStatuses ?? ['OPEN','IN_PROGRESS','RESOLVED','CLOSED']) as $st)
                                        <option value="{{ $st }}">{{ str_replace('_',' ', $st) }}</option>
                                    @endforeach
                                </select>

                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-black text-white text-sm font-medium hover:bg-gray-800">
                                    <x-heroicon-o-arrow-path class="w-4 h-4"/>
                                    Update Status
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <p class="text-sm text-gray-700 leading-relaxed">{{ $ticket->description }}</p>

            <div class="mt-3 text-[11px] text-gray-500 inline-flex items-center gap-2">
                <x-heroicon-o-clock class="w-3.5 h-3.5"/>
                <span>Created: {{ optional($ticket->created_at)->format('Y-m-d H:i') }}</span>
                <span class="mx-2">•</span>
                <x-heroicon-o-arrow-path class="w-3.5 h-3.5"/>
                <span>Updated: {{ optional($ticket->updated_at)->format('Y-m-d H:i') }}</span>
            </div>

            @if(method_exists($ticket, 'attachments') && $ticket->relationLoaded('attachments') && $ticket->attachments->count())
                <div class="mt-5 border-t border-black/10 pt-4">
                    <div class="text-xs uppercase tracking-wide text-gray-600 mb-2">Attachments ({{ $ticket->attachments->count() }})</div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach ($ticket->attachments as $a)
                            @php $isImage = str_starts_with(strtolower($a->file_type ?? ''), 'image/'); @endphp
                            <div class="rounded-lg overflow-hidden border-2 border-black/20">
                                @if ($isImage)
                                    <a href="{{ $a->file_url }}" target="_blank" class="block">
                                        <img src="{{ $a->file_url }}" class="w-full h-40 object-cover" alt="{{ $a->original_filename ?? 'image' }}">
                                    </a>
                                @else
                                    <a href="{{ $a->file_url }}" target="_blank" class="flex items-center gap-2 p-3 text-sm text-gray-800 hover:bg-gray-50">
                                        <x-heroicon-o-document class="w-5 h-5"/>
                                        <span class="truncate">{{ $a->original_filename ?? 'file' }}</span>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl border-2 border-black/80 shadow p-6">
            <h3 class="text-xl font-bold mb-4 inline-flex items-center gap-2">
                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5"/> Discussion
            </h3>

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
                                <x-heroicon-o-paper-airplane class="w-4 h-4 rotate-45"/>
                                Post Comment
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="space-y-5">
                @forelse ($ticket->comments as $comment)
                    @php
                        $isMine = $comment->user_id === auth()->id();
                        $name = $comment->user->full_name ?? $comment->user->name ?? 'User';
                        $init = $initials($name);
                    @endphp

                    <div class="flex {{ $isMine ? 'flex-row' : 'flex-row-reverse' }} items-start gap-3">
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
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 border-black bg-white hover:bg-gray-50 font-medium">
                    <x-heroicon-o-arrow-uturn-left class="w-4 h-4"/>
                    Back to list
                </a>
            </div>
        </div>
    </div>
</div>
