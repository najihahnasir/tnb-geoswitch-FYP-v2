@extends('layouts.master')

@section('title', 'Substations Summary')
@section('page-title', 'Substations Summary')

@section('content')
<div class="container py-4" style="background-color: #f8f9fa; border-radius: 8px;">
    {{-- Summary Badges --}}
    {{-- Overview Header Row --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap my-3 px-1">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-bolt text-primary"></i>
            <span class="fw-semibold">Substation Overview:</span>
            <span class="badge bg-success">Total Substations: {{ $substations->count() }}</span>
        </div>

        <div class="d-flex gap-2 mt-2 mt-md-0">
            <a href="{{ route('substations.summaryPdf') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf me-1"></i> Download PDF
            </a>
            <a href="{{ route('substations.summaryCsv') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </a>
        </div>
    </div>



    {{-- Filter Panel --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="fas fa-filter me-2"></i>Filter Substations</span>
            <button class="btn btn-sm btn-light border" data-bs-toggle="collapse" data-bs-target="#filterPanel">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div id="filterPanel" class="collapse show">
            <div class="card-body">
                <form method="GET" action="{{ route('substations.summary') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="code_prefix" class="form-label">Substation Code Prefix</label>
                        <select name="code_prefix" id="code_prefix" class="form-select">
                            <option value="">All Types</option>
                            <option value="PPU" {{ request('code_prefix') == 'PPU' ? 'selected' : '' }}>PPU</option>
                            <option value="SSU" {{ request('code_prefix') == 'SSU' ? 'selected' : '' }}>SSU</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="location" class="form-label">Location Keyword</label>
                        <input type="text" name="location" id="location" class="form-control" placeholder="e.g. Bangsar">
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                        <a href="{{ route('substations.summary') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Substation Table Card --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">
            <i class="fas fa-table me-2"></i>Substation Table
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover table-striped align-middle" id="substationTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Substation Name</th>
                        <th>Location</th>
                        <th>Total Sensors</th>
                        <th>Status Summary</th>
                        <th>Last Update</th>
                        <th>Action</th>
                        {{-- <th>Action</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($substations as $index => $substation)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $substation->substation_name }}</td>
                        <td>{{ $substation->latitude }}, {{ $substation->longitude }}</td>
                        <td>{{ $substation->total_sensors }}</td>
                        <td>
                            <span class="badge bg-success">
                                online: {{ $substation->sensors->filter(fn($s) => strtolower($s->status) === 'online')->count() }}
                            </span>
                            <span class="badge bg-warning text-dark">
                                repairing: {{ $substation->sensors->filter(fn($s) => strtolower($s->status) === 'repairing')->count() }}
                            </span>
                            <span class="badge bg-danger">
                                offline: {{ $substation->sensors->filter(fn($s) => strtolower($s->status) === 'offline')->count() }}
                            </span>
                        </td>

                        
                        <td>{{ $substation->last_updated ? $substation->last_updated->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            {{-- <a href="{{ route('dashboard.substation-map', ['id' => $substation['id']]) }}" class="btn btn-sm btn-outline-primary">Map</a> --}}
                        
                            <a href="{{ route('substation.show', $substation->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                        </td>
                        </td>
                        {{-- <td>
                            <button class="btn btn-sm btn-primary" onclick="openSubstationModal({{ $substation->id }})">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                        </td> --}}
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Substation Modal --}}
<div class="modal fade" id="substationModal" tabindex="-1" aria-labelledby="substationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="substationModalLabel">Substation Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Name:</strong> <span id="modalName"></span></p>
        <p><strong>Location:</strong> <span id="modalLocation"></span></p>
        <p><strong>Total Sensors:</strong> <span id="modalTotalSensors"></span></p>
        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
        <p><strong>Last Updated:</strong> <span id="modalUpdated"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('after-scripts')
<script>
    function openSubstationModal(id) {
        const sub = @json($substations).find(s => s.id === id);
        if (sub) {
            document.getElementById('modalName').textContent = sub.substation_name;
            document.getElementById('modalLocation').textContent = `${sub.latitude}, ${sub.longitude}`;
            document.getElementById('modalTotalSensors').textContent = sub.total_sensors;
            document.getElementById('modalStatus').textContent = `Online: ${sub.online}, Repairing: ${sub.repairing}, Offline: ${sub.offline}`;
            document.getElementById('modalUpdated').textContent = sub.last_updated ?? '-';

            const modal = new bootstrap.Modal(document.getElementById('substationModal'));
            modal.show();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery && $.fn.DataTable) {
            $('#substationTable').DataTable({
                paging: true,
                info: true,
                searching: true,
                order: []
            });
        }
    });
</script>
@endpush
@endsection
