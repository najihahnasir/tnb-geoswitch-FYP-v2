@push('after-styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    #map {
        height: 500px;
        width: 100%;
        border-radius: 12px;
        z-index: 400;
    }
    .map-section {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-top: 10px;
    }
    #map-container,
    .map-section {
        position: relative;
        z-index: 1; /* stay behind fixed popups */
    }

     .controls-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 16px;
        margin-bottom: 10px;
    }
    
    .control-group {
        background: #f8f9fa;
        padding: 15px 20px;
        border-radius: 8px;
        border: 1px solid #e2e2e2;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
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
        /* background: rgba(255, 255, 255, 0.9); */
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(2px);
        border: 1px solid #eee;
    }

    .sensor-marker, .substation-marker {
        z-index: 500;
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
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background-color: #2ecc71;
        border: 2px solid white;
        transition: all 0.3s ease;
    }
    .sensor-marker:hover {
        stroke: white;
        stroke-width: 3px;
        transform: scale(1.3);
    }
    
    /* Pulse animation for alarm sensors */
    /* .sensor-alarm {
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    } */
     /* Pulse animation for alarm sensors */
    /* .sensor-alarm {
        position: relative;
        z-index: 1000;
    } */

    /* Blinking circular border */
    .sensor-alarm {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background-color: #e74c3c;
        position: relative;
        animation: blink-marker 1s infinite ease-in-out;
        border: 2px solid white;
        box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7);
    }

    @keyframes blink-marker {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(231, 76, 60, 0);
        }
    }

    /* #sensor-summary-panel {
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
    } */

     #sensor-summary-panel {
        position: fixed;
        right: -320px;
        top: 100px;
        width: 300px;
        background: #fff;
        border-left: 1px solid #ddd;
        box-shadow: -2px 0 8px rgba(0,0,0,0.1);
        padding: 20px 25px;
        z-index: 2000;
        height: auto;
        transition: right 0.3s ease;
        border-radius: 8px 0 0 8px;
        font-family: 'Segoe UI', sans-serif;
    }

    #sensor-summary-panel.active {
        right: 20px;
    }

    .summary-row {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
    }

    .summary-row label {
        font-weight: 600;
        font-size: 14px;
        color: #555;
        margin-bottom: 4px;
    }

    .summary-row span {
        font-size: 14px;
        color: #222;
    }

    /* Optional colored badge styling for sensor status */
    .badge-status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
        background: #eee;
        color: #444;
    }

    .badge-status.online { background: #d4f4dd; color: #2ecc71; }
    .badge-status.repairing { background: #fff4d6; color: #f39c12; }
    .badge-status.offline { background: #fbe2e2; color: #e74c3c; }

</style>
@endpush
<div>
    <!-- Map Section -->
    <div class="map-section">
    {{-- <h5 style="margin-top: 0;">Substation Map</h5> --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Substation Map</h5>
        <button id="resetFiltersBtn" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-undo-alt me-1"></i> Reset Filters
        </button>
    </div>

    <div class="controls-container">
        <div class="control-group">
            <label for="substationFilter" class="form-label fw-semibold">Substation</label>
            <select id="substationFilter" class="form-select">
                <option value="all">All Substations</option>
                @foreach ($substations as $s)
                    <option value="{{ $s->substation_name }}">{{ $s->substation_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="control-group">
            <label for="statusFilter" class="form-label fw-semibold">Sensor Status</label>
            <select id="statusFilter" class="form-select">
                <option value="ALL">All Statuses</option>
                <option value="online">Online</option>
                <option value="repairing">Repairing</option>
                <option value="offline">Offline</option>
            </select>
        </div>

        <div class="control-group">
            <label class="form-label fw-semibold">Sensor Type</label>
            <div class="d-flex flex-wrap gap-2 mt-2">
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

        {{-- <div class="control-group align-self-end">
            <button id="resetFiltersBtn" class="btn btn-secondary w-100 mt-2">
                Reset Filters
            </button>
        </div> --}}
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
    
    {{-- <div id="sensor-summary-panel">
        <button class="close-btn" id="summary-close-btn">✖</button>
        <h5>Sensor Summary</h5>

        <div class="row"><label>Tag:</label><span id="summary-tag">-</span></div>
        <div class="row"><label>Type:</label><span id="summary-type">-</span></div>
        <div class="row"><label>Status:</label><span id="summary-status">-</span></div>
        <div class="row"><label>Panel:</label><span id="summary-panel">-</span></div>
        <div class="row"><label>Substation:</label><span id="summary-substation">-</span></div>
    </div> --}}
    <div id="sensor-summary-panel">
    <button class="close-btn" id="summary-close-btn" style="position: absolute; top: 10px; right: 10px; background: transparent; border: none; font-size: 18px; cursor: pointer;">✖</button>
    
    <h4 style="margin-bottom: 20px; font-size: 16px; font-weight: 600; color: #333;">Sensor Summary</h4>

    <div class="summary-row">
        <label>Tag:</label>
        <span id="summary-tag">-</span>
    </div>
    <div class="summary-row">
        <label>Type:</label>
        <span id="summary-type">-</span>
    </div>
    <div class="summary-row">
        <label>Status:</label>
        <span id="summary-status" class="badge-status">-</span>
    </div>
    <div class="summary-row">
        <label>Panel:</label>
        <span id="summary-panel">-</span>
    </div>
    <div class="summary-row">
        <label>Substation:</label>
        <span id="summary-substation">-</span>
    </div>
</div>

</div>


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

        function updateStatusCards() {
            const online = document.getElementById('online');
            const repairing = document.getElementById('repairing');
            const offline = document.getElementById('offline');

            if (!online || !repairing || !offline) {
                // These elements don't exist (probably not on dashboard), skip update
                return;
            }

            online.textContent = statusCounts.online || 0;
            repairing.textContent = statusCounts.repairing || 0;
            offline.textContent = statusCounts.offline || 0;
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
            const status = sensor.status?.toLowerCase() || '-';
            const statusSpan = document.getElementById('summary-status');
            statusSpan.textContent = status;
            statusSpan.className = 'badge-status ' + status;

            document.getElementById('summary-tag').textContent = sensor.tag || '-';
            document.getElementById('summary-type').textContent = sensor.type || '-';
            document.getElementById('summary-status').textContent = sensor.status || '-';
            document.getElementById('summary-panel').textContent = sensor.switchgear?.panel || '-';
            document.getElementById('summary-substation').textContent = sensor.switchgear?.substation?.substation_name || '-';

            document.getElementById('sensor-summary-panel').classList.add('active');

            function closeSensorSummaryPanel() {
                document.getElementById('sensor-summary-panel').classList.remove('active');
            }

            // Add the event listener here (NEW CODE)
            document.getElementById('summary-close-btn').addEventListener('click', closeSensorSummaryPanel);
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

                console.log(`Sensor ${sensor.tag} in ${sub?.substation_name} → Status: ${status}, Type: ${type}`);

                
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

                    // const marker = L.circleMarker([offsetLat, offsetLng], {
                    //     radius: 10,
                    //     color: getStatusColor(status),
                    //     fillColor: getStatusColor(status),
                    //     fillOpacity: 0.8,
                    //     weight: 2,
                    //     className: `sensor-marker ${status.toLowerCase() === 'offline' ? 'sensor-alarm' : ''}`
                    // }).on('click', () => {
                    //     updateSensorSummaryPanel(sensor);
                    // }).bindPopup(`
                    //     <b>${sensor.tag || 'Sensor'}</b><br>
                    //     Type: ${type || 'N/A'}<br>
                    //     Status: ${status}<br>
                    //     Panel: ${switchgear?.panel || 'N/A'}<br>
                    //     Substation: ${sub?.substation_name || 'N/A'}
                    // `);

                    const icon = L.divIcon({
                        className: '',
                        html: `<div class="sensor-marker ${status === 'offline' ? 'sensor-alarm' : ''}" style="background-color: ${getStatusColor(status)};"></div>`,                        
                        iconSize: [18, 18],
                        iconAnchor: [9, 9]
                    });

                    const marker = L.marker([offsetLat, offsetLng], {
                        icon: icon
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

            // data.forEach(sub => {
            //     const sensorCount = sensors.filter(s => 
            //         s.switchgear?.substation?.substation_name === sub.substation_name
            //     ).length;
                
            //     const marker = L.marker([parseFloat(sub.latitude), parseFloat(sub.longitude)], {
            //         icon: new L.Icon.Default()
            //     }).bindPopup(`
            //         <b>${sub.substation_name}</b><br>
            //         ${sub.substation_location}<br>
            //         <small>Sensors: ${sensorCount}</small>
            //     `).addTo(map);

            //     markers.push(marker);
            // });
            data.forEach(sub => {
                const sensorsAtSub = sensors.filter(s =>
                    s.switchgear?.substation?.substation_name === sub.substation_name
                );

                const sensorCount = sensorsAtSub.length;

                // Map sensor type to its status
                // const sensorDetails = sensorsAtSub.map(s => {
                //     const type = s.type || 'Unknown';
                //     const status = s.status?.toLowerCase() || 'unknown';
                //     const color = status === 'online' ? '#2ecc71' :
                //                 status === 'repairing' ? '#f39c12' :
                //                 status === 'offline' ? '#e74c3c' : '#ccc';
                //     return `<li>${type}: <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${color};margin:0 6px 0 4px;"></span>${status}</li>`;
                // }).join('');
                const grouped = {};
                sensorsAtSub.forEach(s => {
                const key = `${s.type || 'Unknown'}-${s.status || 'unknown'}-${s.panel || 'Unknown Panel'}`;
                    grouped[key] = (grouped[key] || 0) + 1;
                });

                const sensorDetails = Object.entries(grouped).map(([key, count]) => {
                    const [type, status] = key.split('-');
                    const color = getStatusColor(status);
                    return `<li>${type} — ${count} <span style="background:${color};width:10px;height:10px;border-radius:50%;display:inline-block;margin-left:6px;"></span> ${status}</li>`;
                }).join('');


                // const popupContent = `
                //     <b>${sub.substation_name}</b><br>
                //     <strong>Total Sensors: ${sensorCount}</strong><br>
                //     <strong>Types:</strong><ul style="padding-left: 16px; margin: 5px 0;">${sensorDetails}</ul>
                // `;
                const popupContent = `
                    <div style="min-width: 180px;">
                        <div style="font-weight: 600; font-size: 14px; margin-bottom: 2px;">📍 ${sub.substation_name}</div>
                        <div style="font-size: 13px; margin-bottom: 2px;">
                            <strong>Total Sensors:</strong> ${sensorCount}
                        </div>
                        <div style="font-size: 13px;"><strong></strong></div>
                        <ul style="list-style: none; padding: 0; margin: 5px 0; font-size: 13px;">
                            ${sensorDetails}
                        </ul>
                    </div>
                `;
                const marker = L.marker([parseFloat(sub.latitude), parseFloat(sub.longitude)], {
                    icon: new L.Icon.Default()
                }).bindPopup(popupContent).addTo(map);

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
                // ✅ Wait a bit and then zoom to visible sensors
                setTimeout(() => {
                    if (sensorMarkers.length > 0) {
                        map.fitBounds(L.featureGroup(sensorMarkers).getBounds(), { padding: [40, 40] });
                    }
                }, 100); // Delay just enough to let markers render
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

        // ✅ Avoid rendering sensors when 'All Substations' selected
        // Just prepare the legend based on sensor counts
        sensors.forEach(sensor => {
            const status = (sensor.status || 'unknown').toLowerCase();
            statusCounts[status] = (statusCounts[status] || 0) + 1;
        });


        // ✅ Force legend to show on initial load
        updateLegend();


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
