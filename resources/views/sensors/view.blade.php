
@extends('layouts.master')

@section('title', 'Sensor Details')
@section('page-title', 'Sensor Information')

@section('content')
<div class="container py-2">
    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #026670;">
            <h4 class="mb-0">Sensor Detail View</h4>
            <a href="{{ route('sensors.index') }}" class="btn btn-light btn-sm">Back to List</a>
        </div>
        <div class="card-body">
            {{-- Tabs --}}
            <ul class="nav nav-tabs" id="sensorTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab">Log History</button>
                </li>
            </ul>

            <div class="tab-content p-3 border border-top-0" id="sensorTabContent">
                {{-- Overview Tab --}}
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sensor Tag:</label>
                            <div>{{ $sensor->tag }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Sensor Type:</label>
                            <div>{{ $sensor->type }}</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status:</label>
                            @php
                                $status = strtolower(trim($sensor->status));
                                $badgeClass = match($status) {
                                    'online' => 'bg-success',
                                    'repairing' => 'bg-warning text-dark',
                                    'offline' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ ucfirst($sensor->status) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Substation:</label>
                            <div>{{ optional($sensor->switchgear->substation)->substation_name ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Panel Name:</label>
                            <div>{{ optional($sensor->switchgear)->panel_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Lineup:</label>
                            <div>{{ optional($sensor->switchgear)->lineup ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Compartment:</label>
                            <div>{{ optional($sensor)->compartment ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Switchgear Code:</label>
                            <div>{{ optional($sensor->switchgear)->switchgear_code ?? '-' }}</div>
                        </div>
                    </div>

                </div>
                {{-- Log History Tab --}}
                <div class="tab-pane fade" id="logs" role="tabpanel">
                    <input type="text" class="form-control mb-3" placeholder="Search logs..." id="logSearch">

                    @if($sensor->logReports && $sensor->logReports->count())
                    <div class="d-flex justify-content-end gap-2 mb-2">
                        <a href="{{ route('sensors.exportLogs', ['id' => $sensor->id, 'format' => 'pdf']) }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </a>
                        <a href="{{ route('sensors.exportLogs', ['id' => $sensor->id, 'format' => 'csv']) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-csv me-1"></i> Export CSV
                        </a>
                    </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="logTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Case ID</th>
                                        <th>Substation Name</th>
                                        <th>Switchgear Panel</th>
                                        <th>Sensor Tag</th>
                                        <th>Report By</th>
                                        <th>Description</th>
                                        {{-- <th>Title</th> --}}
                                        <th>Status</th>
                                        <th>Report Date</th>
                                        <th>Resolved Time</th>
                                        <th>Downtime Duration</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sensor->logReports as $report)
                                        <tr>
                                            {{-- <td>#{{ $report->id }}</td> --}}
                                            <td>{{ $report->case_id ?? '-' }}</td>
                                            <td>{{ optional($report->substation)->substation_name ?? '-' }}</td>
                                            <td>
                                                @if($report->switchgear)
                                                    <strong>{{ $report->switchgear->panel_name ?? '-' }}</strong><br>
                                                    <small class="text-muted">
                                                        {{ $report->switchgear->compartment ?? '-' }} | 
                                                        Lineup: {{ $report->switchgear->lineup ?? '-' }}<br>
                                                        Code: {{ $report->switchgear->switchgear_code ?? '-' }}
                                                    </small>
                                                @else
                                                    -
                                                @endif
                                            </td>                                            
                                            <td>
                                                <strong>{{ $report->sensor->tag ?? '-' }}</strong><br>
                                                <small class="text-muted">
                                                    {{ $report->sensor->type ?? '-' }}
                                                </small>
                                            </td>
                                            <td>{{ optional($report->reporter)->name ?? '-' }}</td>
                                            <td>{{ Str::limit($report->description ?? '-', 50) }}</td>
                                            {{-- <td>{{ $report->title ?? '-' }}</td> --}}
                                            <td>
                                                @php
                                                    $normalizedStatus = strtolower(trim($report->status));
                                                    $statusColor = match($normalizedStatus) {
                                                        'open' => 'danger',
                                                        'repairing' => 'warning text-dark',
                                                        'resolved', 'closed' => 'success',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusColor }}">
                                                    {{ ucfirst($normalizedStatus) }}
                                                </span>
                                            </td>

                                            <td>{{ $report->reported_at ? $report->reported_at->format('Y-m-d H:i') : '-' }}</td>
                                            <td>{{ $report->resolved_at ? $report->resolved_at->format('Y-m-d H:i') : '-' }}</td>
                                            <td>
                                                @if($report->status === 'open')
                                                    <span class="live-countdown text-danger fw-bold"
                                                        data-reported-at="{{ $report->reported_at->format('Y-m-d H:i:s') }}">
                                                        IN PROGRESS – calculating...
                                                    </span>
                                                @else
                                                    {{ $report->calculated_duration }}
                                                @endif
                                            </td>
                                            <td>{{ $report->remarks ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('log-reports.show', $report) }}" class="btn btn-sm btn-info">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No sensor log reports available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
    document.getElementById('logSearch').addEventListener('keyup', function () {
        let value = this.value.toLowerCase();
        document.querySelectorAll('#logTable tbody tr').forEach(row => {
            row.style.display = [...row.cells].some(td => td.innerText.toLowerCase().includes(value)) ? '' : 'none';
        });
    });
</script>
<script>
    function updateLiveCountdowns() {
        const elements = document.querySelectorAll('.live-countdown');
        const now = new Date();

        elements.forEach(el => {
            const reportedAt = new Date(el.getAttribute('data-reported-at'));
            const diff = Math.floor((now - reportedAt) / 1000); // seconds

            const days = Math.floor(diff / (60 * 60 * 24));
            const hours = Math.floor((diff % (60 * 60 * 24)) / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = diff % 60;

            el.innerText = `IN PROGRESS – ${days}d ${hours}h ${minutes}m ${seconds}s`;
        });
    }

    // Initial call and interval
    updateLiveCountdowns();
    setInterval(updateLiveCountdowns, 1000);
</script>
@endpush




