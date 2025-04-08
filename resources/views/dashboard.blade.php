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
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

{{-- <!-- Google Maps Container with Complex Markers -->
<gmp-map center="-33.9,151.2" zoom="10" map-id="DEMO_MAP_ID" style="height: 400px; width: 100%; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <gmp-advanced-marker position="-33.890542, 151.274856" title="Bondi Beach">
        <img class="flag-icon"
             src="https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png"/>
    </gmp-advanced-marker>
    <gmp-advanced-marker position="-33.923036, 151.259052" title="Coogee Beach">
        <img class="flag-icon"
             src="https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png"/>
    </gmp-advanced-marker>
    <gmp-advanced-marker position="-34.028249, 151.157507" title="Cronulla Beach">
        <img class="flag-icon"
             src="https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png"/>
    </gmp-advanced-marker>
    <gmp-advanced-marker position="-33.800101, 151.287478" title="Manly Beach">
        <img class="flag-icon"
             src="https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png"/>
    </gmp-advanced-marker>
    <gmp-advanced-marker position="-33.950198, 151.259302" title="Maroubra Beach">
        <img class="flag-icon"
             src="https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png"/>
    </gmp-advanced-marker>
</gmp-map> --}}

<!-- Dashboard Content -->
<div class="dashboard-content">
    <h3>Substation Map</h3>

    <!-- Dropdown Filter -->
    <select id="substationFilter" style="margin-bottom: 15px; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
        <option value="all">All Substations</option>
        @foreach ($substations as $station)
            <option value="{{ $station['name'] }}">{{ $station['name'] }}</option>
        @endforeach
    </select>

    <!-- Leaflet Map Container -->
    <div id="map" style="height: 500px; width: 100%; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-bottom: 30px;"></div>

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
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
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
</script>

@endsection