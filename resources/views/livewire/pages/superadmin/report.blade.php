<div class="min-h-screen bg-gray-50">
    <!-- Download overlay -->
    <div id="downloadOverlay" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative h-full w-full flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-xs text-center">
                <div class="mx-auto mb-3 h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-gray-900"></div>
                <p class="font-semibold text-gray-900">Menyiapkan PDF…</p>
                <p class="text-xs text-gray-500 mt-1">Tunggu sampai dialog download muncul.</p>
                <button id="hideOverlay" type="button" class="mt-4 text-xs text-gray-600 underline">Sembunyikan</button>
            </div>
        </div>
    </div>

    <main class="px-4 sm:px-6 py-6 space-y-8">

        {{-- Header with company --}}
        <div class="rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white p-6 sm:p-8 shadow-2xl">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    @if(!empty($company['image']))
                        <img src="{{ $company['image'] }}" alt="Logo" class="h-10 w-10 rounded-full object-cover border border-white/20">
                    @endif
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">
                            {{ $company['company_name'] ?? '—' }} — Reports & Evaluation
                        </h2>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <select wire:model.live="year"
                        class="h-10 rounded-xl border-2 border-white/20 bg-white/10 text-white px-3">
                        @for($y = now()->year; $y >= now()->year - 9; $y--)
                            <option value="{{ $y }}" class="text-gray-900">{{ $y }}</option>
                        @endfor
                    </select>

                    <button id="downloadPdfBtn"
                        class="px-4 py-2 text-sm rounded-xl bg-white text-gray-900 hover:bg-gray-100 font-semibold shadow inline-flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg id="btnSpinner" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25" stroke-width="4"></circle>
                            <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4"></path>
                        </svg>
                        <span id="btnLabel">Download PDF</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <p class="text-sm text-gray-500">Selected Year</p>
                <h3 class="text-2xl font-semibold text-gray-900">{{ $summary['selected_year'] }}</h3>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <p class="text-sm text-gray-500">Total Activity (Room + Vehicle + Ticket + Guestbook + Delivery)</p>
                <h3 class="text-2xl font-semibold text-gray-900">{{ number_format($summary['total_activity']) }}</h3>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <p class="text-sm text-gray-500">Busiest Month</p>
                <h3 class="text-2xl font-semibold text-gray-900">
                    {{ $summary['busiest_month'] }} ({{ number_format($summary['busiest_total']) }})
                </h3>
            </div>
        </section>

        {{-- Quick Evaluation --}}
        <section class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Quick Evaluation</h3>
            <ul class="text-sm text-gray-700 list-disc pl-5 space-y-1">
                <li>Company: <strong>{{ $company['company_name'] ?? '—' }}</strong></li>
                <li>
                    Year {{ $summary['selected_year'] }} recorded
                    <strong>{{ number_format($summary['total_activity']) }}</strong> combined activities.
                </li>
                <li>Peak month is <strong>{{ $summary['busiest_month'] }}</strong>.</li>
                <li>
                    Ticket growth vs previous year:
                    <strong>
                        @php $g = $summary['growth_yoy']['ticket'] ?? null; @endphp
                        {{ is_null($g) ? 'n/a' : ($g . '%') }}
                    </strong>.
                </li>
            </ul>
        </section>

        {{-- Charts --}}
        <section class="grid grid-cols-1 2xl:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">
                    Monthly Activity – {{ $year }}
                </h3>
                <div wire:ignore>
                    <canvas id="monthlyChart" wire:key="monthly-{{ $year }}" class="w-full" style="max-height:420px"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">
                    Yearly Totals – Last {{ $yearsBack }} Years
                </h3>
                <div wire:ignore>
                    <canvas id="yearlyChart" wire:key="yearly-{{ $year }}" class="w-full" style="max-height:420px"></canvas>
                </div>
            </div>
        </section>

        {{-- Data Table --}}
        <section class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Numbers (Monthly)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-600">
                            <th class="py-2 pr-4">Month</th>
                            <th class="py-2 pr-4">Room</th>
                            <th class="py-2 pr-4">Vehicle</th>
                            <th class="py-2 pr-4">Ticket</th>
                            <th class="py-2 pr-4">Guestbook</th>
                            <th class="py-2 pr-4">Delivery</th>
                            <th class="py-2 pr-4">Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800">
                        @foreach($monthly['labels'] as $i => $m)
                            <tr class="border-t">
                                <td class="py-2 pr-4">{{ $m }}</td>
                                <td class="py-2 pr-4">{{ $monthly['room'][$i] }}</td>
                                <td class="py-2 pr-4">{{ $monthly['vehicle'][$i] }}</td>
                                <td class="py-2 pr-4">{{ $monthly['ticket'][$i] }}</td>
                                <td class="py-2 pr-4">{{ $monthly['guestbook'][$i] }}</td>
                                <td class="py-2 pr-4">{{ $monthly['delivery'][$i] }}</td>
                                <td class="py-2 pr-4">
                                    {{ $monthly['room'][$i] + $monthly['vehicle'][$i] + $monthly['ticket'][$i] + $monthly['guestbook'][$i] + $monthly['delivery'][$i] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        let __monthlyChart, __yearlyChart;

        function rebuildCharts(monthly, yearly) {
            if (__monthlyChart) __monthlyChart.destroy();
            if (__yearlyChart) __yearlyChart.destroy();

            const paletteLine = {
                room: '#1d4ed8',
                vehicle: '#059669',
                ticket: '#dc2626',
                guestbook: '#7c3aed',
                delivery: '#f59e0b',
            };
            const paletteFill = {
                room: '#1d4ed8',
                vehicle: '#059669',
                ticket: '#dc2626',
                guestbook: '#7c3aed',
                delivery: '#f59e0b',
            };

            // Monthly
            const mctx = document.getElementById('monthlyChart')?.getContext('2d');
            if (mctx) {
                const keys = ['room','vehicle','ticket','guestbook','delivery'];
                const mDatasets = keys.filter(k => Array.isArray(monthly[k])).map(k => ({
                    label: k.charAt(0).toUpperCase() + k.slice(1),
                    data: monthly[k],
                    borderColor: paletteLine[k],
                    tension: .35,
                    pointRadius: 3,
                    fill: false
                }));
                __monthlyChart = new Chart(mctx, {
                    type: 'line',
                    data: { labels: monthly.labels, datasets: mDatasets },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { position: 'top' } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } }, x: { grid: { display: false } } }
                    }
                });
            }

            // Yearly
            const yctx = document.getElementById('yearlyChart')?.getContext('2d');
            if (yctx) {
                const keys = ['room','vehicle','ticket','guestbook','delivery'];
                const yDatasets = keys.filter(k => Array.isArray(yearly[k])).map(k => ({
                    label: k.charAt(0).toUpperCase() + k.slice(1),
                    data: yearly[k],
                    backgroundColor: paletteFill[k],
                }));
                __yearlyChart = new Chart(yctx, {
                    type: 'bar',
                    data: { labels: yearly.labels, datasets: yDatasets },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { position: 'top' } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } } }
                    }
                });
            }
        }

        // Initial render
        document.addEventListener('DOMContentLoaded', () => {
            rebuildCharts(@json($monthly), @json($yearly));
        });

        // Livewire payload
        document.addEventListener('report-data-updated', (e) => {
            const { monthly, yearly } = e.detail || {};
            if (monthly && yearly) rebuildCharts(monthly, yearly);
        });

        // PDF helpers
        function canvasToDataURL(id) {
            const c = document.getElementById(id);
            if (!c) return null;
            try { return c.toDataURL('image/png', 1.0); } catch (e) { return null; }
        }

        // ——— LOADING STATE HELPERS ———
        const dlBtn = document.getElementById('downloadPdfBtn');
        const dlOverlay = document.getElementById('downloadOverlay');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnLabel = document.getElementById('btnLabel');
        const hideOverlayBtn = document.getElementById('hideOverlay');

        function setDownloading(state) {
            if (!dlBtn) return;
            if (state) {
                dlBtn.disabled = true;
                btnSpinner?.classList.remove('hidden');
                btnLabel && (btnLabel.textContent = 'Menyiapkan…');
                dlOverlay?.classList.remove('hidden');
            } else {
                dlBtn.disabled = false;
                btnSpinner?.classList.add('hidden');
                btnLabel && (btnLabel.textContent = 'Download PDF');
                dlOverlay?.classList.add('hidden');
            }
        }
        hideOverlayBtn?.addEventListener('click', () => setDownloading(false));

        // PDF button
        dlBtn?.addEventListener('click', async () => {
            const monthly_img = canvasToDataURL('monthlyChart');
            const yearly_img  = canvasToDataURL('yearlyChart');

            setDownloading(true);
            try {
                await @this.call('exportPdf', { monthly_img, yearly_img });
            } catch (e) {
                console.error(e);
            } finally {
                // beri jeda kecil supaya dialog download sempat tampil
                setTimeout(() => setDownloading(false), 1200);
            }
        });

        // Pastikan overlay tertutup jika ada error global dari Livewire
        window.addEventListener('livewire:error', () => setDownloading(false));
    </script>
</div>
