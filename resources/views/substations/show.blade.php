@extends('layouts.master')

@section('title', 'Substation Profile')
@section('page-title', $substation->substation_name)

@push('after-styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    /* #map { height: 450px; border-radius: 10px; } */
    #map {
        height: 300px !important; /* Reduced from 450px */
        border-radius: 10px;
        width: 100%;
    }
    .leaflet-container {
        z-index: 0; /* keep it lower than modals or navbars */
        overflow: visible !important;
        border-radius: 8px;
    }

    .leaflet-popup {
        z-index: 1051 !important;
    }


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
    .accordion-button {
        background-color: #ffffff !important;
        color: #000;
    }
    .accordion-button.collapsed {
        background-color: #ffffff;
    }
    .accordion-button {
        border-bottom: 1px solid #dee2e6;
        box-shadow: none;
    }



</style>
@endpush

@section('content')
<div class="container py-3 px-2 px-md-4">
    {{-- ⚡ Substation Info + Map --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items">
                <!-- Substation Info: narrower -->
                <div class="col-md-4">
                    <h5 class="card-title text-primary d-flex align-items-center mb-2">
                        <i class="fas fa-bolt text-warning me-2"></i>{{ $substation->substation_name }}
                    </h5>

                    <p class="mb-1">
                        <strong>Location:</strong> {{ $substation->latitude }}, {{ $substation->longitude }}
                    </p>

                    <p class="mb-2">
                        <strong>Total Panels:</strong> {{ $substation->switchgears->groupBy('panel_code')->count() }}
                    </p>

                    <p class="mb-2">
                        <strong>Total Compartments:</strong> {{ $substation->switchgears->flatMap->sensors->pluck('compartment')->unique()->count() }}
                    </p>

                    <p class="mb-2">
                        <strong>Total Sensors:</strong> {{ $substation->switchgears->flatMap->sensors->count() }}
                    </p>


                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <span class="badge bg-success px-3 py-2">
                            🟢 Online: {{ collect($sensorMarkers)->filter(fn($s) => strtolower($s['status']) === 'online')->count() }}
                        </span>
                        <span class="badge bg-warning text-dark px-3 py-2">
                            🟠 Repairing: {{ collect($sensorMarkers)->filter(fn($s) => strtolower($s['status']) === 'repairing')->count() }}
                        </span>
                        <span class="badge bg-danger px-3 py-2">
                            🔴 Offline: {{ collect($sensorMarkers)->filter(fn($s) => strtolower($s['status']) === 'offline')->count() }}
                        </span>
                    </div>
                </div>

                <!-- Sensor Map: wider -->
                <div class="col-md-8">
                    <div class="rounded border overflow-hidden" style="height: 320px; width: 100%;">
                        <div id="map" style="height: 100%; width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- 🔌 Switchgear & Sensor Summary --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-secondary text-white d-flex align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas fa-plug me-2"></i>
                <h5 class="mb-0">Switchgear & Sensor Summary</h5>
            </div>
            <a href="{{ route('substations.exportPdf', $substation->id) }}" class="btn btn-sm btn-light text-danger ms-auto">
                <i class="fas fa-file-pdf me-1"></i> Download PDF
            </a>
        </div>
        <div class="card-body p-0">
            <div class="accordion" id="panelAccordion">
                @foreach($substation->switchgears->groupBy('panel_code') as $panelCode => $switchgearsInPanel)
                    @php
                        $first = $switchgearsInPanel->first();
                        $panelName = $first->panel_name;
                        $lineup = $first->lineup;
                        $switchgearCode = $first->switchgear_code;

                        $sensors = $switchgearsInPanel->flatMap->sensors;
                        $total = $sensors->count();
                        $online = $sensors->filter(fn($s) => strtolower($s->status) === 'online')->count();
                        $repairing = $sensors->filter(fn($s) => strtolower($s->status) === 'repairing')->count();
                        $offline = $sensors->filter(fn($s) => strtolower($s->status) === 'offline')->count();
                    @endphp

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $panelCode }}">
                            <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $panelCode }}"
                                aria-expanded="false" aria-controls="collapse{{ $panelCode }}">
                                <strong>{{ $panelName }} ({{ $panelCode }})</strong>
                                <span class="ms-3 text-muted small">
                                    • Lineup: {{ $lineup }} • SWG Code: {{ $switchgearCode }}
                                </span>
                                <span class="ms-auto d-flex gap-2">
                                    <span class="badge bg-secondary">{{ $total }} sensors</span>
                                    <span class="badge bg-success">🟢 {{ $online }}</span>
                                    <span class="badge bg-warning text-dark">🟠 {{ $repairing }}</span>
                                    <span class="badge bg-danger">🔴 {{ $offline }}</span>
                                </span>
                            </button>
                        </h2>
                        <div id="collapse{{ $panelCode }}" class="accordion-collapse collapse"
                            aria-labelledby="heading{{ $panelCode }}" data-bs-parent="#panelAccordion">
                            <div class="accordion-body">
                                @if($total > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle text-center mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Compartment</th>
                                                    <th>Sensor Type(s)</th>
                                                    <th>Total</th>
                                                    <th>🟢 Online</th>
                                                    <th>🟠 Repairing</th>
                                                    <th>🔴 Offline</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white">
                                                @foreach($sensors->groupBy('compartment') as $compName => $compSensors)
                                                    @php
                                                        $typeCounts = $compSensors->groupBy('type')->map->count();
                                                        $typeDisplay = $typeCounts->map(fn($count, $type) => "{$type} ({$count})")->implode(', ');
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $compName }}</td>
                                                        <td class="text-muted">{{ $typeDisplay }}</td>
                                                        <td>{{ $compSensors->count() }}</td>
                                                        <td><span class="badge bg-success">{{ $compSensors->filter(fn($s) => strtolower($s->status) === 'online')->count() }}</span></td>
                                                        <td><span class="badge bg-warning text-dark">{{ $compSensors->filter(fn($s) => strtolower($s->status) === 'repairing')->count() }}</span></td>
                                                        <td><span class="badge bg-danger">{{ $compSensors->filter(fn($s) => strtolower($s->status) === 'offline')->count() }}</span></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">No sensors found for this panel.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    

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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
