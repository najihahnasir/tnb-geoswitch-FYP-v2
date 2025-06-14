@extends('layouts.master') <!-- Extend the base layout -->

@section('title', 'TNB-GEOSWICTH DASHBOARD') <!-- Set page title -->
@section('page-title', 'TNB-GEOSWITCH DASHBOARD')

{{-- @section('breadcrumbs')
<x-breadcrumbs 
    title="Welcome to GeoSwitch Dashboard"
    :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Home'],
        ['url' => '', 'label' => 'Main Dashboard']
    ]"
/>
@endsection --}}
@push('after-styles')
<!--Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<!--MarkerCluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

<style>
    .dashboard-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }
    h2 {color: #2c3e50; font-weight: 600; margin-bottom: 30px;}

    .chart-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        text-align: center;
    }

    .chart-box {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .chart-container {
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
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
        .dashboard-content {flex-direction: column;}
        /* .chart-container {flex: 1 1 100%;} */
        .legend-box {
            top: auto;
            bottom: 20px;
            right: 10px;
        }
        .charts-grid {
            grid-template-columns: 1fr;
        }
    }

    .status-cards {
        display: flex;
        gap:1rem;
        margin-bottom: 0; /* Remove space below */
        flex-wrap: wrap;
        /* justify-content: space-between; */
    }

    @media (max-width: 1200px) {
        .status-card {
            flex: 1 1 48%; /* 2 cards per row */
            max-width: 48%;
        }
    }


    @media (max-width: 576px) {
        .status-card {
            flex: 1 1 100%; /* Full width on mobile */
            max-width: 100%;
        }
    }
        
    .status-card {
        flex: 1 1 calc(25% - 16px); /* 4 cards per row */
        min-width: 160px;
        max-width: calc(25% - 16px);
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border-left: 4px solid;
    }

    .col-md-4 {
    flex: 0 0 24% !important;
    max-width: 24% !important;
}
.col-md-8 {
    flex: 0 0 75% !important;
    max-width: 75% !important;c
}


    
    .status-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 12px rgba(0,0,0,0.12);
    }
    
    .card-value {
        font-size: 24px;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 4px;
    }
    
    .card-label {
        color: #7f8c8d;
        font-size: 14px;
        font-weight: 500;
    }
    
    .chart-container {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
    }

    .dashboard-top {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 0;
    }

    
</style>
@endpush

@section('content')

<div class="dashboard-intro py-2 px-1 mb-4 rounded shadow-sm bg-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center" style="max-width: 98.5%; margin-left: -2px; margin-right: auto;">
    <div>
    <h5 class="fw-bold mb-1" style="color: #026670">TNB-GEOSWITCH</h5>
    <p class="text-muted small mb-0">
        Welcome back, <span class="d-none d-sm-inline">here’s the latest system status and analytics overview.</span>
    </p>
</div>

    <span class="text-muted small mt-2 mt-md-0">
        <i class="bi bi-calendar-event me-1"></i>{{ now()->format('l, d M Y') }}
    </span>
</div>

<div class="dashboard-container">
    <div class="dashboard-top row">
        <div class="status-cards">
            <div class="status-card" style="border-left: 4px solid #3F51B5;">
                <div class="card-value">{{ $severityStats['total'] ?? 0 }}</div>
                <div class="card-label">Total Sensors</div>
            </div>

            <div onclick="showOnlineSensors()" class="status-card cursor-pointer" style="border-left: 4px solid #4CAF50;">
                <div class="card-value">{{ $onlineCount ?? 0 }}</div>
                <div class="card-label">Online Sensors</div>
            </div>

            <div onclick="showRepairingSensors()" class="status-card cursor-pointer" style="border-left: 4px solid #FFC107;">
                <div class="card-value">{{ $repairingCount ?? 0 }}</div>
                <div class="card-label">Repairing Sensors</div>
            </div>

            <div onclick="showOfflineSensors()" class="status-card cursor-pointer" style="border-left: 4px solid #F44336;">
                <div class="card-value">{{ $offlineCount ?? 0 }}</div>
                <div class="card-label">Offline Sensors</div>
            </div>

            {{-- <div class="status-card" style="border-left: 4px solid #E67E22;"> 
                <div class="card-label" style="text-align: center; font-weight: 500; font-size: 14px;">
                    Total Substations with Offline Sensors
                </div>
                <div class="card-value text-danger" style="text-align: center;">
                    {{ $totalDefectSubstations }}
                </div>
            </div> --}}
        </div>
    </div>
    
    @include('partial-dashboard.chart')

    @include('partial-dashboard.map')

    @foreach($substations as $substation)
        @include('partial-dashboard.popup-sensor', ['substation' => $substation])
    @endforeach

    <div id="sensor-summary-panel">
        <h4>Sensor Summary</h4>
        <div class="row"><label>Tag:</label><span id="summary-tag">-</span></div>
        <div class="row"><label>Type:</label><span id="summary-type">-</span></div>
        <div class="row"><label>Status:</label><span id="summary-status">-</span></div>
        <div class="row"><label>Panel:</label><span id="summary-panel">-</span></div>
        <div class="row"><label>Substation:</label><span id="summary-substation">-</span></div>
    </div>
    
    <div class="modal fade" id="offlineSensorModal" tabindex="-1" aria-labelledby="offlineSensorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <h5 class="modal-title mb-0" style="align-items: center" id="offlineSensorModalLabel">Urgent: Offline Sensors Detected</h5>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="fs-6">There are <strong>{{ $offlineCount }}</strong> sensors currently <span class="text-danger">offline</span> in the system.</p>
                    <div class="mb-3">
                        <ul class="list-group list-group-flush">
                            @foreach($offlineSummary as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="fw-semibold text-dark">
                                        <i class="fas fa-bolt me-1 text-warning"></i>{{ $item['substation'] }}
                                    </div>
                                    <div class="text-muted small text-end">
                                        <div>{{ $item['sensor_type'] }}</div>
                                        <div class="text-dark">({{ $item['sensor_tag'] }})</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <p class="text-muted small mb-0">Last updated: {{ now()->format('d M Y, h:i A') }}</p>
                </div>
                <div class="modal-footer bg-light border-0">
                    <a href="{{ route('sensors.index', ['filter' => 'offline']) }}" class="btn btn-danger">
                        <i class="fas fa-eye me-1"></i> View All Offline Sensors
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Dismiss
                    </button>
                </div>
            </div>
        </div>
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
    document.addEventListener('DOMContentLoaded', function () {
        const offlineCount = {{ $offlineCount ?? 0 }};
        if (offlineCount > 0 && !sessionStorage.getItem('sensorAlertDismissed')) {
            const modal = new bootstrap.Modal(document.getElementById('offlineSensorModal'));
            modal.show();

            document.getElementById('offlineSensorModal')
                .addEventListener('hidden.bs.modal', () => sessionStorage.setItem('sensorAlertDismissed', true));
        }
    });
</script>

@endpush
