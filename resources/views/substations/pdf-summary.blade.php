<!DOCTYPE html>
<html>
<head>
    <title>Substation Summary Report</title>
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

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
        }

        .bg-success { background-color: #28a745; color: #fff; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-danger { background-color: #dc3545; color: #fff; }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #888;
            margin-top: 40px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        tfoot tr td {
            font-weight: bold;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <img src="{{ public_path('images/tnb-geoswitch-logo.png') }}" class="logo" alt="Logo">
        <div>
            <div class="system-name">TNB-GEOSWITCH</div>
            <div class="report-meta">Substation Overview Summary</div>
            <div class="report-meta">Generated at: {{ now()->format('l, d M Y H:i') }}</div>
        </div>
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Substation</th>
                <th>Location</th>
                <th>Total Sensors</th>
                <th>Partial Discharge</th>
                <th>Thermal</th>
                <th>🟢 Online</th>
                <th>🟠 Repairing</th>
                <th>🔴 Offline</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalSensors = 0;
                $totalPD = 0;
                $totalTH = 0;
                $totalOnline = 0;
                $totalRepairing = 0;
                $totalOffline = 0;
            @endphp

            @foreach($substations as $index => $sub)
                @php
                    $sensors = $sub->sensors;
                    $count = $sensors->count();
                    $pd = $sensors->filter(fn($s) => strtolower($s->type) === 'partial discharge')->count();
                    $th = $sensors->filter(fn($s) => strtolower($s->type) === 'thermal')->count();
                    $online = $sensors->where('status', 'online')->count();
                    $repairing = $sensors->where('status', 'repairing')->count();
                    $offline = $sensors->where('status', 'offline')->count();

                    $totalSensors += $count;
                    $totalPD += $pd;
                    $totalTH += $th;
                    $totalOnline += $online;
                    $totalRepairing += $repairing;
                    $totalOffline += $offline;
                @endphp

                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $sub->substation_name }}</td>
                    <td>{{ $sub->latitude }}, {{ $sub->longitude }}</td>
                    <td>{{ $count }}</td>
                    <td>{{ $pd }}</td>
                    <td>{{ $th }}</td>
                    <td><span class="badge bg-success">{{ $online }}</span></td>
                    <td><span class="badge bg-warning">{{ $repairing }}</span></td>
                    <td><span class="badge bg-danger">{{ $offline }}</span></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">TOTAL</td>
                <td>{{ $totalSensors }}</td>
                <td>{{ $totalPD }}</td>
                <td>{{ $totalTH }}</td>
                <td><span class="badge bg-success">{{ $totalOnline }}</span></td>
                <td><span class="badge bg-warning">{{ $totalRepairing }}</span></td>
                <td><span class="badge bg-danger">{{ $totalOffline }}</span></td>
            </tr>
        </tfoot>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        © {{ now()->year }} TNB-GEOSWITCH System – Asset Management Dept. | For internal reporting use only.
    </div>
</body>
</html>
