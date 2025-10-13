{{-- resources/views/livewire/pages/user/bookvehicle.blade.php --}}
<div class="max-w-3xl mx-auto py-6">
    <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-black/5">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 flex items-center justify-center rounded-md bg-gray-100 text-sm font-semibold text-gray-700">
                    BV
                </div>
                <div>
                    <h1 class="text-base font-semibold leading-tight">Booking Kendaraan</h1>
                </div>
            </div>

            <div class="text-xs text-gray-500">Status: <span
                    class="font-medium">{{ $hasVehicles ? 'Data kendaraan tersedia' : 'Menunggu data kendaraan' }}</span>
            </div>
        </div>

        {{-- Form --}}
        <form wire:submit.prevent="submit" class="px-4 py-4 space-y-3">
            @if(session()->has('success'))
                <div class="text-sm bg-green-50 border border-green-100 text-green-800 px-3 py-2 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                {{-- Kendaraan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kendaraan</label>
                    <select wire:model="vehicle_id" @if(!$hasVehicles) disabled @endif
                        class="w-full text-sm rounded-md border border-gray-300 focus:ring-0 py-2 px-3 bg-white">
                        @if(!$hasVehicles)
                            <option value="">Data kendaraan belum tersedia</option>
                        @else
                            <option value="">— Pilih kendaraan —</option>
                            @foreach($vehicles as $v)
                                @php
                                    // support multiple column names (vehicle_id / id) from dump variations
                                    $id = $v->vehicle_id ?? $v->id ?? null;
                                    $label = $v->vehicle_name ?? $v->room_name ?? ($v->name ?? 'Kendaraan');
                                    $plate = $v->license_plate ?? ($v->vehicle_plate ?? '');
                                @endphp
                                <option value="{{ $id }}">
                                    {{ $label }} {{ $plate ? " — $plate" : '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('vehicle_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal</label>
                    <input wire:model="date" type="date"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3 focus:ring-0">
                    @error('date') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Berangkat --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jam Berangkat</label>
                    <input wire:model="departure_time" type="time"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3 focus:ring-0">
                    @error('departure_time') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Kembali --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jam Kembali (opsional)</label>
                    <input wire:model="return_time" type="time"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3 focus:ring-0">
                    @error('return_time') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Tujuan full width --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tujuan</label>
                    <input wire:model="destination" type="text" placeholder="Contoh: Kantor Cabang Cibinong"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3 focus:ring-0">
                    @error('destination') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Jumlah --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah Penumpang</label>
                    <input wire:model="number_of_passengers" type="number" min="1"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3 focus:ring-0">
                    @error('number_of_passengers') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Keperluan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Keperluan</label>
                    <input wire:model="purpose" type="text" placeholder="Contoh: Inspeksi proyek"
                        class="w-full text-sm rounded-md border border-gray-300 py-2 px-3 focus:ring-0">
                    @error('purpose') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Aksi --}}
            <div class="flex items-center justify-end gap-3 pt-1">
                <button type="button" wire:click="resetForm"
                    class="text-sm px-3 py-2 rounded-md border border-gray-200 bg-gray-50 hover:bg-gray-100">
                    Reset
                </button>

                <button type="submit" class="text-sm px-4 py-2 rounded-md bg-slate-900 text-white hover:bg-slate-800">
                    Simpan Draft
                </button>
            </div>
        </form>

        {{-- Divider --}}
        <div class="border-t border-black/5"></div>

        {{-- Draft list (session) --}}
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
                                <th class="px-2 py-2 text-left">Kendaraan</th>
                                <th class="px-2 py-2 text-left">Tanggal</th>
                                <th class="px-2 py-2 text-left">Berangkat</th>
                                <th class="px-2 py-2 text-left">Tujuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach(session('bookvehicle.drafts', []) as $d)
                                <tr>
                                    <td class="px-2 py-2 text-gray-600">
                                        {{ \Illuminate\Support\Str::limit($d['saved_at'] ?? '-', 19) }}</td>
                                    <td class="px-2 py-2 text-gray-700 text-xs">{{ $d['vehicle_id'] ?? '-' }}</td>
                                    <td class="px-2 py-2 text-gray-700 text-xs">{{ $d['date'] ?? '-' }}</td>
                                    <td class="px-2 py-2 text-gray-700 text-xs">{{ $d['departure_time'] ?? '-' }}</td>
                                    <td class="px-2 py-2 text-gray-700 text-xs">
                                        {{ \Illuminate\Support\Str::limit($d['destination'] ?? '-', 30) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- clear drafts button --}}
                <div class="mt-3 flex justify-end">
                    <form method="POST" action="{{ route('book-vehicle.clear-drafts') }}">
                        @csrf
                        <button type="submit"
                            class="text-xs px-3 py-1 rounded-md border border-gray-200 bg-gray-50 hover:bg-gray-100">
                            Hapus Semua Draft
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>