{{-- resources/views/livewire/pages/user/bookvehicle.blade.php --}}
<div class="max-w-7xl mx-auto py-6 grid grid-cols-1 lg:grid-cols-3 gap-6 px-4">
    {{-- LEFT: main form (2/3) --}}
    <div class="lg:col-span-2">
        <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-black/5">
                <h2 class="text-2xl font-semibold">
                    {{-- Judul dinamis berdasarkan state $booking --}}
                    @if($booking)
                        Upload Foto - Booking #{{ $booking->vehiclebooking_id }}
                    @else
                        Book a Vehicle
                    @endif
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{-- Deskripsi dinamis --}}
                    @if($booking)
                        @if($booking->status === 'approved')
                            Booking sudah <span class="font-medium text-emerald-700">approved</span>.
                            Silakan unggah **foto sebelum** penggunaan untuk memulai perjalanan.
                        @elseif($booking->status === 'returned')
                            Kendaraan sudah dikembalikan.
                            Silakan unggah **foto setelah** penggunaan untuk menyelesaikan booking.
                        @endif
                    @else
                        Fill out the form below to request a vehicle booking
                    @endif
                </p>
            </div>

            <div class="p-6">
                {{-- Session Messages --}}
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

                {{--
                KONDISI 1: STATE UPLOAD (jika $booking ada)
                Tampilkan form untuk upload foto (before/after)
                --}}
                @if($booking)
                    <div class="mb-4 space-y-3 border-b border-gray-200 pb-4">
                        <div class="text-sm text-gray-700">Nama: <span
                                class="font-medium">{{ $booking->borrower_name }}</span></div>
                        <div class="text-sm text-gray-700">Departemen: <span
                                class="font-medium">{{ $departments->firstWhere('department_id', $booking->department_id)->department_name ?? '-' }}</span>
                        </div>
                        <div class="text-sm text-gray-700">Kendaraan: <span
                                class="font-medium">{{ $vehicles->firstWhere('vehicle_id', $booking->vehicle_id)->name ?? 'N/A' }}
                                —
                                {{ $vehicles->firstWhere('vehicle_id', $booking->vehicle_id)->plate_number ?? 'N/A' }}</span>
                        </div>
                        <div class="text-sm text-gray-700">Waktu: <span
                                class="font-medium">{{ \Carbon\Carbon::parse($booking->start_at)->format('d M Y, H:i') }} →
                                {{ \Carbon\Carbon::parse($booking->end_at)->format('d M Y, H:i') }}</span></div>
                        <div class="text-sm text-gray-700">Tujuan: <span
                                class="font-medium">{{ $booking->destination ?? '-' }}</span></div>
                    </div>

                    <form wire:submit.prevent="handlePhotoUpload" enctype="multipart/form-data" class="space-y-4">
                        {{-- Tampilkan input 'photo_before' HANYA JIKA status 'approved' --}}
                        @if($booking->status === 'approved')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Sebelum <span
                                        class="text-red-600">*</span></label>
                                <input wire:model="photo_before" type="file" accept="image/*"
                                    class="w-full text-sm border border-gray-300 rounded-md p-2">
                                @error('photo_before') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>
                        @endif

                        {{-- Tampilkan input 'photo_after' HANYA JIKA status 'returned' --}}
                        @if($booking->status === 'returned')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Setelah <span
                                        class="text-red-600">*</span></label>
                                <input wire:model="photo_after" type="file" accept="image/*"
                                    class="w-full text-sm border border-gray-300 rounded-md p-2">
                                @error('photo_after') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>
                        @endif

                        {{-- Loading indicator --}}
                        <div wire:loading wire:target="photo_before, photo_after">
                            <span class="text-sm text-blue-600">Uploading photo...</span>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <a href="{{ route('vehiclestatus') }}" wire:navigate
                                class="px-4 py-2 rounded-md border border-gray-300 bg-white text-sm text-gray-700">Kembali
                                ke Status</a>

                            @if($booking->status === 'approved')
                                <button type="submit" class="px-4 py-2 rounded-md bg-emerald-600 text-white text-sm"
                                    wire:loading.attr="disabled">Upload & Mulai Perjalanan</button>
                            @elseif($booking->status === 'returned')
                                <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white text-sm"
                                    wire:loading.attr="disabled">Upload & Selesaikan Booking</button>
                            @endif
                        </div>
                    </form>

                    {{--
                    KONDISI 2: STATE CREATE (jika $booking == null)
                    Tampilkan form untuk membuat booking baru
                    --}}
                @else
                    <form wire:submit.prevent="submitBooking" class="space-y-4" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Nama --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nama <span
                                        class="text-red-600">*</span></label>
                                @if($name)
                                    <div
                                        class="w-full text-sm rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-gray-700">
                                        {{ $name }}</div>
                                    <input type="hidden" wire:model="name" />
                                @else
                                    <input wire:model="name" type="text" placeholder="Nama"
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
                                    <input type="hidden" wire:model="department_id" />
                                @else
                                    <select wire:model="department_id"
                                        class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                        <option value="">Select department</option>
                                        @foreach($departments as $d)
                                            <option value="{{ $d->department_id }}">{{ $d->department_name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('department_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Pukul Mulai --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Pukul Mulai <span
                                        class="text-red-600">*</span></label>
                                <input wire:model="start_time" type="time"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                @error('start_time') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Pukul Selesai --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Pukul Selesai <span
                                        class="text-red-600">*</span></label>
                                <input wire:model="end_time" type="time"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                @error('end_time') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Tanggal Peminjaman --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Peminjaman <span
                                        class="text-red-600">*</span></label>
                                <input wire:model.live="date_from" type="date"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                @error('date_from') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Tanggal Pengembalian --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Pengembalian <span
                                        class="text-red-600">*</span></label>
                                <input wire:model.live="date_to" type="date"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                @error('date_to') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Keperluan --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Keperluan <span
                                        class="text-red-600">*</span></label>
                                <input wire:model="purpose" type="text" placeholder="Uraian singkat keperluan"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                @error('purpose') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Tujuan Lokasi --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tujuan Lokasi</label>
                                <input wire:model="destination" type="text" placeholder="Contoh: Kantor Cabang Cibubur"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                @error('destination') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Odd/Even --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Masuk Area Ganjil/Genap</label>
                                <select wire:model="odd_even_area"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                    <option value="tidak">Tidak Masuk</option>
                                    <option value="ganjil">Ganjil</option>
                                    <option value="genap">Genap</option>
                                </select>
                                @error('odd_even_area') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Jenis Keperluan (diubah valuenya agar sesuai DB) --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Jenis Keperluan</label>
                                <select wire:model="jenis_keperluan"
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2">
                                    <option value="">Pilih Keperluan</option>
                                    <option value="dinas">Dinas (Visitasi)</option>
                                    <option value="operasional">Operasional (Logistik Barang)</option>
                                    <option value="antar_jemput">Antar Jemput</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                                @error('jenis_keperluan') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Kendaraan --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Kendaraan (opsional)</label>
                                <select wire:model.live="vehicle_id" @if(!$hasVehicles) disabled @endif
                                    class="w-full text-sm rounded-md border border-gray-300 px-3 py-2 bg-white">
                                    @if(!$hasVehicles)
                                        <option value="">Data kendaraan belum tersedia</option>
                                    @else
                                        <option value="">Pilih kendaraan jika tahu</option>
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
                                @error('vehicle_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Syarat & Ketentuan --}}
                        <div class="mt-3 text-sm border-t border-gray-200 pt-4">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <label class="inline-flex items-center">
                                    <input wire:model="has_sim_a" type="checkbox" class="rounded mr-2 border-gray-400">
                                    Saya memiliki SIM A (wajib)
                                </label>

                                <label class="inline-flex items-center">
                                    <input wire:model="agree_terms" type="checkbox" class="rounded mr-2 border-gray-400">
                                    Saya Menyetujui Syarat dan Ketentuan <span class="text-red-600">*</span>
                                </label>
                            </div>
                            {{-- INI PERBAIKANNYA --}}
                            @error('has_sim_a') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                            @error('agree_terms') <div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Actions --}}
                        <div class="mt-4 flex items-center gap-3">
                            <button type="button" wire:click="resetForm"
                                class="px-4 py-2 rounded-md border border-gray-300 text-sm bg-white text-gray-700">Clear
                                Form</button>
                            <button type="submit" class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submitBooking">Submit Request</span>
                                <span wire:loading wire:target="submitBooking">Sending...</span>
                            </button>
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
            <div class="text-xs text-gray-500 mt-1 mb-3">
                @if($date_from && $start_time)
                    For: {{ \Carbon\Carbon::parse($date_from)->format('d M Y') }}, {{ $start_time }}
                @else
                    For: (Pilih tanggal & jam)
                @endif
            </div>

            <div class="space-y-3" wire:poll.5000ms="loadAvailability">
                @forelse($availability as $a)
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
                @empty
                    <div class="text-xs text-gray-500">Pilih tanggal dan jam untuk cek ketersediaan.</div>
                @endforelse
            </div>
        </div>

        {{-- Recent bookings --}}
        <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm p-5">
            <h3 class="text-base font-medium">My Dept's Recent Bookings</h3>
            <div class="mt-3 space-y-3 text-sm">
                @forelse($recentBookings as $rb)
                    @php
                        $statusText = str_replace('_', ' ', ucfirst($rb->status));
                        $statusColorClass = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-emerald-100 text-emerald-800',
                            'on_progress' => 'bg-blue-100 text-blue-800',
                            'returned' => 'bg-indigo-100 text-indigo-800',
                            'completed' => 'bg-gray-100 text-gray-700',
                            'rejected' => 'bg-red-100 text-red-800',
                            'cancelled' => 'bg-gray-100 text-gray-700',
                        ][$rb->status] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <div class="flex items-start gap-3">
                        <div
                            class="w-3 h-3 rounded-full {{ $rb->status === 'completed' ? 'bg-gray-300' : 'bg-blue-400' }} mt-1">
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">{{ $rb->borrower_name ?? ($rb->purpose ?? 'Booking') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($rb->start_at ?? now())->format('M d, H:i') }}</div>
                        </div>
                        <div class="text-xs px-2 py-1 rounded-md {{ $statusColorClass }}">
                            {{ $statusText }}
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-gray-500">No recent bookings from your department.</div>
                @endforelse
            </div>

            <div class="mt-4 text-xs text-gray-500 border-t border-gray-200 pt-3">
                Catatan: Aksi upload foto akan muncul di halaman <a href="{{ route('vehiclestatus') }}" wire:navigate
                    class="text-blue-600 hover:underline">Vehicle Status</a>.
            </div>
        </div>
    </div>
</div>