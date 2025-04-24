<!DOCTYPE html>
<html>
<head>
    <title>Sensor Status Summary Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; margin: 0; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; color: #2c3e50; }
        .header p { margin: 5px 0 0; color: #7f8c8d; font-size: 14px; }
        .filters { margin-bottom: 20px; font-size: 14px; }
        .filters span { font-weight: bold; }
        table { border-collapse: collapse; width: 100%; font-size: 12px; margin-bottom: 20px; }
        th { background-color: #3498db; color: white; text-align: center; padding: 10px; }
        td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .status-alarm { color: #e74c3c; font-weight: bold; }
        .status-await { color: #f39c12; font-weight: bold; }
        .status-normal { color: #2ecc71; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 12px; text-align: right; color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="header">
        <h2>TNB-GeoSwitch Sensor Status Summary Report</h2>
        <p>Generated on {{ now()->format('d M Y H:i') }}</p>
    </div>
    
    @if(isset($filters) && (array_filter($filters)))
    <div class="filters">
        <h4>Filters Applied:</h4>
        <p>
            @if(!empty($filters['substation_id']))
                <span>Substation:</span> {{ \App\Models\Substation::find($filters['substation_id'])->substation_name }}<br>
            @endif
            @if(!empty($filters['sensor_type']))
                <span>Sensor Type:</span> {{ $filters['sensor_type'] }}<br>
            @endif
            @if(!empty($filters['date_from']) && !empty($filters['date_to']))
                <span>Date Range:</span> {{ $filters['date_from'] }} to {{ $filters['date_to'] }}
            @endif
        </p>
    </div>
    @endif
    
    <table>
        <thead>
            <tr>
                <th>Substation</th>
                <th>Total Sensors</th>
                <th class="status-alarm">ALARM</th>
                <th class="status-await">AWAIT</th>
                <th class="status-normal">NORMAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row['substation'] }}</td>
                <td>{{ $row['total'] }}</td>
                <td class="status-alarm">{{ $row['ALARM'] }}</td>
                <td class="status-await">{{ $row['AWAIT'] }}</td>
                <td class="status-normal">{{ $row['NORMAL'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        TNB-GeoSwitch Reporting Module | Page 1 of 1
    </div>
</body>
</html>