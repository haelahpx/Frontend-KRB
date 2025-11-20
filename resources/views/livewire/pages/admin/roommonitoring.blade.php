<div class="bg-gray-50 min-h-screen" wire:key="room-monitoring-history">
    @php
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnLt = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition';
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $titleC = 'text-base font-semibold text-gray-900';
    $field = 'text-sm text-gray-600';
    @endphp

    <main class="px-4 sm:px-6 py-6 space-y-8">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-6 w-28 h-28 bg-white/20 rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-6 w-20 h-20 bg-white/10 rounded-full blur-lg"></div>
            </div>

            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    {{-- LEFT SECTION --}}
                    <div class="flex items-start gap-4 sm:gap-6">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20 shrink-0">
                            <x-heroicon-o-information-circle class="w-6 h-6 text-white" />
                        </div>

                        <div class="space-y-1.5">
                            <h2 class="text-xl sm:text-2xl font-semibold leading-tight">
                                Booking Center
                            </h2>

                            <div class="text-sm text-white/80 flex flex-col sm:block">
                                <span>Perusahaan: <span class="font-semibold">{{ $company_name }}</span></span>
                                <span class="hidden sm:inline mx-2">•</span>
                                <span>Departemen: <span class="font-semibold">{{ $department_name }}</span></span>
                            </div>

                            <p class="text-xs text-white/60 pt-1 sm:pt-0">
                                Menampilkan booking untuk departemen:
                                <span class="font-medium">{{ $department_name }}</span>.
                            </p>
                        </div>
                    </div>

                    {{-- RIGHT SECTION --}}
                    @if ($showSwitcher)
                    <div class="w-full lg:w-[32rem] lg:ml-6">
                        <label class="block text-xs font-medium text-white/80 mb-2">
                            Pilih Departemen
                        </label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <select
                                wire:model.live="selected_department_id"
                                class="w-full h-11 sm:h-12 px-3 sm:px-4 rounded-lg border border-white/20 bg-white/10 text-white text-sm placeholder:text-white/60 focus:border-white focus:ring-2 focus:ring-white/30 focus:outline-none transition">
                                <option class="text-gray-900" value="{{ auth()->user()->department_id }}">
                                    {{ auth()->user()->department->name }} (Your Primary Department)
                                </option>
                                @foreach ($deptOptions as $opt)
                                <option class="text-gray-900" value="{{ $opt['id'] }}">
                                    {{ $opt['name'] }}{{ $opt['id'] === $primary_department_id ? ' — Primary' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    {{-- SEARCH VERSION --}}
                    <div class="w-full lg:w-80 lg:ml-auto">
                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Cari deskripsi atau catatan…"
                            class="w-full rounded-lg bg-white/10 text-white border border-white/20
                                       px-3 py-2 backdrop-blur-sm focus:ring-2 focus:ring-white/30 focus:outline-none">
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- GRID: OFFLINE (left) & ONLINE (right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- OFFLINE --}}
            <section class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-building-office-2 class="w-5 h-5 text-gray-700" />
                        <div>
                            <h3 class="{{ $titleC }}">Offline Meetings</h3>
                            <p class="text-xs text-gray-500">Riwayat meeting di ruang fisik.</p>
                        </div>
                    </div>
                    <span class="{{ $mono }}">Total: {{ $offline->total() }}</span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($offline as $b)
                    @php
                    $status = strtolower($b->status);
                    $color = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'completed' => 'bg-blue-100 text-blue-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    ][$status] ?? 'bg-gray-100 text-gray-800';

                    $rowNumber = $offline->firstItem() + $loop->index;
                    @endphp

                    <div class="px-5 py-4 hover:bg-gray-50 transition-colors" wire:key="off-{{ $b->bookingroom_id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-900 truncate">
                                    {{ $b->meeting_title }}
                                </div>
                                <div class="{{ $field }} mt-0.5 flex flex-wrap items-center gap-2">
                                    <span class="{{ $mono }}">#{{ $rowNumber }}</span>
                                    <span class="text-gray-400">•</span>
                                    <span>
                                        {{ \Illuminate\Support\Carbon::parse($b->start_time)->format('d M Y, H:i') }}
                                        – {{ \Illuminate\Support\Carbon::parse($b->end_time)->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $color }}">
                                {{ ucfirst($status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 mt-3">
                            <div>
                                <div class="text-gray-500 flex items-center gap-1">
                                    <x-heroicon-o-rectangle-stack class="w-4 h-4 text-gray-400" />
                                    <span>Room</span>
                                </div>
                                <div class="font-medium">
                                    {{ $b->room->room_name ?? '—' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500">Attendees</div>
                                <div class="font-medium">{{ $b->number_of_attendees }}</div>
                            </div>
                            <div class="col-span-2">
                                <div class="text-gray-500">Notes</div>
                                <div class="line-clamp-2">
                                    {{ $b->special_notes ?: '—' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-10 text-center text-gray-500 text-sm">
                        Tidak ada riwayat offline meeting.
                    </div>
                    @endforelse
                </div>

                {{-- Pagination Links for Offline --}}
                @if ($offline->hasPages())
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $offline->links() }}
                    </div>
                </div>
                @endif
            </section>

            {{-- ONLINE --}}
            <section class="{{ $card }}">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-wifi class="w-5 h-5 text-gray-700" />
                        <div>
                            <h3 class="{{ $titleC }}">Online Meetings</h3>
                            <p class="text-xs text-gray-500">Riwayat meeting via platform online.</p>
                        </div>
                    </div>
                    <span class="{{ $mono }}">Total: {{ $online->total() }}</span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($online as $b)
                    @php
                    $status = strtolower($b->status);
                    $color = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'completed' => 'bg-blue-100 text-blue-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    ][$status] ?? 'bg-gray-100 text-gray-800';
                    @endphp

                    <div class="px-5 py-4 hover:bg-gray-50 transition-colors" wire:key="on-{{ $b->bookingroom_id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-900 truncate">
                                    {{ $b->meeting_title }}
                                </div>
                                <div class="{{ $field }} mt-0.5 flex flex-wrap items-center gap-2">
                                    <span class="{{ $mono }}">#{{ $b->bookingroom_id }}</span>
                                    <span class="text-gray-400">•</span>
                                    <span>
                                        {{ \Illuminate\Support\Carbon::parse($b->start_time)->format('d M Y, H:i') }}
                                        – {{ \Illuminate\Support\Carbon::parse($b->end_time)->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $color }}">
                                {{ ucfirst($status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 mt-3">
                            <div>
                                <div class="text-gray-500 flex items-center gap-1">
                                    <x-heroicon-o-swatch class="w-4 h-4 text-gray-400" />
                                    <span>Provider</span>
                                </div>
                                <div class="font-medium capitalize">
                                    {{ str_replace('_', ' ', $b->online_provider ?? '—') }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500">Attendees</div>
                                <div class="font-medium">{{ $b->number_of_attendees }}</div>
                            </div>

                            <div class="col-span-2">
                                <div class="text-gray-500">Meeting URL</div>
                                @if ($b->online_meeting_url)
                                <a href="{{ $b->online_meeting_url }}" target="_blank"
                                    class="text-blue-600 hover:underline break-all">
                                    {{ $b->online_meeting_url }}
                                </a>
                                @else
                                <div class="text-gray-700">—</div>
                                @endif
                            </div>

                            <div>
                                <div class="text-gray-500">Meeting Code</div>
                                <div class="font-medium">{{ $b->online_meeting_code ?: '—' }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Password</div>
                                <div class="font-medium">{{ $b->online_meeting_password ?: '—' }}</div>
                            </div>

                            <div class="col-span-2">
                                <div class="text-gray-500">Notes</div>
                                <div class="line-clamp-2">
                                    {{ $b->special_notes ?: '—' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-10 text-center text-gray-500 text-sm">
                        Tidak ada riwayat online meeting.
                    </div>
                    @endforelse
                </div>

                {{-- Pagination Links for Online --}}
                @if ($online->hasPages())
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        {{ $online->links() }}
                    </div>
                </div>
                @endif
            </section>
        </div>
    </main>
</div>