<div class="min-h-screen bg-gray-50" wire:poll.1000ms>
    <div>
        <main class="px-4 sm:px-6 lg:pl-0 lg:pr-6 py-6">
            <div class="max-w-7xl space-y-8">
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
                                <h2 class="text-lg sm:text-xl font-semibold">Meeting Schedule</h2>
                                <p class="text-sm text-white/80">Kelola jadwal rapat harian/mingguan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">
                                    {{ $editingId ? 'Edit Meeting' : 'Tambah Meeting' }}
                                </h3>
                                <p class="text-sm text-gray-500">Isi detail rapat berikut.</p>
                            </div>
                            <div class="flex items-center gap-3">
                                @if($editingId)
                                    <button wire:click="cancelEdit"
                                        class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition font-medium">
                                        Batal
                                    </button>
                                @endif
                                <button wire:click="{{ $editingId ? 'update' : 'save' }}"
                                    class="inline-flex items-center gap-2 bg-gray-900 text-white px-5 py-2 rounded-xl font-medium hover:bg-gray-800 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ $editingId ? 'Update' : 'Tambah' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <label for="date" class="block text-sm font-medium text-gray-900">Tanggal</label>
                                <input id="date" type="date" wire:model.defer="date"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:border-gray-900 focus:outline-none transition">
                                @error('date')
                                    <p class="text-xs text-red-500 font-medium flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="time" class="block text-sm font-medium text-gray-900">Waktu Mulai</label>
                                <input id="time" type="time" wire:model.defer="time"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:border-gray-900 focus:outline-none transition">
                                @error('time') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="time_end" class="block text-sm font-medium text-gray-900">Waktu
                                    Selesai</label>
                                <input id="time_end" type="time" wire:model.defer="time_end"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:border-gray-900 focus:outline-none transition">
                                @error('time_end') <p class="text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="department"
                                    class="block text-sm font-medium text-gray-900">Departemen</label>
                                <select id="department" wire:model.defer="department"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:border-gray-900 focus:outline-none transition">
                                    <option value="" hidden>Pilih departemen</option>
                                    <option value="IT">IT</option>
                                    <option value="HR">HR</option>
                                    <option value="Finance">Finance</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Operasional">Operasional</option>
                                    <option value="Riset">Riset</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                @error('department') <p class="text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="participant" class="block text-sm font-medium text-gray-900">Jumlah
                                    Peserta</label>
                                <input id="participant" type="number" min="1" wire:model.defer="participant"
                                    placeholder="Berapa orang yang ikut"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-500 focus:border-gray-900 focus:outline-none transition">
                                @error('participant') <p class="text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="with" class="block text-sm font-medium text-gray-900">Dengan</label>
                                <input id="with" type="text" wire:model.defer="with" placeholder="Nama tim/klien"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-500 focus:border-gray-900 focus:outline-none transition">
                            </div>

                            <div class="space-y-2 md:col-span-3 lg:col-span-1">
                                <label for="location" class="block text-sm font-medium text-gray-900">Lokasi</label>
                                <select id="location" wire:model.defer="location"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:border-gray-900 focus:outline-none transition">
                                    <option value="" hidden>Pilih ruangan</option>
                                    <option value="Ruangan 1">Ruangan 1</option>
                                    <option value="Ruangan 2">Ruangan 2</option>
                                    <option value="Ruangan 3">Ruangan 3</option>
                                </select>
                                @error('location') <p class="text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-3 space-y-2">
                                <label for="notes" class="block text-sm font-medium text-gray-900">Catatan</label>
                                <textarea id="notes" wire:model.defer="notes" rows="4"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-500 focus:border-gray-900 focus:outline-none transition resize-none"
                                    placeholder="Agenda singkat / yang perlu dibawa"></textarea>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
                    <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" wire:model.live.debounce.400ms="q"
                                    placeholder="Cari participant, with, location, notes..."
                                    class="w-full border border-gray-200 rounded-xl pl-12 pr-4 py-3 text-gray-900 placeholder-gray-500 focus:border-gray-900 focus:outline-none transition">
                            </div>
                        </div>
                        <select wire:model.live="statusFilter"
                            class="border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:border-gray-900 focus:outline-none transition min-w-[160px]">
                            <option value="all">Semua Status</option>
                            <option value="planned">Planned</option>
                            <option value="done">Done</option>
                        </select>
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">Daftar Meeting</h3>
                                <p class="text-sm text-gray-500">Total: {{ count($rows) }} meeting</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b border-gray-200">
                                    <th class="px-5 py-3 font-medium">Tanggal</th>
                                    <th class="px-5 py-3 font-medium">Waktu (Mulai–Selesai)</th>
                                    <th class="px-5 py-3 font-medium">Departemen</th>
                                    <th class="px-5 py-3 font-medium">Peserta</th>
                                    <th class="px-5 py-3 font-medium">Dengan</th>
                                    <th class="px-5 py-3 font-medium">Lokasi</th>
                                    <th class="px-5 py-3 font-medium">Status</th>
                                    <th class="px-5 py-3 font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($rows as $m)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-5 py-3 font-medium text-gray-900">
                                            {{ \Illuminate\Support\Carbon::parse($m['date'])->format('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3 text-gray-700 font-medium">
                                            {{ $m['time'] }}{{ isset($m['time_end']) ? ' – ' . $m['time_end'] : '' }}
                                        </td>
                                        <td class="px-5 py-3 text-gray-700">{{ $m['department'] ?? '-' }}</td>
                                        <td class="px-5 py-3 text-gray-900 font-medium">{{ $m['participant'] }}</td>
                                        <td class="px-5 py-3 text-gray-700 max-w-[200px] truncate" title="{{ $m['with'] }}">
                                            {{ $m['with'] }}
                                        </td>
                                        <td class="px-5 py-3 text-gray-700 max-w-[220px] truncate"
                                            title="{{ $m['location'] }}">
                                            {{ $m['location'] }}
                                        </td>
                                        <td class="px-5 py-3">
                                            @if($m['status'] === 'done')
                                                <span
                                                    class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">
                                                    Done
                                                </span>
                                            @else
                                                <span
                                                    class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                                                    Planned
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="flex flex-wrap gap-2 justify-end">
                                                <button wire:click="edit({{ $m['id'] }})" wire:loading.attr="disabled"
                                                    wire:target="edit({{ $m['id'] }})"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-700 hover:border-gray-900 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-600/20 disabled:opacity-60 transition">
                                                    <span wire:loading.remove wire:target="edit({{ $m['id'] }})">Edit</span>
                                                    <span wire:loading wire:target="edit({{ $m['id'] }})">Memuat…</span>
                                                </button>
                                                @if($m['status'] === 'planned')
                                                    <button wire:click="markDone({{ $m['id'] }})" wire:loading.attr="disabled"
                                                        wire:target="markDone({{ $m['id'] }})"
                                                        class="px-3 py-2 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-600/20 disabled:opacity-60 transition">
                                                        <span wire:loading.remove wire:target="markDone({{ $m['id'] }})">Mark
                                                            Done</span>
                                                        <span wire:loading wire:target="markDone({{ $m['id'] }})">Memuat…</span>
                                                    </button>
                                                @else
                                                    <button wire:click="markPlanned({{ $m['id'] }})"
                                                        wire:loading.attr="disabled" wire:target="markPlanned({{ $m['id'] }})"
                                                        class="px-3 py-2 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 disabled:opacity-60 transition">
                                                        <span wire:loading.remove wire:target="markPlanned({{ $m['id'] }})">Set
                                                            Planned</span>
                                                        <span wire:loading
                                                            wire:target="markPlanned({{ $m['id'] }})">Memuat…</span>
                                                    </button>
                                                @endif
                                                <button wire:click="destroy({{ $m['id'] }})" wire:loading.attr="disabled"
                                                    wire:target="destroy({{ $m['id'] }})"
                                                    class="px-3 py-2 text-xs font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-600/20 disabled:opacity-60 transition">
                                                    <span wire:loading.remove
                                                        wire:target="destroy({{ $m['id'] }})">Delete</span>
                                                    <span wire:loading
                                                        wire:target="destroy({{ $m['id'] }})">Menghapus…</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-12 text-center text-gray-500" colspan="8">
                                            <div class="flex flex-col items-center gap-3">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p class="text-gray-500 font-medium">Belum ada jadwal meeting</p>
                                                <p class="text-sm text-gray-400">Tambah meeting pertama Anda di form di atas
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
        </main>
    </div>
</div>