{{-- resources/views/livewire/pages/user/bookvehicle.blade.php --}}
<div class="max-w-7xl mx-auto py-6 grid grid-cols-1 lg:grid-cols-3 gap-6 px-4">
    {{-- LEFT: main form (2/3) --}}
    <div class="lg:col-span-2">
        <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-black/5">
                <h2 class="text-2xl font-semibold">
                    @if($isEdit)
                        Upload Foto - Booking #{{ $editingBookingId }}
                    @else
                        Book a Vehicle
                    @endif
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    @if($isEdit)
                        Booking sudah <span class="font-medium text-emerald-700">approved</span>. Silakan unggah foto
                        sebelum dan setelah penggunaan.
                    @else
                        Fill out the form below to request a vehicle booking
                    @endif
                </p>
            </div>

            <div class="p-6">
                @if(session()->has('success'))
                    <div class="text-sm bg-green-50 border border-green-100 text-green-800 px-3 py-2 rounded-md mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="text-sm bg-red-50 border border-red-100 text-red-800 px-3 py-2 rounded-md mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- EDIT MODE --}}
                @if($isEdit && $editingBooking)
                    <div class="mb-4 space-y-3">
                        <div class="text-sm text-gray-700">Nama: <span
                                class="font-medium">{{ $editingBooking->borrower_name }}</span></div>
                        <div class="text-sm text-gray-700">Departemen: <span
                                class="font-medium">{{ $departments->firstWhere('department_id', $editingBooking->department_id)->department_name ?? '-' }}</span>
                        </div>
                        <div class="text-sm text-gray-700">Tanggal: <span
                                class="font-medium">{{ \Carbon\Carbon::parse($editingBooking->start_at)->format('Y-m-d') }}
                                → {{ \Carbon\Carbon::parse($editingBooking->end_at)->format('Y-m-d') }}</span></div>
                        <div class="text-sm text-gray-700">Waktu: <span
                                class="font-medium">{{ \Carbon\Carbon::parse($editingBooking->start_at)->format('H:i') }} →
                                {{ \Carbon\Carbon::parse($editingBooking->end_at)->format('H:i') }}</span></div>
                        <div class="text-sm text-gray-700">Tujuan: <span
                                class="font-medium">{{ $editingBooking->destination ?? '-' }}</span></div>
                    </div>

                    <form wire:submit.prevent="submit" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Foto Sebelum <span
                                    class="text-red-600">*</span></label>
                            <input wire:model="photo_before" type="file" accept="image/*" class="w-full text-xs">
                            @error('photo_before') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Foto Setelah <span
                                    class="text-red-600">*</span></label>
                            <input wire:model="photo_after" type="file" accept="image/*" class="w-full text-xs">
                            @error('photo_after') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="button" wire:click="$set('isEdit', false)"
                                class="px-4 py-2 rounded-md border border-gray-200 bg-white">Cancel</button>
                            <button type="submit" class="px-4 py-2 rounded-md bg-emerald-600 text-white">Upload &
                                Selesai</button>
                        </div>
                    </form>

                    {{-- CREATE MODE --}}
                @else
                    <form wire:submit.prevent="submit" class="space-y-4" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Nama --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nama <span
                                        class="text-red-600">*</span></label>
                                @if($name)
                                    <div
                                        class="w-full text-sm rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-gray-700">
                                        {{ $name }}</div>
                                    <input type="hidden" wire:model.defer="name" />
                                @else
                                    <input wire:model.defer="name" type="text" placeholder="Nama"
                                        class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                @endif
                                @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Departemen --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Departement <span
                                        class="text-red-600">*</span></label>
                                @php $dept = $departments->firstWhere('department_id', $department_id); @endphp
                                @if(isset($dept) && $dept)
                                    <div
                                        class="w-full text-sm rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-gray-700">
                                        {{ $dept->department_name }}</div>
                                    <input type="hidden" wire:model.defer="department_id" />
                                @else
                                    <select wire:model.defer="department_id"
                                        class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                        <option value="">Select department</option>
                                        @foreach($departments as $d)
                                            <option value="{{ $d->department_id }}">{{ $d->department_name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('department_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Pukul --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Pukul <span
                                        class="text-red-600">*</span></label>
                                <input wire:model.defer="start_time" type="time"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                @error('start_time') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Selesai Pukul --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Selesai Pukul <span
                                        class="text-red-600">*</span></label>
                                <input wire:model.defer="end_time" type="time"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                @error('end_time') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Tanggal Peminjaman --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Peminjaman <span
                                        class="text-red-600">*</span></label>
                                <input wire:model.defer="date_from" type="date"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                @error('date_from') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Tanggal Pengembalian --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Pengembalian <span
                                        class="text-red-600">*</span></label>
                                <input wire:model.defer="date_to" type="date"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                @error('date_to') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Keperluan --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Keperluan <span
                                        class="text-red-600">*</span></label>
                                <input wire:model.defer="purpose" type="text" placeholder="Uraian singkat keperluan"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                @error('purpose') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Tujuan Lokasi --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tujuan Lokasi</label>
                                <input wire:model.defer="destination" type="text"
                                    placeholder="Contoh: Kantor Cabang Cibubur" class="w-full text-sm rounded-...">
                                @error('destination') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Odd/Even --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Masuk Area Ganjil/Genap</label>
                                <select wire:model.defer="odd_even_area"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                    <option value="tidak">Tidak Masuk</option>
                                    <option value="ganjil">Ganjil</option>
                                    <option value="genap">Genap</option>
                                </select>
                                @error('odd_even_area') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Jenis Keperluan --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Keperluan</label>
                                <select wire:model.defer="jenis_keperluan"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                    <option value="">Pilih Keperluan</option>
                                    <option value="visitasi">Visitasi</option>
                                    <option value="logistik barang">Logistik Barang</option>
                                </select>
                                @error('jenis_keperluan') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Kendaraan --}}
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

                            {{-- Foto before --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Foto Sebelum</label>
                                <input wire:model="photo_before" type="file" accept="image/*" class="w-full text-xs">
                                @error('photo_before') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Foto after --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Foto Setelah</label>
                                <input wire:model="photo_after" type="file" accept="image/*" class="w-full text-xs">
                                @error('photo_after') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
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
                @endif
            </div>
        </div>
    </div>

    {{-- RIGHT: sidebar --}}
    <div class="space-y-6">
        {{-- Availability --}}
        <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm p-5">
            <h3 class="text-base font-medium">Vehicle Availability</h3>
            <div class="text-xs text-gray-500 mt-1 mb-3">For selected date: {{ $date_from ?? '—' }} —
                {{ $start_time ?? '' }}</div>

            <div class="space-y-3" wire:poll.5000ms="loadAvailability">
                @foreach($availability as $a)
                    <div
                        class="flex items-center justify-between rounded-md px-3 py-2
                            {{ $a['status'] === 'available' ? 'bg-green-50 border border-green-100' : 'bg-red-50 border border-red-100' }}">
                        <div class="flex items-center gap-3">
                            <span
                                class="w-2 h-2 rounded-full {{ $a['status'] === 'available' ? 'bg-emerald-500' : 'bg-red-600' }}"></span>
                            <div class="text-sm text-gray-700">{{ $a['label'] }}</div>
                        </div>
                        <div
                            class="text-xs font-medium {{ $a['status'] === 'available' ? 'text-emerald-600' : 'text-red-700' }}">
                            {{ $a['status'] === 'available' ? 'Available' : 'Booked' }}
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
                            <div class="font-medium text-gray-800">{{ $rb->borrower_name ?? ($rb->purpose ?? 'Booking') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($rb->start_at ?? now())->format('M d, Y H:i') }}</div>
                        </div>
                        <div
                            class="text-xs px-2 py-1 rounded-md {{ $rb->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($rb->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($rb->status === 'draft' ? 'bg-sky-100 text-sky-800' : 'bg-gray-100 text-gray-700')) }}">
                            {{ ucfirst($rb->status ?? 'pending') }}
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-gray-500">No recent bookings.</div>
                @endforelse
            </div>

            <div class="mt-3 text-xs text-gray-500">
                Catatan: Setelah admin <span class="font-medium">approve</span>, buka halaman ini dengan query
                <code>?edit=&lt;booking_id&gt;</code> untuk upload foto.
            </div>
        </div>

        {{-- helper --}}
        <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm p-4 text-sm text-gray-600">
            Tip: Pastikan mengisi tanggal & jam dengan benar. Jika kendaraan belum tersedia, hubungi FM Ops.
        </div>
    </div>
</div>