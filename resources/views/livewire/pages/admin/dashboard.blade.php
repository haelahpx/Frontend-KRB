<div class="bg-gray-50" wire:poll.3500ms="tick">
    <main class="px-4 sm:px-6 py-6 space-y-5">

        {{-- HERO --}}
        <header class="rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-xl px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/20 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6V4m0 16v-2m0-10v2m0 6v2M6 12H4m16 0h-2m-10 0h2m6 0h2M9 17l-2 2M15 7l2-2M7 7l-2-2M17 17l2 2" />
                </svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-base sm:text-lg font-semibold truncate">Welcome, {{ $admin_name }}!</h2>
                <p class="text-xs text-white/80 truncate">Overview of ticketing and system activity.</p>
            </div>
        </header>

        {{-- KPI STRIP - 4 columns for tickets --}}
        <section>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($stats as $s)
                <div class="rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
                    <p class="text-[12px] text-gray-500 truncate">{{ $s['label'] }}</p>
                    <h3 class="text-xl font-semibold text-gray-900 mt-1 leading-none">{{ $s['value'] }}</h3>
                </div>
                @endforeach
            </div>
        </section>

        {{-- MAIN LAYOUT: Full Width Content --}}
        <section class="space-y-5"> {{-- This section now holds all content below KPIs --}}

            {{-- CHART & STATUS ROW (from Receptionist) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                {{-- Activity Chart --}}
                <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Weekly Ticket Activity
                        </h3>
                        <p class="text-xs text-gray-500">
                            Last 7 days (New, Closed, In Progress)
                        </p>
                    </div>
                    {{-- wire:ignore is crucial for Chart.js --}}
                    <div class="h-[180px]" wire:ignore>
                        <canvas id="adminActivityChart" class="w-full" style="max-height:180px"></canvas>
                    </div>
                </div>

                {{-- Status Distribution --}}
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Ticket Status Distribution</h3>
                    <div class="space-y-4">
                        @php
                        $totalTickets = $ticketStatusDistribution['total_count'] ?? 0;
                        @endphp
                        @foreach(['Open', 'In Progress', 'Closed'] as $status)
                        @php
                        $data = $ticketStatusDistribution[$status] ?? ['percent' => 0];
                        $color = match($status) {
                        'Open' => 'bg-red-500',
                        'In Progress' => 'bg-blue-500',
                        'Closed' => 'bg-green-500',
                        default => 'bg-gray-400',
                        };
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-medium text-gray-700">{{ $status }}</span>
                                <span class="text-xs font-bold text-gray-900">{{ $data['percent'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="{{ $color }} h-1.5 rounded-full" style="width: {{ $data['percent'] }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-600 mb-0.5">Total Active Tickets</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($totalTickets) }}</p>
                    </div>
                </div>
            </div>

        </section>
    </main>

    {{-- Chart.js v4 integration for Admin Activity --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    @verbatim
    <script>
        let __adminActivityChart;

        function rebuildAdminActivityChart(weeklyData) {
            const canvas = document.getElementById('adminActivityChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            const defaultWeekly = {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                new: [15, 20, 18, 25, 22, 10, 14],
                closed: [12, 15, 16, 20, 18, 9, 11],
                in_progress: [5, 8, 7, 10, 9, 4, 6],
            };
            const weekly = weeklyData && weeklyData.labels ? weeklyData : defaultWeekly;

            if (__adminActivityChart) {
                __adminActivityChart.destroy();
            }

            const paletteLine = {
                new: '#1d4ed8',
                closed: '#059669',
                in_progress: '#f59e0b',
            };

            const datasets = [{
                    label: 'New',
                    data: weekly.new,
                    borderColor: paletteLine.new,
                    tension: 0.35,
                    pointRadius: 3,
                    fill: false,
                },
                {
                    label: 'Closed',
                    data: weekly.closed,
                    borderColor: paletteLine.closed,
                    tension: 0.35,
                    pointRadius: 3,
                    fill: false,
                },
                {
                    label: 'In Progress',
                    data: weekly.in_progress,
                    borderColor: paletteLine.in_progress,
                    tension: 0.35,
                    pointRadius: 3,
                    fill: false,
                },
            ];

            __adminActivityChart = new Chart(ctx, {
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
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                            },
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
                                stepSize: 5,
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

        // Listen for the Livewire custom event `admin-chart-updated`
        document.addEventListener('admin-chart-updated', function(event) {
            const weeklyData = event.detail.weeklyData;
            rebuildAdminActivityChart(weeklyData);
        });

        // Initial render when the page is loaded or after Livewire navigated
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Livewire !== 'undefined' && !Livewire.firstVisit) {
                rebuildAdminActivityChart(@json($weeklyTicketActivity));
            }
        });
    </script>

    @endverbatim
</div>