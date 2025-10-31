<div class="min-h-screen bg-gray-50" wire:poll.1000ms>
    @php
        use Carbon\Carbon;

        if (!function_exists('fmtDate')) {
            function fmtDate($v){ try{ return $v ? Carbon::parse($v)->format('d M Y') : '—'; }catch(\Throwable){ return '—'; } }
        }
        if (!function_exists('fmtTime')) {
            function fmtTime($v){ try{ return $v ? Carbon::parse($v)->format('H:i') : '—'; }catch(\Throwable){ return (is_string($v) && preg_match('/^\d{2}:\d{2}/',$v)) ? substr($v,0,5) : '—'; } }
        }

        // Theme tokens (mirror GuestbookHistory)
        $card   = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $label  = 'block text-sm font-medium text-gray-700 mb-2';
        $input  = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
        $btnGrn = 'px-3 py-2 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition';

        $chip      = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
        $icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    @endphp

    <div class="px-4 sm:px-6 py-6 space-y-8">
        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Documents & Packages — History</h2>
                            <p class="text-sm text-white/80">Pantau status document & package dengan cepat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER BAR (unchanged) --}}
        <section class="{{ $card }}">
            <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    <div class="lg:col-span-3">
                        <label class="{{ $label }}">Search</label>
                        <div class="relative">
                            <input type="text" class="{{ $input }} pl-9"
                                   placeholder="Cari nama item / pengirim / penerima / receptionist…"
                                   wire:model.live="q">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                            </svg>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="{{ $label }}">Type</label>
                        <select wire:model.live="type" class="{{ $input }}">
                            <option value="all">Semua Type</option>
                            <option value="document">Document</option>
                            <option value="package">Package</option>
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="{{ $label }}">Tanggal (created)</label>
                        <div class="relative">
                            <input type="date" wire:model.live="selectedDate" class="{{ $input }} pl-9">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="{{ $label }}">Urutkan</label>
                        <select wire:model.live="dateMode" class="{{ $input }}">
                            <option value="semua">Urut Default</option>
                            <option value="terbaru">Terbaru</option>
                            <option value="terlama">Terlama</option>
                        </select>
                    </div>

                    <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="space-y-2">
                            <label class="{{ $label }}">Department</label>
                            <input type="text" wire:model.live="departmentQ" class="{{ $input }}" placeholder="Cari department..." />
                            <select wire:model.live="departmentId" class="{{ $input }}">
                                <option value="">Semua Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="{{ $label }}">Receptionist / User</label>
                            <input type="text" wire:model.live="userQ" class="{{ $input }}" placeholder="Cari user..." />
                            <select wire:model.live="userId" class="{{ $input }}">
                                <option value="">Semua User</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->user_id }}">{{ $u->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ONLY ONE BOX: DONE --}}
        <div class="{{ $card }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Done</h3>
                            <p class="text-sm text-gray-500">Document (delivered) & Package (taken)</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($done as $row)
                    @php $rowNoDone = ($done->firstItem() ?? 1) + $loop->index; @endphp
                    <div class="px-6 py-5 hover:bg-gray-50 transition-colors" wire:key="done-{{ $row->delivery_id }}">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            {{-- LEFT --}}
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="{{ $icoAvatar }}">{{ strtoupper(substr(($row->item_name ?? 'D')[0] ?? 'D',0,1)) }}</div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <h4 class="font-semibold text-gray-900 text-base truncate">
                                            [{{ strtoupper($row->type) }}] {{ $row->item_name }}
                                        </h4>
                                        <span class="text-[11px] px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 border border-gray-200">
                                            #{{ $row->delivery_id }}
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        <span class="{{ $chip }}">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="font-medium text-gray-700">
                                                {{ $row->status === 'delivered'
                                                    ? 'Delivered: ' . optional($row->pengiriman)->format('d M Y H:i')
                                                    : 'Taken: ' . optional($row->pengambilan)->format('d M Y H:i') }}
                                            </span>
                                        </span>

                                        @if($row->receptionist?->full_name)
                                            <span class="{{ $chip }}">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.89 0 5.566.915 7.879 2.464M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="font-medium text-gray-700">Recp: {{ $row->receptionist->full_name }}</span>
                                            </span>
                                        @endif
                                        <span class="{{ $chip }}">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium text-gray-700">Status: {{ $row->status }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT: NUMBER + ACTIONS --}}
                            <div class="text-right shrink-0 space-y-2">
                                <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                                    No. {{ $rowNoDone }}
                                </span>
                                <div class="mt-2 flex flex-col gap-2">
                                    <button class="{{ $btnBlk }}" wire:click="openEdit({{ $row->delivery_id }})">Edit</button>
                                    <button class="px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition"
                                            wire:click="softDelete({{ $row->delivery_id }})">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center text-gray-500 text-sm">Tidak ada data selesai.</div>
                @endforelse
            </div>

            <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-center">
                    {{ $done->onEachSide(1)->links() }}
                </div>
            </div>
        </div>

        {{-- EDIT MODAL (for Done edits) --}}
        <x-modal wire:model="showEdit">
            <x-slot:title>Edit Data</x-slot:title>
            <div class="space-y-3">
                <div>
                    <label class="{{ $label }}">Item Name</label>
                    <input type="text" class="{{ $input }}" wire:model.defer="edit.item_name">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="{{ $label }}">Nama Pengirim</label>
                        <input type="text" class="{{ $input }}" wire:model.defer="edit.nama_pengirim">
                    </div>
                    <div>
                        <label class="{{ $label }}">Nama Penerima</label>
                        <input type="text" class="{{ $input }}" wire:model.defer="edit.nama_penerima">
                    </div>
                </div>
                <div>
                    <label class="{{ $label }}">Catatan</label>
                    <textarea class="w-full min-h-[100px] px-3 py-2 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition" wire:model.defer="edit.catatan"></textarea>
                </div>
            </div>
            <x-slot:footer>
                <div class="flex items-center justify-end gap-2">
                    <button class="px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 border hover:bg-gray-200" wire:click="$set('showEdit', false)">Cancel</button>
                    <button class="{{ $btnBlk }}" wire:click="saveEdit">Save</button>
                </div>
            </x-slot:footer>
        </x-modal>
    </div>
</div>
