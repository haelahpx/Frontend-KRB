@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Storage; // Added if you intend to use image display like in the target style

    if (!function_exists('fmtDate')) {
        function fmtDate($v)
        {
            try {
                return $v ? Carbon::parse($v)->format('d M Y') : '—';
            } catch (\Throwable) {
                return '—';
            }
        }
    }

    if (!function_exists('fmtTime')) {
        function fmtTime($v)
        {
            try {
                // Adopted from the target style's fmtTime logic for consistency
                return $v ? Carbon::parse($v)->format('H.i') : '—';
            } catch (\Throwable) {
                if (is_string($v)) {
                    if (preg_match('/^\d{2}:\d{2}/', $v))
                        return str_replace(':', '.', substr($v, 0, 5));
                    if (preg_match('/^\d{2}\.\d{2}/', $v))
                        return substr($v, 0, 5);
                }
                return '—';
            }
        }
    }

    // Theme tokens adopted from the original guestbook page to match the target page's class names
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    // Simplified button tokens for the card list action buttons to match the target style
    $btnEdit = 'px-2.5 py-1.5 text-xs font-medium rounded-lg bg-black text-white focus:outline-none focus:ring-2 focus:ring-gray-500/20 transition';
    $btnDelete = 'px-2.5 py-1.5 text-xs font-medium rounded-lg bg-rose-700 text-white hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-700/20 transition';
    $btnRestore = 'px-2.5 py-1.5 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition';
    $btnKeluar = 'px-2.5 py-1.5 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition'; // Used for 'Keluar sekarang' button
    
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
    $icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    $editIn = 'w-full h-10 bg-white border border-gray-300 rounded-lg px-3 text-gray-800 focus:border-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-gray-900/10 transition hover:border-gray-400 placeholder:text-gray-400';
@endphp

<div class="min-h-screen bg-gray-50" wire:poll.1000ms.keep-alive>
    <main class="px-4 sm:px-6 py-6 space-y-6">
        {{-- HERO (Adopted Document History Style) --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            {{-- Changed Icon for new aesthetic --}}
                            <x-heroicon-o-user-group class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            {{-- Changed title and description to match the aesthetic, keeping the 'Guestbook' context --}}
                            <h2 class="text-lg sm:text-xl font-semibold">Guestbook Entries — Monitor</h2>
                            <p class="text-sm text-white/80">Pantau kunjungan yang masih aktif dan riwayat kunjungan tamu.</p>
                        </div>
                    </div>

                    {{-- Include deleted checkbox --}}
                    <div class="flex items-center gap-3">
                        <label class="inline-flex items-center gap-2 text-sm text-white/90">
                            <input type="checkbox"
                                wire:model.live="withTrashed"
                                class="rounded border-white/30 bg-white/10 focus:ring-white/40">
                            <span>Include deleted</span>
                        </label>
                    </div>

                    {{-- ADDED: MOBILE FILTER BUTTON (Copied from Document History) --}}
                    <button type="button"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 text-xs font-medium border border-white/30 hover:bg-white/20 md:hidden"
                        wire:click="openFilterModal">
                        <x-heroicon-o-bars-3 class="w-4 h-4"/>
                        <span>Filter</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- MAIN LAYOUT: LEFT (ITEMS LIST) + RIGHT (SIDEBAR - Desktop/Tablet) --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            {{-- LEFT: MAIN CARD WITH TABS --}}
            <section class="{{ $card }} md:col-span-3">
                
                {{-- Header: title + tabs --}}
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Guestbook Entries</h3>
                            <p class="text-xs text-gray-500">Beralih antara riwayat kunjungan dan kunjungan terbaru yang masih aktif.</p>
                        </div>

                        {{-- Segmented tabs (Copied from original, but slightly stylized to match the smaller 'Type scope' style) --}}
                        <div class="inline-flex items-center bg-gray-100 rounded-full p-1 text-[11px] font-medium">
                            <button type="button"
                                wire:click="setTab('entries')"
                                class="px-3 py-1 rounded-full transition
                                    {{ $activeTab === 'entries'
                                        ? 'bg-gray-900 text-white shadow-sm'
                                        : 'text-gray-700 hover:bg-gray-200' }}">
                                Riwayat Kunjungan
                            </button>
                            <button type="button"
                                wire:click="setTab('latest')"
                                class="px-3 py-1 rounded-full transition
                                    {{ $activeTab === 'latest'
                                        ? 'bg-gray-900 text-white shadow-sm'
                                        : 'text-gray-700 hover:bg-gray-200' }}">
                                Kunjungan Aktif
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Filters (search, date, order) - Moved below title/tabs to match the Document History Layout --}}
                <div class="px-4 sm:px-6 pt-4 pb-3 border-b border-gray-200">
                    @if($activeTab === 'entries')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Search --}}
                            <div>
                                <label class="{{ $label }}">Search</label>
                                <div class="relative">
                                    <input type="text"
                                        class="{{ $input }} pl-9"
                                        placeholder="Cari nama, no HP, instansi, petugas, keperluan…"
                                        wire:model.live="q">
                                    <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                </div>
                            </div>

                            {{-- Date --}}
                            <div>
                                <label class="{{ $label }}">Tanggal</label>
                                <div class="relative">
                                    <input type="date"
                                        class="{{ $input }} pl-9"
                                        wire:model.live="filter_date">
                                    <x-heroicon-o-calendar-days class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                </div>
                            </div>

                            {{-- Sort --}}
                            <div>
                                <label class="{{ $label }}">Urutkan</label>
                                <select class="{{ $input }}" wire:model.live="dateMode">
                                    <option value="semua">Default (terbaru)</option>
                                    <option value="terbaru">Tanggal terbaru</option>
                                    <option value="terlama">Tanggal terlama</option>
                                </select>
                            </div>
                        </div>
                    @else
                        <p class="mt-1 text-xs text-gray-500">
                            Menampilkan kunjungan hari ini yang belum mencatat jam keluar.
                        </p>
                    @endif
                </div>

                {{-- LIST AREA: switch between entries & latest --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 p-4 bg-gray-50/50">
                    @php
                        $list = $activeTab === 'entries' ? $entries : $latest;
                        $listName = $activeTab === 'entries' ? 'entries' : 'latest';
                    @endphp
                    @forelse ($list as $e)
                        @php
                            $isDeleted = $e->deleted_at !== null;
                            $rowNo = ($list->firstItem() ?? 1) + $loop->index;
                            $isLatestActive = $activeTab === 'latest' && empty($e->jam_out);
                        @endphp
                        
                        {{-- Card Item (Adopted Document History Style) --}}
                        <div class="flex flex-col justify-between p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200"
                            wire:key="{{ $listName }}-{{ $e->guestbook_id }}">
                            
                            <div class="flex items-start gap-3 mb-4">
                                <div class="{{ $icoAvatar }}">
                                    {{ strtoupper(substr($e->name ?? '—', 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                        <h4 class="font-semibold text-gray-900 text-base truncate max-w-full">
                                            {{ $e->name }}
                                        </h4>
                                        <div class="flex gap-1">
                                            @if ($e->phone_number)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded border border-gray-300 text-gray-600 bg-gray-50">
                                                    {{ $e->phone_number }}
                                                </span>
                                            @endif
                                            @if($isLatestActive)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded font-medium bg-emerald-100 text-emerald-800">
                                                    Aktif
                                                </span>
                                            @endif
                                            @if($isDeleted)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded font-medium bg-rose-100 text-rose-800">
                                                    Deleted
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="space-y-1 text-[13px] text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <x-heroicon-o-building-office class="w-3.5 h-3.5 text-gray-400"/>
                                            <span class="truncate">Instansi: {{ $e->instansi ?? '—' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <x-heroicon-o-clipboard-document class="w-3.5 h-3.5 text-gray-400"/>
                                            <span class="truncate">Keperluan: {{ $e->keperluan ?? '—' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <x-heroicon-o-calendar-days class="w-3.5 h-3.5 text-gray-400"/>
                                            <span>
                                                {{ fmtDate($e->date) }}
                                            </span>
                                            <x-heroicon-o-clock class="w-3.5 h-3.5 text-emerald-600"/>
                                            <span>{{ fmtTime($e->jam_in) }}</span>
                                            <span class="mx-0.5">–</span>
                                            <x-heroicon-o-clock class="w-3.5 h-3.5 {{ empty($e->jam_out) ? 'text-gray-400' : 'text-rose-600' }}"/>
                                            <span>{{ fmtTime($e->jam_out) }}</span>
                                        </div>
                                        @if($e->petugas_penjaga)
                                            <div class="flex items-center gap-2">
                                                <x-heroicon-o-user class="w-3.5 h-3.5 text-gray-400"/>
                                                <span class="truncate">Petugas: {{ $e->petugas_penjaga }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Actions and Row Number --}}
                            <div class="pt-3 mt-auto border-t border-gray-100 flex items-center justify-between">
                                <span class="text-[10px] text-gray-400 font-medium">
                                    No. {{ $rowNo }} | Created: {{ \Carbon\Carbon::parse($e->created_at)->format('d M Y H.i') }}
                                </span>
                                <div class="flex gap-2">
                                    <button type="button" wire:click="openEdit({{ $e->guestbook_id }})"
                                        wire:loading.attr="disabled"
                                        class="{{ $btnEdit }}">
                                        Edit
                                    </button>
                                    
                                    @if(!$isDeleted)
                                        @if($activeTab === 'latest' && empty($e->jam_out))
                                            {{-- Latest Tab: Show 'Keluar sekarang' --}}
                                            <button wire:click="setJamKeluarNow({{ $e->guestbook_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="setJamKeluarNow({{ $e->guestbook_id }})"
                                                class="{{ $btnKeluar }}">
                                                <span wire:loading.remove wire:target="setJamKeluarNow({{ $e->guestbook_id }})">
                                                    Keluar sekarang
                                                </span>
                                                <span wire:loading wire:target="setJamKeluarNow({{ $e->guestbook_id }})">
                                                    Menyimpan…
                                                </span>
                                            </button>
                                        @else
                                            {{-- Entries Tab (or active but already checked out): Show Soft Delete --}}
                                            <button wire:click="delete({{ $e->guestbook_id }})"
                                                onclick="return confirm('Hapus entri ini?')"
                                                wire:loading.attr="disabled"
                                                class="{{ $btnDelete }}">
                                                Hapus
                                            </button>
                                        @endif
                                    @else
                                        {{-- Deleted State: Restore and Permanent Delete --}}
                                        <button wire:click="restore({{ $e->guestbook_id }})"
                                                wire:loading.attr="disabled"
                                                class="{{ $btnRestore }}">
                                            Restore
                                        </button>
                                        <button wire:click="destroyForever({{ $e->guestbook_id }})"
                                                onclick="return confirm('Hapus permanen entri ini? Tindakan tidak bisa dibatalkan!')"
                                                wire:loading.attr="disabled"
                                                class="{{ $btnDelete }}">
                                            Perm. Delete
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="lg:col-span-2 py-14 text-center text-gray-500 text-sm">
                            @if($activeTab === 'entries')
                                Tidak ada entri kunjungan yang ditemukan
                            @else
                                Belum ada kunjungan aktif hari ini
                            @endif
                        </div>
                    @endforelse
                </div>

                {{-- Pagination (switch based on active tab) --}}
                <div class="px-4 sm:px-6 py-5 bg-white border-t border-gray-200 rounded-b-2xl">
                    <div class="flex justify-center">
                        @if($activeTab === 'entries')
                            {{ $entries->onEachSide(1)->links() }}
                        @else
                            {{ $latest->onEachSide(1)->links() }}
                        @endif
                    </div>
                </div>
            </section>

            {{-- RIGHT: SIDEBAR (DESKTOP / TABLET) - Adopted Document History Style --}}
            <aside class="hidden md:flex md:flex-col md:col-span-1 gap-4">
                {{-- Placeholder for future filters (Keep the same placeholder style) --}}
                <section class="{{ $card }}">
                    <div class="px-4 py-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Advanced Filters</h3>
                        <p class="text-xs text-gray-500 mt-1">Tambahkan filter lain di sini.</p>
                    </div>

                    <div class="px-4 py-3 space-y-4">
                        <p class="text-xs text-gray-500">No additional filters available for Guestbook History.</p>
                    </div>
                </section>
            </aside>
        </div>

        {{-- MOBILE FILTER MODAL (Adopted Document History Style) --}}
        @if(isset($showFilterModal) && $showFilterModal)
            <div class="fixed inset-0 z-40 md:hidden">
                <div class="absolute inset-0 bg-black/40" wire:click="closeFilterModal"></div>
                <div class="absolute inset-x-0 bottom-0 bg-white rounded-t-2xl shadow-xl max-h-[80vh] overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Advanced Filters</h3>
                            <p class="text-[11px] text-gray-500">Filter lanjutan ada di sini.</p>
                        </div>
                        <button type="button" class="text-gray-500 hover:text-gray-700" wire:click="closeFilterModal">
                            <x-heroicon-o-x-mark class="w-5 h-5"/>
                        </button>
                    </div>

                    <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
                        <p class="text-xs text-gray-500">No additional filters available for Guestbook History.</p>
                        {{-- Add your filter logic here if you introduce new mobile filters --}}
                    </div>

                    <div class="px-4 py-3 border-t border-gray-200">
                        <button type="button" class="w-full h-10 rounded-xl bg-gray-900 text-white text-xs font-medium"
                            wire:click="closeFilterModal">
                            Apply & Close
                        </button>
                    </div>
                </div>
            </div>
        @endif


        {{-- EDIT MODAL (Adopted Document History Style) --}}
        @if ($showEdit)
            <div class="fixed inset-0 z-50">
                <div class="absolute inset-0 bg-black/50" wire:click="closeEdit"></div>
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="w-full max-w-lg bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden transform transition-all duration-300 max-h-[90vh] flex flex-col">
                        
                        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="font-semibold text-black">Edit Entri Kunjungan</h3>
                            <button type="button" class="text-gray-500 hover:text-gray-700" wire:click="closeEdit">
                                <x-heroicon-o-x-mark class="w-5 h-5"/>
                            </button>
                        </div>

                        <div class="p-5 overflow-y-auto flex-1">
                            <form wire:submit.prevent="saveEdit">
                                <div class="grid grid-cols-1 gap-6">
                                    {{-- Nama --}}
                                    <div>
                                        <label for="edit_name" class="{{ $label }}">Nama Tamu <span class="text-rose-500">*</span></label>
                                        <input type="text" id="edit_name" class="{{ $input }}" wire:model.defer="editForm.name">
                                        @error('editForm.name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- No HP --}}
                                    <div>
                                        <label for="edit_phone_number" class="{{ $label }}">No. HP</label>
                                        <input type="text" id="edit_phone_number" class="{{ $input }}" wire:model.defer="editForm.phone_number">
                                        @error('editForm.phone_number') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- Instansi --}}
                                    <div>
                                        <label for="edit_instansi" class="{{ $label }}">Instansi</label>
                                        <input type="text" id="edit_instansi" class="{{ $input }}" wire:model.defer="editForm.instansi">
                                        @error('editForm.instansi') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- Keperluan --}}
                                    <div>
                                        <label for="edit_keperluan" class="{{ $label }}">Keperluan <span class="text-rose-500">*</span></label>
                                        <textarea id="edit_keperluan" class="{{ $input }} h-20 pt-2" wire:model.defer="editForm.keperluan"></textarea>
                                        @error('editForm.keperluan') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- Petugas Penjaga --}}
                                    <div>
                                        <label for="edit_petugas_penjaga" class="{{ $label }}">Petugas Penjaga <span class="text-rose-500">*</span></label>
                                        <input type="text" id="edit_petugas_penjaga" class="{{ $input }}" wire:model.defer="editForm.petugas_penjaga">
                                        @error('editForm.petugas_penjaga') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- Date / Jam In / Jam Out --}}
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label for="edit_date" class="{{ $label }}">Tanggal <span class="text-rose-500">*</span></label>
                                            <input type="date" id="edit_date" class="{{ $input }}" wire:model.defer="editForm.date">
                                            @error('editForm.date') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label for="edit_jam_in" class="{{ $label }}">Jam Masuk <span class="text-rose-500">*</span></label>
                                            <input type="time" id="edit_jam_in" class="{{ $input }}" wire:model.defer="editForm.jam_in">
                                            @error('editForm.jam_in') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label for="edit_jam_out" class="{{ $label }}">Jam Keluar</label>
                                            <input type="time" id="edit_jam_out" class="{{ $input }}" wire:model.defer="editForm.jam_out">
                                            @error('editForm.jam_out') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 pt-4 border-t border-gray-200 flex justify-end gap-3">
                                    <button type="button" wire:click="closeEdit"
                                        class="h-10 px-4 rounded-xl bg-gray-200 text-gray-900 text-sm font-medium hover:bg-gray-300 focus:outline-none">
                                        Batal
                                    </button>
                                    <button type="submit" wire:loading.attr="disabled" wire:target="saveEdit"
                                        class="h-10 px-4 rounded-xl bg-gray-900 text-white text-sm font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition shadow-sm">
                                        <span wire:loading.remove wire:target="saveEdit">Simpan Perubahan</span>
                                        <span wire:loading wire:target="saveEdit" class="flex items-center gap-2">
                                            <x-heroicon-o-arrow-path class="animate-spin -ml-1 mr-1 h-4 w-4 text-white"/>
                                            Menyimpan…
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>