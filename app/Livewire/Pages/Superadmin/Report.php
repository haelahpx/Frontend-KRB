<?php

namespace App\Livewire\Pages\Superadmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\BookingRoom;
use App\Models\VehicleBooking;
use App\Models\Ticket;
use App\Models\Guestbook;
use App\Models\Delivery;
use App\Models\Company;

#[Layout('layouts.superadmin')]
#[Title('Reports & Evaluation')]
class Report extends Component
{
    public int $year;
    public int $yearsBack = 5;

    public array $monthly = [];
    public array $yearly = [];
    public array $summary = [];
    public array $analysis = [];

    public ?int $companyId = null;
    public ?array $company = null; // ['company_name' => ..., 'image' => ...]

    public function mount(): void
    {
        $this->companyId = Auth::user()->company_id ?? null;

        // Company header data
        $co = $this->companyId ? Company::find($this->companyId) : null;
        $imageUrl = null;
        if ($co && $co->image) {
            $imageUrl = str_starts_with($co->image, 'http')
                ? $co->image
                : (Storage::disk(config('filesystems.default'))->exists($co->image)
                    ? Storage::url($co->image)
                    : $co->image);
        }
        $this->company = [
            'company_name' => $co->company_name ?? '—',
            'image' => $imageUrl,
        ];

        $this->year = (int) now()->year;
        $this->buildData();

        $this->dispatch('report-data-updated', monthly: $this->monthly, yearly: $this->yearly);
    }

    public function updatedYear(): void
    {
        $this->buildData();
        $this->dispatch('report-data-updated', monthly: $this->monthly, yearly: $this->yearly);
    }

    /** Scope queries by company_id if the table has that column */
    private function scoped($query)
    {
        if ($this->companyId) {
            $table = $query->getModel()->getTable();
            if (Schema::hasColumn($table, 'company_id')) {
                $query->where($table . '.company_id', $this->companyId);
            }
        }
        return $query;
    }

    /** -------- MAIN DATA BUILDER (this was missing) -------- */
    private function buildData(): void
    {
        $months = range(1, 12);
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Monthly
        $room = $this->scoped(BookingRoom::query())
            ->selectRaw('MONTH(created_at) m, COUNT(*) c')
            ->whereYear('created_at', $this->year)
            ->groupBy('m')->pluck('c', 'm')->toArray();

        $veh = $this->scoped(VehicleBooking::query())
            ->selectRaw('MONTH(created_at) m, COUNT(*) c')
            ->whereYear('created_at', $this->year)
            ->groupBy('m')->pluck('c', 'm')->toArray();

        $tick = $this->scoped(Ticket::query())
            ->selectRaw('MONTH(created_at) m, COUNT(*) c')
            ->whereYear('created_at', $this->year)
            ->groupBy('m')->pluck('c', 'm')->toArray();

        $gb = $this->scoped(Guestbook::query())
            ->selectRaw('MONTH(created_at) m, COUNT(*) c')
            ->whereYear('created_at', $this->year)
            ->groupBy('m')->pluck('c', 'm')->toArray();

        $del = $this->scoped(Delivery::query())
            ->selectRaw('MONTH(created_at) m, COUNT(*) c')
            ->whereYear('created_at', $this->year)
            ->groupBy('m')->pluck('c', 'm')->toArray();

        $roomArr = array_map(fn($i) => (int) ($room[$i] ?? 0), $months);
        $vehArr = array_map(fn($i) => (int) ($veh[$i] ?? 0), $months);
        $tickArr = array_map(fn($i) => (int) ($tick[$i] ?? 0), $months);
        $gbArr = array_map(fn($i) => (int) ($gb[$i] ?? 0), $months);
        $delArr = array_map(fn($i) => (int) ($del[$i] ?? 0), $months);

        $this->monthly = [
            'labels' => $labels,
            'room' => $roomArr,
            'vehicle' => $vehArr,
            'ticket' => $tickArr,
            'guestbook' => $gbArr,
            'delivery' => $delArr,
        ];

        // Yearly
        $startYear = $this->year - ($this->yearsBack - 1);
        $years = range($startYear, $this->year);
        $startDate = Carbon::create($startYear, 1, 1, 0, 0, 0);
        $endDate = Carbon::create($this->year, 12, 31, 23, 59, 59);

        $yrRoom = $this->scoped(BookingRoom::query())
            ->selectRaw('YEAR(created_at) y, COUNT(*) c')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('y')->pluck('c', 'y')->toArray();

        $yrVeh = $this->scoped(VehicleBooking::query())
            ->selectRaw('YEAR(created_at) y, COUNT(*) c')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('y')->pluck('c', 'y')->toArray();

        $yrTick = $this->scoped(Ticket::query())
            ->selectRaw('YEAR(created_at) y, COUNT(*) c')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('y')->pluck('c', 'y')->toArray();

        $yrGb = $this->scoped(Guestbook::query())
            ->selectRaw('YEAR(created_at) y, COUNT(*) c')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('y')->pluck('c', 'y')->toArray();

        $yrDel = $this->scoped(Delivery::query())
            ->selectRaw('YEAR(created_at) y, COUNT(*) c')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('y')->pluck('c', 'y')->toArray();

        $this->yearly = [
            'labels' => array_map(fn($y) => (string) $y, $years),
            'room' => array_map(fn($y) => (int) ($yrRoom[$y] ?? 0), $years),
            'vehicle' => array_map(fn($y) => (int) ($yrVeh[$y] ?? 0), $years),
            'ticket' => array_map(fn($y) => (int) ($yrTick[$y] ?? 0), $years),
            'guestbook' => array_map(fn($y) => (int) ($yrGb[$y] ?? 0), $years),
            'delivery' => array_map(fn($y) => (int) ($yrDel[$y] ?? 0), $years),
        ];

        // Summary + analysis
        $totRoom = array_sum($roomArr);
        $totVeh = array_sum($vehArr);
        $totTick = array_sum($tickArr);
        $totGb = array_sum($gbArr);
        $totDel = array_sum($delArr);
        $totAll = $totRoom + $totVeh + $totTick + $totGb + $totDel;

        $idx = range(0, 11);
        $totalsPerMonth = array_map(
            fn($i) => $roomArr[$i] + $vehArr[$i] + $tickArr[$i] + $gbArr[$i] + $delArr[$i],
            $idx
        );

        $bestIndex = $this->maxIndex($totalsPerMonth);
        $worstIndex = $this->minIndex($totalsPerMonth);

        $prevYear = $this->year - 1;
        $prevTotals = [
            'room' => $this->scoped(BookingRoom::query())->whereYear('created_at', $prevYear)->count(),
            'vehicle' => $this->scoped(VehicleBooking::query())->whereYear('created_at', $prevYear)->count(),
            'ticket' => $this->scoped(Ticket::query())->whereYear('created_at', $prevYear)->count(),
            'guestbook' => $this->scoped(Guestbook::query())->whereYear('created_at', $prevYear)->count(),
            'delivery' => $this->scoped(Delivery::query())->whereYear('created_at', $prevYear)->count(),
        ];

        $growthYoY = [
            'room' => $this->pct($totRoom, $prevTotals['room']),
            'vehicle' => $this->pct($totVeh, $prevTotals['vehicle']),
            'ticket' => $this->pct($totTick, $prevTotals['ticket']),
            'guestbook' => $this->pct($totGb, $prevTotals['guestbook']),
            'delivery' => $this->pct($totDel, $prevTotals['delivery']),
        ];
        $growthYoY['overall'] = $this->pct($totAll, array_sum($prevTotals));

        $this->summary = [
            'selected_year' => $this->year,
            'total_activity' => $totAll,
            'busiest_month' => $labels[$bestIndex] ?? '-',
            'busiest_total' => $totalsPerMonth[$bestIndex] ?? 0,
            'quietest_month' => $labels[$worstIndex] ?? '-',
            'quietest_total' => $totalsPerMonth[$worstIndex] ?? 0,
            'growth_yoy' => $growthYoY,
        ];

        $momOverall = $this->momSeries($totalsPerMonth);
        $this->analysis = [
            'kpi' => [
                'total_room' => $totRoom,
                'total_vehicle' => $totVeh,
                'total_ticket' => $totTick,
                'total_guestbook' => $totGb,
                'total_delivery' => $totDel,
                'overall_total' => $totAll,
                'avg_per_month' => round($totAll / 12, 2),
                'growth_yoy' => $growthYoY,
            ],
            'peaks' => [
                'best' => ['label' => $labels[$bestIndex] ?? '-', 'value' => $totalsPerMonth[$bestIndex] ?? 0],
                'worst' => ['label' => $labels[$worstIndex] ?? '-', 'value' => $totalsPerMonth[$worstIndex] ?? 0],
            ],
            'mom' => [
                'overall' => $momOverall,
                'room' => $this->momSeries($roomArr),
                'vehicle' => $this->momSeries($vehArr),
                'ticket' => $this->momSeries($tickArr),
                'guestbook' => $this->momSeries($gbArr),
                'delivery' => $this->momSeries($delArr),
            ],
            'volatility' => [
                'room_cv' => round($this->coefVar($roomArr), 2),
                'vehicle_cv' => round($this->coefVar($vehArr), 2),
                'ticket_cv' => round($this->coefVar($tickArr), 2),
                'guestbook_cv' => round($this->coefVar($gbArr), 2),
                'delivery_cv' => round($this->coefVar($delArr), 2),
            ],
            'moving_avg_3' => $this->movingAverage($totalsPerMonth, 3),
            'recommendations' => $this->buildRecs($growthYoY, $totalsPerMonth, $labels),
        ];
    }

    private function buildRecs(array $growthYoY, array $totalsPerMonth, array $labels): array
    {
        $recs = [];
        if (($growthYoY['ticket'] ?? 0) > 10) {
            $recs[] = 'Ticket volume grew >10% YoY — consider adding support capacity or triage automation.';
        }
        if (($growthYoY['delivery'] ?? 0) > 10) {
            $recs[] = 'Deliveries trending up — verify storage capacity and handover SOPs.';
        }
        $bestIndex = $this->maxIndex($totalsPerMonth);
        if ($bestIndex !== null) {
            $recs[] = 'Peak month ' . ($labels[$bestIndex] ?? '') . ' — plan staffing and asset availability ahead of this period.';
        }
        if (empty($recs)) {
            $recs[] = 'Utilization is relatively stable — maintain current capacity while monitoring monthly trends.';
        }
        return $recs;
    }

    // ---------- Helpers ----------
    private function pct(int|float $cur, int|float $prev): ?float
    {
        if ($prev <= 0)
            return null;
        return round((($cur - $prev) / $prev) * 100, 2);
    }

    private function momSeries(array $values): array
    {
        $out = [];
        for ($i = 1; $i < count($values); $i++) {
            $prev = $values[$i - 1];
            $cur = $values[$i];
            $out[] = $prev > 0 ? round((($cur - $prev) / $prev) * 100, 2) : null;
        }
        return $out;
    }

    private function movingAverage(array $values, int $window): array
    {
        $n = count($values);
        $out = [];
        for ($i = 0; $i < $n; $i++) {
            if ($i + 1 < $window) {
                $out[] = null;
                continue;
            }
            $slice = array_slice($values, $i + 1 - $window, $window);
            $out[] = round(array_sum($slice) / $window, 2);
        }
        return $out;
    }

    private function coefVar(array $values): float
    {
        $n = count($values);
        if ($n === 0)
            return 0.0;
        $mean = array_sum($values) / $n ?: 0.0;
        if ($mean == 0.0)
            return 0.0;
        $var = 0.0;
        foreach ($values as $v)
            $var += ($v - $mean) ** 2;
        $std = sqrt($var / $n);
        return $std / $mean;
    }

    private function maxIndex(array $values): ?int
    {
        if (!$values)
            return null;
        $max = max($values);
        return array_search($max, $values, true);
    }

    private function minIndex(array $values): ?int
    {
        if (!$values)
            return null;
        $min = min($values);
        return array_search($min, $values, true);
    }

    // Turn a logo path/URL into data URI for Dompdf
    private function imageToDataUri(?string $pathOrUrl): ?string
    {
        if (!$pathOrUrl)
            return null;
        try {
            if (!str_starts_with($pathOrUrl, 'http')) {
                $candidate = public_path($pathOrUrl);
                if (!is_file($candidate)) {
                    $candidate = Storage::disk(config('filesystems.default'))->path($pathOrUrl);
                }
                if (is_file($candidate)) {
                    $bin = file_get_contents($candidate);
                    $mime = mime_content_type($candidate) ?: 'image/png';
                    return 'data:' . $mime . ';base64,' . base64_encode($bin);
                }
            }
            $ctx = stream_context_create([
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
                'http' => ['timeout' => 5, 'follow_location' => 1],
            ]);
            $bin = @file_get_contents($pathOrUrl, false, $ctx);
            if ($bin === false)
                return null;
            $ext = strtolower(pathinfo(parse_url($pathOrUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'jpg', 'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'webp' => 'image/webp',
                default => 'image/png',
            };
            return 'data:' . $mime . ';base64,' . base64_encode($bin);
        } catch (\Throwable) {
            return null;
        }
    }

    // PDF
    public function exportPdf(array $payload = [])
    {
        $monthlyImg = $payload['monthly_img'] ?? null;
        $yearlyImg = $payload['yearly_img'] ?? null;

        $data = [
            'year' => $this->year,
            'monthly' => $this->monthly,
            'yearly' => $this->yearly,
            'summary' => $this->summary,
            'analysis' => $this->analysis,
            'company' => $this->company,
            'company_logo_datauri' => $this->imageToDataUri($this->company['image'] ?? null),
            'img' => ['monthly' => $monthlyImg, 'yearly' => $yearlyImg],
            'generated_by' => Auth::user()->full_name ?? 'System',
            'generated_at' => now()->format('d M Y H:i'),
        ];

        $pdf = app('dompdf.wrapper')->setPaper('a4', 'portrait');
        $pdf->getDomPDF()->setHttpContext(stream_context_create([
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
        ]));
        $pdf->loadView('livewire.pages.superadmin.report-pdf', $data);

        return response()->streamDownload(fn() => print ($pdf->output()), "Report-{$this->year}.pdf");
    }

    public function render()
    {
        return view('livewire.pages.superadmin.report', [
            'year' => $this->year,
            'yearsBack' => $this->yearsBack,
            'monthly' => $this->monthly,
            'yearly' => $this->yearly,
            'summary' => $this->summary,
            'company' => $this->company,
        ]);
    }
}
