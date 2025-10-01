    <div class=" bg-gray-50" wire:poll.800ms="tick" wire:poll.keep-alive.2s="tick">
        @php
            $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
            $label = 'block text-sm font-medium text-gray-700 mb-2';
            $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
            $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
            $btnBlu = 'px-3 py-2 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 disabled:opacity-60 transition';
            $btnGrn = 'px-3 py-2 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition';
            $btnOrn = 'px-3 py-2 text-xs font-medium rounded-lg bg-amber-600 text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-600/20 disabled:opacity-60 transition';
            $btnRed = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
            $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
            $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
            $ico = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
        @endphp

        <main class="px-4 sm:px-6 py-6">
            <div class="space-y-8">

                <div
                    class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
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
                                <h2 class="text-lg sm:text-xl font-semibold">Booking Room</h2>
                                <p class="text-sm text-white/80">Kelola jadwal rapat harian/mingguan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="{{ $card }}">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">
                            {{ $editingId ? 'Edit Meeting' : 'Tambah Meeting' }}
                        </h3>
                        <p class="text-sm text-gray-500">Isi detail rapat berikut.</p>
                    </div>

                    <form class="p-5" wire:submit.prevent="{{ $editingId ? 'update' : 'save' }}">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="md:col-span-3">
                                <label class="{{ $label }}">Meeting Title</label>
                                <input type="text" wire:model.defer="meeting_title" class="{{ $input }}"
                                    placeholder="Contoh: Weekly Sync, Kickoff Project, dll">
                                @error('meeting_title') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Room</label>
                                <select wire:model.defer="location" class="{{ $input }}">
                                    <option value="" hidden>Pilih room</option>
                                    <option value="Ruangan 1">Ruangan 1</option>
                                    <option value="Ruangan 2">Ruangan 2</option>
                                    <option value="Ruangan 3">Ruangan 3</option>
                                </select>
                                @error('location') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Departemen</label>
                                <select wire:model.defer="department_id" class="{{ $input }}">
                                    <option value="" hidden>Pilih departemen</option>
                                    @foreach ($departments as $d)
                                        <option value="{{ $d['department_id'] }}">{{ $d['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('department_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Tanggal</label>
                                <input type="date" wire:model.defer="date" class="{{ $input }}">
                                @error('date') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Jumlah Peserta</label>
                                <input type="number" min="1" wire:model.defer="participant" class="{{ $input }}"
                                    placeholder="Berapa orang yang ikut">
                                @error('participant') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Waktu Mulai</label>
                                <input type="time" wire:model.defer="time" class="{{ $input }}">
                                @error('time') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="{{ $label }}">Waktu Selesai</label>
                                <input type="time" wire:model.defer="time_end" class="{{ $input }}">
                                @error('time_end') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-3">
                                <label class="{{ $label }}">Kebutuhan Ruangan</label>
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-3 text-sm text-gray-700">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" value="video" wire:model="requirements"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span>Video Conference</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" value="projector" wire:model="requirements"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span>Projector</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" value="whiteboard" wire:model="requirements"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span>Whiteboard</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" value="catering" wire:model="requirements"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span>Catering</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer sm:col-span-2">
                                        <input type="checkbox" value="other" wire:model="requirements"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span>Other</span>
                                    </label>
                                </div>
                                @error('requirements.*') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            @if (in_array('other', $requirements ?? [], true))
                                <div class="md:col-span-3">
                                    <label class="{{ $label }}">Catatan</label>
                                    <textarea rows="4" wire:model.defer="notes" class="{{ $input }} !h-auto resize-none"
                                        placeholder="Jelaskan kebutuhan lainnya (wajib jika memilih Other)"></textarea>
                                    @error('notes') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <div class="pt-5">
                            <div class="flex items-center gap-3">
                                @if($editingId)
                                    <button type="button" wire:click="closeEdit"
                                        class="px-4 h-10 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 hover:border-gray-400 transition">
                                        Batal
                                    </button>
                                @endif

                                <button type="submit" wire:loading.attr="disabled"
                                    wire:target="{{ $editingId ? 'update' : 'save' }}"
                                    class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium
                                            shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900/20
                                            transition active:scale-95 hover:bg-black disabled:opacity-60 relative overflow-hidden"
                                    wire:loading.class="opacity-80 cursor-wait">

                                    <span class="flex items-center gap-2" wire:loading.remove
                                        wire:target="{{ $editingId ? 'update' : 'save' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ $editingId ? 'Simpan Perubahan' : 'Simpan Data' }}
                                    </span>

                                    <span class="flex items-center gap-2" wire:loading
                                        wire:target="{{ $editingId ? 'update' : 'save' }}">
                                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                                        </svg>
                                        Menyimpan…
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </section>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <section class="{{ $card }}">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Planned</h3>
                                    <p class="text-sm text-gray-500">Rapat yang sudah dijadwalkan.</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-3">
                            @forelse ($planned as $m)
                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-200" wire:key="planned-{{ $m['id'] }}">
                                    <div class="flex items-start gap-3">
                                        <div class="{{ $ico }}">{{ strtoupper(substr(($m['meeting_title'] ?? 'M'), 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <div class="font-semibold text-gray-900 text-sm">
                                                    {{ $m['meeting_title'] ?? '—' }}
                                                </div>
                                                <span
                                                    class="{{ $chip }}">{{ \Illuminate\Support\Carbon::parse($m['date'])->format('d M Y') }}</span>
                                                <span class="{{ $chip }}">{{ $m['time'] }}–{{ $m['time_end'] }}</span>
                                            </div>
                                            <div class="mt-1 text-[12px] text-gray-600">
                                                {{ $m['location'] }} • {{ $m['participant'] }} peserta
                                            </div>

                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <button class="{{ $btnBlk }}" wire:click="openEdit({{ $m['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="openEdit({{ $m['id'] }})">Edit</button>
                                                <button class="{{ $btnRed }}" wire:click="destroy({{ $m['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="destroy({{ $m['id'] }})">Hapus</button>
                                            </div>
                                        </div>
                                        <div class="{{ $mono }}">#{{ $m['id'] }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 text-sm">Belum ada item planned</div>
                            @endforelse
                        </div>
                    </section>

                    <section class="{{ $card }}">
                        <div class="px-6 py-4 border border-t-0 border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-amber-600 rounded-full"></div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Ongoing</h3>
                                    <p class="text-sm text-gray-500">Rapat sedang berlangsung.</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-3">
                            @forelse ($ongoing as $m)
                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-200" wire:key="ongoing-{{ $m['id'] }}">
                                    <div class="flex items-start gap-3">
                                        <div class="{{ $ico }}">{{ strtoupper(substr(($m['meeting_title'] ?? 'M'), 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <div class="font-semibold text-gray-900 text-sm">
                                                    {{ $m['meeting_title'] ?? '—' }}
                                                </div>
                                                <span
                                                    class="{{ $chip }}">{{ \Illuminate\Support\Carbon::parse($m['date'])->format('d M Y') }}</span>
                                                <span class="{{ $chip }}">{{ $m['time'] }}–{{ $m['time_end'] }}</span>
                                            </div>
                                            <div class="mt-1 text-[12px] text-gray-600">
                                                {{ $m['location'] }} • {{ $m['participant'] }} peserta
                                            </div>

                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <button class="{{ $btnBlk }}" wire:click="openEdit({{ $m['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="openEdit({{ $m['id'] }})">Edit</button>
                                                <button class="{{ $btnRed }}" wire:click="destroy({{ $m['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="destroy({{ $m['id'] }})">Hapus</button>
                                            </div>
                                        </div>
                                        <div class="{{ $mono }}">#{{ $m['id'] }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 text-sm">Belum ada item ongoing</div>
                            @endforelse
                        </div>
                    </section>

                    {{-- DONE --}}
                    <section class="{{ $card }}">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 bg-emerald-600 rounded-full"></div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Done</h3>
                                    <p class="text-sm text-gray-500">Rapat yang telah selesai.</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-3">
                            @forelse ($done as $m)
                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-200" wire:key="done-{{ $m['id'] }}">
                                    <div class="flex items-start gap-3">
                                        <div class="{{ $ico }}">{{ strtoupper(substr(($m['meeting_title'] ?? 'M'), 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <div class="font-semibold text-gray-900 text-sm">
                                                    {{ $m['meeting_title'] ?? '—' }}
                                                </div>
                                                <span
                                                    class="{{ $chip }}">{{ \Illuminate\Support\Carbon::parse($m['date'])->format('d M Y') }}</span>
                                                <span class="{{ $chip }}">{{ $m['time'] }}–{{ $m['time_end'] }}</span>
                                            </div>
                                            <div class="mt-1 text-[12px] text-gray-600">
                                                {{ $m['location'] }} • {{ $m['participant'] }} peserta
                                            </div>

                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <button class="{{ $btnBlk }}" wire:click="openEdit({{ $m['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="openEdit({{ $m['id'] }})">Edit</button>
                                                <button class="{{ $btnRed }}" wire:click="destroy({{ $m['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="destroy({{ $m['id'] }})">Hapus</button>
                                            </div>
                                        </div>
                                        <div class="{{ $mono }}">#{{ $m['id'] }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 text-sm">Belum ada item selesai</div>
                            @endforelse
                        </div>
                    </section>

                </div>
            </div>
        </main>

        @if ($modalEdit)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:poll.800ms>
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeEdit"></div>

                <div
                    class="relative w-full max-w-xl bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden max-h-[90vh] flex flex-col">
                    <div class="bg-gradient-to-r from-gray-900 to-black p-5 text-white relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10 pointer-events-none">
                            <div class="absolute top-0 -right-6 w-24 h-24 bg-white rounded-full blur-2xl"></div>
                            <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-xl"></div>
                        </div>
                        <div class="relative z-10 flex items-center justify-between">
                            <h3 class="text-lg font-semibold tracking-tight">Edit Meeting</h3>
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <div class="md:col-span-2">
                                <label class="{{ $label }}">Meeting Title</label>
                                <input type="text" class="{{ $input }}" wire:model.defer="meeting_title"
                                    placeholder="Judul meeting">
                            </div>

                            <div>
                                <label class="{{ $label }}">Room</label>
                                <select class="{{ $input }}" wire:model.defer="location">
                                    <option value="" hidden>Pilih room</option>
                                    <option value="Ruangan 1">Ruangan 1</option>
                                    <option value="Ruangan 2">Ruangan 2</option>
                                    <option value="Ruangan 3">Ruangan 3</option>
                                </select>
                            </div>

                            <div>
                                <label class="{{ $label }}">Departemen</label>
                                <select class="{{ $input }}" wire:model.defer="department_id">
                                    <option value="" hidden>Pilih departemen</option>
                                    @foreach ($departments as $d)
                                        <option value="{{ $d['department_id'] }}">{{ $d['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="{{ $label }}">Tanggal</label>
                                <input type="date" class="{{ $input }}" wire:model.defer="date">
                            </div>

                            <div>
                                <label class="{{ $label }}">Peserta</label>
                                <input type="number" min="1" class="{{ $input }}" wire:model.defer="participant">
                            </div>

                            <div>
                                <label class="{{ $label }}">Mulai</label>
                                <input type="time" class="{{ $input }}" wire:model.defer="time">
                            </div>

                            <div>
                                <label class="{{ $label }}">Selesai</label>
                                <input type="time" class="{{ $input }}" wire:model.defer="time_end">
                            </div>

                            <div class="md:col-span-2">
                                <label class="{{ $label }}">Kebutuhan Ruangan</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm text-gray-700">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" value="video" wire:model="requirements"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span>Video Conference</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" value="projector" wire:model="requirements"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span>Projector</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" value="whiteboard" wire:model="requirements"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span>Whiteboard</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" value="catering" wire:model="requirements"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span>Catering</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer sm:col-span-2">
                                        <input type="checkbox" value="other" wire:model="requirements"
                                            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                        <span>Other</span>
                                    </label>
                                </div>
                                @error('requirements.*') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            @if (in_array('other', $requirements ?? [], true))
                                <div class="md:col-span-2">
                                    <label class="{{ $label }}">Catatan</label>
                                    <textarea rows="3" class="{{ $input }} !h-auto resize-none" wire:model.defer="notes"
                                        placeholder="Jelaskan kebutuhan lainnya (wajib jika memilih Other)"></textarea>
                                    @error('notes') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 border-t border-gray-200 p-5">
                        <div class="flex items-center justify-end gap-2.5">
                            <button type="button"
                                class="px-4 h-10 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 hover:border-gray-400 transition"
                                wire:click="closeEdit">Batal</button>
                            <button type="button"
                                class="px-4 h-10 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition"
                                wire:click="update">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>