{{-- @extends('layouts.master')

@section('title', 'Sensor Details')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Sensor Detail View</h4>
            <a href="{{ route('sensors.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <div class="card-body">
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
                    <span class="badge 
                        {{ $sensor->status === 'online' ? 'bg-success' : ($sensor->status === 'repairing' ? 'bg-warning text-dark' : 'bg-danger') }}">
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
                    <label class="form-label fw-bold">Panel:</label>
                    <div>{{ optional($sensor->switchgear)->panel ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Reported By:</label>
                    <div>{{ $sensor->reported_by ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Reported At:</label>
                    <div>{{ $sensor->report_date ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Rectified By:</label>
                    <div>{{ $sensor->rectified_by ?? 'Pending' }}</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Report Description:</label>
                <div class="border p-3 bg-light rounded">{{ $sensor->report ?? 'No report available.' }}</div>
            </div>

            @if($sensor->statusHistory && $sensor->statusHistory->count())
            <div class="mt-4">
                <h5>Status History</h5>
                <table class="table table-bordered table-striped mt-2">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Old Status</th>
                            <th>New Status</th>
                            <th>Changed By</th>
                            <th>Changed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sensor->statusHistory as $index => $history)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ ucfirst($history->old_status) }}</td>
                            <td>{{ ucfirst($history->new_status) }}</td>
                            <td>{{ optional($history->user)->name ?? 'Unknown' }}</td>
                            <td>{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection --}}

{{-- @extends('layouts.master')

@section('title', 'Sensor Details')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Sensor Detail View</h4>
            <a href="{{ route('sensors.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <div class="card-body">
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
                    <span class="badge 
                        {{ $sensor->status === 'online' ? 'bg-success' : ($sensor->status === 'repairing' ? 'bg-warning text-dark' : 'bg-danger') }}">
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
                    <label class="form-label fw-bold">Panel:</label>
                    <div>{{ optional($sensor->switchgear)->panel ?? '-' }}</div>
                </div>
            </div>

            <div class="mt-4">
                <h5>Sensor Log Reports</h5>
                @if($sensor->logReports && $sensor->logReports->count())
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Reported By</th>
                            <th>Reported At</th>
                            <th>Old Status</th>
                            <th>New Status</th>
                            <th>Status Changed By</th>
                            <th>Status Changed At</th>
                            <th>Report Status</th>
                            <th>Resolved At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sensor->logReports as $index => $report)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $report->title }}</td>
                            <td>{{ Str::limit($report->description ?? '-', 50) }}</td>
                            <td>{{ optional($report->reporter)->name ?? 'Unknown' }}</td>
                            <td>{{ $report->reported_at ? \Carbon\Carbon::parse($report->reported_at)->format('Y-m-d H:i') : '-' }}</td>
                            <td>{{ ucfirst($report->old_status ?? '-') }}</td>
                            <td>{{ ucfirst($report->new_status ?? '-') }}</td>
                            <td>{{ optional($report->statusChanger)->name ?? '-' }}</td>
                            <td>{{ $report->status_changed_at ? \Carbon\Carbon::parse($report->status_changed_at)->format('Y-m-d H:i') : '-' }}</td>                            <td>
                                <span class="badge {{ $report->status == 'open' ? 'bg-danger' : 'bg-success' }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td>{{ $report->resolved_at ? \Carbon\Carbon::parse($report->resolved_at)->format('Y-m-d H:i') : '-' }}</td>                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-muted">No sensor log reports available.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection --}}

@extends('layouts.master')

@section('title', 'Sensor Details')
@section('page-title', 'Sensor Information')

@section('content')
<div class="container py-2">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
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
                                        {{-- <th>Remarks</th> --}}
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
                                            {{-- <td>{{ $report->remarks ?? '-' }}</td> --}}
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




