<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Agent Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            padding: 10px;
        }

        h2 {
            font-size: 16px;
            margin: 0 0 5px;
        }

        .logo {
            height: 60px;
            margin-bottom: 10px;
        }

        .agent-block {
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
        }

        th {
            background: #eee;
        }

        .badge-green {
            color: #0a0;
            font-weight: bold;
        }

        .badge-red {
            color: #d00;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- Logo (optional) -->
    <img class="logo" src="{{ public_path('images/kebun-raya-bogor.png') }}">

    <h2>Agent Report</h2>
    <div>Generated: {{ $generatedAt->format('Y-m-d H:i') }}</div>

    @foreach($agents as $i => $agent)
        @php
            $agentStats = $stats[$agent->user_id] ?? [];
            $agentTickets = $allTickets->where('user_id', $agent->user_id);
        @endphp

        <div class="agent-block">
            {{ $i + 1 }}. {{ $agent->full_name }}
            — {{ $agent->company->company_name ?? '-' }}
            / {{ $agent->department->department_name ?? '-' }}
            (Total: {{ $agentStats['Total'] ?? 0 }})
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>SLA</th>
                    <th>Elapsed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agentTickets as $ticket)
                    @php
                        $sla = $ticket->sla_state ?? [];
                        $slaClass = $sla['state'] === 'expired' ? 'badge-red' : 'badge-green';
                        $slaLabel = $sla['label'] ?? '-';

                        $h = $sla['hours_elapsed'] ?? 0;

                        if ($h > 0) {
                            if ($h < 1)
                                $elapsed = floor($h * 60) . 'm';
                            elseif ($h < 24)
                                $elapsed = floor($h) . 'h';
                            elseif ($h < 720)
                                $elapsed = floor($h / 24) . 'd';
                            elseif ($h < 8760)
                                $elapsed = floor($h / 720) . 'mo';
                            else
                                $elapsed = floor($h / 8760) . 'y';
                        } else {
                            $elapsed = '';
                        }
                    @endphp

                    <tr>
                        <td>#{{ $ticket->ticket_id }}</td>
                        <td>{{ $ticket->subject ?? 'No Subject' }}</td>
                        <td>{{ ucwords(strtolower($ticket->status)) }}</td>
                        <td>{{ ucwords(strtolower($ticket->priority)) }}</td>
                        <td class="{{ $slaClass }}">{{ $slaLabel }}</td>
                        <td>{{ $elapsed }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endforeach

</body>

</html>