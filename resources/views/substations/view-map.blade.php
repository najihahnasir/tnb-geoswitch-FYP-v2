@extends('layouts.master')

@section('title', 'Substation Map View')
@section('page-title', 'Substation Map')

@push('after-styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    #map {
        height: 500px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
    }

    .legend-box {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.95);
        padding: 12px 16px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        font-size: 13px;
        z-index: 999;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h4 class="mb-4">📍 {{ $substation->name }} Sensor Map</h4>
    <div id="map" class="mb-4"></div>

    <div class="legend-box">
        <div class="legend-item"><span class="legend-color" style="background:#2ecc71"></span>Online</div>
        <div class="legend-item"><span class="legend-color" style="background:#f39c12"></span>Repairing</div>
        <div class="legend-item"><span class="legend-color" style="background:#e74c3c"></span>Offline</div>
    </div>
</div>
@endsection

@push('after-scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const substation = @json($substation);
        const sensors = @json($sensors);

        // Initialize the map
        const map = L.map('map').setView([substation.latitude, substation.longitude], 16);

        // Base map
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add substation marker (blue pin)
        L.marker([substation.latitude, substation.longitude]).addTo(map)
            .bindPopup(`<strong>${substation.name}</strong>`)
            .openPopup();

        // Color map
        const statusColors = {
            online: '#2ecc71',
            repairing: '#f39c12',
            offline: '#e74c3c'
        };

        // Add sensor markers
        sensors.forEach(sensor => {
            if (!sensor.lat || !sensor.lng) return;

            const color = statusColors[sensor.status?.toLowerCase()] || '#999';

            const marker = L.circleMarker([sensor.lat, sensor.lng], {
                radius: 8,
                fillColor: color,
                color: color,
                weight: 2,
                fillOpacity: 0.9
            }).addTo(map).bindPopup(`
                <b>${sensor.tag}</b><br>
                Type: ${sensor.type}<br>
                Status: ${sensor.status}<br>
                Panel: ${sensor.panel}
            `);
        });

        // Resize fix
        setTimeout(() => {
            map.invalidateSize();
        }, 500);
    });
</script>
@endpush
