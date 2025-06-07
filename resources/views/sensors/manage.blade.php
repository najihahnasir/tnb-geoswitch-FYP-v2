@extends('layouts.master')

@section('title', 'Sensor Status Summary')
@section('page-title', 'Sensor Status Summary')

@section('content')
<div class="container py-1" style="background-color: #f8f9fa; border-radius: 8px;">
    {{-- Alert for critical sensors --}}
    @if($offlineCount > 0 || $repairingCount > 0)
    <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            Attention: {{ $offlineCount }} offline and {{ $repairingCount }} repairing sensors need action.
        </div>
    </div>
    @endif

    {{-- Status Summary --}}
    <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
        <h5 class="mb-0 text-dark"><i class="fas fa-signal me-2 text-primary"></i>Sensor Summary:</h5>
        <span class="badge bg-success">{{ $onlineCount ?? 0 }} Online</span>
        <span class="badge bg-warning text-dark">{{ $repairingCount ?? 0 }} Repairing</span>
        <span class="badge bg-danger">{{ $offlineCount ?? 0 }} Offline</span>
    </div>

    {{-- Status Filter Tabs --}}
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link {{ request('filter') === null ? 'active' : '' }}" href="{{ route('sensors.index') }}">All</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('filter') === 'offline' ? 'active' : '' }}" href="{{ route('sensors.index', ['filter' => 'offline']) }}">Offline</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('filter') === 'repairing' ? 'active' : '' }}" href="{{ route('sensors.index', ['filter' => 'repairing']) }}">Repairing</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('filter') === 'online' ? 'active' : '' }}" href="{{ route('sensors.index', ['filter' => 'online']) }}">Online</a>
        </li>
    </ul>

    {{-- Filter Panel Card --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="fas fa-filter me-2"></i>Filter Sensors</span>
            <button class="btn btn-sm btn-light border" data-bs-toggle="collapse" data-bs-target="#filterPanel">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div id="filterPanel" class="collapse show">
            <div class="card-body">
                <form method="GET" action="{{ route('sensors.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="substation" class="form-label">Substation</label>
                        <select name="substation" id="substation" class="form-select">
                            <option value="">All Substations</option>
                            @foreach($substations as $sub)
                                <option value="{{ $sub->id }}" {{ request('substation') == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->substation_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="type" class="form-label">Sensor Type</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($sensorTypes as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter" class="form-label">Status</label>
                        <select name="filter" id="filter" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="online" {{ request('filter') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="repairing" {{ request('filter') == 'repairing' ? 'selected' : '' }}>Repairing</option>
                            <option value="offline" {{ request('filter') == 'offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Filter</button>
                        <a href="{{ route('sensors.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sensor Table Card --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">
            <i class="fas fa-table me-2"></i>Sensor Table
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover table-striped align-middle" id="sensorTable">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Sensor Tag</th>
                        <th>Type</th>
                        <th>Substation</th>
                        <th>Status</th>
                        <th>Panel</th>
                        @if(request('filter') === 'offline')
                        <th>Downtime</th>
                        @endif
                        <th>Updated At</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sensors as $index => $sensor)
                    <tr>
                        <td>{{ $sensors->firstItem() + $index }}</td>
                        <td>{{ $sensor->tag }}</td>
                        <td>{{ $sensor->type }}</td>
                        <td>{{ optional($sensor->switchgear->substation)->substation_name ?? '-' }}</td>
                        <td>
                            @php $status = strtolower($sensor->status); @endphp
                            <span class="badge {{ $status == 'online' ? 'bg-success' : ($status == 'repairing' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td>{{ optional($sensor->switchgear)->panel ?? '-' }}</td>
                        @if(request('filter') === 'offline')
                        <td>{{ $sensor->latestLogReport?->downtime_duration ?? '-' }}</td>
                        @endif
                        <td>{{ $sensor->updated_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $sensor->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('sensors.show', $sensor->id) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                                @if($sensor->latestLogReport && $sensor->latestLogReport->status === 'open')
                                    <a href="{{ route('log-reports.editStatus', $sensor->latestLogReport->id) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                @else
                                    <a href="{{ route('log-reports.create') }}" class="btn btn-outline-primary btn-sm">
                                        Report Issue
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No sensors found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 d-flex justify-content-center">
            {{ $sensors->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
