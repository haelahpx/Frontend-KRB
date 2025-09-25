<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    @php
        $card='bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden';
        $head='bg-gradient-to-r from-black to-gray-800';
        $hpad='px-8 py-6';
        $tag='w-2 bg-white rounded-full';
        $label='block text-sm font-semibold text-gray-700 mb-2';
        $input='w-full px-4 py-3 rounded-xl border-2 border-gray-200 text-gray-700 focus:border-black focus:ring-4 focus:ring-black/10 bg-gray-50 focus:bg-white';
        $btnBlk='px-3 py-1.5 text-sm rounded-lg bg-black text-white hover:bg-gray-800 disabled:opacity-60';
        $btnGrn='px-3 py-1.5 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-60';
        $chip='inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-100 text-sm';
        $mono='text-xs font-mono text-gray-400 bg-gray-100 px-3 py-1 rounded-lg';
        $icoAvatar='w-12 h-12 bg-black rounded-2xl flex items-center justify-center text-white font-bold text-lg shrink-0';
        $icoDot='w-2 h-8';
        $sectPad='px-8 py-6';
        $editIn='w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:border-black focus:bg-white focus:outline-none transition-all duration-200 hover:border-gray-300';
    @endphp

    <div class="space-y-8">
        <!-- FORM -->
        <div class="{{ $card }}">
            <div class="{{ $head }} {{ $hpad }}">
                <div class="flex items-center gap-3">
                    <div class="{{ $tag }} {{ $icoDot }}"></div>
                    <div>
                        <h2 class="text-xl font-semibold text-white">Tambah Entri Baru</h2>
                        <p class="text-gray-300 text-sm">Lengkapi data kunjungan hari ini</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="save" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="{{ $label }}">📅 Hari / Tanggal</label>
                        <input type="date" wire:model.defer="date" class="{{ $input }}">
                        @error('date') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">🕐 Jam Masuk</label>
                        <input type="time" wire:model.defer="jam_in" class="{{ $input }}">
                        @error('jam_in') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="{{ $label }}">👤 Nama Lengkap</label>
                        <input type="text" wire:model.defer="name" placeholder="Masukkan nama lengkap" class="{{ $input }}">
                        @error('name') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">📱 Nomor HP</label>
                        <input type="text" wire:model.defer="phone_number" placeholder="08xxxxxxxxxx" class="{{ $input }}">
                        @error('phone_number') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="{{ $label }}">🏢 Nama Instansi</label>
                        <input type="text" wire:model.defer="instansi" placeholder="Nama instansi/perusahaan" class="{{ $input }}">
                        @error('instansi') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">🎯 Keperluan</label>
                        <input type="text" wire:model.defer="keperluan" placeholder="Tujuan kunjungan" class="{{ $input }}">
                        @error('keperluan') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="{{ $label }}">👮 Nama Petugas Penjaga</label>
                    <input type="text" wire:model.defer="petugas_penjaga" placeholder="Nama petugas yang bertugas" class="{{ $input }}">
                    @error('petugas_penjaga') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="inline-flex items-center gap-3 px-8 py-4 rounded-xl bg-black text-white font-semibold hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-black/20 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
        
        <div class="{{ $card }}">
            <div class="bg-gradient-to-r from-gray-800 to-black px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="{{ $tag }} h-6"></div>
                    <h3 class="text-lg font-semibold text-white">Kunjungan Terbaru (Belum Keluar)</h3>
                </div>
            </div>

            <div class="p-6 space-y-4">
                @forelse ($todayLatest as $r)
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-gray-50 border border-gray-100" wire:key="latest-{{ $r->guestbook_id }}">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-black rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                {{ substr($r->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">{{ $r->name }}</div>
                                <div class="text-sm text-gray-600">{{ $r->instansi ?? '—' }}</div>
                                <div class="text-xs text-gray-500">Masuk {{ \Carbon\Carbon::parse($r->jam_in)->format('H:i') }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button wire:click="openEdit({{ $r->guestbook_id }})" wire:loading.attr="disabled" wire:target="openEdit({{ $r->guestbook_id }})" class="{{ $btnBlk }}">
                                <span wire:loading.remove wire:target="openEdit({{ $r->guestbook_id }})">Edit</span>
                                <span wire:loading wire:target="openEdit({{ $r->guestbook_id }})">Memuat…</span>
                            </button>

                            <button wire:click="setJamKeluarNow({{ $r->guestbook_id }})" wire:loading.attr="disabled" wire:target="setJamKeluarNow({{ $r->guestbook_id }})" class="{{ $btnGrn }}">
                                <span wire:loading.remove wire:target="setJamKeluarNow({{ $r->guestbook_id }})">Keluar sekarang</span>
                                <span wire:loading wire:target="setJamKeluarNow({{ $r->guestbook_id }})">Menyimpan…</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">Belum ada kunjungan aktif hari ini</div>
                @endforelse
            </div>
        </div>

        <div class="{{ $card }}">
            <div class="{{ $head }} {{ $hpad }}">
                <div class="flex items-center gap-3">
                    <div class="{{ $tag }} {{ $icoDot }}"></div>
                    <div>
                        <h2 class="text-xl font-semibold text-white">Riwayat Kunjungan</h2>
                        <p class="text-gray-300 text-sm">Hanya entri yang sudah keluar</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-6 bg-gray-50 border-b border-gray-100">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                    <div class="relative">
                        <input type="date" wire:model.live="filter_date" class="w-full lg:w-56 {{ $input }} pl-12">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="relative flex-1">
                        <input type="text" wire:model.live="q" placeholder="Cari nama, no hp, instansi, atau petugas..." class="{{ $input }} pl-12 w-full placeholder:text-gray-400">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/></svg>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($entries as $e)
                    <div class="px-8 py-6 hover:bg-gray-50 transition-colors" wire:key="entry-{{ $e->guestbook_id }}">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-4 flex-1">
                                <div class="{{ $icoAvatar }}">{{ substr($e->name, 0, 1) }}</div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="font-semibold text-gray-800 text-lg">{{ $e->name }}</h4>
                                        @if ($e->phone_number)
                                            <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">{{ $e->phone_number }}</span>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span class="{{ $chip }}">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <span class="font-medium text-gray-700">{{ $e->instansi ?? '—' }}</span>
                                        </span>
                                        <span class="{{ $chip }}">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span class="font-medium text-gray-700">{{ $e->keperluan ?? '—' }}</span>
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $e->date?->format('d M Y') ?? \Carbon\Carbon::parse($e->date)->format('d M Y') }}
                                        </span>
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 003-3h7a3 3 0 013 3v1"/></svg>
                                            {{ \Carbon\Carbon::parse($e->jam_in)->format('H:i') }}
                                        </span>
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 003-3h4a3 3 0 013 3v1"/></svg>
                                            {{ \Carbon\Carbon::parse($e->jam_out)->format('H:i') }}
                                        </span>
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span class="font-medium text-gray-700">{{ $e->petugas_penjaga }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right shrink-0 space-y-2">
                                <div class="{{ $mono }}">#{{ $e->guestbook_id }}</div>
                                <div class="text-xs text-gray-500">{{ $e->created_at->format('d M Y H:i') }}</div>

                                <div class="flex flex-wrap gap-2 justify-end pt-2">
                                    <button wire:click="openEdit({{ $e->guestbook_id }})" wire:loading.attr="disabled" wire:target="openEdit({{ $e->guestbook_id }})" class="{{ $btnBlk }}">
                                        <span wire:loading.remove wire:target="openEdit({{ $e->guestbook_id }})">Edit</span>
                                        <span wire:loading wire:target="openEdit({{ $e->guestbook_id }})">Memuat…</span>
                                    </button>
                                    <button wire:click="delete({{ $e->guestbook_id }})" onclick="return confirm('Hapus entri ini?')" wire:loading.attr="disabled" wire:target="delete({{ $e->guestbook_id }})" class="px-3 py-1.5 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700 disabled:opacity-60">
                                        <span wire:loading.remove wire:target="delete({{ $e->guestbook_id }})">Hapus</span>
                                        <span wire:loading wire:target="delete({{ $e->guestbook_id }})">Menghapus…</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-8 py-16 text-center text-gray-500">Tidak ada entri kunjungan yang ditemukan</div>
                @endforelse
            </div>

            <div class="px-8 py-6 bg-gray-50 border-t border-gray-100">
                <div class="flex justify-center">
                    {{ $entries->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>

    @if ($showEdit)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:poll.1000ms>
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-all duration-300" wire:click="closeEdit"></div>

        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all duration-300 scale-100 max-h-[90vh] flex flex-col">
            <div class="bg-gradient-to-r from-gray-900 to-black text-white p-6 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                    <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
                </div>

                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold tracking-tight">Edit Entri</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                            <p class="text-sm text-gray-200 font-mono">{{ $this->serverClock }}</p>
                        </div>
                    </div>

                    <button class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 flex items-center justify-center transition-all duration-200 hover:scale-105 active:scale-95" wire:click="closeEdit">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-5 overflow-y-auto flex-1">
                <div class="space-y-2">
                    <label class="{{ $label }}">Tanggal</label>
                    <div class="relative">
                        <input type="date" wire:model="edit.date" class="{{ $editIn }}">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    @error('edit.date') <p class="text-xs text-red-500 font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="{{ $label }}">Jam Masuk</label>
                        <input type="time" wire:model="edit.jam_in" class="{{ $editIn }}">
                        @error('edit.jam_in') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="{{ $label }}">Jam Keluar</label>
                        <input type="time" wire:model="edit.jam_out" class="{{ $editIn }}">
                        @error('edit.jam_out') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror

                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-xs text-gray-600 leading-relaxed">
                                💡 <span class="font-medium">Tips:</span> Klik <span class="font-semibold text-black">Keluar sekarang</span> di "Kunjungan Terbaru" untuk menggunakan waktu real-time.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="{{ $label }}">Nama</label>
                        <input type="text" wire:model="edit.name" placeholder="Masukkan nama lengkap" class="{{ $editIn }} placeholder-gray-400">
                        @error('edit.name') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="{{ $label }}">No HP</label>
                        <input type="text" wire:model="edit.phone_number" placeholder="08xxxxxxxxxx" class="{{ $editIn }} placeholder-gray-400">
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="space-y-2">
                        <label class="{{ $label }}">Instansi</label>
                        <input type="text" wire:model="edit.instansi" placeholder="Nama perusahaan/instansi" class="{{ $editIn }} placeholder-gray-400">
                    </div>
                    <div class="space-y-2">
                        <label class="{{ $label }}">Keperluan</label>
                        <input type="text" wire:model="edit.keperluan" placeholder="Tujuan kunjungan" class="{{ $editIn }} placeholder-gray-400">
                    </div>
                    <div class="space-y-2">
                        <label class="{{ $label }}">Petugas Penjaga</label>
                        <input type="text" wire:model="edit.petugas_penjaga" placeholder="Nama petugas yang bertugas" class="{{ $editIn }} placeholder-gray-400">
                        @error('edit.petugas_penjaga') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 border-t border-gray-200 p-6">
                <div class="flex items-center justify-end gap-3">
                    <button type="button" wire:click="closeEdit" class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-100 hover:border-gray-400 transition-all duration-200 active:scale-95">Batal</button>
                    <button type="button" wire:click="saveEdit" wire:loading.attr="disabled" wire:target="saveEdit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-gray-900 to-black text-white font-semibold hover:from-black hover:to-gray-800 disabled:opacity-60 disabled:cursor-not-allowed transition-all duration-200 active:scale-95 shadow-lg hover:shadow-xl">
                        <span wire:loading.remove wire:target="saveEdit" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Perubahan
                        </span>
                        <span wire:loading wire:target="saveEdit" class="flex items-center gap-2">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            Menyimpan…
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
