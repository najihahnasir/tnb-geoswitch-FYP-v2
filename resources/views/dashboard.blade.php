@extends('layouts.master') <!-- Extend the base layout -->

@section('title', 'GeoSwitch Dashboard') <!-- Set page title -->
@section('page-title', 'GeoSwitch Dashboard')

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
    h2 {color: #2c3e50; font-weight: 600; margin-bottom: 20px;}

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
        gap: 16px;
        margin-bottom: 0; /* Remove space below */
        flex-wrap: wrap;
        /* justify-content: space-between; */
    }

    @media (max-width: 1200px) {
        .status-card {
            flex: 1 1 calc(50% - 16px); /* 2 cards on smaller desktops */
        }
    }

    @media (max-width: 576px) {
        .status-card {
            flex: 1 1 100%; /* Full width on mobile */
            max-width: 100%;
        }
    }
        
    .status-card {
         flex: 1 1 calc(33.333% - 16px); /* 3 cards */
        min-width: 180px;
        max-width: calc(33.333% - 16px); /* Ensures 4 cards per row */
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border-left: 4px solid;
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
@include('partial-dashboard.alert-popup')


{{-- <h2>GeoSwitch Dashboard</h2> --}}
{{-- <div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0 mt-0">Welcome back, here's the latest system status and analytics overview.</p>
    <span class="text-muted small">{{ now()->format('l, d M Y') }}</span>
</div> --}}
<div class="dashboard-container">
    <div class="dashboard-top row">
        {{-- Right: Status Cards --}}
        <div class="status-cards">
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
   

    <div id="sensor-summary-panel">
        <h4>Sensor Summary</h4>
        <div class="row"><label>Tag:</label><span id="summary-tag">-</span></div>
        <div class="row"><label>Type:</label><span id="summary-type">-</span></div>
        <div class="row"><label>Status:</label><span id="summary-status">-</span></div>
        <div class="row"><label>Panel:</label><span id="summary-panel">-</span></div>
        <div class="row"><label>Substation:</label><span id="summary-substation">-</span></div>
    </div>
    @include('partial-dashboard.popup-sensor')

     <!-- Charts Section -->
     <div class="charts-grid">
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
</div>
@endsection

@push('after-scripts')
<!--Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<!--MarkerCluster JS (must come AFTER Leaflet JS) -->
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<!-- Include Chart.js for Analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- 
<script>
    window.addEventListener('DOMContentLoaded', function () {
        const offlineCount = {{ $offlineCount }};
        if (offlineCount > 0 && !sessionStorage.getItem('popupDismissed')) {
            document.getElementById('offlineSensorPopup').style.display = 'block';
        }
    });

    function closeOfflinePopup() {
        document.getElementById('offlineSensorPopup').style.display = 'none';
        sessionStorage.setItem('popupDismissed', true);
    }
</script> --}}

{{-- alert --}}
{{-- <script>
    window.addEventListener('DOMContentLoaded', function () {
        const popupOverlay = document.getElementById('popupOverlay');
        const popup = document.getElementById('offlineSensorPopup');

        console.log("Popup script running. Offline count: {{ $offlineCount }}");

        if (!popup) {
            console.warn("Popup element NOT FOUND in DOM");
        } else {
            console.log("Popup element FOUND in DOM");
        }

        if (popupOverlay && !sessionStorage.getItem('popupDismissed')) {
            popupOverlay.style.display = 'flex'; // make sure overlay is visible
            console.log("Popup displayed.");
        }
    });

    function closeOfflinePopup() {
        const popupOverlay = document.getElementById('popupOverlay');
        if (popupOverlay) {
            popupOverlay.style.display = 'none';
            sessionStorage.setItem('popupDismissed', true);
        }
    }
</script> --}}




{{-- latest script map --}}
{{-- <script>
    var map = L.map('map').setView([3.1390, 101.6869], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    //substations passed array from controller
    var substations = @json($substations ?? []);
    var markers = [];
    const sensors = @json($sensors ?? []);
    let sensorMarkers = [];
    let selectedTypes = []; // All selected by default
    let statusCounts = { online: 0, repairing: 0, offline: 0, unknown: 0 };
    let selectedStatus = 'ALL';

    // Get color based on status
    function getStatusColor(status) {
        switch ((status || '').toLowerCase()) {
            case 'online': return '#2ecc71';    // green
            case 'repairing': return '#f39c12'; // orange
            case 'offline': return '#e74c3c';   // red
            default: return '#3498db';          // blue fallback
        }
    }

    // Update status cards
    function updateStatusCards() {
        document.getElementById('normal-count').textContent = statusCounts.online || 0;
        document.getElementById('await-count').textContent = statusCounts.repairing || 0;
        document.getElementById('alarm-count').textContent = statusCounts.offline || 0;
    }

    function updateLegend() {
        const legend = document.getElementById('legend-items');
        legend.innerHTML = '';

        const statuses = {
        'online':   { color: '#2ecc71', label: 'Online' },
        'repairing':{ color: '#f39c12', label: 'Repairing' },
        'offline':  { color: '#e74c3c', label: 'Offline' },
        'unknown':  { color: '#3498db', label: 'Unknown' }
    };

        Object.entries(statuses).forEach(([status, { color, label }]) => {
            const count = statusCounts[status] || 0;
            if (count > 0) {
                const item = document.createElement('div');
                item.className = 'legend-item';
                item.innerHTML = `
                    <span class="legend-color" style="background: ${color}"></span>
                    <span>${label} (${count})</span>
                `;
                legend.appendChild(item);
            }
        });
    }

    function updateSensorSummaryPanel(sensor) {
        document.getElementById('summary-tag').textContent = sensor.tag || '-';
        document.getElementById('summary-type').textContent = sensor.type || '-';
        document.getElementById('summary-status').textContent = sensor.latest_measurement?.status || '-';
        document.getElementById('summary-panel').textContent = sensor.switchgear?.panel || '-';
        document.getElementById('summary-substation').textContent = sensor.switchgear?.substation?.substation_name || '-';

        document.getElementById('sensor-summary-panel').classList.add('active');
    }

    function renderSensorMarkers(substationName, selectedTypes) {
        sensorMarkers.forEach(marker => map.removeLayer(marker));
        sensorMarkers = [];
        statusCounts = { ALARM: 0, AWAIT: 0, NORMAL: 0, UNKNOWN: 0 };

        console.log("🧭 Selected Substation:", substationName);
        console.log("📦 Selected Sensor Types:", selectedTypes);

        sensors.forEach(sensor => {
            const switchgear = sensor.switchgear;
            const sub = switchgear?.substation;

            if (!sub || !switchgear) {
                console.warn("⚠️ Missing switchgear or substation:", sensor);
                return;
            }

            const type = sensor.type;
            // const status = sensor.latest_measurement?.status || 'UNKNOWN'.toUpperCase();
            const status = (sensor.status || 'unknown').toLowerCase();
            // Count status regardless of filters
            statusCounts[status] = (statusCounts[status] || 0) + 1;

            const matchSubstation = substationName === 'all' ||
                sub.substation_name.trim().toLowerCase() === substationName.trim().toLowerCase();
            
            const matchType = selectedTypes.includes('ALL') ||
                selectedTypes.includes(type);

            console.log(` Sensor [${sensor.tag}] — Type: ${type}, Status: ${status}`);
            console.log(` Substation: ${sub.substation_name}`);
            console.log(` Match Substation: ${matchSubstation}, Match Type: ${matchType}`);

            const matchStatus = selectedStatus === 'ALL' || status === selectedStatus;

            if (matchSubstation && matchType && matchStatus) {
                const latNum = parseFloat(sub.latitude);
                const lngNum = parseFloat(sub.longitude);

                if (isNaN(latNum) || isNaN(lngNum)) {
                    console.warn(`Invalid lat/lng for substation: ${sub.substation_name}`, sub.latitude, sub.longitude);
                    return;
                }

                const offsetLat = +(latNum + (Math.random() - 0.5) * 0.0003).toFixed(6);
                const offsetLng = +(lngNum + (Math.random() - 0.5) * 0.0003).toFixed(6);

                const marker = L.circleMarker([offsetLat, offsetLng], {
                    radius: 10,
                    color: getStatusColor(status),
                    fillColor: getStatusColor(status),
                    fillOpacity: 0.8,
                    weight: 2,
                    className: `sensor-marker ${status === 'ALARM' ? 'sensor-alarm' : ''}`
                }).on('click', () => {
                    updateSensorSummaryPanel(sensor);
                }).bindPopup(`
                    <b>${sensor.tag || 'Sensor'}</b><br>
                    Type: ${type || 'N/A'}<br>
                    Status: ${status}<br>
                    Panel: ${switchgear.panel || 'N/A'}<br>
                    Substation: ${sub.substation_name}
                `);

                marker.addTo(map);
                sensorMarkers.push(marker);
            } else {
                console.log(` Skipped - does not match filters.`);
            }
        });

        updateStatusCards();
        updateLegend();

        if (sensorMarkers.length > 0) {
            map.fitBounds(L.featureGroup(sensorMarkers).getBounds(), { padding: [40, 40] });
        } else {
            console.warn("📭 No matching sensors to display.");
        }
        // Initialize on DOM load
        document.addEventListener('DOMContentLoaded', function() {
            // Count all sensors initially
            sensors.forEach(sensor => {
                const status = (sensor.latest_measurement?.status || 'UNKNOWN').toUpperCase();
                statusCounts[status] = (statusCounts[status] || 0);
            });
            updateStatusCards();
            console.table(statusCounts); // for visual table in console

            updateLegend();
        });
    }

    // Sensor type checkbox filters
    document.querySelectorAll('.sensor-filter').forEach(cb => {
        cb.addEventListener('change', function () {
            selectedTypes = Array.from(document.querySelectorAll('.sensor-filter:checked')).map(cb => cb.value);
            const selectedSubstation = document.getElementById('substationFilter').value;
            renderSensorMarkers(selectedSubstation, selectedTypes);
        });
    });

    //to render markers on map
    function renderMarkers(data) {
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        data.forEach(sub => {
            const sensorCount = sensors.filter(s => s.switchgear?.substation?.substation_name === sub.substation_name).length;
            const marker = createSubstationMarker(
                parseFloat(sub.latitude),
                parseFloat(sub.longitude),
                sub.substation_name,
                sub.substation_location,
                sensorCount
            ).addTo(map);

            markers.push(marker);
        });
    }

    function createSubstationMarker(lat, lng, name, location, sensorCount) {
        return L.marker([lat, lng], {
            icon: new L.Icon.Default()
        }).bindPopup(`<b>${name}</b><br>${location}<br><small>Sensors: ${sensorCount}</small>`);
    }

    renderMarkers(substations);
    renderSensorMarkers('all', selectedTypes);

    // filter logic
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

    // Status filter dropdown
    document.getElementById('statusFilter').addEventListener('change', function () {
        selectedStatus = this.value;
        const selectedSubstation = document.getElementById('substationFilter').value;
        renderSensorMarkers(selectedSubstation, selectedTypes);
    });

    // renderSubstations(substations);
    setTimeout(() => {
        map.invalidateSize();
    }, 500);

</script> --}}





{{-- chart script --}}
{{-- <script>
    // Function to initialize charts with responsive settings
    function initializeCharts() {
        // Destroy existing charts if they exist
        if (window.sensorConditionChart) window.sensorConditionChart.destroy();
        if (window.maintenanceChart) window.maintenanceChart.destroy();
        if (window.sensorPerformanceChart) window.sensorPerformanceChart.destroy();

        // Chart 1: Sensor Condition Analytics (Doughnut)
        const sensorConditionCtx = document.getElementById('sensorConditionChart').getContext('2d');
        window.sensorConditionChart = new Chart(sensorConditionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Normal', 'Warning', 'Alarm', 'Offline'],
                datasets: [{
                    data: [65, 15, 10, 10], // Replace with actual data from API
                    backgroundColor: [
                        '#2ecc71', // Normal - green
                        '#f39c12', // Warning - orange
                        '#e74c3c', // Alarm - red
                        '#95a5a6'  // Offline - gray
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: false
                    }
                },
                cutout: '70%'
            }
        });

        // Chart 2: Maintenance Analytics (Bar)
        const maintenanceCtx = document.getElementById('maintenanceChart').getContext('2d');
        window.maintenanceChart = new Chart(maintenanceCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Maintenance Activities',
                    data: [12, 19, 8, 15, 10, 7], // Replace with actual data
                    backgroundColor: '#3498db',
                    borderColor: '#2980b9',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Chart 3: Sensor Performance (Line)
        const performanceCtx = document.getElementById('sensorPerformanceChart').getContext('2d');
        window.sensorPerformanceChart = new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
                datasets: [{
                    label: 'Performance Index',
                    data: [85, 79, 82, 81, 86, 83],
                    fill: false,
                    backgroundColor: '#9b59b6',
                    borderColor: '#9b59b6',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 70,
                        max: 100
                    }
                }
            }
        });
    }

    // Function to fetch chart data from API
    async function fetchChartData() {
        try {
            // Example API calls - replace with your actual endpoints
            const [conditionData, maintenanceData, performanceData] = await Promise.all([
                fetch('/api/sensor-condition').then(res => res.json()),
                fetch('/api/maintenance').then(res => res.json()),
                fetch('/api/sensor-performance').then(res => res.json())
            ]);
            
            // Update charts with real data
            updateChartsWithData(conditionData, maintenanceData, performanceData);
        } catch (error) {
            console.error('Error fetching chart data:', error);
            // Initialize with default data if API fails
            initializeCharts();
        }
    }

    // Function to update charts with fetched data
    function updateChartsWithData(conditionData, maintenanceData, performanceData) {
        // Update Sensor Condition Chart
        window.sensorConditionChart.data.datasets[0].data = [
            conditionData.normal || 0,
            conditionData.warning || 0,
            conditionData.alarm || 0,
            conditionData.offline || 0
        ];
        window.sensorConditionChart.update();

        // Update Maintenance Chart
        window.maintenanceChart.data.labels = maintenanceData.labels || [];
        window.maintenanceChart.data.datasets[0].data = maintenanceData.values || [];
        window.maintenanceChart.update();

        // Update Performance Chart
        window.sensorPerformanceChart.data.labels = performanceData.labels || [];
        window.sensorPerformanceChart.data.datasets[0].data = performanceData.values || [];
        window.sensorPerformanceChart.update();
    }

    // Initialize charts when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
        // Uncomment to fetch real data
        // fetchChartData();
        
        // Handle window resize to maintain chart responsiveness
        window.addEventListener('resize', function() {
            if (window.sensorConditionChart) window.sensorConditionChart.resize();
            if (window.maintenanceChart) window.maintenanceChart.resize();
            if (window.sensorPerformanceChart) window.sensorPerformanceChart.resize();
        });
    });

    // Make sure charts resize properly within their containers
    function handleChartResize() {
        const chartContainers = document.querySelectorAll('.chart-container');
        chartContainers.forEach(container => {
            const canvas = container.querySelector('canvas');
            if (canvas) {
                canvas.style.width = '100%';
                canvas.style.height = '100%';
            }
        });

        if (window.sensorConditionChart) window.sensorConditionChart.resize();
        if (window.maintenanceChart) window.maintenanceChart.resize();
        if (window.sensorPerformanceChart) window.sensorPerformanceChart.resize();
    }

    // Call this after any layout changes that might affect chart containers
    setTimeout(handleChartResize, 100);
</script> --}}


@endpush
