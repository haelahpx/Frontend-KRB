<div class="bg-gray-50" wire:poll.1000ms>
    <main class="px-4 sm:px-6 py-6">
        <div class="space-y-8">
            <div
                class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
                <div class="pointer-events-none absolute inset-0 opacity-10">
                    <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                    <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
                </div>
                <div class="relative z-10 p-6 sm:p-8">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Selamat datang di Kebun Raya Bogor</h2>
                            <p class="text-sm text-white/80">Ringkasan kegiatan hari ini</p>
                        </div>
                    </div>
                </div>
            </div>

            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($stats as $s)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">{{ $s['label'] }}</p>
                    <div class="mt-2 flex items-end gap-2">
                        <h3 class="text-2xl font-semibold text-gray-900">{{ $s['value'] }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $s['badgeClass'] }}">{{ $s['badge'] }}</span>
                    </div>
                </div>
                @endforeach
            </section>

            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Jadwal Meeting Hari Ini</h3>
                        <p class="text-sm text-gray-500">Ringkasan ruangan & waktu</p>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse ($meetings as $m)
                        <li class="px-5 py-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">{{ $m['time'] }} • {{ $m['room'] }}</p>
                                <p class="font-medium text-gray-900">{{ $m['title'] }}</p>
                            </div>
                            @php
                            $statusClass = match ($m['status']) {
                                'Berlangsung' => 'bg-green-100 text-green-700',
                                'Berikutnya' => 'bg-blue-100 text-blue-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                            @endphp
                            <span class="text-xs px-2 py-1 rounded-full {{ $statusClass }}">{{ $m['status'] }}</span>
                        </li>
                        @empty
                        <li class="px-5 py-8 text-center text-sm text-gray-500">Tidak ada meeting terjadwal hari ini.</li>
                        @endforelse
                    </ul>
                    <div class="px-5 py-3 border-t border-gray-200 text-right">
                        <a href="{{ route('receptionist.schedule') }}"
                            class="text-sm font-medium text-gray-700 hover:text-gray-900">Lihat semua →</a>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Buku Tamu (Hari Ini)</h3>
                        <p class="text-sm text-gray-500">{{ count($guests) }} entri terbaru</p>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse ($guests as $g)
                        <li class="px-5 py-4 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $g['name'] }}</p>
                                <p class="text-sm text-gray-500">{{ $g['purpose'] }}</p>
                            </div>
                            <span class="text-sm text-gray-500">{{ $g['time'] }}</span>
                        </li>
                        @empty
                        <li class="px-5 py-8 text-center text-sm text-gray-500">Belum ada tamu hari ini.</li>
                        @endforelse
                    </ul>
                    <div class="px-5 py-3 border-t border-gray-200 text-right">
                        <a href="{{ route('receptionist.guestbook') }}"
                            class="text-sm font-medium text-gray-700 hover:text-gray-900">Kelola buku tamu →</a>
                    </div>
                </div>
            </section>
            
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Dokumen Terbaru</h3>
                        <p class="text-sm text-gray-500">5 unggahan terakhir</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b border-gray-200">
                                    <th class="px-5 py-3 font-medium">Nama</th>
                                    <th class="px-5 py-3 font-medium">Kategori</th>
                                    <th class="px-5 py-3 font-medium">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($documents as $d)
                                <tr>
                                    <td class="px-5 py-3 text-gray-900">{{ $d['name'] }}</td>
                                    <td class="px-5 py-3 text-gray-700">{{ $d['cat'] }}</td>
                                    <td class="px-5 py-3 text-gray-700">{{ $d['date'] }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-8 text-center text-sm text-gray-500">Tidak ada dokumen terbaru.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-3 border-t border-gray-200 text-right">
                        <a href="{{ route('receptionist.documents') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Lihat semua dokumen →</a>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Kalender</h3>
                        <p class="text-sm text-gray-500">{{ now()->translatedFormat('F Y') }}</p>
                    </div>
                    <div class="p-5">
                        @php
                            $daysInMonth = (int) now()->endOfMonth()->format('j');
                            $today = (int) now()->format('j');
                        @endphp
                        <div class="grid grid-cols-7 text-center text-xs text-gray-500">
                            <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                        </div>
                        <div class="mt-2 grid grid-cols-7 gap-1 text-sm text-center">
                            @foreach (range(1, $daysInMonth) as $day)
                            <div class="py-2 rounded-lg border
                                {{ $day === $today ? 'border-gray-900 bg-gray-900 text-white font-semibold' : 'border-gray-200 text-gray-700' }}">
                                {{ $day }}
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="inline-block w-3 h-3 rounded bg-gray-900"></span>
                                <span>Hari ini</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="inline-block w-3 h-3 rounded bg-blue-600"></span>
                                <span>Memiliki meeting</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>