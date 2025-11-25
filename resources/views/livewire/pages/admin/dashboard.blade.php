<div class="bg-gray-50" wire:poll.2000ms.keep-alive>
    <main class="px-4 sm:px-6 py-6">
        <div class="space-y-8">

            {{-- Greeting --}}
            <div class="rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white p-6 shadow-2xl">
                <h2 class="text-lg font-semibold">Selamat Datang di Dashboard Admin</h2>
                <p class="text-sm text-gray-300">
                    Berikut total data 7 hari terakhir dari modul **Tiket, Room Bookings, dan Information**
                    yang terkait dengan **Perusahaan** dan **Departemen** Anda.
                </p>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Total Tickets --}}
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Tickets Created (7 hari)</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $weeklyTicketsCount }}
                            </p>
                            <p class="text-xs text-green-600 mt-1">Status change vs last week (dummy)</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-lg">
                            <x-heroicon-o-ticket class="w-6 h-6 text-gray-700" />
                        </div>
                    </div>
                </div>

                {{-- Total Room Bookings --}}
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Room Bookings (7 hari)</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $weeklyRoomBookingsCount }}
                            </p>
                            <p class="text-xs text-green-600 mt-1">Status change vs last week (dummy)</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-lg">
                            <x-heroicon-o-calendar-days class="w-6 h-6 text-gray-700" />
                        </div>
                    </div>
                </div>

                {{-- Total Information Entries --}}
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Information Entries (7 hari)</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $weeklyInformationCount }}
                            </p>
                            <p class="text-xs text-green-600 mt-1">Status change vs last week (dummy)</p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-lg">
                            <x-heroicon-o-document-text class="w-6 h-6 text-gray-700" />
                        </div>
                    </div>
                </div>

                {{-- Top Agent --}}
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Top Agent (Solved Tickets)</p>
                            <p class="text-xl font-bold text-gray-900 truncate" title="{{ $topAgent->full_name ?? 'N/A' }}">
                                {{ $topAgent->full_name ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-700 mt-1">
                                Solved: <span class="font-semibold text-gray-900">{{ $topAgent->solved_count ?? 0 }}</span>
                            </p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-lg">
                            <x-heroicon-o-star class="w-6 h-6 text-gray-700" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Activity Chart --}}
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <x-heroicon-o-chart-bar class="h-5 w-5 text-gray-900" />
                            Weekly Activity – Ticket / Room Bookings / Information
                        </h3>
                        <p class="text-xs text-gray-500">
                            7 hari terakhir (dummy data)
                        </p>
                    </div>

                    <div class="h-[320px]" wire:ignore>
                        <canvas id="activityChart" class="w-full" style="max-height:320px"></canvas>
                    </div>
                </div>

                {{-- Status Distribution (Tickets Priority) --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Ticket Priority Distribution</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">High Priority</span>
                                <span class="text-sm font-bold text-gray-900">{{ $approvedPercent }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full" style="width: {{ $approvedPercent }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Medium Priority</span>
                                <span class="text-sm font-bold text-gray-900">{{ $pendingPercent }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $pendingPercent }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Low Priority</span>
                                <span class="text-sm font-bold text-gray-900">{{ $rejectedPercent }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $rejectedPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-600 mb-1">Total Tickets This Month</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalTicketsThisMonth }}</p>
                        <p class="text-xs text-gray-600 mt-1">↑ % from last month (dummy)</p>
                    </div>
                </div>
            </div>

        </div>
    </main>

    {{-- Chart.js v4 like in report page --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    @verbatim
        <script>
            let __activityChart;

            function rebuildActivityChart() {
                const canvas = document.getElementById('activityChart');
                if (!canvas) return;

                const ctx = canvas.getContext('2d');

                // Dummy weekly data – replace with dynamic data later
                const weekly = {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    ticket: [8, 10, 7, 15, 12, 5, 9],
                    room: [3, 5, 4, 8, 6, 2, 5],
                    information: [2, 3, 1, 5, 4, 1, 3],
                };

                if (__activityChart) {
                    __activityChart.destroy();
                }

                const paletteLine = {
                    ticket: '#1d4ed8', // blue
                    room: '#059669', // emerald
                    information: '#f59e0b', // amber
                };

                const datasets = [
                    {
                        label: 'Tickets',
                        data: weekly.ticket,
                        borderColor: paletteLine.ticket,
                        tension: 0.35,
                        pointRadius: 3,
                        fill: false,
                    },
                    {
                        label: 'Room Bookings',
                        data: weekly.room,
                        borderColor: paletteLine.room,
                        tension: 0.35,
                        pointRadius: 3,
                        fill: false,
                    },
                    {
                        label: 'Information',
                        data: weekly.information,
                        borderColor: paletteLine.information,
                        tension: 0.35,
                        pointRadius: 3,
                        fill: false,
                    },
                ];

                __activityChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: weekly.labels,
                        datasets: datasets,
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    stepSize: 1,
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.05)',
                                },
                            },
                            x: {
                                grid: {
                                    display: false,
                                },
                            },
                        },
                        interaction: {
                            mode: 'nearest',
                            intersect: false,
                        },
                    },
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                rebuildActivityChart();
            });

            document.addEventListener('livewire:load', () => {
                rebuildActivityChart();
            });

            document.addEventListener('livewire:navigated', () => {
                rebuildActivityChart();
            });
        </script>
    @endverbatim
</div>