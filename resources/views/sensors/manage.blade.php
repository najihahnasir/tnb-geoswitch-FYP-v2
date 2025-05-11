{{-- @extends('layouts.master')

@section('title', 'Manage Sensors')

@section('breadcrumb')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Manage Sensors</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Sensor Management</h2>

    @if(session('success'))
        <div class="toast show position-fixed bottom-0 end-0 m-4 bg-success text-white" role="alert">
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(request('filter') || request('substation') || request('sensor') || request('type'))
        <div class="alert alert-warning">
            Showing filtered sensors.
            <a href="{{ route('sensors.index') }}" class="text-decoration-underline">Clear Filters</a>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('sensors.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="substation" class="form-label">Substation</label>
                    <select name="substation" id="substation" class="form-control">
                        <option value="">-- All Substations --</option>
                        @foreach($substations as $sub)
                            <option value="{{ $sub->id }}" {{ request('substation') == $sub->id ? 'selected' : '' }}>
                                {{ $sub->substation_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="type" class="form-label">Sensor Type</label>
                    <select name="type" id="type" class="form-control">
                        <option value="">-- All Types --</option>
                        @foreach($sensorTypes as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="filter" class="form-label">Status</label>
                    <select name="filter" id="filter" class="form-control">
                        <option value="">-- All Statuses --</option>
                        <option value="online" {{ request('filter') == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="repairing" {{ request('filter') == 'repairing' ? 'selected' : '' }}>Repairing</option>
                        <option value="offline" {{ request('filter') == 'offline' ? 'selected' : '' }}>Offline</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-3">
        <span class="badge bg-success">Online</span>
        <span class="badge bg-warning text-dark">Repairing</span>
        <span class="badge bg-danger">Offline</span>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Sensor Tag</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Substation</th>
                    <th>Panel</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sensors as $index => $sensor)
                <tr title="Last updated: {{ $sensor->updated_at }}">
                    <td>{{ $sensors->firstItem() + $index }}</td>
                    <td>{{ $sensor->tag }}</td>
                    <td>{{ $sensor->type }}</td>
                    <td>
                        <span class="badge 
                            {{ $sensor->status == 'online' ? 'bg-success' : 
                            ($sensor->status == 'repairing' ? 'bg-warning text-dark' : 'bg-danger') }}">
                            {{ ucfirst($sensor->status) }}
                        </span>
                    </td>
                    <td>{{ optional($sensor->switchgear->substation)->substation_name ?? '-' }}</td>
                    <td>{{ optional($sensor->switchgear)->panel ?? '-' }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="openUpdateModal({{ $sensor->id }})">Update</button>
                            <a href="{{ route('sensors.show', $sensor->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No sensors found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-end">
            {{ $sensors->withQueryString()->links() }}
        </div>
    </div>
</div>

<div class="modal fade" id="updateSensorModal" tabindex="-1" aria-labelledby="updateSensorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="updateSensorForm">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateSensorModalLabel">Update Sensor Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" required>
              <option value="online">Online</option>
              <option value="repairing">Repairing</option>
              <option value="offline">Offline</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Report By</label>
            <input type="text" name="reported_by" value="{{ Auth::check() ? Auth::user()->name : 'N/A' }}" class="form-control" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">Report Date</label>
            <input type="text" name="report_date" value="{{ now()->format('Y-m-d H:i:s') }}" class="form-control" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea name="report" rows="3" class="form-control" placeholder="Enter remarks or findings..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Status</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('after-scripts')
<script>
    function openUpdateModal(sensorId) {
        const form = document.getElementById('updateSensorForm');
        form.action = `/sensors/${sensorId}`;
        const modal = new bootstrap.Modal(document.getElementById('updateSensorModal'));
        modal.show();
    }
</script>
@endpush
@endsection --}}

@extends('layouts.master')

@section('title', 'Manage Sensors')



@section('content')
<div class="container py-4" style="background-color: #f8f9fa; border-radius: 8px;">
    <h2 class="mb-4 text-primary"><i class="fas fa-cogs me-2"></i>Manage Sensors</h2>
    @section('breadcrumb')
        <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage Sensors</li>
        </ol>
        </nav>
    @endsection
    {{-- Status Summary --}}
    <div class="d-flex gap-3 mb-4">
        <span class="badge bg-success p-2 px-3">{{ $onlineCount ?? 0 }} Online</span>
        <span class="badge bg-warning text-dark p-2 px-3">{{ $repairingCount ?? 0 }} Repairing</span>
        <span class="badge bg-danger p-2 px-3">{{ $offlineCount ?? 0 }} Offline</span>
    </div>

    @if(session('success'))
        <div class="toast show position-fixed bottom-0 end-0 m-4 bg-success text-white" role="alert">
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Filter Panel Card --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="fas fa-filter me-2"></i>Filter Sensors</span>
            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterPanel">Toggle</button>
        </div>
        <div id="filterPanel" class="collapse show">
            <div class="card-body">
                <form method="GET" action="{{ route('sensors.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="substation" class="form-label">Substation</label>
                        <select name="substation" id="substation" class="form-select">
                            <option value="">-- All Substations --</option>
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
                            <option value="">-- All Types --</option>
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
                            <option value="">-- All Statuses --</option>
                            <option value="online" {{ request('filter') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="repairing" {{ request('filter') == 'repairing' ? 'selected' : '' }}>Repairing</option>
                            <option value="offline" {{ request('filter') == 'offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Filter</button>
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
            <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Sensor Tag</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Substation</th>
                        <th>Panel</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sensors as $index => $sensor)
                    <tr title="Last updated: {{ $sensor->updated_at }}">
                        <td>{{ $sensors->firstItem() + $index }}</td>
                        <td>{{ $sensor->tag }}</td>
                        <td>{{ $sensor->type }}</td>
                        <td>
                            <span class="badge 
                                {{ $sensor->status == 'online' ? 'bg-success' : 
                                ($sensor->status == 'repairing' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ ucfirst($sensor->status) }}
                            </span>
                        </td>
                        <td>{{ optional($sensor->switchgear->substation)->substation_name ?? '-' }}</td>
                        <td>{{ optional($sensor->switchgear)->panel ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="openUpdateModal({{ $sensor->id }})">
                                    <i class="fas fa-edit me-1"></i> Update
                                </button>
                                <a href="{{ route('sensors.show', $sensor->id) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No sensors found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{ $sensors->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="updateSensorModal" tabindex="-1" aria-labelledby="updateSensorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="updateSensorForm">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="updateSensorModalLabel">Update Sensor Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="online">Online</option>
              <option value="repairing">Repairing</option>
              <option value="offline">Offline</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Report By</label>
            <input type="text" name="reported_by" value="{{ Auth::check() ? Auth::user()->name : 'N/A' }}" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Report Date</label>
            <input type="text" name="report_date" value="{{ now()->format('Y-m-d H:i:s') }}" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea name="report" rows="3" class="form-control" placeholder="Enter remarks or findings..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Status</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('after-scripts')
<script>
    function openUpdateModal(sensorId) {
        const form = document.getElementById('updateSensorForm');
        form.action = `/sensors/${sensorId}`;
        const modal = new bootstrap.Modal(document.getElementById('updateSensorModal'));
        modal.show();
    }
</script>
@endpush
@endsection

