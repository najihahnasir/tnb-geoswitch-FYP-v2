<!DOCTYPE html>
<html>
<head>
    <title>Sensor Log Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 40px;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo {
            height: 50px;
            margin-right: 20px;
        }

        .system-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }

        .report-meta {
            margin-top: 5px;
            font-size: 12px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #888;
            margin-top: 40px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <img src="{{ public_path('images/tnb-geoswitch-logo.png') }}" class="logo" alt="Logo">
        <div>
            <div class="system-name">TNB-GEOSWITCH</div>
            <div class="report-meta">Sensor Log Report for: <strong>{{ $sensor->tag }}</strong></div>
            <div class="report-meta">Generated at: {{ now()->format('l, d M Y H:i') }}</div>
        </div>
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Case ID</th>
                <th>Status</th>
                <th>Reported By</th>
                <th>Reported At</th>
                <th>Resolved At</th>
                <th>Downtime Duration</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sensor->logReports as $index => $report)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $report->case_id ?? '-' }}</td>
                <td>{{ ucfirst($report->status) }}</td>
                <td>{{ optional($report->reporter)->name ?? '-' }}</td>
                <td>{{ $report->reported_at ? $report->reported_at->format('Y-m-d H:i') : '-' }}</td>
                <td>{{ $report->resolved_at ? $report->resolved_at->format('Y-m-d H:i') : '-' }}</td>
                <td>
                    @if($report->status === 'open')
                        In Progress
                    @else
                        {{ $report->calculated_duration ?? '-' }}
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No log reports available for this sensor.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        © {{ now()->year }} TNB-GEOSWITCH System – Asset Management Dept. | For internal reporting use only.
    </div>
</body>
</html>
