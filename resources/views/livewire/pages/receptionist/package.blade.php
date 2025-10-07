<div class="min-h-screen bg-gray-50" wire:poll.1000ms.keep-alive>
    @php
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $head = 'bg-gradient-to-r from-gray-900 to-black';
        $hpad = 'px-6 py-5';
        $tag = 'w-1.5 bg-white rounded-full';
        $label = 'block text-sm font-medium text-gray-700 mb-2';
        $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
        $btnGrn = 'px-3 py-2 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition';
        $btnAmb = 'px-3 py-2 text-xs font-medium rounded-lg bg-amber-600 text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-600/20 disabled:opacity-60 transition';
        $btnRed = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
        $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
        $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
        $icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
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
                    <div
                        class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v8m-4-4h8M4 6h16v12H4z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Packages</h2>
                        <p class="text-sm text-white/80">Kelola paket masuk (stored) & pengambilan (taken)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="{{ $card }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Tambah Package</h3>
                        <p class="text-sm text-gray-500">Lengkapi data paket baru</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="save" class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="{{ $label }}">Nama Paket</label>
                        <input type="text" wire:model.defer="form.package_name" class="{{ $input }}"
                            placeholder="Contoh: Paket Dokumen PT ABC">
                        @error('form.package_name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Penyimpanan</label>
                        <select wire:model.defer="form.penyimpanan" class="{{ $input }}">
                            <option value="">-</option>
                            <option value="rak1">Rak 1</option>
                            <option value="rak2">Rak 2</option>
                            <option value="rak3">Rak 3</option>
                        </select>
                        @error('form.penyimpanan') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="{{ $label }}">Expedition / Sender</label>
                        <input type="text" wire:model.defer="form.nama_pengirim" class="{{ $input }}"
                            placeholder="Kurir / Pengirim">
                        @error('form.nama_pengirim') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Owner (Penerima)</label>
                        <input type="text" wire:model.defer="form.nama_penerima" class="{{ $input }}"
                            placeholder="Nama penerima">
                        @error('form.nama_penerima') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-2 flex items-center gap-3">
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-gray-900 text-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition active:scale-95 hover:bg-black disabled:opacity-60 relative overflow-hidden">
                        <span class="flex items-center gap-2" wire:loading.remove wire:target="save">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Data
                        </span>
                        <span class="flex items-center gap-2" wire:loading wire:target="save">
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
            </form>
        </div>

        <div class="{{ $card }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">On-going Packages</h3>
                        <p class="text-sm text-gray-500">Menampilkan semua paket berstatus <b>stored</b></p>
                    </div>
                </div>
            </div>

            <div class="p-5 space-y-3">
                @forelse ($ongoing as $r)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-200"
                        wire:key="ongoing-{{ $r->package_id }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="w-9 h-9 bg-gray-900 rounded-lg flex items-center justify-center text-white text-xs font-semibold">
                                {{ strtoupper(substr($r->package_name, 0, 1)) }}
                            </div>
                            <div class="leading-tight min-w-0">
                                <div class="font-medium text-gray-800 text-sm truncate">{{ $r->package_name }}</div>
                                <div class="text-[11px] text-gray-500">
                                    Stored {{ optional($r->created_at)->format('d M Y H:i') ?? '—' }}
                                    • Container {{ $r->penyimpanan ?? '—' }}
                                    • Recep. {{ $r->receptionist->full_name ?? '—' }}
                                    • Sender {{ $r->nama_pengirim ?? '—' }}
                                    • Owner {{ $r->nama_penerima ?? '—' }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button wire:click="openEdit({{ $r->package_id }})" wire:loading.attr="disabled"
                                wire:target="openEdit({{ $r->package_id }})" class="{{ $btnBlk }}">
                                <span wire:loading.remove wire:target="openEdit({{ $r->package_id }})">Edit</span>
                                <span wire:loading wire:target="openEdit({{ $r->package_id }})">Memuat…</span>
                            </button>

                            <button wire:click="markDone({{ $r->package_id }})" wire:loading.attr="disabled"
                                wire:target="markDone({{ $r->package_id }})" class="{{ $btnGrn }}">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" wire:loading
                                        wire:target="markDone({{ $r->package_id }})">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                                    </svg>
                                    <span>Done</span>
                                </span>
                            </button>

                            <button wire:click="delete({{ $r->package_id }})" onclick="return confirm('Hapus paket ini?')"
                                wire:loading.attr="disabled" wire:target="delete({{ $r->package_id }})"
                                class="{{ $btnRed }}">
                                <span wire:loading.remove wire:target="delete({{ $r->package_id }})">Hapus</span>
                                <span wire:loading wire:target="delete({{ $r->package_id }})">Menghapus…</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 text-sm">Tidak ada paket on-going.</div>
                @endforelse
            </div>

            <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-center">
                    {{ $ongoing->onEachSide(1)->links() }}
                </div>
            </div>
        </div>

        <div class="{{ $card }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-emerald-600 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Completed Packages</h3>
                        <p class="text-sm text-gray-500">Paket berstatus <b>taken</b></p>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse ($done as $e)
                    <div class="px-6 py-5 hover:bg-gray-50 transition-colors" wire:key="done-{{ $e->package_id }}">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="{{ $icoAvatar }}">{{ strtoupper(substr($e->package_name, 0, 1)) }}</div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <h4 class="font-semibold text-gray-900 text-base truncate">{{ $e->package_name }}
                                        </h4>
                                        @if ($e->nama_pengirim)
                                            <span class="text-[11px] text-gray-600 bg-gray-100 px-2 py-0.5 rounded-md">Dari:
                                                {{ $e->nama_pengirim }}</span>
                                        @endif
                                        @if ($e->nama_penerima)
                                            <span class="text-[11px] text-gray-600 bg-gray-100 px-2 py-0.5 rounded-md">Ke:
                                                {{ $e->nama_penerima }}</span>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        <span class="{{ $chip }}">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="font-medium text-gray-700">{{ $e->penyimpanan ?? '—' }}</span>
                                        </span>
                                        <span class="{{ $chip }}">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span
                                                class="font-medium text-gray-700">{{ $e->receptionist->full_name ?? '—' }}</span>
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-4 text-[13px] text-gray-600">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Stored: {{ optional($e->created_at)->format('d M Y H:i') ?? '—' }}
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            Taken: {{ optional($e->pengambilan)->format('d M Y H:i') ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right shrink-0 space-y-2">
                                <div class="{{ $mono }}">#{{ $e->package_id }}</div>
                                <div class="text:[11px] text-gray-500">{{ optional($e->created_at)->format('d M Y H:i') }}
                                </div>
                                <div class="flex flex-wrap gap-2 justify-end pt-1.5">
                                    <button wire:click="openEdit({{ $e->package_id }})" wire:loading.attr="disabled"
                                        wire:target="openEdit({{ $e->package_id }})" class="{{ $btnBlk }}">
                                        <span wire:loading.remove wire:target="openEdit({{ $e->package_id }})">Edit</span>
                                        <span wire:loading wire:target="openEdit({{ $e->package_id }})">Memuat…</span>
                                    </button>

                                    <button wire:click="markStored({{ $e->package_id }})" wire:loading.attr="disabled"
                                        wire:target="markStored({{ $e->package_id }})" class="{{ $btnAmb }}">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" wire:loading
                                                wire:target="markStored({{ $e->package_id }})">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4" />
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                                            </svg>
                                            <span>Move to On-going</span>
                                        </span>
                                    </button>

                                    <button wire:click="delete({{ $e->package_id }})"
                                        onclick="return confirm('Hapus paket ini?')" wire:loading.attr="disabled"
                                        wire:target="delete({{ $e->package_id }})" class="{{ $btnRed }}">
                                        <span wire:loading.remove wire:target="delete({{ $e->package_id }})">Hapus</span>
                                        <span wire:loading wire:target="delete({{ $e->package_id }})">Menghapus…</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center text-gray-500 text-sm">Tidak ada paket selesai.</div>
                @endforelse
            </div>

            <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-center">
                    {{ $done->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-all duration-300" wire:click="closeModal">
            </div>
            <div
                class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden transform transition-all duration-300 scale-100 max-h-[90vh] flex flex-col">
                <div class="bg-gradient-to-r from-gray-900 to-black p-5 text-white relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 pointer-events-none">
                        <div class="absolute top-0 -right-6 w-24 h-24 bg-white rounded-full blur-2xl"></div>
                        <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-xl"></div>
                    </div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold tracking-tight">{{ $editId ? 'Edit Package' : 'Add Package' }}
                            </h3>
                            <div class="mt-1 text-[11px] text-gray-200">
                                Created At: <span class="font-mono">{{ $createdAtDisplay }}</span>
                            </div>
                        </div>
                        <button
                            class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 flex items-center justify-center transition-all duration-200"
                            wire:click="closeModal">
                            <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto flex-1">
                    <div>
                        <label class="{{ $label }}">Package Name</label>
                        <input type="text" wire:model.defer="form.package_name" class="{{ $editIn }}"
                            placeholder="Nama paket">
                        @error('form.package_name') <p class="text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $label }}">Expedition / Sender</label>
                            <input type="text" wire:model.defer="form.nama_pengirim" class="{{ $editIn }}"
                                placeholder="Kurir / Pengirim">
                            @error('form.nama_pengirim') <p class="text-[11px] text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Owner (Penerima)</label>
                            <input type="text" wire:model.defer="form.nama_penerima" class="{{ $editIn }}"
                                placeholder="Nama penerima">
                            @error('form.nama_penerima') <p class="text-[11px] text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="{{ $label }}">Container</label>
                        <select wire:model.defer="form.penyimpanan" class="{{ $editIn }}">
                            <option value="">-</option>
                            <option value="rak1">Rak 1</option>
                            <option value="rak2">Rak 2</option>
                            <option value="rak3">Rak 3</option>
                        </select>
                        @error('form.penyimpanan') <p class="text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($editId)
                        <div>
                            <label class="{{ $label }}">Taken At</label>
                            <input type="datetime-local" wire:model.defer="form.pengambilan" class="{{ $editIn }}">
                            @error('form.pengambilan') <p class="text-[11px] text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-gray-500 mt-1">
                                Isi untuk menandai <b>taken</b>, kosongkan untuk kembali <b>stored</b>.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50 border-t border-gray-200 p-5">
                    <div class="flex items-center justify-end gap-2.5">
                        <button type="button" wire:click="closeModal"
                            class="px-4 h-10 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 hover:border-gray-400 transition">
                            Batal
                        </button>
                        <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save"
                            class="px-4 h-10 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition shadow-sm">
                            <span wire:loading.remove wire:target="save" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2">
                                <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                </svg>
                                Menyimpan…
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div x-data="{
            toasts: [],
            addToast(t) {
                t.id = crypto.randomUUID ? crypto.randomUUID() : Date.now() + Math.random();
                t.type = t.type || 'info';
                t.message = t.message || '';
                t.title = t.title || '';
                t.duration = Number(t.duration ?? 3500);
                this.toasts.push(t);
                if (t.duration > 0) {
                    setTimeout(() => this.removeToast(t.id), t.duration);
                }
            },
            removeToast(id) {
                this.toasts = this.toasts.filter(tt => tt.id !== id);
            },
            getToastClasses(type) {
                const base =
                'relative overflow-hidden rounded-xl p-4 border border-black/15 bg-white/95 text-black shadow-[0_10px_30px_-12px_rgba(0,0,0,0.35)] backdrop-blur-sm transition-all duration-500 ease-out';
                const variants = {
                    success: 'border-black/20',
                    error:   'border-black/25',
                    warning: 'border-black/20',
                    info:    'border-black/15',
                    neutral: 'border-black/15'
                };
                return base + ' ' + (variants[type] || variants.info);
            },
            getAccentClasses(type) {
                const variants = {
                    success: 'bg-gradient-to-r from-black/90 to-black/70',
                    error:   'bg-gradient-to-r from-black/90 to-black/70',
                    warning: 'bg-gradient-to-r from-black/90 to-black/70',
                    info:    'bg-gradient-to-r from-black/90 to-black/70',
                    neutral: 'bg-gradient-to-r from-black/90 to-black/70'
                };
                return variants[type] || variants.info;
            },
            getIconClasses(type) {
                const base =
                'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg';
                const variants = {
                    success: 'bg-black text-white',
                    error:   'bg-black text-white',
                    warning: 'bg-black text-white',
                    info:    'bg-black text-white',
                    neutral: 'bg-black text-white'
                };
                return base + ' ' + (variants[type] || variants.info);
            },
            getIcon(type) {
                const icons = { success: '✓', error: '✕', warning: '⚠', info: 'ⓘ', neutral: '•' };
                return icons[type] || icons.info;
            }
        }" x-on:toast.window="addToast($event.detail)"
        class="fixed top-6 right-6 z-50 flex flex-col gap-4 w-[calc(100vw-3rem)] max-w-sm pointer-events-none"
        aria-live="polite">

        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-x-full opacity-0 scale-95"
                x-transition:enter-end="translate-x-0 opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-x-0 opacity-100 scale-100"
                x-transition:leave-end="translate-x-full opacity-0 scale-95" class="pointer-events-auto"
                :class="getToastClasses(toast.type)">
                <div class="absolute top-0 left-0 right-0 h-1" :class="getAccentClasses(toast.type)"></div>
                <div class="absolute top-0 left-0 right-0 h-1 opacity-50 animate-pulse"
                    :class="getAccentClasses(toast.type)"></div>
                <div class="flex items-start gap-4">
                    <div :class="getIconClasses(toast.type)">
                        <span x-text="getIcon(toast.type)"></span>
                    </div>
                    <div class="flex-1 min-w-0 pt-1">
                        <h4 x-show="toast.title" x-text="toast.title"
                            class="font-semibold text-base mb-1 leading-tight tracking-tight"></h4>
                        <p x-show="toast.message" x-text="toast.message" class="text-sm leading-relaxed text-black/70">
                        </p>
                    </div>
                    <button @click="removeToast(toast.id)"
                        class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-black/60 hover:text-black hover:bg-black/5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-black/20"
                        aria-label="Close notification">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>