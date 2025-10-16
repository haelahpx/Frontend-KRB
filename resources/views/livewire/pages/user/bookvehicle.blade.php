{{-- resources/views/livewire/pages/user/bookvehicle.blade.php --}}
<div class="max-w-7xl mx-auto py-6 grid grid-cols-1 lg:grid-cols-3 gap-6 px-4">
    {{-- LEFT: main form (2/3 width) --}}
    <div class="lg:col-span-2">
        <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm overflow-hidden">
            {{-- header --}}
            <div class="px-6 py-5 border-b border-black/5">
                <h2 class="text-2xl font-semibold">Book a Vehicle</h2>
                <p class="text-sm text-gray-500 mt-1">Fill out the form below to request a vehicle booking</p>
            </div>

            <div class="p-6">
                {{-- rules box --}}
                <div class="bg-blue-50 border border-blue-100 rounded-md p-4 text-sm text-gray-700 mb-5">
                    <div class="font-medium mb-2">Booking Rules</div>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Penyerahan formulir paling lambat 2 hari kerja sebelum pelaksanaan kegiatan.</li>
                        <li>Waktu disesuaikan — pastikan jam berangkat & selesai tidak tumpang tindih.</li>
                        <li>Segala pelanggaran lalu lintas menjadi tanggung jawab peminjam.</li>
                        <li>Kembalikan kendaraan dalam keadaan bersih & bensin minimal 50% (foto sebelum & sesudah).
                        </li>
                        <li>Pengguna wajib memiliki SIM A.</li>
                    </ul>
                </div>

                {{-- form --}}
                <form wire:submit.prevent="submit" class="space-y-4" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Meeting Title / Name <span
                                    class="text-red-600">*</span></label>
                            <input wire:model.defer="name" type="text" placeholder="Enter meeting title or your name"
                                class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                            @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Departement <span
                                    class="text-red-600">*</span></label>
                            <select wire:model.defer="department_id"
                                class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                <option value="">Select department</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->department_id }}">{{ $d->department_name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date From <span
                                    class="text-red-600">*</span></label>
                            <input wire:model.defer="date_from" type="date"
                                class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                            @error('date_from') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date To <span
                                    class="text-red-600">*</span></label>
                            <input wire:model.defer="date_to" type="date"
                                class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                            @error('date_to') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Start Time <span
                                    class="text-red-600">*</span></label>
                            <input wire:model.defer="start_time" type="time"
                                class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                            @error('start_time') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">End Time</label>
                            <input wire:model.defer="end_time" type="time"
                                class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                            @error('end_time') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tujuan Lokasi <span
                                    class="text-red-600">*</span></label>
                            <input wire:model.defer="destination" type="text"
                                placeholder="Contoh: Kantor Cabang Cibubur"
                                class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                            @error('destination') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Keperluan</label>
                            <input wire:model.defer="jenis_keperluan" type="text" placeholder="Dinas / Kunjungan"
                                class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Masuk Area Ganjil/Genap</label>
                            <select wire:model.defer="odd_even_area"
                                class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                <option value="no">Tidak</option>
                                <option value="odd">Ganjil</option>
                                <option value="even">Genap</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Kendaraan (opsional)</label>
                            <select wire:model.defer="vehicle_id" @if(!$hasVehicles) disabled @endif
                                class="w-full text-sm rounded-md border border-gray-300 px-3 py-2 bg-white">
                                @if(!$hasVehicles)
                                    <option value="">Data kendaraan belum tersedia</option>
                                @else
                                    <option value="">Select vehicle</option>
                                    @foreach($vehicles as $v)
                                        @php
                                            $id = $v->vehicle_id ?? $v->id;
                                            $label = $v->vehicle_name ?? ($v->name ?? 'Kendaraan');
                                            $plate = $v->plate_number ?? ($v->license_plate ?? '');
                                        @endphp
                                        <option value="{{ $id }}">{{ $label }}{{ $plate ? " — $plate" : '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Foto Sebelum</label>
                            <input wire:model="photo_before" type="file" accept="image/*" class="w-full text-xs">
                            @error('photo_before') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Foto Setelah</label>
                            <input wire:model="photo_after" type="file" accept="image/*" class="w-full text-xs">
                            @error('photo_after') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Keperluan (uraian) <span
                                    class="text-red-600">*</span></label>
                            <input wire:model.defer="purpose" type="text" placeholder="Uraian singkat keperluan"
                                class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                            @error('purpose') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- syarat --}}
                    <div class="mt-3 text-sm">
                        <div class="flex items-start gap-4">
                            <label class="inline-flex items-center">
                                <input wire:model.defer="has_sim_a" type="checkbox" class="mr-2">
                                Saya memiliki SIM A (wajib)
                            </label>

                            <label class="inline-flex items-center">
                                <input wire:model.defer="agree_terms" type="checkbox" class="mr-2">
                                Saya Menyetujui Syarat dan Ketentuan diatas <span class="text-red-600">*</span>
                            </label>
                        </div>
                        @error('has_sim_a') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        @error('agree_terms') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- actions --}}
                    <div class="mt-4 flex items-center gap-3">
                        <button type="button" wire:click="resetForm"
                            class="px-4 py-2 rounded-md border border-gray-200 text-sm bg-white">Clear Form</button>
                        <button type="submit" class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm">Submit
                            Request</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- RIGHT: sidebar (1/3 width) --}}
    <div class="space-y-6">
        {{-- Availability card --}}
        <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm p-5">
            <h3 class="text-base font-medium">Vehicle Availability</h3>
            <div class="text-xs text-gray-500 mt-1 mb-3">For selected date: {{ $date_from ?? '—' }} —
                {{ $start_time ?? '' }}</div>

            <div class="space-y-3">
                @foreach($availability as $a)
                    <div class="flex items-center justify-between bg-green-50 border border-green-100 rounded-md px-3 py-2">
                        <div class="flex items-center gap-3">
                            <span
                                class="w-2 h-2 rounded-full {{ $a['status'] === 'available' ? 'bg-emerald-500' : 'bg-yellow-400' }}"></span>
                            <div class="text-sm text-gray-700">{{ $a['label'] }}</div>
                        </div>
                        <div
                            class="text-xs font-medium {{ $a['status'] === 'available' ? 'text-emerald-600' : 'text-yellow-700' }}">
                            {{ ucfirst($a['status']) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent bookings --}}
        <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm p-5">
            <h3 class="text-base font-medium">Recent Bookings</h3>
            <div class="mt-3 space-y-3 text-sm">
                @forelse($recentBookings as $rb)
                    <div class="flex items-start gap-3">
                        <div class="w-3 h-3 rounded-full bg-gray-300 mt-1"></div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">{{ $rb->name ?? ($rb->purpose ?? 'Booking') }}</div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($rb->date_from ?? now())->format('M d, Y') }} •
                                {{ $rb->start_time ?? '-' }}</div>
                        </div>
                        <div
                            class="text-xs px-2 py-1 rounded-md {{ $rb->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($rb->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700') }}">
                            {{ ucfirst($rb->status ?? 'pending') }}
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-gray-500">No recent bookings.</div>
                @endforelse
            </div>
        </div>

        {{-- small helper card --}}
        <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm p-4 text-sm text-gray-600">
            Tip: Pastikan mengisi tanggal & jam dengan benar. Jika kendaraan belum tersedia, hubungi FM Ops.
        </div>
    </div>
</div>