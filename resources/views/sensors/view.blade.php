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

@extends('layouts.master')

@section('title', 'Sensor Details')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Sensor Detail View</h4>
            <a href="{{ route('sensors.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <div class="card-body">
            {{-- Sensor Basic Info --}}
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

            {{-- Sensor Log Reports Table --}}
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
                            <td>{{ $report->reported_at ? $report->reported_at->format('Y-m-d H:i') : '-' }}</td>
                            <td>{{ ucfirst($report->old_status ?? '-') }}</td>
                            <td>{{ ucfirst($report->new_status ?? '-') }}</td>
                            <td>{{ optional($report->statusChanger)->name ?? '-' }}</td>
                            <td>{{ $report->status_changed_at ? $report->status_changed_at->format('Y-m-d H:i') : '-' }}</td>
                            <td>
                                <span class="badge {{ $report->status == 'open' ? 'bg-danger' : 'bg-success' }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td>{{ $report->resolved_at ? $report->resolved_at->format('Y-m-d H:i') : '-' }}</td>
                        </tr>
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
@endsection


