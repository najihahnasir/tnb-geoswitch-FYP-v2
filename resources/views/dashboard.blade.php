@extends('layouts.master') <!-- Extend the base layout -->

@section('title', 'GeoSwitch Dashboard') <!-- Set page title -->

@section('breadcrumbs')
<x-breadcrumbs 
    title="Welcome to GeoSwitch Dashboard"
    :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Home'],
        ['url' => '', 'label' => 'Main Dashboard']
    ]"
/>
@endsection

<!--Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<!--MarkerCluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />


@section('content')
<style>
    /**
     * @license
     * Copyright 2024 Google LLC. All Rights Reserved.
     * SPDX-License-Identifier: Apache-2.0
     */

    /* Optional: Makes the sample page fill the window. */
    /* html, body {
        height: 100%;
        margin: 0;
    } */

    /* Shift the flag icon to the right so that the bottom of the flagpole
     * aligns with the anchor point. */
    .flag-icon {
        position: relative;
        left: 10px;
    }

    /* Dashboard-specific styles */
    h2 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .dashboard-content {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .chart-container {
        flex: 1 1 calc(33.333% - 20px);
        min-width: 250px;
        height: 250px;
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .chart-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .chart-container h3 {
        font-size: 18px;
        font-weight: 500;
        color: #34495e;
        margin-bottom: 15px;
    }

    @media (max-width: 768px) {
        .dashboard-content {
            flex-direction: column;
        }

        .chart-container {
            flex: 1 1 100%;
        }
    }
    #map {
    height: 500px;
    width: 100%;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}
</style>

<h2>GeoSwitch Dashboard</h2>
<!-- Dashboard Content -->
<div class="dashboard-content">
    <h3 style="margin-bottom: 10px;">Substation Map</h3>

    <div style="margin-bottom: 15px;">
        <label for="substationFilter"><strong>Substation:</strong></label>
        <select id="substationFilter" style="padding: 8px; border-radius: 6px;">
            <option value="all">All Substations</option>
            @foreach ($substations as $s)
                <option value="{{ $s->substation_name }}">{{ $s->substation_name }}</option>
            @endforeach
        </select>
    </div>
    
    <div style="margin-bottom: 15px;">
        <strong>Filter by Sensor Type:</strong><br>
        @foreach(['Partial Discharge', 'SF6', 'Thermal', 'DGA'] as $type)
            <label style="margin-right: 10px;">
                <input type="checkbox" class="sensor-filter" value="{{ $type }}" checked>
                {{ $type }}
            </label>
        @endforeach
    </div>
    
    <div id="map"
        style="height: 500px; width: 100%; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-bottom: 30px;">
    </div>

    <!-- Analytics Charts -->
    <div class="chart-container">
        <h3>Sensor Condition Analytics</h3>
        <canvas id="sensorConditionChart"></canvas>
    </div>
    <div class="chart-container">
        <h3>Maintenance Analytics</h3>
        <canvas id="maintenanceChart"></canvas>
    </div>
    <div class="chart-container">
        <h3>Sensor Performance</h3>
        <canvas id="sensorPerformanceChart"></canvas>
    </div>
</div>
@endsection

@push('after-scripts')
<!--Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<!--MarkerCluster JS (must come AFTER Leaflet JS) -->
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<!-- Include Chart.js for Analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Example Chart.js Configuration
    const sensorConditionChart = new Chart(document.getElementById('sensorConditionChart'), {
        type: 'bar',
        data: {
            labels: ['Healthy', 'Warning', 'Critical'],
            datasets: [{
                label: 'Sensor Condition',
                data: [75, 20, 5],
                backgroundColor: ['#4CAF50', '#FFC107', '#F44336'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    const maintenanceChart = new Chart(document.getElementById('maintenanceChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Maintenance Requests',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#2196F3',
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    const sensorPerformanceChart = new Chart(document.getElementById('sensorPerformanceChart'), {
        type: 'doughnut',
        data: {
            labels: ['Optimal', 'Suboptimal', 'Degraded'],
            datasets: [{
                label: 'Sensor Performance',
                data: [70, 20, 10],
                backgroundColor: ['#4CAF50', '#FFC107', '#F44336'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>

{{-- <script>
    var map = L.map('map').setView([3.1390, 101.6869], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    //substations passed array from controller
    var substations = @json($substations);
    var markers = [];

    //to render markers on map
    function renderMarkers(data) {
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        data.forEach(substation => {
            var marker = L.marker([substation.latitude, substation.longitude])
                .addTo(map)
                .bindPopup(`<b>${substation.name}</b><br>${substation.location}`);
            markers.push(marker);
        });
    }

    renderMarkers(substations);

    //filter logic
    document.getElementById('substationFilter').addEventListener('change', function () {
        var selected = this.value;
        if (selected === 'all') {
            renderMarkers(substations);
            map.setView([3.1390, 101.6869], 11); // reset to default
        } else {
            var filtered = substations.filter(substation => substation.substation_name === selected);
            renderMarkers(filtered);
            if (filtered.length > 0) {
                map.setView([filtered[0].latitude, filtered[0].longitude], 14);
            }
        }
    });
    setTimeout(() => {
        map.invalidateSize();
    }, 500);
</script> --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var map = L.map('map').setView([3.1390, 101.6869], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const substations = @json($substations);
        const sensors = @json($sensors);
        console.log(sensors);


        console.log('Loaded sensors:', sensors); 
        // var sensors = @json($sensors);
        // let sensorMarkers = [];

        
        let markerCluster = L.markerClusterGroup(); // ✅ initialize clustering

        function renderSensorMarkers(substationName, filteredTypes) {
        markerCluster.clearLayers(); // remove existing clustered markers
        
        if (map.hasLayer(markerCluster)) {
            map.removeLayer(markerCluster);
        }
        sensors.forEach(sensor => {
            const substation = sensor.switchgear.substation;
            const type = sensor.type;

            const matchSubstation = substationName === 'all' || substation.substation_name === substationName;
            const matchType = filteredTypes.length === 0 || filteredTypes.includes(type);

            if (matchSubstation && matchType) {
                const lat = substation.latitude;
                const lng = substation.longitude;

                const marker = L.marker([lat, lng]).bindPopup(`
                    <b>${sensor.tag}</b><br>
                    Type: ${type}<br>
                    Panel: ${sensor.switchgear.panel}<br>
                    Substation: ${substation.substation_name}
                `);

                markerCluster.addLayer(marker); // ✅ add marker to cluster group
            }
        });

        map.addLayer(markerCluster); // ✅ show cluster on map

        if (markerCluster.getLayers().length > 0) {
            map.fitBounds(markerCluster.getBounds());
        } else {
            map.setView([3.1390, 101.6869], 11); // fallback to default view
        }
    }

    // Get all selected sensor types
    function getSelectedTypes() {
        return Array.from(document.querySelectorAll('.sensor-filter:checked'))
                    .map(cb => cb.value);
    }

    // Initial marker load
    renderSensorMarkers('all', getSelectedTypes());

    // Substation dropdown filter
    document.getElementById('substationFilter').addEventListener('change', function () {
        const selectedSubstation = this.value;
        renderSensorMarkers(selectedSubstation, getSelectedTypes());
    });

    // Sensor type checkbox filter
    document.querySelectorAll('.sensor-filter').forEach(cb => {
        cb.addEventListener('change', function () {
            const selectedSubstation = document.getElementById('substationFilter').value;
            renderSensorMarkers(selectedSubstation, getSelectedTypes());
        });
    });

    // Fix initial map rendering glitch
    setTimeout(() => {
        map.invalidateSize();
    }, 500);
});
</script>
@endpush