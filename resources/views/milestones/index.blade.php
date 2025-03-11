@extends('dashboard')

@section('title', 'Milestones for Project') <!-- Page Title -->

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Listen for change events on the status dropdowns
        document.querySelectorAll('.update-status').forEach(select => {
            select.addEventListener('change', function () {
                const milestoneId = this.getAttribute('data-milestone-id');
                const researchGrantId = this.getAttribute('data-researchgrant-id');
                const newStatus = this.value;

                // Send AJAX request to update the status
                fetch(`/researchgrants/${researchGrantId}/milestones/${milestoneId}/update-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ status: newStatus }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Status updated successfully.');
                    } else {
                        alert('Failed to update status.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the status.');
                });
            });
        });
    });
</script>
@endpush


@push('css')
    <!-- Optionally add custom CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet"> <!-- NiceAdmin Template CSS -->
    <style>
        .table thead {
            background-color: #f8f9fa;
        }

        .table thead th {
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody tr:hover {
            background-color: #f2f2f2;
        }

        .action-buttons a,
        .action-buttons button {
            margin-right: 5px;
        }

        .badge {
            font-size: 0.9rem;
        }
        .action-buttons .btn {
            min-width: 40px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }
    </style>
@endpush

@section('breadcrumbs')
<!-- Dynamic Breadcrumb -->
<x-breadcrumbs 
    title="Milestones for {{ $researchGrant->project_title }}" 
    :breadcrumbs="[ 
        ['url' => route('dashboard'), 'label' => 'Home'],
        ['url' => route('researchgrants.index'), 'label' => 'Research Grants'],
        ['url' => '', 'label' => 'Milestones']
    ]"
/>
@endsection

@section('content')

<!-- Success and Error Messages -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clipboard-data text-primary me-2"></i>Milestones for {{ $researchGrant->project_title }}
                    </h5>
                    <a href="{{ route('researchgrants.milestones.create', ['researchgrant' => $researchGrant->id]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create New Milestone
                    </a>
                </div>
                <div class="card-body">
                    <!-- Styled Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Milestone Name</th>
                                    <th>Completion Date</th>
                                    <th>Deliverable</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($milestones as $index => $milestone)
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                        </td>
                                        <td>{{ $milestone->milestone_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($milestone->completion_date)->format('d-m-Y') }}</td>
                                        <td>{{ $milestone->deliverable }}</td>
                                        <td>
                                            <span class="badge {{ $milestone->status === 'Completed' ? 'bg-success' : ($milestone->status === 'In Progress' ? 'bg-warning' : 'bg-secondary') }}">
                                                {{ $milestone->status }}
                                            </span>
                                        </td>
                                        {{-- <td class="text-center">
                                            <select class="form-select form-select-sm update-status" 
                                                    data-milestone-id="{{ $milestone->id }}" 
                                                    data-researchgrant-id="{{ $researchGrant->id }}">
                                                <option value="Not Started" {{ $milestone->status === 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                                <option value="In Progress" {{ $milestone->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="Completed" {{ $milestone->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </td> --}}
                                        
                                        <td>{{ $milestone->remark }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <a href="{{ route('researchgrants.milestones.show', ['researchgrant' => $researchGrant->id, 'milestone' => $milestone->id]) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('researchgrants.milestones.edit',[$researchGrant->id, $milestone]) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Edit Details">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('researchgrants.milestones.destroy', [$researchGrant->id, $milestone]) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No Milestones Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination (if needed) -->
                    <div class="d-flex justify-content-end mt-3">
                        {{-- Pagination links can go here if implemented --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
