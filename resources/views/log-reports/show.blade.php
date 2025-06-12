@extends('layouts.master')

@section('title', 'Sensor Log Report Detail')
@section('page-title', 'Log Report Detail')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Report #{{ $logReport->id }} — {{ $logReport->title }}</h5>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Substation</dt>
                <dd class="col-sm-9">{{ optional($logReport->substation)->substation_name ?? '-' }}</dd>

                {{-- <dt class="col-sm-3">Switchgear</dt>
                <dd class="col-sm-9">{{ optional($logReport->switchgear)->panel ?? '-' }}</dd> --}}
                <dt class="col-sm-3">Switchgear</dt>
                <dd class="col-sm-9">
                    @if($logReport->switchgear)
                        <div class="switchgear-details">
                            <div><strong>Panel:</strong> {{ $logReport->switchgear->panel_name ?? '-' }}</div>
                            <div><strong>Code:</strong> {{ $logReport->switchgear->switchgear_code ?? '-' }}</div>
                            <div class="text-muted small mt-1">
                                Lineup: {{ $logReport->switchgear->lineup ?? '-' }} | 
                                Compartment: {{ $logReport->switchgear->compartment ?? '-' }} ({{ $logReport->switchgear->compartment_code ?? '-' }})
                            </div>
                        </div>
                    @else
                        -
                    @endif
                </dd>
                <dt class="col-sm-3">Sensor</dt>
                <dd class="col-sm-9">
                    {{ optional($logReport->sensor)->tag ?? '-' }} ({{ optional($logReport->sensor)->type ?? '-' }})
                </dd>

                <dt class="col-sm-3">Reported By</dt>
                <dd class="col-sm-9">{{ optional($logReport->reporter)->name ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Reported At</dt>
                <dd class="col-sm-9">{{ \Carbon\Carbon::parse($logReport->reported_at)->format('d M Y, h:i A') }}</dd>

                <dt class="col-sm-3">Current Status</dt>
                <dd class="col-sm-9">
                    <span class="badge 
                        @if($logReport->new_status == 'online') bg-success
                        @elseif($logReport->new_status == 'repairing') bg-warning text-dark
                        @elseif($logReport->new_status == 'offline') bg-danger
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($logReport->new_status) }}
                    </span>
                </dd>

                <dt class="col-sm-3">Previous Status</dt>
                <dd class="col-sm-9">{{ ucfirst($logReport->old_status ?? 'N/A') }}</dd>

                <dt class="col-sm-3">Updated By</dt>
                <dd class="col-sm-9">{{ optional($logReport->statusChanger)->name ?? '-' }}</dd>

                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ \Carbon\Carbon::parse($logReport->status_changed_at)->format('d M Y, h:i A') }}</dd>

                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $logReport->description ?? '-' }}</dd>

                <dt class="col-sm-3">Remarks</dt>
                <dd class="col-sm-9">{{ $logReport->remarks ?? '-' }}</dd>

                <dt class="col-sm-3">Report Status</dt>
                <dd class="col-sm-9">
                    <span class="badge {{ $logReport->status == 'resolved' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($logReport->status) }}
                    </span>
                </dd>
            </dl>
        </div>
        <div class="card-footer text-end bg-light">
            <a href="{{ route('log-reports.index') }}" class="btn btn-secondary">← Back to Reports</a>
            @if($logReport->status !== 'resolved')
                <form action="{{ route('log-reports.resolve', $logReport->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">Mark as Resolved</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
