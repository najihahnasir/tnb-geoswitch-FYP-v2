<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Substation Report - {{ $substation->substation_name }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2, h4 {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .status-online { background-color: #d4edda; }
        .status-repairing { background-color: #fff3cd; }
        .status-offline { background-color: #f8d7da; }
        .report-footer {
            text-align: right;
            font-size: 10px;
            margin-top: 20px;
            color: #777;
        }
    </style>
</head>
<body>

    <h2>Substation Profile Report</h2>
    <p><strong>Substation:</strong> {{ $substation->substation_name }}</p>
    <p><strong>Coordinates:</strong> {{ $substation->latitude }}, {{ $substation->longitude }}</p>

    <h4>Sensor Status Summary</h4>
    @php
        $sensorCollection = $substation->switchgears->flatMap->sensors;
        $total = $sensorCollection->count();
        $online = $sensorCollection->filter(fn($s) => strtolower($s->status) === 'online')->count();
        $repairing = $sensorCollection->filter(fn($s) => strtolower($s->status) === 'repairing')->count();
        $offline = $sensorCollection->filter(fn($s) => strtolower($s->status) === 'offline')->count();
    @endphp
    <table>
        <thead>
            <tr>
                <th>Total Sensors</th>
                <th>Online</th>
                <th>Repairing</th>
                <th>Offline</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $total }}</td>
                <td class="status-online">{{ $online }}</td>
                <td class="status-repairing">{{ $repairing }}</td>
                <td class="status-offline">{{ $offline }}</td>
            </tr>
        </tbody>
    </table>

    <h4>Switchgear & Sensor Breakdown</h4>
   @foreach($substation->switchgears->groupBy('panel_code') as $panelCode => $panelSwitchgears)
        @php
            $first = $panelSwitchgears->first();
            $panelName = $first->panel_name;
            $lineup = $first->lineup;
            $sensors = $panelSwitchgears->flatMap->sensors;
        @endphp

        <h4 style="margin-top: 20px;">
            <strong>Panel:</strong> {{ $panelName }} ({{ $panelCode }}) — Lineup: {{ $lineup }}
        </h4>

        @foreach($sensors->groupBy('compartment') as $compName => $compSensors)
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ccc; padding: 6px;">Compartment</th>
                        <th style="border: 1px solid #ccc; padding: 6px;">Sensor Tag</th>
                        <th style="border: 1px solid #ccc; padding: 6px;">Type</th>
                        <th style="border: 1px solid #ccc; padding: 6px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compSensors as $sensor)
                        <tr>
                            <td style="border: 1px solid #ccc; padding: 6px;">{{ $compName }}</td>
                            <td style="border: 1px solid #ccc; padding: 6px;">{{ $sensor->tag }}</td>
                            <td style="border: 1px solid #ccc; padding: 6px;">{{ $sensor->type }}</td>
                            <td style="border: 1px solid #ccc; padding: 6px;">
                                @php
                                    $status = strtolower($sensor->status);
                                @endphp
                                <span style="color:
                                    {{ $status === 'online' ? 'green' :
                                    ($status === 'repairing' ? 'orange' :
                                    ($status === 'offline' ? 'red' : 'black')) }};
                                    font-weight: bold;">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endforeach

    <div class="report-footer">
        Report generated on {{ now()->format('d M Y, H:i A') }}
    </div>

</body>
</html>
