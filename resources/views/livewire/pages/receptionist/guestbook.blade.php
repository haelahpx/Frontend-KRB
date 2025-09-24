<div class="max-w-6xl mx-auto px-4 py-6 space-y-6 text-black">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-black">GuestBook</h1>
            <div class="mt-2 h-[2px] w-16 bg-black rounded"></div>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-5 py-3">
            <h2 class="text-base font-medium text-black">Tambah Entri</h2>
            <p class="text-sm text-black/70">Isi data tamu yang berkunjung hari ini.</p>
        </div>

        <form wire:submit.prevent="save" class="p-5 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-black mb-1">Hari / Tanggal</label>
                    <input type="date" wire:model.live="date"
                           class="w-full rounded-lg border-gray-300 text-black placeholder:text-black/60 focus:border-black focus:ring-black">
                    @error('date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-black mb-1">Jam Masuk</label>
                    <input type="time" wire:model.live="jam_in"
                           class="w-full rounded-lg border-gray-300 text-black placeholder:text-black/60 focus:border-black focus:ring-black">
                    @error('jam_in') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-black mb-1">Jam Keluar</label>
                    <input type="time" wire:model.live="jam_out"
                           class="w-full rounded-lg border-gray-300 text-black placeholder:text-black/60 focus:border-black focus:ring-black">
                    @error('jam_out') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-black mb-1">Nama</label>
                    <input type="text" wire:model.live="name" placeholder="Nama lengkap"
                           class="w-full rounded-lg border-gray-300 text-black placeholder:text-black/60 focus:border-black focus:ring-black">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-black mb-1">No HP</label>
                    <input type="text" wire:model.live="phone_number" placeholder="08xxxxxxxxxx"
                           class="w-full rounded-lg border-gray-300 text-black placeholder:text-black/60 focus:border-black focus:ring-black">
                    @error('phone_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-black mb-1">Nama Instansi</label>
                    <input type="text" wire:model.live="instansi"
                           class="w-full rounded-lg border-gray-300 text-black placeholder:text-black/60 focus:border-black focus:ring-black">
                    @error('instansi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-black mb-1">Keperluan</label>
                    <input type="text" wire:model.live="keperluan"
                           class="w-full rounded-lg border-gray-300 text-black placeholder:text-black/60 focus:border-black focus:ring-black">
                    @error('keperluan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-black mb-1">Nama Petugas Penjaga</label>
                <input type="text" wire:model.live="petugas_penjaga"
                       class="w-full rounded-lg border-gray-300 text-black placeholder:text-black/60 focus:border-black focus:ring-black">
                @error('petugas_penjaga') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="pt-1">
                <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-black text-white text-sm font-medium hover:bg-black/90 focus:outline-none focus:ring-2 focus:ring-black">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    {{-- Snapshot: latest today --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-5 py-3">
            <h2 class="text-base font-medium text-black">Terbaru Hari Ini</h2>
        </div>
        <div class="px-5 py-4 space-y-3">
            @forelse ($todayLatest as $r)
                <div class="flex items-center justify-between text-sm">
                    <div class="min-w-0">
                        <span class="font-medium text-black truncate">{{ $r->nama }}</span>
                        <span class="text-black/70">• {{ $r->instansi ?? '—' }}</span>
                        <span class="text-black/70">• In {{ \Carbon\Carbon::parse($r->jam_in)->format('H:i') }}</span>
                    </div>
                    <span class="text-xs text-black/70">#{{ $r->guestbook_id }}</span>
                </div>
            @empty
                <div class="text-black/70 text-sm">Belum ada entri hari ini.</div>
            @endforelse
        </div>
    </div>

    {{-- Main list (paged) --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-5 py-3">
            <h2 class="text-base font-medium text-black">Daftar Kunjungan</h2>
        </div>

        <div class="px-5 py-4 flex flex-col gap-3 md:flex-row md:items-center">
            <input type="date" wire:model.live="filter_date"
                   class="w-full md:w-48 rounded-lg border-gray-300 text-black placeholder:text-black/60 focus:border-black focus:ring-black">
            <div class="relative flex-1">
                <input type="text" wire:model.live="q" placeholder="Cari nama / no hp / instansi / petugas…"
                       class="w-full rounded-lg border-gray-300 pl-9 text-black placeholder:text-black/60 focus:border-black focus:ring-black">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-black/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/>
                </svg>
            </div>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse ($entries as $e)
                <div class="px-5 py-4 flex flex-col md:flex-row md:items-start md:justify-between gap-3" wire:key="row-{{ $e->guestbook_id }}">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-black truncate">{{ $e->nama }}</span>
                            <span class="text-xs text-black/70">({{ $e->phone_number ?? '—' }})</span>
                        </div>
                        <div class="mt-1 text-sm text-black/80">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md border border-gray-200 bg-gray-50">
                                <span class="text-xs text-black/60">Instansi</span>
                                <span class="font-medium text-black">{{ $e->instansi ?? '—' }}</span>
                            </span>
                            <span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-md border border-gray-200 bg-gray-50">
                                <span class="text-xs text-black/60">Keperluan</span>
                                <span class="font-medium text-black">{{ $e->keperluan ?? '—' }}</span>
                            </span>
                        </div>
                        <div class="mt-1 text-xs text-black/70">
                            {{ $e->date?->format('d M Y') ?? \Carbon\Carbon::parse($e->date)->format('d M Y') }} •
                            In: {{ \Carbon\Carbon::parse($e->jam_in)->format('H:i') }}
                            @if($e->jam_out) • Out: {{ \Carbon\Carbon::parse($e->jam_out)->format('H:i') }} @endif
                            • Petugas: <span class="font-medium text-black">{{ $e->petugas_penjaga }}</span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-xs text-black/70">#{{ $e->guestbook_id }}</div>
                        <div class="mt-1 text-xs text-black/70">{{ $e->created_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-black/70">Belum ada entri.</div>
            @endforelse
        </div>

        <div class="px-5 py-4">
            <div class="flex justify-end">
                {{ $entries->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</div>
