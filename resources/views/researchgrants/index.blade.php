@extends('dashboard')

@section('title', 'Research Grants') <!-- Page Title -->

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
<x-breadcrumbs 
    title="Research Grants" 
    :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Home'],
        ['url' => '', 'label' => 'Research Grants']
    ]"
/>
@endsection

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- <section class="section">
    <div class="row">
        @if(auth()->user()->userCategory === 'Admin' || auth()->user()->userCategory === 'iRMC Staff')
        <!-- Display all research grants for Admin and iRMC Staff -->            
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card">
                        <!-- Card Header -->
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text-fill me-2"></i>Research Grants</h5>
                            <a href="{{ route('researchgrants.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Add New Research Grant
                            </a>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="text-center">
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Project Title</th>
                                            <th>Grant Amount (RM)</th>
                                            <th>Grant Provider</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Duration</th>
                                            <th>Project Leader</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($researchGrants as $index => $grant)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                                </td>
                                                <td class="fw-bold">{{ $grant->project_title }}</td>
                                                <td class="text-end">{{ number_format($grant->grant_amount, 2) }}</td>
                                                <td>{{ $grant->grant_provider }}</td>
                                                <td>{{ \Carbon\Carbon::parse($grant->start_date)->format('d-m-Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($grant->end_date)->format('d-m-Y') }}</td>
                                                <td class="text-center">{{ $grant->duration_month }} months</td>
                                                <td class="text-center text-primary fw-bold">{{ $grant->leader->name }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                                        <a href="{{ route('researchgrants.show', $grant->id) }}" 
                                                        class="btn btn-outline-info btn-sm" 
                                                        title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('researchgrants.edit', $grant->id) }}" 
                                                        class="btn btn-outline-warning btn-sm" 
                                                        title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('researchgrants.destroy', $grant->id) }}" 
                                                            method="POST" 
                                                            class="d-inline" 
                                                            onsubmit="return confirm('Are you sure you want to delete this research grant?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-danger btn-sm" 
                                                                    title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted">No Research Grants Found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
            <!-- Projects I Lead -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <h5 class="card-title mb-0"><i class="bi bi-list-task me-2"></i>List of Project (as Leader)</h5>
                    </div>
                    @if(isset($leaderProjects) && $leaderProjects->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-secondary">
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Project Title</th>
                                        <th>Grant Amount (RM)</th>
                                        <th>Grant Provider</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Duration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaderProjects as $index => $project)
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="fw-bold">{{ $project->project_title }}</td>
                                            <td class="text-end">{{ number_format($project->grant_amount, 2) }}</td>
                                            <td>{{ $project->grant_provider }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d-m-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d-m-Y') }}</td>
                                            <td class="text-center">{{ $project->duration_month }} months</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                    <a href="{{ route('researchgrants.show', $project->id) }}" 
                                                    class="btn btn-outline-info btn-sm" 
                                                    title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('researchgrants.milestones.index', ['researchgrant' => $project->id]) }}" 
                                                    class="btn btn-outline-primary btn-sm" 
                                                    title="Manage Milestones">
                                                        <i class="bi bi-clipboard"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">No projects found where you are the leader.</p>
                    @endif
                </div>
            </div>

            <!-- Projects I Participate In -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <h5 class="card-title mb-0"><i class="bi bi-list-task me-2"></i>List of Participated Project (as Member)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-secondary">
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Project Title</th>
                                        <th>Grant Amount (RM)</th>
                                        <th>Grant Provider</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Duration</th>
                                        <th>Project Leader</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($memberProjects as $index => $grant)
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="fw-bold">{{ $grant->project_title }}</td>
                                            <td class="text-end">{{ number_format($grant->grant_amount, 2) }}</td>
                                            <td>{{ $grant->grant_provider }}</td>
                                            <td>{{ \Carbon\Carbon::parse($grant->start_date)->format('d-m-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($grant->end_date)->format('d-m-Y') }}</td>
                                            <td class="text-center">{{ $grant->duration_month }} months</td>
                                            <td class="text-center text-primary fw-bold">{{ $grant->leader->name }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('researchgrants.show', $grant->id) }}" 
                                                   class="btn btn-outline-info btn-sm" 
                                                   title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">No Projects Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection --}}
<section class="section">
    <div class="row">
        @if(auth()->user()->userCategory === 'Admin' || auth()->user()->userCategory === 'iRMC Staff')
        <!-- Display all research grants for Admin and iRMC Staff -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card">
                        <!-- Card Header -->
                        <div class="card-header d-flex justify-content-between align-items-center bg-gradient-primary text-white">
                            <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text-fill me-2"></i>Research Grants</h5>
                            <a href="{{ route('researchgrants.create') }}" class="btn btn-light text-primary fw-bold">
                                <i class="bi bi-plus-circle"></i> Add New Research Grant
                            </a>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle shadow-sm border-0">
                                    <thead class="bg-gradient-secondary text-white text-uppercase">
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Project Title</th>
                                            <th>Grant Amount (RM)</th>
                                            <th>Grant Provider</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Duration</th>
                                            <th>Project Leader</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($researchGrants as $index => $grant)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span>
                                                </td>
                                                <td class="fw-bold">{{ $grant->project_title }}</td>
                                                <td class="text-end">{{ number_format($grant->grant_amount, 2) }}</td>
                                                <td>{{ $grant->grant_provider }}</td>
                                                <td>{{ \Carbon\Carbon::parse($grant->start_date)->format('d-m-Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($grant->end_date)->format('d-m-Y') }}</td>
                                                <td class="text-center">{{ $grant->duration_month }} months</td>
                                                <td class="text-center text-primary fw-bold">{{ $grant->leader->name }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                                        <a href="{{ route('researchgrants.show', $grant->id) }}" 
                                                           class="btn btn-outline-info btn-sm" 
                                                           title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('researchgrants.edit', $grant->id) }}" 
                                                           class="btn btn-outline-warning btn-sm" 
                                                           title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('researchgrants.destroy', $grant->id) }}" 
                                                              method="POST" 
                                                              class="d-inline" 
                                                              onsubmit="return confirm('Are you sure you want to delete this research grant?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-danger btn-sm" 
                                                                    title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted">No Research Grants Found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
            <!-- Projects I Lead -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-list-task me-2"></i>List of Projects (as Leader)</h5>
                    </div>
                    @if(isset($leaderProjects) && $leaderProjects->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle shadow-sm border-0">
                                <thead class="bg-gradient-secondary text-white">
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Project Title</th>
                                        <th>Grant Amount (RM)</th>
                                        <th>Grant Provider</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Duration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaderProjects as $index => $project)
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="fw-bold">{{ $project->project_title }}</td>
                                            <td class="text-end">{{ number_format($project->grant_amount, 2) }}</td>
                                            <td>{{ $project->grant_provider }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d-m-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d-m-Y') }}</td>
                                            <td class="text-center">{{ $project->duration_month }} months</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                    <a href="{{ route('researchgrants.show', $project->id) }}" 
                                                       class="btn btn-outline-info btn-sm" 
                                                       title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('researchgrants.milestones.index', ['researchgrant' => $project->id]) }}" 
                                                       class="btn btn-outline-primary btn-sm" 
                                                       title="Manage Milestones">
                                                        <i class="bi bi-clipboard"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">No projects found where you are the leader.</p>
                    @endif
                </div>
            </div>

            <!-- Projects I Participate In -->
            <div class="col-12 mt-5" style="margin-top: 2px;">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-list-task me-2"></i>List of Participated Projects (as Member)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle shadow-sm border-0">
                                <thead class="bg-gradient-secondary text-white">
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Project Title</th>
                                        <th>Grant Amount (RM)</th>
                                        <th>Grant Provider</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Duration</th>
                                        <th>Project Leader</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($memberProjects as $index => $grant)
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-secondary rounded-pill">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="fw-bold">{{ $grant->project_title }}</td>
                                            <td class="text-end">{{ number_format($grant->grant_amount, 2) }}</td>
                                            <td>{{ $grant->grant_provider }}</td>
                                            <td>{{ \Carbon\Carbon::parse($grant->start_date)->format('d-m-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($grant->end_date)->format('d-m-Y') }}</td>
                                            <td class="text-center">{{ $grant->duration_month }} months</td>
                                            <td class="text-center text-primary fw-bold">{{ $grant->leader->name }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('researchgrants.show', $grant->id) }}" 
                                                   class="btn btn-outline-info btn-sm" 
                                                   title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">No Projects Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection


