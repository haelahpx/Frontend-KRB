@php
    use Carbon\Carbon;

    if (!function_exists('fmtDate')) {
        function fmtDate($v){ try { return $v ? Carbon::parse($v)->format('d M Y') : '—'; } catch (\Throwable) { return '—'; } }
    }
    if (!function_exists('fmtTime')) {
        function fmtTime($v){ try { return $v ? Carbon::parse($v)->format('H:i') : '—'; } catch (\Throwable) { return (is_string($v) && preg_match('/^\d{2}:\d{2}/',$v)) ? substr($v,0,5) : '—'; } }
    }

    $card  = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk= 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnRed= 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
    $chip  = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    $mono  = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
    $ico   = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
@endphp

<div class="bg-gray-50" wire:poll.2s="tick">
    <main class="px-4 sm:px-6 py-6 space-y-8">

        {{-- HERO --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zm7-6v3l2 2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Booking Room</h2>
                        <p class="text-sm text-white/80">Form booking & riwayat selesai</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM --}}
        <section class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">
                    {{ ($editingId ?? null) ? 'Edit Meeting' : 'Tambah Meeting' }}
                </h3>
                <p class="text-sm text-gray-500">Akan tersimpan sebagai <b>Pending</b> sampai receptionist menyetujui.</p>
            </div>

            <form class="p-5" wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="md:col-span-3">
                        <label class="{{ $label }}">Meeting Title</label>
                        <input type="text" wire:model.defer="form.meeting_title" class="{{ $input }}" placeholder="Contoh: Weekly Sync">
                        @error('form.meeting_title') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Room</label>
                        <select wire:model.defer="form.location" class="{{ $input }}">
                            <option value="" hidden>Pilih room</option>
                            @foreach (($roomOptions ?? []) as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('form.location') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Departemen</label>
                        <select wire:model.defer="form.department_id" class="{{ $input }}">
                            <option value="" hidden>Pilih departemen</option>
                            @foreach (($departments ?? []) as $d)
                                <option value="{{ data_get($d,'department_id') }}">{{ data_get($d,'name','—') }}</option>
                            @endforeach
                        </select>
                        @error('form.department_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Tanggal</label>
                        <input type="date" wire:model.defer="form.date" class="{{ $input }}">
                        @error('form.date') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Peserta</label>
                        <input type="number" min="1" wire:model.defer="form.participant" class="{{ $input }}">
                        @error('form.participant') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Mulai</label>
                        <input type="time" wire:model.defer="form.time" class="{{ $input }}">
                        @error('form.time') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Selesai</label>
                        <input type="time" wire:model.defer="form.time_end" class="{{ $input }}">
                        @error('form.time_end') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-3">
                        <label class="{{ $label }}">Kebutuhan Ruangan</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-3 text-sm text-gray-700">
                            @foreach (($requirementOptions ?? []) as $opt)
                                <label class="inline-flex items-center gap-2 cursor-pointer" wire:key="req-{{ $opt['id'] }}">
                                    <input type="checkbox" value="{{ $opt['id'] }}" wire:model="form.requirements"
                                           class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                    <span>{{ $opt['name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('form.requirements.*') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    @if (!empty($otherId) && in_array($otherId, ($form['requirements'] ?? []), true))
                        <div class="md:col-span-3">
                            <label class="{{ $label }}">Catatan</label>
                            <textarea rows="4" wire:model.defer="form.notes" class="{{ $input }} !h-auto resize-none"
                                      placeholder="Jelaskan kebutuhan lainnya (wajib jika memilih 'Other')"></textarea>
                            @error('form.notes') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                        </div>
                    @endif
                </div>

                <div class="pt-5">
                    <div class="flex items-center gap-3">
                        @if(($editingId ?? null))
                            <button type="button" wire:click="closeEdit"
                                class="px-4 h-10 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 hover:border-gray-400 transition">
                                Batal
                            </button>
                        @endif

                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition active:scale-95 hover:bg-black disabled:opacity-60">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ ($editingId ?? null) ? 'Simpan Perubahan' : 'Simpan Data' }}
                        </button>
                    </div>
                </div>
            </form>
        </section>

        {{-- DONE ONLY --}}
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
                @forelse (($done ?? []) as $m)
                    @php $id = data_get($m,'id'); $rowNo = $loop->iteration; @endphp
                    <div class="p-3 rounded-lg bg-gray-50 border border-gray-200" wire:key="done-{{ $id }}">
                        <div class="flex items-start gap-3">
                            <div class="{{ $ico }}">{{ strtoupper(substr((string) data_get($m,'meeting_title','M'),0,1)) }}</div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <div class="font-semibold text-gray-900 text-sm">{{ data_get($m,'meeting_title','—') }}</div>
                                    <span class="{{ $chip }}">{{ fmtDate(data_get($m,'date')) }}</span>
                                    <span class="{{ $chip }}">{{ fmtTime(data_get($m,'time')) }}–{{ fmtTime(data_get($m,'time_end')) }}</span>
                                </div>
                                <div class="mt-1 text-[12px] text-gray-600">
                                    {{ data_get($m,'location','—') }} • {{ (int) data_get($m,'participant',0) }} peserta
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button class="{{ $btnBlk }}" wire:click="openEdit({{ $id }})">Edit</button>
                                    <button class="{{ $btnRed }}" wire:click="destroy({{ $id }})">Hapus</button>
                                </div>
                            </div>
                            <div class="{{ $mono }}">No. {{ $rowNo }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 text-sm">Belum ada item selesai</div>
                @endforelse
            </div>
        </section>
    </main>
</div>
