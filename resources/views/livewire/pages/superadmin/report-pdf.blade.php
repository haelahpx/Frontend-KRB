<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $company['company_name'] ?? 'Company' }} — Operational Analytics Report — {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 2.5cm 3cm 2.5cm 3cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            font-size: 12pt;
            line-height: 1.5;
            padding: 0 20px;
        }

        .page-content {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header */
        .report-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .logo-container {
            margin-bottom: 15px;
        }

        .logo {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .report-title {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .report-meta {
            font-size: 11pt;
            line-height: 1.6;
        }

        /* Section Headers */
        .section {
            margin-bottom: 30px;
        }

        .section-header {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 12px;
            margin-top: 20px;
        }

        .subsection-header {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
            margin-top: 15px;
        }

        /* Paragraphs */
        p {
            text-align: justify;
            margin-bottom: 10px;
        }

        /* Summary Stats */
        .summary-paragraph {
            text-indent: 40px;
            margin-bottom: 12px;
        }

        .inline-stat {
            font-weight: bold;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11pt;
        }

        th {
            background: #000;
            color: #fff;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
        }

        td {
            border: 1px solid #000;
            padding: 8px;
        }

        tbody tr:nth-child(even) {
            background: #f5f5f5;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .table-label {
            font-weight: bold;
        }

        .table-caption {
            font-size: 10pt;
            font-style: italic;
            text-align: center;
            margin-top: 5px;
            margin-bottom: 20px;
        }

        /* Charts */
        .chart-figure {
            margin: 20px 0;
            text-align: center;
        }

        .chart-img {
            max-width: 100%;
            height: auto;
            border: 1px solid #000;
            margin: 10px auto;
            display: block;
        }

        .figure-caption {
            font-size: 10pt;
            font-style: italic;
            margin-top: 8px;
        }

        /* Info Box */
        .note-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 15px 0;
            background: #f9f9f9;
            font-size: 10pt;
        }

        /* Page Break */
        .page-break {
            page-break-before: always;
        }

        /* Footer */
        .document-footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #000;
            font-size: 9pt;
            text-align: center;
        }

        /* List styling */
        ul {
            margin-left: 40px;
            margin-bottom: 12px;
        }

        li {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="page-content">

        <!-- Document Header -->
        <div class="report-header">
            @if(!empty($company_logo_datauri))
                <div class="logo-container">
                    <img src="{{ $company_logo_datauri }}" alt="Logo" class="logo">
                </div>
            @elseif(!empty($company['image']))
                <div class="logo-container">
                    <img src="{{ $company['image'] }}" alt="Logo" class="logo">
                </div>
            @endif
            <div class="company-name">{{ $company['company_name'] ?? 'Company' }}</div>
            <div class="report-title">Operational Analytics Report</div>
            <div class="report-meta">
                Reporting Period: {{ $year }}<br>
                Prepared by: {{ $generated_by }}<br>
                Date: {{ $generated_at }}
            </div>
        </div>

        <!-- Executive Summary -->
        <div class="section">
            <h1 class="section-header">1. EXECUTIVE SUMMARY</h1>

            <p class="summary-paragraph">
                This report presents a comprehensive analysis of operational activities for
                <strong>{{ $company['company_name'] ?? 'the Company' }}</strong> during the fiscal year
                <strong>{{ $year }}</strong>. The analysis encompasses five primary operational categories: Room
                Bookings, Vehicle Rentals, Support Tickets, Guestbook Entries, and Delivery Services.
            </p>

            <p class="summary-paragraph">
                During the reporting period, the organization recorded a total of <span
                    class="inline-stat">{{ number_format($analysis['kpi']['overall_total'] ?? 0) }}</span> transactions
                across all operational categories. This represents an average monthly activity level of <span
                    class="inline-stat">{{ number_format($analysis['kpi']['avg_per_month'] ?? 0, 2) }}</span>
                transactions.
                @php $gy = $analysis['kpi']['growth_yoy']['overall'] ?? null; @endphp
                @if(!is_null($gy))
                    Compared to the previous fiscal year ({{ $year - 1 }}), overall activity demonstrated a year-over-year
                    growth rate of <span class="inline-stat">{{ $gy }}%</span>.
                @else
                    Year-over-year growth data compared to {{ $year - 1 }} is not available for comparative analysis.
                @endif
            </p>

            <h2 class="subsection-header">1.1 Performance by Operational Category</h2>

            <p class="summary-paragraph">
                The following breakdown details the performance of each operational category during {{ $year }}:
            </p>

            <ul>
                <li><strong>Room Bookings:</strong> A total of {{ number_format($analysis['kpi']['total_room'] ?? 0) }}
                    room reservations were processed
                    @if(!is_null($analysis['kpi']['growth_yoy']['room'] ?? null))
                        , representing a {{ $analysis['kpi']['growth_yoy']['room'] }}% change from the prior year
                    @endif
                    .
                </li>
                <li><strong>Vehicle Rentals:</strong> The organization completed
                    {{ number_format($analysis['kpi']['total_vehicle'] ?? 0) }} vehicle rental transactions
                    @if(!is_null($analysis['kpi']['growth_yoy']['vehicle'] ?? null))
                        , showing {{ $analysis['kpi']['growth_yoy']['vehicle'] }}% growth year-over-year
                    @endif
                    .
                </li>
                <li><strong>Support Tickets:</strong> Customer service operations handled
                    {{ number_format($analysis['kpi']['total_ticket'] ?? 0) }} support requests
                    @if(!is_null($analysis['kpi']['growth_yoy']['ticket'] ?? null))
                        , marking a {{ $analysis['kpi']['growth_yoy']['ticket'] }}% variance from {{ $year - 1 }}
                    @endif
                    .
                </li>
                <li><strong>Guestbook Entries:</strong> {{ number_format($analysis['kpi']['total_guestbook'] ?? 0) }}
                    guestbook submissions were recorded
                    @if(!is_null($analysis['kpi']['growth_yoy']['guestbook'] ?? null))
                        , with a {{ $analysis['kpi']['growth_yoy']['guestbook'] }}% change from the previous period
                    @endif
                    .
                </li>
                <li><strong>Delivery Services:</strong> The delivery department processed
                    {{ number_format($analysis['kpi']['total_delivery'] ?? 0) }} deliveries
                    @if(!is_null($analysis['kpi']['growth_yoy']['delivery'] ?? null))
                        , reflecting {{ $analysis['kpi']['growth_yoy']['delivery'] }}% growth compared to {{ $year - 1 }}
                    @endif
                    .
                </li>
            </ul>

            <table>
                <caption class="table-caption">Table 1: Summary of Operational Performance by Category</caption>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th class="text-right">Total Transactions</th>
                        <th class="text-right">YoY Growth (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="table-label">Room Bookings</td>
                        <td class="text-right">{{ number_format($analysis['kpi']['total_room'] ?? 0) }}</td>
                        <td class="text-right">
                            {{ is_null($analysis['kpi']['growth_yoy']['room'] ?? null) ? 'N/A' : $analysis['kpi']['growth_yoy']['room'] . '%' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="table-label">Vehicle Rentals</td>
                        <td class="text-right">{{ number_format($analysis['kpi']['total_vehicle'] ?? 0) }}</td>
                        <td class="text-right">
                            {{ is_null($analysis['kpi']['growth_yoy']['vehicle'] ?? null) ? 'N/A' : $analysis['kpi']['growth_yoy']['vehicle'] . '%' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="table-label">Support Tickets</td>
                        <td class="text-right">{{ number_format($analysis['kpi']['total_ticket'] ?? 0) }}</td>
                        <td class="text-right">
                            {{ is_null($analysis['kpi']['growth_yoy']['ticket'] ?? null) ? 'N/A' : $analysis['kpi']['growth_yoy']['ticket'] . '%' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="table-label">Guestbook Entries</td>
                        <td class="text-right">{{ number_format($analysis['kpi']['total_guestbook'] ?? 0) }}</td>
                        <td class="text-right">
                            {{ is_null($analysis['kpi']['growth_yoy']['guestbook'] ?? null) ? 'N/A' : $analysis['kpi']['growth_yoy']['guestbook'] . '%' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="table-label">Deliveries</td>
                        <td class="text-right">{{ number_format($analysis['kpi']['total_delivery'] ?? 0) }}</td>
                        <td class="text-right">
                            {{ is_null($analysis['kpi']['growth_yoy']['delivery'] ?? null) ? 'N/A' : $analysis['kpi']['growth_yoy']['delivery'] . '%' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Trend Analysis -->
        <div class="section">
            <h1 class="section-header">2. TREND ANALYSIS</h1>

            <p class="summary-paragraph">
                This section presents visual representations of operational activity patterns throughout the reporting
                period. Figure 1 illustrates the monthly distribution of activities during {{ $year }}, while Figure 2
                provides a multi-year comparative analysis spanning the last {{ count($yearly['labels'] ?? []) }} years.
            </p>

            @if(!empty($img['monthly']))
                <div class="chart-figure">
                    <img src="{{ $img['monthly'] }}" class="chart-img">
                    <p class="figure-caption">Figure 1: Monthly Activity Distribution for {{ $year }}</p>
                </div>
            @endif

            @if(!empty($img['yearly']))
                <div class="chart-figure">
                    <img src="{{ $img['yearly'] }}" class="chart-img">
                    <p class="figure-caption">Figure 2: Year-over-Year Comparative Analysis
                        ({{ count($yearly['labels'] ?? []) }}-Year Period)</p>
                </div>
            @endif

            <p class="summary-paragraph">
                The monthly trend data reveals patterns in operational activity throughout the fiscal year. Analysis of
                these trends provides insights into seasonal variations, peak operational periods, and potential areas
                requiring resource allocation adjustments.
            </p>
        </div>

        <!-- Month-over-Month Growth -->
        <div class="section">
            <h1 class="section-header">3. MONTH-OVER-MONTH GROWTH ANALYSIS</h1>

            <p class="summary-paragraph">
                The following table presents percentage changes in activity levels compared to the immediately preceding
                month. This metric provides insight into short-term operational momentum and helps identify emerging
                trends or anomalies in operational performance.
            </p>

            @php
                $mom = $analysis['mom'] ?? [];
                $labels = $monthly['labels'] ?? [];
                $pad = fn($arr) => array_merge(['—'], $arr);
            @endphp

            <table>
                <caption class="table-caption">Table 2: Month-over-Month Growth Percentages by Category</caption>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-right">Overall</th>
                        <th class="text-right">Room</th>
                        <th class="text-right">Vehicle</th>
                        <th class="text-right">Ticket</th>
                        <th class="text-right">Guestbook</th>
                        <th class="text-right">Delivery</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($labels as $i => $m)
                        <tr>
                            <td class="table-label">{{ $m }}</td>
                            <td class="text-right">@php $v = $pad($mom['overall'] ?? [])[$i] ?? '—'; @endphp
                                {{ is_null($v) ? 'N/A' : $v . '%' }}</td>
                            <td class="text-right">@php $v = $pad($mom['room'] ?? [])[$i] ?? '—'; @endphp
                                {{ is_null($v) ? 'N/A' : $v . '%' }}</td>
                            <td class="text-right">@php $v = $pad($mom['vehicle'] ?? [])[$i] ?? '—'; @endphp
                                {{ is_null($v) ? 'N/A' : $v . '%' }}</td>
                            <td class="text-right">@php $v = $pad($mom['ticket'] ?? [])[$i] ?? '—'; @endphp
                                {{ is_null($v) ? 'N/A' : $v . '%' }}</td>
                            <td class="text-right">@php $v = $pad($mom['guestbook'] ?? [])[$i] ?? '—'; @endphp
                                {{ is_null($v) ? 'N/A' : $v . '%' }}</td>
                            <td class="text-right">@php $v = $pad($mom['delivery'] ?? [])[$i] ?? '—'; @endphp
                                {{ is_null($v) ? 'N/A' : $v . '%' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Moving Average -->
        <div class="section">
            <h1 class="section-header">4. STABILITY ANALYSIS AND MOVING AVERAGE</h1>

            <p class="summary-paragraph">
                To better understand underlying trends and reduce the impact of month-to-month volatility, a three-month
                moving average has been calculated. This statistical measure smooths short-term fluctuations and
                provides a clearer view of the directional trend in overall operational activity.
            </p>

            <div class="note-box">
                <strong>Methodology Note:</strong> The 3-month moving average is calculated by averaging the current
                month's value with the two preceding months. Consequently, moving average values are not available for
                January and February, as they require data from the previous fiscal year. Values begin from March
                onwards.
            </div>

            <table>
                <caption class="table-caption">Table 3: Three-Month Moving Average (Overall Activity)</caption>
                <thead>
                    <tr>
                        @foreach($labels as $m) <th class="text-center">{{ $m }}</th> @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach(($analysis['moving_avg_3'] ?? []) as $v)
                            <td class="text-center">{{ is_null($v) ? '—' : number_format($v, 2) }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>

            <p class="summary-paragraph">
                The moving average analysis provides valuable context for strategic planning and resource allocation
                decisions, highlighting sustained trends while minimizing the influence of temporary fluctuations.
            </p>
        </div>

        <!-- Appendix A -->
        <div class="section page-break">
            <h1 class="section-header">APPENDIX A: DETAILED MONTHLY DATA</h1>

            <p class="summary-paragraph">
                This appendix provides a complete monthly breakdown of all operational categories for the {{ $year }}
                fiscal year. The data presented here supports the analyses and conclusions presented in the main body of
                this report.
            </p>

            <table>
                <caption class="table-caption">Table A1: Monthly Transaction Volumes by Category</caption>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="text-right">Room</th>
                        <th class="text-right">Vehicle</th>
                        <th class="text-right">Ticket</th>
                        <th class="text-right">Guestbook</th>
                        <th class="text-right">Delivery</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthly['labels'] as $i => $m)
                        <tr>
                            <td class="table-label">{{ $m }}</td>
                            <td class="text-right">{{ number_format($monthly['room'][$i]) }}</td>
                            <td class="text-right">{{ number_format($monthly['vehicle'][$i]) }}</td>
                            <td class="text-right">{{ number_format($monthly['ticket'][$i]) }}</td>
                            <td class="text-right">{{ number_format($monthly['guestbook'][$i]) }}</td>
                            <td class="text-right">{{ number_format($monthly['delivery'][$i]) }}</td>
                            <td class="text-right table-label">
                                {{ number_format($monthly['room'][$i] + $monthly['vehicle'][$i] + $monthly['ticket'][$i] + $monthly['guestbook'][$i] + $monthly['delivery'][$i]) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Appendix B -->
        <div class="section">
            <h1 class="section-header">APPENDIX B: HISTORICAL YEARLY TOTALS</h1>

            <p class="summary-paragraph">
                This appendix presents historical data covering the past {{ count($yearly['labels'] ?? []) }} years,
                enabling comprehensive year-over-year comparisons and long-term trend identification across all
                operational categories.
            </p>

            <table>
                <caption class="table-caption">Table B1: Annual Transaction Volumes by Category (Multi-Year Comparison)
                </caption>
                <thead>
                    <tr>
                        <th>Year</th>
                        <th class="text-right">Room</th>
                        <th class="text-right">Vehicle</th>
                        <th class="text-right">Ticket</th>
                        <th class="text-right">Guestbook</th>
                        <th class="text-right">Delivery</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($yearly['labels'] as $i => $y)
                        <tr>
                            <td class="table-label">{{ $y }}</td>
                            <td class="text-right">{{ number_format($yearly['room'][$i]) }}</td>
                            <td class="text-right">{{ number_format($yearly['vehicle'][$i]) }}</td>
                            <td class="text-right">{{ number_format($yearly['ticket'][$i]) }}</td>
                            <td class="text-right">{{ number_format($yearly['guestbook'][$i]) }}</td>
                            <td class="text-right">{{ number_format($yearly['delivery'][$i]) }}</td>
                            <td class="text-right table-label">
                                {{ number_format($yearly['room'][$i] + $yearly['vehicle'][$i] + $yearly['ticket'][$i] + $yearly['guestbook'][$i] + $yearly['delivery'][$i]) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="document-footer">
            <p><strong>{{ $company['company_name'] ?? 'Company' }}</strong> — Operational Analytics Report {{ $year }}
            </p>
            <p>This document contains confidential and proprietary information</p>
        </div>

        <script type="text/php">
    if (isset($pdf)) {
      $pdf->page_text(520, 812, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0,0,0));
    }
    </script>

    </div>
</body>

</html>