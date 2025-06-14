<!DOCTYPE html>
<html>
<head>
    <title>Sensor Log Summary</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px; display: flex; align-items: center; }
        .logo { height: 40px; margin-right: 20px; }
        .footer { font-size: 10px; color: #888; text-align: center; margin-top: 30px; border-top: 1px solid #ccc; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/tnb-geoswitch-logo.png') }}" class="logo" alt="Logo">
        <div>
            <h2 style="margin: 0;">TNB-GEOSWITCH</h2>
            <p style="margin: 0; font-size: 14px;">Sensor Log Summary Report</p>
            <small>Generated at: {{ now()->format('l, d M Y H:i') }}</small>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Case ID</th>
                <th>Substation</th>
                <th>Panel</th>
                <th>Sensor Tag</th>
                <th>Status</th>
                <th>Report Date</th>
                <th>Resolved</th>
                <th>Downtime</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $index => $report)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $report->case_id }}</td>
                    <td>{{ optional($report->substation)->substation_name }}</td>
                    <td>{{ optional($report->switchgear)->panel_name }}</td>
                    <td>{{ optional($report->sensor)->tag }}</td>
                    <td>{{ ucfirst($report->new_status) }}</td>
                    <td>{{ optional($report->reported_at)?->format('Y-m-d H:i') }}</td>
                    <td>{{ optional($report->resolved_at)?->format('Y-m-d H:i') }}</td>
                    <td>{{ $report->calculated_duration ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ now()->year }} TNB-GEOSWITCH System – Asset Management Dept.
    </div>
</body>
</html>
