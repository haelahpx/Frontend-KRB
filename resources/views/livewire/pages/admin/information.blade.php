{{-- resources/views/livewire/pages/admin/information.blade.php --}}
<div class="bg-gray-50 min-h-screen" wire:key="information-root">
    @php
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk = 'inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg bg-gray-900 text-white focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnLt = 'inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition';
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    $mono = 'text-[10px] font-mono text-gray-600 bg-gray-100 px-2.5 py-1 rounded-md';
    $ico = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white shrink-0';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">
        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <!-- Soft glow background -->
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-6 w-28 h-28 bg-white/20 rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-6 w-20 h-20 bg-white/10 rounded-full blur-lg"></div>
            </div>

            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <!-- LEFT: Icon + Title + Meta -->
                    <div class="flex items-start gap-4 sm:gap-6">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3M5 11h14M5 19h14M5 11a2 2 0 012-2h10a2 2 0 012 2M5 19a2 2 0 002 2h10a2 2 0 002-2" />
                            </svg>
                        </div>

                        <div class="space-y-1.5">
                            <h2 class="text-xl sm:text-2xl font-semibold leading-tight">
                                Information
                            </h2>
                            <p class="text-sm text-white/80">
                                Perusahaan: <span class="font-semibold">{{ $company_name }}</span>
                                <span class="mx-2">•</span>
                                Departemen: <span class="font-semibold">{{ $department_name }}</span>
                            </p>
                            <p class="text-xs text-white/60">
                                Menampilkan informasi untuk departemen: <span class="font-medium">{{ $department_name }}</span>.
                            </p>
                        </div>
                    </div>

                    <!-- RIGHT: Switcher or Search -->
                    @if ($showSwitcher)
                    <div class="w-full lg:w-[32rem] lg:ml-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-white/80 mb-2">
                                    Pilih Departemen
                                </label>
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                    <select
                                        wire:model.live="selected_department_id"
                                        class="w-full h-11 sm:h-12 px-3 sm:px-4 rounded-lg border border-white/20 bg-white/10 text-white text-sm placeholder:text-white/60 focus:border-white focus:ring-2 focus:ring-white/30 focus:outline-none transition">
                                        @foreach ($deptOptions as $opt)
                                        <option class="text-gray-900" value="{{ $opt['id'] }}">
                                            {{ $opt['name'] }}{{ $opt['id'] === $primary_department_id ? ' — Primary' : '' }}
                                        </option>
                                        @endforeach
                                    </select>

                                    <button
                                        type="button"
                                        wire:click="resetToPrimaryDepartment"
                                        class="inline-flex items-center justify-center gap-2 px-3 py-2.5 text-xs font-medium rounded-lg bg-white/10 text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/30 transition">
                                        <x-heroicon-o-star class="w-4 h-4" />
                                        Primary
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Search (no switcher) -->
                    <div class="w-full lg:w-80 lg:ml-auto">
                        <label class="sr-only">Search</label>
                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Cari judul atau catatan…"
                            class="w-full h-11 px-3 sm:px-3.5 bg-white/10 border border-white/20 rounded-lg text-sm placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white transition">
                    </div>
                    @endif
                </div>
            </div>
        </div>



        {{-- REQUEST QUEUE (RoomMonitoring moved here) --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- OFFLINE --}}
            <div class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="inline-flex items-center gap-2">
                        <x-heroicon-o-building-office-2 class="w-5 h-5 text-gray-700" />
                        <h3 class="text-base font-semibold text-gray-900">Offline Meetings (Approved & Request)</h3>
                    </div>
                    <span class="{{ $mono }}">Total: {{ $offline->total() }}</span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($offline as $b)
                    @php
                    $title = $b->meeting_title ?: 'Meeting';
                    $date = \Carbon\Carbon::parse($b->date)->translatedFormat('d M Y');
                    $start = \Carbon\Carbon::parse($b->start_time)->format('H:i');
                    $end = \Carbon\Carbon::parse($b->end_time)->format('H:i');
                    $room = optional($b->room)->name ?? '-';
                    $status = strtoupper($b->status ?? '-');
                    $needInform = ($b->requestinformation ?? null) === 'request';
                    @endphp

                    <div class="px-5 py-5" wire:key="off-{{ $b->bookingroom_id }}">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="{{ $ico }}"><span class="text-xs font-semibold">OF</span></div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $title }}</h4>
                                        <span class="{{ $chip }}">
                                            <x-heroicon-o-rectangle-stack class="w-4 h-4 text-gray-500" />
                                            <span class="text-gray-500">Room:</span>
                                            <span class="font-medium text-gray-700">{{ $room }}</span>
                                        </span>
                                        <span class="{{ $chip }}">
                                            <x-heroicon-o-check-badge class="w-4 h-4 text-gray-500" />
                                            <span class="text-gray-500">Status:</span>
                                            <span class="font-medium text-gray-700">{{ $status }}</span>
                                        </span>
                                    </div>
                                    <p class="text-[12px] text-gray-500 mt-1">
                                        {{ $date }} &middot; {{ $start }} - {{ $end }}
                                    </p>
                                    @if(!empty($b->special_notes))
                                    <p class="text-[12px] text-gray-700 mt-2">
                                        <span class="font-medium">Notes:</span> {{ $b->special_notes }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right shrink-0 space-y-2">
                                @if($needInform)
                                <button type="button" class="{{ $btnBlk }}"
                                    wire:click.prevent="openInformModal({{ $b->bookingroom_id }})">
                                    <x-heroicon-o-paper-airplane class="w-5 h-5" />
                                    <span>Inform My Department</span>
                                </button>
                                @else
                                <div class="text-xs text-gray-600">
                                    Info status: {{ $b->requestinformation ?? '-' }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-14 text-center text-gray-500 text-sm">Tidak ada offline meeting menunggu inform.</div>
                    @endforelse
                </div>

                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $offline->onEachSide(1)->links() }}
                </div>
            </div>

            {{-- ONLINE --}}
            <div class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="inline-flex items-center gap-2">
                        <x-heroicon-o-wifi class="w-5 h-5 text-gray-700" />
                        <h3 class="text-base font-semibold text-gray-900">Online Meetings (Approved & Request)</h3>
                    </div>
                    <span class="{{ $mono }}">Total: {{ $online->total() }}</span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($online as $b)
                    @php
                    $title = $b->meeting_title ?: 'Online Meeting';
                    $date = \Carbon\Carbon::parse($b->date)->translatedFormat('d M Y');
                    $start = \Carbon\Carbon::parse($b->start_time)->format('H:i');
                    $end = \Carbon\Carbon::parse($b->end_time)->format('H:i');
                    $provider = strtoupper((string)($b->online_provider ?? '-'));
                    $code = $b->online_meeting_code ?: '-';
                    $url = $b->online_meeting_url ?: null;
                    $pass = $b->online_meeting_password ?: '-';
                    $status = strtoupper($b->status ?? '-');
                    $needInform = ($b->requestinformation ?? null) === 'request';
                    @endphp

                    <div class="px-5 py-5" wire:key="on-{{ $b->bookingroom_id }}">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="{{ $ico }}"><span class="text-xs font-semibold">ON</span></div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $title }}</h4>
                                        <span class="{{ $chip }}">
                                            <x-heroicon-o-swatch class="w-4 h-4 text-gray-500" />
                                            <span class="text-gray-500">Provider:</span>
                                            <span class="font-medium text-gray-700">{{ $provider }}</span>
                                        </span>
                                        <span class="{{ $chip }}">
                                            <x-heroicon-o-check-badge class="w-4 h-4 text-gray-500" />
                                            <span class="text-gray-500">Status:</span>
                                            <span class="font-medium text-gray-700">{{ $status }}</span>
                                        </span>
                                    </div>
                                    <p class="text-[12px] text-gray-500 mt-1">
                                        {{ $date }} &middot; {{ $start }} - {{ $end }}
                                    </p>

                                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 text-[12px] text-gray-700">
                                        <div>Kode: <span class="font-medium">{{ $code }}</span></div>
                                        <div>Password: <span class="font-medium">{{ $pass }}</span></div>
                                        <div class="col-span-1 sm:col-span-2 break-words">
                                            Link:
                                            @if(!empty($url))
                                            <a href="{{ $url }}" target="_blank" class="underline break-words text-blue-700"> {{ $url }} </a>
                                            @else
                                            -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0 space-y-2">
                                @if($needInform)
                                <button type="button" class="{{ $btnBlk }}"
                                    wire:click.prevent="openInformModal({{ $b->bookingroom_id }})">
                                    <x-heroicon-o-paper-airplane class="w-5 h-5" />
                                    <span>Inform My Department</span>
                                </button>
                                @else
                                <div class="text-xs text-gray-600">
                                    Info status: {{ $b->requestinformation ?? '-' }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-14 text-center text-gray-500 text-sm">Tidak ada online meeting menunggu inform.</div>
                    @endforelse
                </div>

                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $online->onEachSide(1)->links() }}
                </div>
            </div>
        </section>

        {{-- INFORMATION TABLE --}}
        @if ($mode === 'index')
        <section class="{{ $card }}">
            {{-- Header --}}
            <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                <div class="inline-flex items-center gap-2">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-gray-700" />
                    <h3 class="text-base font-semibold text-gray-900">Information (Selected Department)</h3>
                </div>
                <span class="{{ $mono }}">Showing: {{ $rows->count() }} / Total: {{ $rows->total() }}</span>
            </div>

            {{-- Toolbar --}}
            <div class="px-5 py-4 sticky top-0 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 border-b border-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    <div class="flex items-center gap-3 w-full lg:w-auto">
                        <div class="relative w-full lg:w-72">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                            <input type="text"
                                wire:model.live.debounce.400ms="search"
                                placeholder="Search description..."
                                class="pl-10 {{ $input }}">
                        </div>

                        <button wire:click="create" class="{{ $btnBlk }}">
                            <x-heroicon-o-plus class="w-5 h-5" />
                            <span>New</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left">ID</th>
                            <th class="px-4 py-2 text-left">Description</th>
                            <th class="px-4 py-2 text-left">Event At</th>
                            <th class="px-4 py-2 text-left">Created</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($rows as $r)
                        <tr wire:key="row-{{ $r->information_id }}">
                            <td class="px-4 py-3">{{ $r->information_id }}</td>
                            <td class="px-4 py-3 max-w-xl">
                                <div class="line-clamp-2 text-gray-800">{{ $r->description }}</div>
                            </td>
                            <td class="px-4 py-3">{{ optional($r->event_at)->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">{{ optional($r->created_at)->diffForHumans() }}</td>
                            <td class="px-4 py-3">
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="edit({{ $r->information_id }})"
                                        class="{{ $btnLt }}">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                        <span>Edit</span>
                                    </button>
                                    <button wire:click="destroy({{ $r->information_id }})"
                                        onclick="return confirm('Delete this item?')"
                                        class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg border border-rose-300 text-rose-700 hover:bg-rose-50 transition">
                                        <x-heroicon-o-trash class="w-5 h-5" />
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">No data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4 border-t border-gray-200">
                {{ $rows->onEachSide(1)->links() }}
            </div>
        </section>
        @endif

        {{-- CREATE / EDIT FORM --}}
        @if ($mode === 'create' || $mode === 'edit')
        <section class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <div class="inline-flex items-center gap-2">
                    <x-heroicon-o-pencil-square class="w-5 h-5 text-gray-700" />
                    <h3 class="text-base font-semibold text-gray-900">
                        {{ $mode === 'create' ? 'Create Information' : 'Edit Information #'.$editingId }}
                    </h3>
                </div>
                <span class="{{ $chip }}">
                    <x-heroicon-o-finger-print class="w-4 h-4 text-gray-500" />
                    <span class="text-gray-500">Auto-fill:</span>
                    <span class="font-medium text-gray-700">
                        company_id={{ auth()->user()->company_id }},
                        department_id={{ $selected_department_id }}
                    </span>
                </span>
            </div>

            <div class="p-5 space-y-5">
                <div>
                    <label class="{{ $label }}">Description</label>
                    <textarea wire:model.defer="description" rows="5"
                        class="w-full rounded-xl border border-gray-300 px-3 py-2 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"></textarea>
                    @error('description') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="{{ $label }}">Event At</label>
                    <input type="datetime-local" wire:model.defer="event_at" class="{{ $input }}">
                    @error('event_at') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    @if ($mode === 'create')
                    <button wire:click="store" class="{{ $btnBlk }}">
                        <x-heroicon-o-check class="w-5 h-5" />
                        <span>Save</span>
                    </button>
                    @else
                    <button wire:click="update" class="{{ $btnBlk }}">
                        <x-heroicon-o-check class="w-5 h-5" />
                        <span>Update</span>
                    </button>
                    @endif

                    <button wire:click="cancel" class="{{ $btnLt }}">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                        <span>Cancel</span>
                    </button>
                </div>
            </div>
        </section>
        @endif

        {{-- MODAL: INFORM --}}
        @if($informModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center" role="dialog" aria-modal="true"
            wire:key="inform-modal" wire:keydown.escape.window="closeInformModal">
            <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay"
                wire:click="closeInformModal"></button>

            <div class="relative w-full max-w-lg mx-4 {{ $card }} focus:outline-none" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="inline-flex items-center gap-2">
                        <x-heroicon-o-paper-airplane class="w-5 h-5 text-gray-700" />
                        <h3 class="text-base font-semibold text-gray-900">Inform My Department</h3>
                    </div>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeInformModal" aria-label="Close">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <p class="text-sm text-gray-700">
                        Informasi dari booking ini akan dikirim ke departemen terpilih:
                        <span class="font-semibold">{{ $department_name }}</span>.
                    </p>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button" class="{{ $btnLt }}" wire:click="closeInformModal">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                            <span>Batal</span>
                        </button>
                        <button type="button" class="{{ $btnBlk }}"
                            wire:click="submitInform"
                            wire:loading.attr="disabled"
                            wire:target="submitInform">
                            <span class="inline-flex items-center gap-2" wire:loading.remove wire:target="submitInform">
                                <x-heroicon-o-paper-airplane class="w-5 h-5" />
                                <span>Kirim</span>
                            </span>
                            <span wire:loading wire:target="submitInform">Mengirim…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </main>
</div>