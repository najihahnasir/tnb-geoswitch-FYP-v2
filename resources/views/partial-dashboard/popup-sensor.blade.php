<!-- Sensor Status Modal -->
<div class="modal fade" id="sensorStatusModal" tabindex="-1" aria-labelledby="sensorStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" id="sensorStatusModalHeader">
        <h5 class="modal-title text-white" id="sensorStatusModalLabel">Sensor Status</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="sensorStatusModalBody">
        <!-- Dynamic content will be injected -->
      </div>
    </div>
  </div>
</div>

<!-- Sensor Templates -->
<div id="onlineSensorTemplate" style="display: none;">
    @php
        // $grouped = $onlineSensors->groupBy(fn($s) => $s->switchgear->substation->substation_name ?? 'Unknown');
        $grouped = $onlineSensors->groupBy(fn($s) => $s->switchgear->substation->id ?? 'unknown');

    @endphp
    @foreach($grouped as $substationId => $sensors)
        @php $firstSensor = $sensors->first(); @endphp
        @php $substation = $firstSensor->switchgear->substation ?? null; @endphp

        @if($substation)
        <div class="border rounded-lg p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-primary">📍 {{ $substation->substation_name }}</strong>
                <span class="badge bg-success">{{ $sensors->count() }} Online</span>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($sensors as $sensor)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $sensor->tag }}</strong> ({{ $sensor->type }})<br>
                                <small class="text-muted">
                                    Panel: {{ $sensor->switchgear->panel_name ?? '-' }}<br>
                                    Lineup: {{ $sensor->switchgear->lineup ?? '-' }} | 
                                    Compartment: {{ $sensor->switchgear->compartment ?? '-' }}<br>
                                    Switchgear Code: {{ $sensor->switchgear->switchgear_code ?? '-' }}
                                </small>
                            </div>
                            <span class="text-success fw-semibold small">{{ ucfirst($sensor->status) }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-2 text-end">
                <a href="{{ route('sensors.index', ['filter' => 'online', 'substation' => $substation->id]) }}" class="text-success text-decoration-none small me-2">
                    View Online →
                </a>
            </div>
        </div>
        @endif
    @endforeach

</div>

<div id="repairingSensorTemplate" style="display: none;">
    @php
        $grouped = $repairingSensors->groupBy(fn($s) => $s->switchgear->substation->id ?? 'unknown');
    @endphp
    @foreach($grouped as $substationId => $sensors)
        @php $substation = $sensors->first()->switchgear->substation ?? null; @endphp
        @if($substation)
        <div class="border rounded-lg p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-warning">📍 {{ $substation->substation_name }}</strong>
                <span class="badge bg-warning text-dark">{{ $sensors->count() }} Repairing</span>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($sensors as $sensor)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $sensor->tag }}</strong> ({{ $sensor->type }})<br>
                                <small class="text-muted">
                                    Panel: {{ $sensor->switchgear->panel_name ?? '-' }}<br>
                                    Lineup: {{ $sensor->switchgear->lineup ?? '-' }} | 
                                    Compartment: {{ $sensor->switchgear->compartment ?? '-' }}<br>
                                    Switchgear Code: {{ $sensor->switchgear->switchgear_code ?? '-' }}
                                </small>
                            </div>
                            <span class="text-warning fw-semibold small">{{ ucfirst($sensor->status) }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-2 text-end">
                <a href="{{ route('sensors.index', ['filter' => 'repairing', 'substation' => $substation->id]) }}" class="text-warning text-decoration-none small me-2">
                    View Repairing →
                </a>
            </div>
        </div>
        @endif
    @endforeach
</div>

<div id="offlineSensorTemplate" style="display: none;">
    @php
        $grouped = $offlineSensors->groupBy(fn($s) => $s->switchgear->substation->id ?? 'unknown');
    @endphp
    @foreach($grouped as $substationId => $sensors)
        @php $substation = $sensors->first()->switchgear->substation ?? null; @endphp
        @if($substation)
        <div class="border rounded-lg p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-danger">📍 {{ $substation->substation_name }}</strong>
                <span class="badge bg-danger">{{ $sensors->count() }} Offline</span>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($sensors as $sensor)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $sensor->tag }}</strong> ({{ $sensor->type }})<br>
                                <small class="text-muted">
                                    Panel: {{ $sensor->switchgear->panel_name ?? '-' }}<br>
                                    Lineup: {{ $sensor->switchgear->lineup ?? '-' }} | 
                                    Compartment: {{ $sensor->switchgear->compartment ?? '-' }}<br>
                                    Switchgear Code: {{ $sensor->switchgear->switchgear_code ?? '-' }}
                                </small>
                            </div>
                            <span class="text-danger fw-semibold small">{{ ucfirst($sensor->status) }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-2 text-end">
                <a href="{{ route('sensors.index', ['filter' => 'offline', 'substation' => $substation->id]) }}" class="text-danger text-decoration-none small me-2">
                    View Offline →
                </a>
            </div>
        </div>
        @endif
    @endforeach
</div>



@push('after-scripts')
<script>
    function openSensorStatusModal(title, htmlContent) {
        const header = document.getElementById('sensorStatusModalHeader');
        const label = document.getElementById('sensorStatusModalLabel');

        label.innerText = title;
        document.getElementById('sensorStatusModalBody').innerHTML = htmlContent;

        // Set header background color by title
        header.classList.remove('bg-secondary', 'bg-warning', 'bg-danger');
        if (title.includes('Online')) header.classList.add('bg-secondary');
        else if (title.includes('Repairing')) header.classList.add('bg-warning');
        else if (title.includes('Offline')) header.classList.add('bg-danger');
        else header.classList.add('bg-dark');

        new bootstrap.Modal(document.getElementById('sensorStatusModal')).show();
    }

    function showOnlineSensors() {
        const html = document.getElementById('onlineSensorTemplate').innerHTML;
        openSensorStatusModal('🟢 Online Sensors', html);
    }

    function showRepairingSensors() {
        const html = document.getElementById('repairingSensorTemplate').innerHTML;
        openSensorStatusModal('🟡 Repairing Sensors', html);
    }

    function showOfflineSensors() {
        const html = document.getElementById('offlineSensorTemplate').innerHTML;
        openSensorStatusModal('🔴 Offline Sensors', html);
    }
</script>
@endpush
