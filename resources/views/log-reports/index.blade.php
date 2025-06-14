@extends('layouts.master')
@section('title', 'Report Log Sensor')
@section('page-title', 'Log Summary')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-primary mb-0"><i class="fas fa-history me-2"></i>Sensor Log Reports</h4>
        <a href="{{ route('log-reports.create') }}" class="btn btn-outline-primary">
            <i class="fas fa-plus-circle me-1"></i> New Report
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3">
            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Filter Panel --}}
    <div class="card mb-4">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="fas fa-filter me-2"></i>Filter Logs</span>
            <button class="btn btn-sm btn-light border" data-bs-toggle="collapse" data-bs-target="#filterPanel">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div id="filterPanel" class="collapse show">
            <div class="card-body">
                <form method="GET" action="{{ route('log-reports.index') }}" class="row g-3">
                    {{-- <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div> --}}
                    <div class="col-md-3">
                        <label for="status" class="form-label">Case Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Cases</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="new_status" class="form-label">Sensor Status</label>
                        <select name="new_status" id="new_status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="online" {{ request('new_status') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="repairing" {{ request('new_status') == 'repairing' ? 'selected' : '' }}>Repairing</option>
                            <option value="offline" {{ request('new_status') == 'offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="substation" class="form-label">Substation</label>
                        <select name="substation" id="substation" class="form-select">
                            <option value="">All Substations</option>
                            @foreach($substations as $sub)
                                <option value="{{ $sub->id }}" {{ request('substation') == $sub->id ? 'selected' : '' }}>{{ $sub->substation_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-md-3">
                        <label for="month" class="form-label">Month</label>
                        <input type="month" class="form-control" name="month" id="month" value="{{ request('month') }}">
                    </div> --}}
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Filter</button>
                        <a href="{{ route('log-reports.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- index summary table  --}}
   <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="fas fa-table me-2"></i>Log Index Table</span>
            <div class="d-flex gap-2">
                <a href="{{ route('log-reports.exportPdf') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
                <a href="{{ route('log-reports.exportCsv') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-csv me-1"></i> Export CSV
                </a>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover table-striped align-middle" id="sensorTable">
                <thead class="table-dark">
                    {{-- <div class="card shadow-sm">
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped
                                <thead class="table-light text-center align-middle"> --}}
                    <tr>
                        <th>#Case ID</th>
                        <th>Substation Name</th>
                        <th>Switchgear/Panel</th>
                        <th>Sensor Tag</th>
                        <th>Report By</th>
                        <th>Description</th>
                        <th>Report Status</th>
                        <th>Report Date</th>
                        <th>Resolved Time</th>
                        <th>Downtime Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td class="fw-semibold">#{{ $report->case_id ?? '-' }}</td>
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
                                <span class="fw-semibold">{{ optional($report->sensor)->tag ?? '-' }}</span><br>
                                <small class="text-muted">
                                    ({{ optional($report->sensor)->type ?? '-' }}) –
                                    <span class="badge {{ 
                                        $report->new_status === 'offline' ? 'bg-danger' :
                                        ($report->new_status === 'repairing' ? 'bg-warning text-dark' : 'bg-success') }}">
                                        {{ ucfirst($report->new_status) }}
                                    </span>
                                    <br>
                                    @if($report->new_status === 'offline')
                                        <span class="text-danger small">Downtime starts when sensor is offline.</span>
                                    @elseif($report->new_status === 'repairing')
                                        <span class="text-warning small">Sensor is under repair. Downtime still applies until it is online.</span>
                                    @endif
                                </small>
                            </td>
                            <td>{{ optional($report->reporter)->name ?? '-' }}</td>
                            {{-- <td>{{ $report->title ?? '-' }}</td> --}}
                            <td>{{ Str::limit($report->description, 50) ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $report->status === 'open' ? 'danger' : 'success' }}">
                                    {{ ucfirst($report->status) }}
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
                                <a href="{{ route('log-reports.show', $report) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted">No sensor log reports found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top-0 d-flex justify-content-center">
            {{ $reports->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
@push('after-scripts')
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

