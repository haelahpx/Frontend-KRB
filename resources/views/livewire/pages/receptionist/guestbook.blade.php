<div class="min-h-screen bg-gray-50" wire:poll.1000ms>
    @php
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $head = 'bg-gradient-to-r from-gray-900 to-black';
        $hpad = 'px-6 py-5';
        $tag = 'w-1.5 bg-white rounded-full';
        $label = 'block text-sm font-medium text-gray-700 mb-2';
        $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
        $btnGrn = 'px-3 py-2 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition';
        $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
        $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
        $icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
        $icoDot = 'h-6';
        $sectPad = 'px-6 py-5';
        $editIn = 'w-full h-10 bg-white border border-gray-300 rounded-lg px-3 text-gray-800 focus:border-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gray-900/10 transition hover:border-gray-400 placeholder:text-gray-400';
    @endphp

    <div class="px-4 sm:px-6 py-6 space-y-8">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Buku Tamu Digital</h2>
                        <p class="text-sm text-white/80">Kelola kunjungan dan entri tamu hari ini</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="{{ $card }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Tambah Entri Baru</h3>
                        <p class="text-sm text-gray-500">Lengkapi data kunjungan hari ini</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="save" class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="{{ $label }}">Hari / Tanggal</label>
                        <input type="date" wire:model.defer="date" class="{{ $input }}">
                        @error('date') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Jam Masuk</label>
                        <input type="time" wire:model.defer="jam_in" class="{{ $input }}">
                        @error('jam_in') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="{{ $label }}">Nama Lengkap</label>
                        <input type="text" wire:model.defer="name" placeholder="Masukkan nama lengkap" class="{{ $input }}">
                        @error('name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Nomor HP</label>
                        <input type="text" wire:model.defer="phone_number" placeholder="08xxxxxxxxxx" class="{{ $input }}">
                        @error('phone_number') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="{{ $label }}">Nama Instansi</label>
                        <input type="text" wire:model.defer="instansi" placeholder="Nama instansi/perusahaan" class="{{ $input }}">
                        @error('instansi') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Keperluan</label>
                        <input type="text" wire:model.defer="keperluan" placeholder="Tujuan kunjungan" class="{{ $input }}">
                        @error('keperluan') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="{{ $label }}">Nama Petugas Penjaga</label>
                    <input type="text" wire:model.defer="petugas_penjaga" placeholder="Nama petugas yang bertugas" class="{{ $input }}">
                    @error('petugas_penjaga') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2 flex items-center gap-3">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        aria-busy="true"
                        class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-gray-900 text-white text-sm font-medium
                            shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900/20
                            transition active:scale-95 hover:bg-black disabled:opacity-60 relative overflow-hidden">
                        <span class="flex items-center gap-2" wire:loading.remove wire:target="save">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Data
                        </span>

                        <span class="flex items-center gap-2" wire:loading wire:target="save">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z"/>
                            </svg>
                            Menyimpan…
                        </span>
                    </button>

                    @if (session('saved'))
                        <span class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg bg-emerald-100 text-emerald-700 text-xs font-medium">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Tersimpan!
                        </span>
                    @endif
                </div>
            </form>
        </div>

        <div class="{{ $card }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-emerald-600 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Kunjungan Terbaru (Belum Keluar)</h3>
                        <p class="text-sm text-gray-500">Entri yang masih aktif hari ini</p>
                    </div>
                </div>
            </div>
            <div class="p-5 space-y-3">
                @forelse ($todayLatest as $r)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-200" wire:key="latest-{{ $r->guestbook_id }}">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-gray-900 rounded-lg flex items-center justify-center text-white text-xs font-semibold">
                                {{ substr($r->name, 0, 1) }}
                            </div>
                            <div class="leading-tight">
                                <div class="font-medium text-gray-800 text-sm">{{ $r->name }}</div>
                                <div class="text-[11px] text-gray-500">{{ $r->instansi ?? '—' }}</div>
                                <div class="text-[11px] text-gray-500">Masuk {{ \Carbon\Carbon::parse($r->jam_in)->format('H:i') }}</div>
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
                    <div class="text-center py-8 text-gray-500 text-sm">Belum ada kunjungan aktif hari ini</div>
                @endforelse
            </div>
        </div>

        <div class="{{ $card }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Riwayat Kunjungan</h3>
                        <p class="text-sm text-gray-500">Hanya entri yang sudah keluar</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <div class="relative">
                        <input type="date" wire:model.live="filter_date" class="w-full lg:w-56 {{ $input }} pl-9">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="relative flex-1">
                        <input type="text" wire:model.live="q" placeholder="Cari nama, no hp, instansi, atau petugas..." class="{{ $input }} pl-9 w-full">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse ($entries as $e)
                    @php
                        $rowNo = ($entries->firstItem() ?? 1) + $loop->index;
                    @endphp
                    <div class="px-6 py-5 hover:bg-gray-50 transition-colors" wire:key="entry-{{ $e->guestbook_id }}">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="{{ $icoAvatar }}">{{ substr($e->name, 0, 1) }}</div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <h4 class="font-semibold text-gray-900 text-base">{{ $e->name }}</h4>
                                        @if ($e->phone_number)
                                            <span class="text-[11px] text-gray-600 bg-gray-100 px-2 py-0.5 rounded-md">{{ $e->phone_number }}</span>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        <span class="{{ $chip }}">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="font-medium text-gray-700">{{ $e->instansi ?? '—' }}</span>
                                        </span>
                                        <span class="{{ $chip }}">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <span class="font-medium text-gray-700">{{ $e->keperluan ?? '—' }}</span>
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-4 text-[13px] text-gray-600">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $e->date?->format('d M Y') ?? \Carbon\Carbon::parse($e->date)->format('d M Y') }}
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 003-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($e->jam_in)->format('H:i') }}
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 003-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($e->jam_out)->format('H:i') }}
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span class="font-medium text-gray-700">{{ $e->petugas_penjaga }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0 space-y-2">
                                <div class="{{ $mono }}">No. {{ $rowNo }}</div>
                                <div class="text-[11px] text-gray-500">{{ $e->created_at->format('d M Y H:i') }}</div>
                                <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                    <button wire:click="openEdit({{ $e->guestbook_id }})" wire:loading.attr="disabled" wire:target="openEdit({{ $e->guestbook_id }})" class="{{ $btnBlk }}">
                                        <span wire:loading.remove wire:target="openEdit({{ $e->guestbook_id }})">Edit</span>
                                        <span wire:loading wire:target="openEdit({{ $e->guestbook_id }})">Memuat…</span>
                                    </button>
                                    <button wire:click="delete({{ $e->guestbook_id }})" onclick="return confirm('Hapus entri ini?')" wire:loading.attr="disabled" wire:target="delete({{ $e->guestbook_id }})" class="px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition">
                                        <span wire:loading.remove wire:target="delete({{ $e->guestbook_id }})">Hapus</span>
                                        <span wire:loading wire:target="delete({{ $e->guestbook_id }})">Menghapus…</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center text-gray-500 text-sm">Tidak ada entri kunjungan yang ditemukan</div>
                @endforelse
            </div>

            <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-center">
                    {{ $entries->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>

    @if ($showEdit)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:poll.1000ms>
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-all duration-300" wire:click="closeEdit"></div>
            <div class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden transform transition-all duration-300 scale-100 max-h-[90vh] flex flex-col">
                <div class="bg-gradient-to-r from-gray-900 to-black p-5 text-white relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 pointer-events-none">
                        <div class="absolute top-0 -right-6 w-24 h-24 bg-white rounded-full blur-2xl"></div>
                        <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-xl"></div>
                    </div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold tracking-tight">Edit Entri</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                <p class="text-[11px] text-gray-200 font-mono">{{ $this->serverClock }}</p>
                            </div>
                        </div>
                        <button class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 flex items-center justify-center transition-all duration-200" wire:click="closeEdit">
                            <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto flex-1">
                    <div class="space-y-1.5">
                        <label class="{{ $label }}">Tanggal</label>
                        <div class="relative">
                            <input type="date" wire:model="edit.date" class="{{ $editIn }}">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        @error('edit.date') <p class="text-[11px] text-red-600 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label class="{{ $label }}">Jam Masuk</label>
                            <input type="time" wire:model="edit.jam_in" class="{{ $editIn }}">
                            @error('edit.jam_in') <p class="text-[11px] text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="{{ $label }}">Jam Keluar</label>
                            <input type="time" wire:model="edit.jam_out" class="{{ $editIn }}">
                            @error('edit.jam_out') <p class="text-[11px] text-red-600 font-medium">{{ $message }}</p> @enderror
                            <div class="bg-gray-50 rounded-md p-3 border border-gray-200">
                                <p class="text-[11px] text-gray-600 leading-relaxed">
                                    💡 <span class="font-medium">Tips:</span> Klik <span class="font-semibold text-gray-900">Keluar sekarang</span> di "Kunjungan Terbaru" untuk menggunakan waktu real-time.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label class="{{ $label }}">Nama</label>
                            <input type="text" wire:model="edit.name" placeholder="Masukkan nama lengkap" class="{{ $editIn }}">
                            @error('edit.name') <p class="text-[11px] text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="{{ $label }}">No HP</label>
                            <input type="text" wire:model="edit.phone_number" placeholder="08xxxxxxxxxx" class="{{ $editIn }}">
                        </div>
                    </div>

                    <div class="space-y-3.5">
                        <div class="space-y-1.5">
                            <label class="{{ $label }}">Instansi</label>
                            <input type="text" wire:model="edit.instansi" placeholder="Nama perusahaan/instansi" class="{{ $editIn }}">
                        </div>
                        <div class="space-y-1.5">
                            <label class="{{ $label }}">Keperluan</label>
                            <input type="text" wire:model="edit.keperluan" placeholder="Tujuan kunjungan" class="{{ $editIn }}">
                        </div>
                        <div class="space-y-1.5">
                            <label class="{{ $label }}">Petugas Penjaga</label>
                            <input type="text" wire:model="edit.petugas_penjaga" placeholder="Nama petugas yang bertugas" class="{{ $editIn }}">
                            @error('edit.petugas_penjaga') <p class="text-[11px] text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border-t border-gray-200 p-5">
                    <div class="flex items-center justify-end gap-2.5">
                        <button type="button" wire:click="closeEdit" class="px-4 h-10 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 hover:border-gray-400 transition">Batal</button>
                        <button type="button" wire:click="saveEdit" wire:loading.attr="disabled" wire:target="saveEdit" class="px-4 h-10 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition shadow-sm">
                            <span wire:loading.remove wire:target="saveEdit" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Perubahan
                            </span>
                            <span wire:loading wire:target="saveEdit" class="flex items-center gap-2">
                                <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Menyimpan…
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
