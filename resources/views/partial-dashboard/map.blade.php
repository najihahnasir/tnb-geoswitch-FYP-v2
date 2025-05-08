@push('after-styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    #map {
        height: 500px;
        width: 100%;
        border-radius: 12px;
    }

    .legend-box {
        position: absolute;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        /* background: white; */
        padding: 10px 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-width: 200px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(2px);
        border: 1px solid #eee;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin: 5px 0;
        font-size: 13px;
        line-height: 1.4;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
        display: inline-block;
        flex-shrink: 0;
    }
    .substation-marker {
        transition: transform 0.2s ease;
    }
    .substation-marker:hover {
        transform: scale(1.2) translateY(-5px);
        z-index: 1000 !important;
    }
    
    .sensor-marker {
        transition: all 0.3s ease;
    }
    .sensor-marker:hover {
        stroke: white;
        stroke-width: 3px;
        transform: scale(1.3);
    }
    
    /* Pulse animation for alarm sensors */
    .sensor-alarm {
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }
    #sensor-summary-panel {
        position: fixed;
        right: -320px;
        top: 100px;
        width: 300px;
        background: #fff;
        border-left: 1px solid #ccc;
        box-shadow: -2px 0 6px rgba(0,0,0,0.1);
        padding: 20px;
        z-index: 2000;
        height: auto;
        transition: right 0.3s ease;
    }
    #sensor-summary-panel.active {
        right: 20px;
    }
    #sensor-summary-panel h4 {
        margin-top: 0;
        font-size: 16px;
        font-weight: 600;
    }
    #sensor-summary-panel .row {
        margin-bottom: 10px;
    }
    #sensor-summary-panel .row label {
        font-weight: 500;
        display: block;
        font-size: 14px;
        color: #555;
    }
    #sensor-summary-panel .row span {
        font-size: 14px;
        color: #333;
    }
</style>
@endpush
<div>
    <!-- Map Section -->
    <div class="map-section">
    <h3 style="margin-top: 0;">Substation Map</h3>
    
    <div class="controls-container">
        <div class="control-group">
            <label for="substationFilter"><strong>Substation:</strong></label>
            <select id="substationFilter" class="form-control">
                <option value="all">All Substations</option>
                @foreach ($substations as $s)
                    <option value="{{ $s->substation_name }}">{{ $s->substation_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="control-group">
            <strong>Filter by Sensor Status:</strong>
            <div style="margin-top: 8px;">
                <select id="statusFilter" class="form-control">
                    <option value="ALL">All Statuses</option>
                    <option value="online">Online</option>
                    <option value="repairing">Repairing</option>
                    <option value="offline">Offline</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <strong>Filter by Sensor Type:</strong>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px;">
                <div class="form-check">
                    <input class="form-check-input sensor-filter" type="checkbox" value="ALL" id="allTypes" checked>
                    <label class="form-check-label" for="allTypes">All Types</label>
                </div>
                @foreach(['Partial Discharge','Thermal'] as $type)
                    <div class="form-check">
                        <input class="form-check-input sensor-filter" type="checkbox" value="{{ $type }}" id="{{ Str::slug($type) }}">
                        <label class="form-check-label" for="{{ Str::slug($type) }}">{{ $type }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="control-group" style="align-self: end;">
            <button id="resetFiltersBtn" class="btn btn-secondary" style="margin-top: 8px;">
                Reset All Filters
            </button>
        </div>
    </div>
    <div id="map-container" style="position: relative;">
        <div id="map"></div>
        <div class="legend-box" style="
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 200px;
        ">
            <h4 style="margin-top: 0; font-size: 14px; margin-bottom: 8px;">Status Legend</h4>
            <div id="legend-items"></div>
        </div>
    </div>
    
    {{-- <div class="legend-box">
        <h4 style="margin-top: 0; font-size: 14px; margin-bottom: 8px;">Status Legend</h4>
        <div id="legend-items"></div>
    </div>

    <div id="map"></div> --}}
    </div>
    <div id="sensor-summary-panel">
        <h4>Sensor Summary</h4>
        <div class="row"><label>Tag:</label><span id="summary-tag">-</span></div>
        <div class="row"><label>Type:</label><span id="summary-type">-</span></div>
        <div class="row"><label>Status:</label><span id="summary-status">-</span></div>
        <div class="row"><label>Panel:</label><span id="summary-panel">-</span></div>
        <div class="row"><label>Substation:</label><span id="summary-substation">-</span></div>
    </div>
</div>

{{-- @push('after-scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script> --}}
{{-- latest script map --}}
{{-- <script>
    var map = L.map('map').setView([3.1390, 101.6869], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    //substations passed array from controller
    // var substations = @json($substations ?? []);
    // var markers = [];
    // const sensors = @json($sensors ?? []);
    // let sensorMarkers = [];
    // let selectedTypes = []; 
    // let statusCounts = { online: 0, repairing: 0, offline: 0, unknown: 0 };
    // let selectedStatus = 'ALL';
    var substations = @json($substations ?? []);
    const sensors = @json($sensors ?? []);
    var markers = [];
    var sensorMarkers = [];
    var statusCounts = { online: 0, repairing: 0, offline: 0, unknown: 0 };
    var selectedStatus = 'ALL';
    var selectedTypes = [];

    // Get color based on status
    function getStatusColor(status) {
        switch ((status || '').toLowerCase()) {
            case 'online': return '#2ecc71';    
            case 'repairing': return '#f39c12'; 
            case 'offline': return '#e74c3c';   
            default: return '#3498db';
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
@push('after-scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the map
        var map = L.map('map').setView([3.1390, 101.6869], 11);

        // Add the base tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Data from controller
        var substations = @json($substations ?? []);
        const sensors = @json($sensors ?? []);
        
        // Map elements storage
        var markers = [];
        var sensorMarkers = [];
        
        // Filter states
        var statusCounts = { online: 0, repairing: 0, offline: 0, unknown: 0 };
        var selectedStatus = 'ALL';
        var selectedTypes = ['ALL']; // Default to show all types

        // Get color based on status
        function getStatusColor(status) {
            switch ((status || '').toLowerCase()) {
                case 'online': return '#2ecc71';    
                case 'repairing': return '#f39c12'; 
                case 'offline': return '#e74c3c';   
                default: return '#3498db';
            }
        }

        // Update status cards
        function updateStatusCards() {
            document.getElementById('normal-count').textContent = statusCounts.online || 0;
            document.getElementById('await-count').textContent = statusCounts.repairing || 0;
            document.getElementById('alarm-count').textContent = statusCounts.offline || 0;
        }

        // Update legend
        function updateLegend() {
            const legend = document.getElementById('legend-items');
            legend.innerHTML = '';

            const statuses = {
                'online': { color: '#2ecc71', label: 'Online' },
                'repairing': { color: '#f39c12', label: 'Repairing' },
                'offline': { color: '#e74c3c', label: 'Offline' },
                'unknown': { color: '#3498db', label: 'Unknown' }
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

        // Update sensor summary panel
        function updateSensorSummaryPanel(sensor) {
            document.getElementById('summary-tag').textContent = sensor.tag || '-';
            document.getElementById('summary-type').textContent = sensor.type || '-';
            document.getElementById('summary-status').textContent = sensor.latest_measurement?.status || '-';
            document.getElementById('summary-panel').textContent = sensor.switchgear?.panel || '-';
            document.getElementById('summary-substation').textContent = sensor.switchgear?.substation?.substation_name || '-';
            document.getElementById('sensor-summary-panel').classList.add('active');
        }

        // Render sensor markers based on filters
        function renderSensorMarkers(substationName, selectedTypes) {
            // Clear existing markers
            sensorMarkers.forEach(marker => map.removeLayer(marker));
            sensorMarkers = [];
            statusCounts = { online: 0, repairing: 0, offline: 0, unknown: 0 };

            sensors.forEach(sensor => {
                const switchgear = sensor.switchgear;
                const sub = switchgear?.substation;
                const type = sensor.type;
                const status = (sensor.status || 'unknown').toLowerCase();

                // Count status regardless of filters
                statusCounts[status] = (statusCounts[status] || 0) + 1;

                // Check filters
                const matchSubstation = substationName === 'all' ||
                    (sub?.substation_name?.trim().toLowerCase() === substationName.trim().toLowerCase());
                
                const matchType = selectedTypes.includes('ALL') ||
                    selectedTypes.includes(type);

                const matchStatus = selectedStatus === 'ALL' || 
                    status === selectedStatus.toLowerCase();

                if (matchSubstation && matchType && matchStatus) {
                    const latNum = parseFloat(sub?.latitude);
                    const lngNum = parseFloat(sub?.longitude);

                    if (isNaN(latNum) || isNaN(lngNum)) {
                        console.warn(`Invalid lat/lng for substation: ${sub?.substation_name}`, sub?.latitude, sub?.longitude);
                        return;
                    }

                    // Add slight offset to prevent marker overlap
                    const offsetLat = +(latNum + (Math.random() - 0.5) * 0.0003).toFixed(6);
                    const offsetLng = +(lngNum + (Math.random() - 0.5) * 0.0003).toFixed(6);

                    const marker = L.circleMarker([offsetLat, offsetLng], {
                        radius: 10,
                        color: getStatusColor(status),
                        fillColor: getStatusColor(status),
                        fillOpacity: 0.8,
                        weight: 2,
                        className: `sensor-marker ${status === 'offline' ? 'sensor-alarm' : ''}`
                    }).on('click', () => {
                        updateSensorSummaryPanel(sensor);
                    }).bindPopup(`
                        <b>${sensor.tag || 'Sensor'}</b><br>
                        Type: ${type || 'N/A'}<br>
                        Status: ${status}<br>
                        Panel: ${switchgear?.panel || 'N/A'}<br>
                        Substation: ${sub?.substation_name || 'N/A'}
                    `);

                    marker.addTo(map);
                    sensorMarkers.push(marker);
                }
            });

            updateStatusCards();
            updateLegend();

            if (sensorMarkers.length > 0) {
                map.fitBounds(L.featureGroup(sensorMarkers).getBounds(), { padding: [40, 40] });
            }
        }

        // Render substation markers
        function renderMarkers(data) {
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            data.forEach(sub => {
                const sensorCount = sensors.filter(s => 
                    s.switchgear?.substation?.substation_name === sub.substation_name
                ).length;
                
                const marker = L.marker([parseFloat(sub.latitude), parseFloat(sub.longitude)], {
                    icon: new L.Icon.Default()
                }).bindPopup(`
                    <b>${sub.substation_name}</b><br>
                    ${sub.substation_location}<br>
                    <small>Sensors: ${sensorCount}</small>
                `).addTo(map);

                markers.push(marker);
            });
        }

        // Initialize filters and event listeners
        function initializeFilters() {
            // Sensor type checkboxes
            document.querySelectorAll('.sensor-filter').forEach(cb => {
                cb.addEventListener('change', function() {
                    selectedTypes = Array.from(document.querySelectorAll('.sensor-filter:checked'))
                        .map(cb => cb.value);
                    
                    if (selectedTypes.length === 0) {
                        // If nothing selected, default to ALL
                        selectedTypes = ['ALL'];
                        document.querySelector('.sensor-filter[value="ALL"]').checked = true;
                    }
                    
                    const selectedSubstation = document.getElementById('substationFilter').value;
                    if (selectedSubstation === 'all') return;
                    renderSensorMarkers(selectedSubstation, selectedTypes);
                });
            });

            // Substation dropdown filter
            document.getElementById('substationFilter').addEventListener('change', function() {
                const selected = this.value;
                if (selected === 'all') {
                    renderMarkers(substations);
                    map.setView([3.1390, 101.6869], 11);
                    // Hide sensors when viewing all substations
                    sensorMarkers.forEach(m => map.removeLayer(m));
                    sensorMarkers = [];
                    return; 
                } else {
                    const filtered = substations.filter(sub => 
                        sub.substation_name === selected
                    );
                    renderMarkers(filtered);
                    if (filtered.length > 0) {
                        map.setView([filtered[0].latitude, filtered[0].longitude], 14);
                    }
                }
                
                renderSensorMarkers(selected, selectedTypes);
            });

            // Status dropdown filter
            document.getElementById('statusFilter').addEventListener('change', function() {
                selectedStatus = this.value;
                const selectedSubstation = document.getElementById('substationFilter').value;
                renderSensorMarkers(selectedSubstation, selectedTypes);
            });
        }

        renderMarkers(substations); // Only show substations on load
        initializeFilters(); // Wait for user interaction to load sensors

        document.getElementById('resetFiltersBtn').addEventListener('click', function () {
        // Reset dropdowns
        document.getElementById('substationFilter').value = 'all';
        document.getElementById('statusFilter').value = 'ALL';

        // Reset checkboxes
        document.querySelectorAll('.sensor-filter').forEach(cb => cb.checked = false);
        document.getElementById('allTypes').checked = true;

        // Update selected values
        selectedTypes = ['ALL'];
        selectedStatus = 'ALL';

        // Re-render map
        renderMarkers(substations);
        map.setView([3.1390, 101.6869], 11);

        // Remove any existing sensor markers
        sensorMarkers.forEach(marker => map.removeLayer(marker));
        sensorMarkers = [];

        // Clear legend & summary panel
        document.getElementById('legend-items').innerHTML = '';
        document.getElementById('sensor-summary-panel').classList.remove('active');
    });


        // Handle mutually exclusive logic for sensor type filters
        document.querySelectorAll('.sensor-filter').forEach(cb => {
            cb.addEventListener('change', function () {
                const allTypesCheckbox = document.getElementById('allTypes');
                const selectedSubstation = document.getElementById('substationFilter').value;

                if (this.value === 'ALL' && this.checked) {
                    // Uncheck all others
                    document.querySelectorAll('.sensor-filter:not(#allTypes)').forEach(c => c.checked = false);
                } else {
                    // Uncheck 'All Types' if any specific is selected
                    if (this.checked) {
                        allTypesCheckbox.checked = false;
                    }

                    // If none are selected, re-check 'All Types'
                    const specificChecked = Array.from(document.querySelectorAll('.sensor-filter:not(#allTypes)'))
                        .some(c => c.checked);
                    if (!specificChecked) {
                        allTypesCheckbox.checked = true;
                    }
                }

                selectedTypes = Array.from(document.querySelectorAll('.sensor-filter:checked'))
                    .map(cb => cb.value);

                // ✅ Prevent rendering sensors if "all" substations is selected
                if (selectedSubstation === 'all') return;

                renderSensorMarkers(selectedSubstation, selectedTypes);
            });
        });


        // Fix map sizing issues
        setTimeout(() => {
            map.invalidateSize();
        }, 500);
    });
</script>

@endpush
