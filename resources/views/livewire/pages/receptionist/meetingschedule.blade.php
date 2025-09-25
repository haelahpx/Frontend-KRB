<div class="min-h-screen bg-gray-50 p-6 space-y-6">
    <div class="bg-gradient-to-r from-gray-900 to-black text-white rounded-2xl p-6 relative overflow-hidden shadow-2xl">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
            <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zm7-6v3l2 2" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Meeting Schedule</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <p class="text-gray-200 text-sm">Kelola jadwal rapat harian/mingguan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-900 to-black text-white px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">{{ $editingId ? 'Edit Meeting' : 'Tambah Meeting' }}</h2>
                <p class="text-gray-200 text-sm">Isi detail rapat berikut.</p>
            </div>
            <div class="flex items-center gap-3">
                @if($editingId)
                    <button wire:click="cancelEdit"
                        class="px-4 py-2 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white hover:bg-white/20 transition-all duration-200">
                        Batal
                    </button>
                @endif
                <button wire:click="{{ $editingId ? 'update' : 'save' }}"
                    class="inline-flex items-center gap-2 bg-white text-gray-900 px-5 py-2 rounded-xl font-semibold hover:bg-gray-100 transition-all duration-200 shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ $editingId ? 'Update' : 'Tambah' }}
                </button>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-800">Tanggal</label>
                    <input type="date" wire:model.defer="date" 
                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:border-black focus:bg-white focus:outline-none transition-all duration-200 hover:border-gray-300">
                    @error('date') 
                        <p class="text-xs text-red-500 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-800">Waktu Mulai</label>
                    <input type="time" wire:model.defer="time" 
                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:border-black focus:bg-white focus:outline-none transition-all duration-200 hover:border-gray-300">
                    @error('time') 
                        <p class="text-xs text-red-500 font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-800">Waktu Selesai</label>
                    <input type="time" wire:model.defer="time_end" 
                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:border-black focus:bg-white focus:outline-none transition-all duration-200 hover:border-gray-300">
                    @error('time_end') 
                        <p class="text-xs text-red-500 font-medium">{{ $message }}</p> 
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-800">Departemen</label>
                    <select wire:model.defer="department"
                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:border-black focus:bg-white focus:outline-none transition-all duration-200 hover:border-gray-300">
                        <option value="" hidden>Pilih departemen</option>
                        <option value="IT">IT</option>
                        <option value="HR">HR</option>
                        <option value="Finance">Finance</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Riset">Riset</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    @error('department') 
                        <p class="text-xs text-red-500 font-medium">{{ $message }}</p> 
                    @enderror
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-800">Jumlah Peserta</label>
                    <input type="number" min="1" wire:model.defer="participant" placeholder="Berapa orang yang ikut"
                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-black focus:bg-white focus:outline-none transition-all duration-200 hover:border-gray-300">
                    @error('participant') 
                        <p class="text-xs text-red-500 font-medium">{{ $message }}</p> 
                    @enderror
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-800">Dengan</label>
                    <input type="text" wire:model.defer="with" placeholder="Nama tim/klien"
                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-black focus:bg-white focus:outline-none transition-all duration-200 hover:border-gray-300">
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-800">Lokasi</label>
                    <select wire:model.defer="location"
                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:border-black focus:bg-white focus:outline-none transition-all duration-200 hover:border-gray-300">
                        <option value="" hidden>Pilih ruangan</option>
                        <option value="Ruangan 1">Ruangan 1</option>
                        <option value="Ruangan 2">Ruangan 2</option>
                        <option value="Ruangan 3">Ruangan 3</option>
                    </select>
                    @error('location') 
                        <p class="text-xs text-red-500 font-medium">{{ $message }}</p> 
                    @enderror
                </div>
                
                <!-- notes -->
                <div class="md:col-span-3 space-y-2">
                    <label class="block text-sm font-semibold text-gray-800">Catatan</label>
                    <textarea wire:model.defer="notes" rows="4" 
                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-black focus:bg-white focus:outline-none transition-all duration-200 hover:border-gray-300 resize-none"
                        placeholder="Agenda singkat / yang perlu dibawa"></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" wire:model.live="q" placeholder="Cari participant, with, location, notes..."
                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl pl-12 pr-4 py-3 text-gray-800 placeholder-gray-400 focus:border-black focus:bg-white focus:outline-none transition-all duration-200 hover:border-gray-300">
                </div>
            </div>
            <select wire:model.live="statusFilter" 
                class="bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:border-black focus:bg-white focus:outline-none transition-all duration-200 hover:border-gray-300 min-w-[150px]">
                <option value="all">Semua Status</option>
                <option value="planned">Planned</option>
                <option value="done">Done</option>
            </select>
        </div>
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-900 to-black text-white px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">Daftar Meeting</h2>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium">Total: {{ count($rows) }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr class="text-left">
                        <th class="py-4 px-6 font-semibold text-gray-800">Tanggal</th>
                        <th class="py-4 px-6 font-semibold text-gray-800">Waktu (Mulai–Selesai)</th>
                        <th class="py-4 px-6 font-semibold text-gray-800">Departemen</th>
                        <th class="py-4 px-6 font-semibold text-gray-800">Peserta</th>
                        <th class="py-4 px-6 font-semibold text-gray-800">Dengan</th>
                        <th class="py-4 px-6 font-semibold text-gray-800">Lokasi</th>
                        <th class="py-4 px-6 font-semibold text-gray-800">Status</th>
                        <th class="py-4 px-6 font-semibold text-gray-800">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($rows as $m)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="py-4 px-6 font-semibold text-gray-900">
                                {{ \Illuminate\Support\Carbon::parse($m['date'])->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-gray-700 font-medium">
                                {{ $m['time'] }}{{ isset($m['time_end']) ? ' – '.$m['time_end'] : '' }}
                            </td>
                            <td class="py-4 px-6 text-gray-700">{{ $m['department'] ?? '-' }}</td>
                            <td class="py-4 px-6 text-gray-900 font-medium">{{ $m['participant'] }}</td>
                            <td class="py-4 px-6 text-gray-700">{{ $m['with'] }}</td>
                            <td class="py-4 px-6 text-gray-700 max-w-xs truncate">{{ $m['location'] }}</td>
                            <td class="py-4 px-6">
                                @if($m['status'] === 'done')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                        Done
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></div>
                                        Planned
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <button wire:click="edit({{ $m['id'] }})"
                                        class="px-3 py-1.5 text-xs rounded-lg border-2 border-gray-200 text-gray-700 hover:border-black hover:bg-gray-50 transition-all duration-200 font-medium">
                                        Edit
                                    </button>

                                    @if($m['status'] === 'planned')
                                        <button wire:click="markDone({{ $m['id'] }})"
                                            class="px-3 py-1.5 text-xs rounded-lg bg-green-600 text-white hover:bg-green-700 transition-all duration-200 font-medium shadow-sm">
                                            Mark Done
                                        </button>
                                    @else
                                        <button wire:click="markPlanned({{ $m['id'] }})"
                                            class="px-3 py-1.5 text-xs rounded-lg bg-yellow-600 text-white hover:bg-yellow-700 transition-all duration-200 font-medium shadow-sm">
                                            Set Planned
                                        </button>
                                    @endif

                                    <button wire:click="destroy({{ $m['id'] }})"
                                        class="px-3 py-1.5 text-xs rounded-lg bg-red-600 text-white hover:bg-red-700 transition-all duration-200 font-medium shadow-sm">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-12 text-center text-gray-500" colspan="8">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium">Belum ada jadwal meeting</p>
                                    <p class="text-sm text-gray-400">Tambah meeting pertama Anda di form di atas</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
