
@extends('dashboard')

@section('title', 'Academician Index') <!-- Page Title -->

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
    title="Academicians" 
    :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Home'],
        ['url' => '', 'label' => 'Index']
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
                        <i class="bi bi-people-fill text-primary me-2"></i>View Report
                    </h5>
                    <a href="{{ route('academicians.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create New Report
                    </a>
                </div>
                <div class="card-body">
                    <!-- Styled Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle"> <!-- Added table-bordered class -->
                            <thead class="text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Substation</th>
                                    <th>Sensor Type</th>
                                    <th>Timestamps</th>
                                    <th>Date</th>
                                    <th>Sensor Reading Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($academicians as $academician)
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $academician->id }}</span>
                                        </td>
                                        <td>{{ $academician->name }}</td>
                                        <td>{{ $academician->staff_number }}</td>
                                        <td>{{ $academician->email }}</td>
                                        <td>{{ $academician->college }}</td>
                                        <td>{{ $academician->department }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $academician->position }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-2"> <!-- Flexbox container -->
                                                <a href="{{ route('academicians.show', $academician->id) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('academicians.edit', $academician->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Edit Details">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('academicians.destroy', $academician->id) }}" 
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
                                        <td colspan="8" class="text-center text-muted">No Academicians Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-end mt-3">
                        {{ $academicians->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
