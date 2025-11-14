<div class="bg-gray-50" wire:poll.2000ms.keep-alive>
    <main class="px-4 sm:px-6 py-6">
        <div class="space-y-8">

            {{-- Greeting --}}
            <div class="rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white p-6 shadow-2xl">
                <h2 class="text-lg font-semibold">Selamat Datang di Dashboard Receptionist</h2>
                <p class="text-sm text-gray-300">Berikut 5 data terbaru dari setiap modul</p>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Total Room Bookings --}}
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Room Bookings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ count($latestBookingRooms) }}</p>
                            <p class="text-xs text-green-600 mt-1">+12% vs last week</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-lg">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Vehicle Bookings --}}
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Vehicle Bookings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ count($latestVehicleBookings) }}</p>
                            <p class="text-xs text-green-600 mt-1">+8% vs last week</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-lg">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Guests --}}
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Guest Visits</p>
                            <p class="text-2xl font-bold text-gray-900">{{ count($latestGuests) }}</p>
                            <p class="text-xs text-green-600 mt-1">+15% vs last week</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-lg">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Documents --}}
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Documents/Packages</p>
                            <p class="text-2xl font-bold text-gray-900">{{ count($latestDocs) }}</p>
                            <p class="text-xs text-green-600 mt-1">+5% vs last week</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-lg">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Activity Chart --}}
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">
                        Weekly Activity (Room / Vehicle / DocPac / Guestbook)
                    </h3>

                    {{-- IMPORTANT: Livewire should not re-render this --}}
                    <div class="h-64" wire:ignore>
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>

                {{-- Status Distribution --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Status Distribution</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Approved</span>
                                <span class="text-sm font-bold text-gray-900">65%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gray-900 h-2 rounded-full" style="width: 65%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Pending</span>
                                <span class="text-sm font-bold text-gray-900">25%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gray-600 h-2 rounded-full" style="width: 25%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Rejected</span>
                                <span class="text-sm font-bold text-gray-900">10%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gray-400 h-2 rounded-full" style="width: 10%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-600 mb-1">Total Requests This Month</p>
                        <p class="text-2xl font-bold text-gray-900">247</p>
                        <p class="text-xs text-gray-600 mt-1">↑ 18% from last month</p>
                    </div>
                </div>
            </div>

            {{-- Grid: 4 boxes --}}
            <section class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-4 gap-6">
                {{-- Booking Room --}}
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b">
                        <h3 class="text-base font-semibold text-gray-900">Newest Booking Room</h3>
                        <p class="text-sm text-gray-500">5 data terbaru</p>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse($latestBookingRooms as $b)
                            <li class="px-5 py-3">
                                <p class="font-medium text-gray-900">{{ $b['title'] }}</p>
                                <p class="text-sm text-gray-500">{{ $b['time'] }} • {{ $b['date'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">Status: {{ $b['status'] }}</p>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-center text-sm text-gray-500">
                                Tidak ada booking terbaru.
                            </li>
                        @endforelse
                    </ul>
                    <div class="px-5 py-3 border-t text-right">
                        <a href="{{ route('receptionist.bookings') }}"
                           class="text-sm font-medium text-gray-700 hover:text-gray-900">
                            Lihat semua →
                        </a>
                    </div>
                </div>

                {{-- Vehicle Booking --}}
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b">
                        <h3 class="text-base font-semibold text-gray-900">Newest Vehicle Booking</h3>
                        <p class="text-sm text-gray-500">5 data terbaru</p>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse($latestVehicleBookings as $v)
                            <li class="px-5 py-3">
                                <p class="font-medium text-gray-900">{{ $v['borrower'] }}</p>
                                <p class="text-sm text-gray-500">{{ $v['purpose'] }} • {{ $v['destination'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">Status: {{ $v['status'] }}</p>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-center text-sm text-gray-500">
                                Tidak ada peminjaman kendaraan.
                            </li>
                        @endforelse
                    </ul>
                    <div class="px-5 py-3 border-t text-right">
                        <a href="{{ route('receptionist.bookingvehicle') }}"
                           class="text-sm font-medium text-gray-700 hover:text-gray-900">
                            Lihat semua →
                        </a>
                    </div>
                </div>

                {{-- Guestbook --}}
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b">
                        <h3 class="text-base font-semibold text-gray-900">Newest Guest List</h3>
                        <p class="text-sm text-gray-500">5 data terbaru</p>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse($latestGuests as $g)
                            <li class="px-5 py-3 flex justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $g['name'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $g['purpose'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">{{ $g['date'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $g['time_in'] }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-center text-sm text-gray-500">
                                Belum ada tamu baru.
                            </li>
                        @endforelse
                    </ul>
                    <div class="px-5 py-3 border-t text-right">
                        <a href="{{ route('receptionist.guestbook') }}"
                           class="text-sm font-medium text-gray-700 hover:text-gray-900">
                            Kelola buku tamu →
                        </a>
                    </div>
                </div>

                {{-- Deliveries --}}
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b">
                        <h3 class="text-base font-semibold text-gray-900">Newest Document / Package</h3>
                        <p class="text-sm text-gray-500">5 data terbaru</p>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse($latestDocs as $d)
                            <li class="px-5 py-3">
                                <p class="font-medium text-gray-900">{{ $d['item'] }}</p>
                                <p class="text-sm text-gray-500">{{ $d['type'] }} • {{ $d['direction'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Status: {{ $d['status'] }} • {{ $d['created'] }}
                                </p>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-center text-sm text-gray-500">
                                Tidak ada dokumen atau paket terbaru.
                            </li>
                        @endforelse
                    </ul>
                    <div class="px-5 py-3 border-t text-right">
                        <a href="{{ route('receptionist.docpackstatus') }}"
                           class="text-sm font-medium text-gray-700 hover:text-gray-900">
                            Lihat semua →
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

@verbatim
<script>
    let activityChartInstance = null;

    function initActivityChart() {
        console.log('[Dashboard] initActivityChart called');

        const canvas = document.getElementById('activityChart');
        if (!canvas) {
            console.warn('[Dashboard] Canvas #activityChart not found');
            return;
        }

        const ctx = canvas.getContext('2d');

        const chartData = {
            labels:  ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            rooms:   [5, 9, 7, 12, 10, 8, 11],
            vehicles:[2, 4, 3, 6,  5,  4,  5],
            docpacs: [1, 3, 2, 4,  3,  2,  3],
            guests:  [4, 6, 5, 9,  8,  7,  9]
        };

        if (activityChartInstance) {
            console.log('[Dashboard] Destroying previous chart instance');
            activityChartInstance.destroy();
        }

        activityChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Room Bookings',
                        data: chartData.rooms,
                        borderColor: 'rgb(31, 41, 55)',
                        backgroundColor: 'rgba(31, 41, 55, 0.10)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Vehicle Bookings',
                        data: chartData.vehicles,
                        borderColor: 'rgb(107, 114, 128)',
                        backgroundColor: 'rgba(107, 114, 128, 0.10)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'DocPac',
                        data: chartData.docpacs,
                        borderColor: 'rgb(156, 163, 175)',
                        backgroundColor: 'rgba(156, 163, 175, 0.10)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Guestbook',
                        data: chartData.guests,
                        borderColor: 'rgb(55, 65, 81)',
                        backgroundColor: 'rgba(55, 65, 81, 0.10)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        console.log('[Dashboard] Chart initialized');
    }

    document.addEventListener('DOMContentLoaded', function () {
        console.log('[Dashboard] DOMContentLoaded');
        initActivityChart();
    });

    document.addEventListener('livewire:load', function () {
        console.log('[Dashboard] livewire:load');
        initActivityChart();
    });

    document.addEventListener('livewire:navigated', function () {
        console.log('[Dashboard] livewire:navigated');
        initActivityChart();
    });
</script>
@endverbatim
