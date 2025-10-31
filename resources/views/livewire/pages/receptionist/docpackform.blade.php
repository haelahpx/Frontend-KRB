<div class="min-h-screen bg-gray-50" wire:poll.1000ms.keep-alive>
    @php
        $card   = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $head   = 'bg-gradient-to-r from-gray-900 to-black';
        $hpad   = 'px-6 py-5';
        $label  = 'block text-sm font-medium text-gray-700 mb-2';
        $input  = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-900 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    @endphp

    {{-- Keep select text readable in dark/Chromium quirks --}}
    <style>
      :root { color-scheme: light; }
      select, option {
        color:#111827 !important;
        background:#ffffff !important;
        -webkit-text-fill-color:#111827 !important;
      }
      option:checked { background:#e5e7eb !important; color:#111827 !important; }
    </style>

    <div class="px-4 sm:px-6 py-6 space-y-8">
        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl {{ $head }} text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8M4 6h16v12H4z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Doc/Pack Form</h2>
                        <p class="text-sm text-white/80">Input paket/dokumen dengan alur masuk/keluar</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM --}}
        <div class="{{ $card }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Tambah Data</h3>
                        <p class="text-sm text-gray-500">Lengkapi detail paket/dokumen</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="save" class="p-6 space-y-6">
                {{-- Row: Direction & Type & Storage --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="{{ $label }}">Arah</label>
                        <select class="{{ $input }}" wire:model.live="direction" wire:key="direction-select">
                            <option value="taken">Masuk untuk internal (Taken)</option>
                            <option value="deliver">Titip untuk dikirim (Deliver later)</option>
                        </select>
                        @error('direction') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Tipe</label>
                        <select class="{{ $input }}" wire:model.live="itemType" wire:key="type-select">
                            <option value="package">Package</option>
                            <option value="document">Document</option>
                        </select>
                        @error('itemType') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Tempat Penyimpanan</label>
                        <select class="{{ $input }}" wire:model.defer="storageId" wire:key="storage-select">
                            <option value="">Pilih penyimpanan…</option>
                            @foreach($storages as $s)
                                <option wire:key="storage-{{ $s['id'] }}" value="{{ $s['id'] }}">{{ $s['name'] }}</option>
                            @endforeach
                        </select>
                        @error('storageId') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Item name --}}
                <div>
                    <label class="{{ $label }}">Nama Paket/Dokumen</label>
                    <input type="text" class="{{ $input }}" wire:model.defer="itemName" placeholder="Contoh: Dokumen Kontrak PT ABC">
                    @error('itemName') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-5">
                        <div>
                            <label class="{{ $label }}">
                                {{ $direction === 'taken' ? 'Departemen Penerima' : 'Departemen Pengirim' }}
                            </label>
                            <select class="{{ $input }}" wire:model.live="departmentId" wire:key="dept-select">
                                <option value="">Pilih departemen…</option>
                                @foreach($departments as $d)
                                    <option wire:key="dept-{{ $d['id'] }}" value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                                @endforeach
                            </select>
                            @error('departmentId') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $label }}">
                                {{ $direction === 'taken' ? 'Nama Penerima (User)' : 'Nama Pengirim (User)' }}
                            </label>

                            {{-- Disabled until a department is selected and users are loaded --}}
                            <select
                                class="{{ $input }} bg-white text-gray-900"
                                wire:model.live="userId"
                                wire:key="user-select-{{ $departmentId ?? 'none' }}"
                                @disabled(!$departmentId || empty($users))
                            >
                                <option value="" selected disabled>
                                    {{ !$departmentId ? 'Pilih departemen dulu…' : (empty($users) ? 'Tidak ada user pada departemen ini' : 'Pilih user…') }}
                                </option>

                                @if($departmentId && !empty($users))
                                    {{-- $users is [id => full_name] --}}
                                    @foreach($users as $id => $name)
                                        <option wire:key="user-{{ $id }}" value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('userId') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-5">
                        @if ($direction === 'taken')
                            <div>
                                <label class="{{ $label }}">Nama Pengirim (Free Text)</label>
                                <input type="text" class="{{ $input }}" wire:model.defer="senderText" placeholder="Kurir / Ekspedisi / Pengirim">
                                @error('senderText') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        @else
                            <div>
                                <label class="{{ $label }}">Nama Penerima (Free Text)</label>
                                <input type="text" class="{{ $input }}" wire:model.defer="receiverText" placeholder="Nama penerima">
                                @error('receiverText') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit" class="{{ $btnBlk }}" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">Simpan</span>
                        <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                            </svg>
                            Menyimpan…
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
