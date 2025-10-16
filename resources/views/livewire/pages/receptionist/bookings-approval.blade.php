    @php
        use Carbon\Carbon;

        // formarter for date and time display
        if (!function_exists('fmtDate')) {
            function fmtDate($v)
            {
                try {
                    return $v ? Carbon::parse($v)->format('d M Y') : '—';
                } catch (\Throwable $e) {
                    return '—';
                }
            }
        }
        if (!function_exists('fmtTime')) {
            function fmtTime($v)
            {
                try {
                    return $v ? Carbon::parse($v)->format('H:i') : '—';
                } catch (\Throwable $e) {
                    return (is_string($v) && preg_match('/^\d{2}:\d{2}/', $v)) ? substr($v, 0, 5) : '—';
                }
            }
        }

    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnRed = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
    $btnGrn = 'px-3 py-2 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition';
    $chip = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-medium';
    $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $ico = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
@endphp

<div class="bg-gray-50">
    <main class="px-4 sm:px-6 py-6 space-y-8">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zm7-6v3l2 2" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Bookings Approval</h2>
                        <p class="text-sm text-white/80">Kelola persetujuan booking & pembuatan online meeting.</p>
                    </div>
                </div>
            </div>
        </div>

        <section class="{{ $card }}">
            <div class="p-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="space-y-0.5">
                        <h3 class="text-base font-semibold text-gray-900">Filter</h3>
                        <p class="text-sm text-gray-500">Cari judul & filter status.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full md:w-auto">
                        <input type="text" wire:model.live="q" placeholder="Search title…"
                            class="{{ $input }} w-full sm:w-56" />
                        <select wire:model.live="filter" class="{{ $input }} w-full sm:w-40">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        @if(!$this->googleConnected)
            <div class="rounded-2xl border border-amber-300 bg-amber-50 p-4 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="text-amber-900">
                        <strong>Google belum terhubung.</strong>
                        <span class="text-amber-800">Hubungkan akun untuk mengaktifkan pembuatan Google Meet
                            otomatis.</span>
                    </div>
                    <a href="{{ route('google.connect') }}"
                        class="px-3 py-2 rounded-lg border border-gray-300 text-sm font-medium hover:bg-white">
                        Connect Google
                    </a>
                </div>
            </div>
        @endif

        <section class="{{ $card }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-emerald-600 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Create Online Meeting</h3>
                        <p class="text-sm text-gray-500">Buat dan langsung approve meeting online.</p>
                    </div>
                </div>
            </div>

            <form class="p-5 grid grid-cols-1 lg:grid-cols-2 gap-6" wire:submit.prevent="createOnlineMeeting">
                <div class="space-y-4">
                    <div>
                        <label class="{{ $label }}">Meeting Title</label>
                        <input type="text" wire:model.defer="meeting_title" class="w-full {{ $input }}"
                            placeholder="Contoh: Standup harian">
                        @error('meeting_title') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="{{ $label }}">Platform</label>
                            <select wire:model.defer="platform" class="w-full {{ $input }}">
                                <option value="google_meet">Google Meet</option>
                                <option value="zoom">Zoom</option>
                            </select>
                            @error('platform') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-end">
                            @if($platform === 'google_meet')
                                <span class="text-[11px] px-2 py-1 rounded bg-yellow-100 text-yellow-800">
                                    {{ $this->googleConnected ? 'Google connected' : 'Google NOT connected' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="{{ $label }}">Department</label>
                        <select wire:model.live="selected_department_id" class="w-full {{ $input }}">
                            <option value="">— Select Department —</option>
                            @foreach($departments as $d)
                                <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                            @endforeach
                        </select>
                        @error('selected_department_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}
                        </p> @enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">User (filtered by department)</label>
                        <select wire:model.defer="selected_user_id" class="w-full {{ $input }}"
                            @disabled(empty($filteredUsers))>
                            <option value="">— Select User —</option>
                            @foreach($filteredUsers as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                            @endforeach
                        </select>
                        @error('selected_user_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="{{ $label }}">Date</label>
                            <input type="date" wire:model.defer="date" class="w-full {{ $input }}">
                            @error('date') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Start</label>
                            <input type="time" wire:model.defer="start_time" class="w-full {{ $input }}">
                            @error('start_time') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">End</label>
                            <input type="time" wire:model.defer="end_time" class="w-full {{ $input }}">
                            @error('end_time') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="lg:sticky lg:top-5 pt-1">
                        <button type="submit" class="{{ $btnBlk }} h-10" wire:loading.attr="disabled">
                            Create & Approve
                        </button>
                    </div>
                </div>
            </form>
        </section>

        <section class="{{ $card }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Daftar Booking</h3>
                        <p class="text-sm text-gray-500">Approve / reject / edit & lihat detail meeting.</p>
                    </div>
                </div>
            </div>

            <div class="p-5 space-y-3">
                @forelse($rows as $b)
                    @php
                        $status = strtolower(trim((string) $b->status));
                        $typeChip = $b->booking_type === 'online_meeting'
                            ? 'bg-blue-50 text-blue-700 border border-blue-200'
                            : 'bg-gray-50 text-gray-700 border border-gray-200';

                        $statusChip = $status === 'pending'
                            ? 'bg-amber-100 text-amber-800'
                            : ($status === 'approved'
                                ? 'bg-emerald-100 text-emerald-800'
                                : 'bg-rose-100 text-rose-800');
                    @endphp

                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                        <div class="flex items-start gap-3">
                            <div class="{{ $ico }}">{{ strtoupper(substr((string) $b->meeting_title, 0, 1)) }}</div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="font-semibold text-gray-900">
                                        #{{ $b->bookingroom_id }} — {{ $b->meeting_title }}
                                    </div>
                                    <span class="{{ $chip }} {{ $typeChip }}">
                                        {{ strtoupper($b->booking_type) }}
                                    </span>
                                    <span class="{{ $chip }} {{ $statusChip }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </div>

                                <div class="mt-1 text-[12px] text-gray-600 flex flex-wrap items-center gap-2">
                                    <span>{{ fmtDate($b->date) }}</span>
                                    <span>•</span>
                                    <span>{{ fmtTime($b->start_time) }}–{{ fmtTime($b->end_time) }}</span>
                                    @if($b->booking_type === 'online_meeting' && $b->online_provider)
                                        <span>•</span>
                                        <span>Provider: {{ ucfirst(str_replace('_', ' ', $b->online_provider)) }}</span>
                                    @endif
                                </div>

                                @if($b->booking_type === 'online_meeting' && $status === 'approved' && $b->online_meeting_url)
                                    <div class="mt-2 text-sm">
                                        <a href="{{ $b->online_meeting_url }}" target="_blank"
                                            class="text-blue-600 underline">Meeting Link</a>
                                        @if($b->online_meeting_code)
                                            <span class="text-gray-600 ml-2">Code:
                                                <span class="font-medium">{{ $b->online_meeting_code }}</span>
                                            </span>
                                        @endif
                                        @if($b->online_meeting_password)
                                            <span class="text-gray-600 ml-2">Password:
                                                <span class="font-medium">{{ $b->online_meeting_password }}</span>
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button wire:click="openEdit({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                        class="{{ $btnBlk }}">
                                        Edit
                                    </button>

                                    @if($status === 'pending')
                                        <button wire:click="approve({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                            class="{{ $btnGrn }}">
                                            Approve
                                        </button>
                                        <button wire:click="reject({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                            class="{{ $btnRed }}">
                                            Reject
                                        </button>
                                    @elseif($status === 'approved')
                                        <button wire:click="reject({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                            title="Change to Rejected" class="{{ $btnRed }}">
                                            Reject
                                        </button>
                                    @elseif($status === 'rejected')
                                        <button wire:click="approve({{ $b->bookingroom_id }})" wire:loading.attr="disabled"
                                            title="Change to Approved" class="{{ $btnGrn }}">
                                            Approve
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="{{ $mono }}">ID: {{ $b->bookingroom_id }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 text-sm">Tidak ada data.</div>
                @endforelse
            </div>

            <div class="px-5 pb-5">
                {{ $rows->links() }}
            </div>
        </section>

        {{-- MODAL --}}
        @if($showEdit ?? false)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeEdit"></div>
                <div
                    class="relative w-full max-w-xl bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden max-h-[90vh] flex flex-col">
                    <div class="bg-gradient-to-r from-gray-900 to-black p-5 text-white relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10 pointer-events-none">
                            <div class="absolute top-0 -right-6 w-24 h-24 bg-white rounded-full blur-2xl"></div>
                            <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-xl"></div>
                        </div>
                        <div class="relative z-10 flex items-center justify-between">
                            <h3 class="text-lg font-semibold tracking-tight">Edit Booking</h3>
                            <button
                                class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 flex items-center justify-center"
                                wire:click="closeEdit">
                                <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-5 space-y-4 overflow-y-auto flex-1">
                        <div>
                            <label class="{{ $label }}">Meeting Title</label>
                            <input type="text" wire:model.defer="meeting_title" class="w-full {{ $input }}"
                                placeholder="Judul meeting">
                            @error('meeting_title') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">Department</label>
                            <select wire:model.live="selected_department_id" class="w-full {{ $input }}">
                                <option value="">— Select Department —</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                                @endforeach
                            </select>
                            @error('selected_department_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}
                            </p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">User (filtered by department)</label>
                            <select wire:model.defer="selected_user_id" class="w-full {{ $input }}"
                                @disabled(empty($filteredUsers))>
                                <option value="">— Select User —</option>
                                @foreach($filteredUsers as $u)
                                    <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                                @endforeach
                            </select>
                            @error('selected_user_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="{{ $label }}">Platform</label>
                                <select wire:model.defer="platform" class="w-full {{ $input }}">
                                    <option value="google_meet">Google Meet</option>
                                    <option value="zoom">Zoom</option>
                                </select>
                                @error('platform') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-end">
                                @if($platform === 'google_meet')
                                    <span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800">
                                        {{ $this->googleConnected ? 'Google connected' : 'Google NOT connected' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="{{ $label }}">Date</label>
                                <input type="date" wire:model.defer="date" class="w-full {{ $input }}">
                                @error('date') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">Start</label>
                                <input type="time" wire:model.defer="start_time" class="w-full {{ $input }}">
                                @error('start_time') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">End</label>
                                <input type="time" wire:model.defer="end_time" class="w-full {{ $input }}">
                                @error('end_time') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 border-t border-gray-200 p-5">
                        <div class="flex items-center justify-end gap-2.5">
                            <button type="button"
                                class="px-4 h-10 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 hover:border-gray-400 transition"
                                wire:click="closeEdit">Cancel</button>
                            <button type="button"
                                class="px-4 h-10 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition"
                                wire:click="update" wire:loading.attr="disabled">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </main>
</div>