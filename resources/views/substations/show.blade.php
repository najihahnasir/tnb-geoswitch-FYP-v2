@extends('layouts.master')

@section('title', 'Substation Profile')
@section('page-title', $substation->substation_name)

@push('after-styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    #map { height: 450px; border-radius: 10px; }
    .legend-box {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: #fff;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 0 6px rgba(0,0,0,0.15);
        z-index: 999;
    }
    .legend-item {
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        font-size: 13px;
    }
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 6px;
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    {{-- Substation Info Card --}}
    <div class="card shadow-sm mb-4 mt-0">
        <div class="card-body">
            <h4 class="card-title text-primary"><i class=" me-2 text-warning"></i>{{ $substation->substation_name }}</h4>
            <p><i class="fas fa-map-marker-alt me-1 text-danger"></i> <strong>Location:</strong> {{ $substation->latitude }}, {{ $substation->longitude }}</p>
            <p><i class="fas fa-plug me-1 text-success"></i> <strong>Total Switchgears:</strong> {{ $substation->switchgears->count() }}</p>
            <p>
                <span class="badge bg-success me-2">Online: {{ $sensorMarkers->where('status', 'online')->count() }}</span>
                <span class="badge bg-warning text-dark me-2">Repairing: {{ $sensorMarkers->where('status', 'repairing')->count() }}</span>
                <span class="badge bg-danger">Offline: {{ $sensorMarkers->where('status', 'offline')->count() }}</span>
            </p>
        </div>
    </div>


    {{-- Switchgear & Sensor Summary --}}
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Switchgear & Sensor Summary</h5>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach($substation->switchgears as $switchgear)
                    <li class="list-group-item">
                        <strong>{{ $switchgear->panel }}</strong>
                        <span class="badge bg-secondary ms-2">{{ $switchgear->sensors->count() }} sensors</span>
                        <ul class="ms-3 text-muted small mt-2">
                            @foreach($switchgear->sensors as $sensor)
                                <li>
                                    <i class="fas fa-circle me-1" style="color: {{ $sensor->status == 'online' ? '#2ecc71' : ($sensor->status == 'repairing' ? '#f39c12' : '#e74c3c') }}"></i>
                                    {{ $sensor->tag }} – {{ $sensor->type }} – 
                                    <span class="text-capitalize">{{ $sensor->status }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Sensor Map --}}
    <div id="map" class="mb-4"></div>
    {{-- <div class="legend-box">
        <div class="legend-item"><span class="legend-color" style="background:#2ecc71"></span>Online</div>
        <div class="legend-item"><span class="legend-color" style="background:#f39c12"></span>Repairing</div>
        <div class="legend-item"><span class="legend-color" style="background:#e74c3c"></span>Offline</div>
    </div> --}}

</div>
@endsection


@push('after-scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const substation = @json($substation);
        const sensors = @json($sensorMarkers);

        const map = L.map('map').setView([substation.latitude, substation.longitude], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([substation.latitude, substation.longitude]).addTo(map)
            .bindPopup(`<strong>${substation.substation_name}</strong>`)
            .openPopup();

        const statusColors = {
            online: '#2ecc71',
            repairing: '#f39c12',
            offline: '#e74c3c'
        };

        sensors.forEach(sensor => {
            if (!sensor.lat || !sensor.lng) return;

            const color = statusColors[sensor.status?.toLowerCase()] || '#999';

            L.circleMarker([sensor.lat, sensor.lng], {
                radius: 8,
                fillColor: color,
                color: color,
                weight: 2,
                fillOpacity: 0.9
            }).addTo(map).bindPopup(`
                <div style="min-width:180px">
                    <strong>${sensor.tag}</strong><br>
                    <i class="fas fa-microchip me-1"></i> <b>Type:</b> ${sensor.type}<br>
                    <i class="fas fa-tachometer-alt me-1"></i> <b>Status:</b> ${sensor.status}<br>
                    <i class="fas fa-columns me-1"></i> <b>Panel:</b> ${sensor.panel}
                </div>
            `);
        });

        setTimeout(() => {
            map.invalidateSize();
        }, 500);
    });
</script>
@endpush
