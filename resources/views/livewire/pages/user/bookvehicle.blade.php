{{-- resources/views/livewire/pages/user/bookvehicle.blade.php --}}
<div class="max-w-3xl mx-auto py-6">
    <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-black/5">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 flex items-center justify-center rounded-md bg-gray-100 text-sm font-semibold text-gray-700">
                    BV</div>
                <div>
                    <h1 class="text-base font-semibold">Booking Kendaraan</h1>
                    <p class="text-xs text-gray-500 -mt-0.5">Form pinjam kendaraan operasional — isi lengkap sesuai
                        Syarat & Ketentuan.</p>
                </div>
            </div>

            <div class="text-xs text-gray-500">{{ count(session('bookvehicle.drafts', [])) }} draft</div>
        </div>

        <form wire:submit.prevent="submit" class="px-4 py-4 space-y-3">
            @if(session()->has('success'))
                <div class="text-sm bg-green-50 border border-green-100 text-green-800 px-3 py-2 rounded-md">
                    {{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                {{-- Name --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama <span
                            class="text-red-600">*</span></label>
                    <input wire:model="name" type="text"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3">
                    @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Department --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Departement <span
                            class="text-red-600">*</span></label>
                    <select wire:model="department_id"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3">
                        <option value="">— Pilih Dept —</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->department_id }}">{{ $d->department_name }}</option>
                        @endforeach
                    </select>
                    @error('department_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Tanggal Peminjaman --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Peminjaman <span
                            class="text-red-600">*</span></label>
                    <input wire:model="date_from" type="date"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3">
                    @error('date_from') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Tanggal Pengembalian --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Pengembalian <span
                            class="text-red-600">*</span></label>
                    <input wire:model="date_to" type="date"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3">
                    @error('date_to') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Pukul Berangkat --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pukul (Berangkat) <span
                            class="text-red-600">*</span></label>
                    <input wire:model="start_time" type="time"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3">
                    @error('start_time') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Selesai Pukul --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Selesai Pukul</label>
                    <input wire:model="end_time" type="time"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3">
                    @error('end_time') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Tujuan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tujuan Lokasi <span
                            class="text-red-600">*</span></label>
                    <input wire:model="destination" type="text" placeholder="Contoh: Kantor Cabang Cibubur"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3">
                    @error('destination') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Jenis Keperluan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Keperluan</label>
                    <select wire:model="jenis_keperluan"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3">
                        <option value="">— Pilih —</option>
                        @foreach($jenisOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('jenis_keperluan') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Masuk Area Ganjil/Genap --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Masuk Area Ganjil/Genap</label>
                    <select wire:model="odd_even_area"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3">
                        <option value="no">Tidak</option>
                        <option value="odd">Ganjil</option>
                        <option value="even">Genap</option>
                    </select>
                    @error('odd_even_area') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Vehicle selection (if available) --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kendaraan</label>
                    <select wire:model="vehicle_id" @if(!$hasVehicles) disabled @endif
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3 bg-white">
                        @if(!$hasVehicles)
                            <option value="">Data kendaraan belum tersedia</option>
                        @else
                            <option value="">— Pilih Kendaraan —</option>
                            @foreach($vehicles as $v)
                                @php $id = $v->vehicle_id ?? $v->id;
                                    $label = $v->vehicle_name ?? ($v->name ?? 'Kendaraan');
                                $plate = $v->license_plate ?? ''; @endphp
                                <option value="{{ $id }}">{{ $label }} {{ $plate ? " — $plate" : '' }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Photo before --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Foto Sebelum Pemakaian (wajib saat
                        submit final)</label>
                    <input wire:model="photo_before" type="file" accept="image/*" class="w-full text-xs">
                    @error('photo_before') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Photo after --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Foto Setelah Pemakaian</label>
                    <input wire:model="photo_after" type="file" accept="image/*" class="w-full text-xs">
                    @error('photo_after') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Keperluan (deskripsi) --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keperluan (uraian) <span
                            class="text-red-600">*</span></label>
                    <input wire:model="purpose" type="text" placeholder="Contoh: Inspeksi lapangan, kunjungan mitra"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3">
                    @error('purpose') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Syarat & Ketentuan --}}
            <div class="text-xs text-gray-700 border rounded-md p-3 bg-gray-50">
                <div class="font-medium mb-1">Syarat & Ketentuan</div>
                <ol class="list-decimal pl-5 space-y-1">
                    <li>Penyerahan formulir paling lambat 2 hari kerja sebelum kegiatan kepada FM Ops & Banquete</li>
                    <li>Segala pelanggaran lalu lintas jadi tanggung jawab peminjam</li>
                    <li>Kembalikan kendaraan bersih & bensin minimal 50%. Lampirkan foto sebelum & sesudah untuk
                        reimburse</li>
                    <li>Patuhi peraturan lalu lintas</li>
                    <li>Dilarang merokok di dalam kendaraan</li>
                    <li>Penggunaan sesuai fungsi kendaraan</li>
                    <li>Kerusakan menjadi tanggung jawab peminjam</li>
                    <li>Pengguna wajib memiliki SIM A</li>
                </ol>
            </div>

            <div class="flex items-start gap-3">
                <label class="inline-flex items-center text-sm">
                    <input wire:model="has_sim_a" type="checkbox" class="mr-2">
                    Saya memiliki SIM A (wajib)
                </label>
            </div>
            @error('has_sim_a') <div class="text-xs text-red-600">{{ $message }}</div>@enderror

            <div class="flex items-start gap-3">
                <label class="inline-flex items-center text-sm">
                    <input wire:model="agree_terms" type="checkbox" class="mr-2">
                    Saya Menyetujui Syarat dan Ketentuan diatas <span class="text-red-600">*</span>
                </label>
            </div>
            @error('agree_terms') <div class="text-xs text-red-600">{{ $message }}</div>@enderror

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-1">
                <button type="button" wire:click="resetForm"
                    class="text-sm px-3 py-2 rounded-md border border-gray-200 bg-gray-50">Reset</button>

                <button type="submit" class="text-sm px-4 py-2 rounded-md bg-slate-900 text-white">Simpan Draft</button>
            </div>
        </form>

        <div class="border-t border-black/5"></div>

        {{-- Draft list & clear --}}
        <div class="px-4 py-3">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium">Draft Booking (session)</h3>
                <div class="text-xs text-gray-500">{{ count(session('bookvehicle.drafts', [])) }} item</div>
            </div>

            @if(!session('bookvehicle.drafts'))
                <div class="text-xs text-gray-500 py-2">Belum ada draft tersimpan.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-2 text-left">Waktu</th>
                                <th class="px-2 py-2 text-left">Nama</th>
                                <th class="px-2 py-2 text-left">Tanggal</th>
                                <th class="px-2 py-2 text-left">Tujuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach(session('bookvehicle.drafts', []) as $d)
                                <tr>
                                    <td class="px-2 py-2 text-gray-600">
                                        {{ \Illuminate\Support\Str::limit($d['saved_at'] ?? '-', 19) }}</td>
                                    <td class="px-2 py-2 text-gray-700 text-xs">{{ $d['name'] ?? '-' }}</td>
                                    <td class="px-2 py-2 text-gray-700 text-xs">
                                        {{ ($d['date_from'] ?? '-') . ' → ' . ($d['date_to'] ?? '-') }}</td>
                                    <td class="px-2 py-2 text-gray-700 text-xs">
                                        {{ \Illuminate\Support\Str::limit($d['destination'] ?? '-', 30) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 flex justify-end">
                    <button wire:click="clearDrafts"
                        class="text-xs px-3 py-1 rounded-md border border-gray-200 bg-gray-50">Hapus Semua Draft</button>
                </div>
            @endif
        </div>
    </div>
</div>