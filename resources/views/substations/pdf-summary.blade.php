<!DOCTYPE html>
<html>
<head>
    <title>Substation Summary PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px; }
        .bg-success { background-color: #28a745; color: white; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-danger { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <h2>Substation Summary Report</h2>
    <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Substation</th>
                <th>Location</th>
                <th>Total Sensors</th>
                <th>Online</th>
                <th>Repairing</th>
                <th>Offline</th>
            </tr>
        </thead>
        <tbody>
            @foreach($substations as $index => $sub)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $sub->substation_name }}</td>
                <td>{{ $sub->latitude }}, {{ $sub->longitude }}</td>
                <td>{{ $sub->sensors->count() }}</td>
                <td><span class="badge bg-success">{{ $sub->sensors->filter(fn($s) => strtolower($s->status) === 'online')->count() }}</span></td>
                <td><span class="badge bg-warning">{{ $sub->sensors->filter(fn($s) => strtolower($s->status) === 'repairing')->count() }}</span></td>
                <td><span class="badge bg-danger">{{ $sub->sensors->filter(fn($s) => strtolower($s->status) === 'offline')->count() }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
