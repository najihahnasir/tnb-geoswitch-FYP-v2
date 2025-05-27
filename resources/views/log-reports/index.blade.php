@extends('layouts.master')
@section('title', 'Report Log Sensor')
@section('page-title', 'Log Summary')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Sensor Log Reports</h4>
        <a href="{{ route('log-reports.create') }}" class="btn btn-primary">New Report</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Case ID</th>
                <th>Substation</th>
                <th>Switchgear</th>
                <th>Sensor</th>
                <th>Reported By</th>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Reported At</th>
                <th>Resolved At</th>
                <th>Duration</th>
                <th>Remarks</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
                <tr>
                    <td>#{{ $report->id }}</td>
                    <td>{{ optional($report->substation)->substation_name ?? '-' }}</td>
                    <td>{{ optional($report->switchgear)->panel ?? '-' }}</td>
                    <td>
                        {{ optional($report->sensor)->tag ?? '-' }} 
                        ({{ optional($report->sensor)->type ?? '-' }})
                    </td>
                    <td>{{ optional($report->reporter)->name ?? '-' }}</td>
                    <td>{{ $report->title ?? '-' }}</td>
                    <td>{{ $report->description ?? '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $report->status === 'open' ? 'danger' : 'success' }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </td>
                    <td>{{ $report->reported_at ? $report->reported_at->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ $report->resolved_at ? $report->resolved_at->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ $report->duration ?? '-' }}</td>
                    <td>{{ $report->remarks ?? '-' }}</td>
                    <td>
                        <a href="{{ route('log-reports.show', $report) }}" class="btn btn-sm btn-info">View</a>
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
@endsection
