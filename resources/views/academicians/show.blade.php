@extends('dashboard')

@section('title', 'Academician Details') <!-- Page Title -->

@push('css')
    <!-- Optionally add custom CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet"> <!-- NiceAdmin Template CSS -->
@endpush

@section('breadcrumbs')
<!-- Dynamic Breadcrumb -->
<x-breadcrumbs 
    title="Academician Details" 
    :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Home'],
        ['url' => route('academicians.index'), 'label' => 'Academicians'],
        ['url' => '', 'label' => 'View Details']
    ]"
/>
@endsection

@section('content')
<div class="pagetitle">


<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <!-- Academician Details Card -->
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="card-title"><i class="bi bi-person-badge-fill text-primary me-2"></i>Academician Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6 mb-4">
                            <div class="info-box d-flex align-items-center">
                                <i class="bi bi-person-fill text-info me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h6 class="mb-0">Name</h6>
                                    <p class="mb-0 fw-bold">{{ $academician->name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Staff Number -->
                        <div class="col-md-6 mb-4">
                            <div class="info-box d-flex align-items-center">
                                <i class="bi bi-card-checklist text-warning me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h6 class="mb-0">Staff Number</h6>
                                    <p class="mb-0 fw-bold">{{ $academician->staff_number }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-4">
                            <div class="info-box d-flex align-items-center">
                                <i class="bi bi-envelope-fill text-success me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h6 class="mb-0">Email</h6>
                                    <p class="mb-0 fw-bold">{{ $academician->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- College -->
                        <div class="col-md-6 mb-4">
                            <div class="info-box d-flex align-items-center">
                                <i class="bi bi-building text-secondary me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h6 class="mb-0">College</h6>
                                    <p class="mb-0 fw-bold">{{ $academician->college }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="col-md-6 mb-4">
                            <div class="info-box d-flex align-items-center">
                                <i class="bi bi-diagram-3-fill text-danger me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h6 class="mb-0">Department</h6>
                                    <p class="mb-0 fw-bold">{{ $academician->department }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Position -->
                        <div class="col-md-6 mb-4">
                            <div class="info-box d-flex align-items-center">
                                <i class="bi bi-briefcase-fill text-primary me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h6 class="mb-0">Position</h6>
                                    <p class="mb-0 fw-bold">{{ $academician->position }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="d-flex justify-content-start mt-4">
                <a href="{{ route('academicians.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
{{-- @extends('dashboard')

@section('title', 'Academician Details') <!-- Page Title -->

@push('css')
    <!-- Optionally add custom CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet"> <!-- NiceAdmin Template CSS -->
@endpush

@section('breadcrumbs')
<!-- Dynamic Breadcrumb -->
<x-breadcrumbs 
    title="Academician Details" 
    :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Home'],
        ['url' => route('academicians.index'), 'label' => 'Academicians'],
        ['url' => '', 'label' => 'View Details']
    ]"
/>
@endsection

@section('content')
<div class="pagetitle">
    <h1>Academician Details</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academicians.index') }}">Academicians</a></li>
            <li class="breadcrumb-item active">Details</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <!-- Academician Details Card -->
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="card-title"><i class="bi bi-person-badge-fill text-primary me-2"></i>Academician Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <!-- Name -->
                        <li class="list-group-item d-flex align-items-center">
                            <i class="bi bi-person-fill text-info me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-0">Name</h6>
                                <p class="mb-0 fw-bold">{{ $academician->name }}</p>
                            </div>
                        </li>

                        <!-- Staff Number -->
                        <li class="list-group-item d-flex align-items-center">
                            <i class="bi bi-card-checklist text-warning me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-0">Staff Number</h6>
                                <p class="mb-0 fw-bold">{{ $academician->staff_number }}</p>
                            </div>
                        </li>

                        <!-- Email -->
                        <li class="list-group-item d-flex align-items-center">
                            <i class="bi bi-envelope-fill text-success me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-0">Email</h6>
                                <p class="mb-0 fw-bold">{{ $academician->email }}</p>
                            </div>
                        </li>

                        <!-- College -->
                        <li class="list-group-item d-flex align-items-center">
                            <i class="bi bi-building text-secondary me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-0">College</h6>
                                <p class="mb-0 fw-bold">{{ $academician->college }}</p>
                            </div>
                        </li>

                        <!-- Department -->
                        <li class="list-group-item d-flex align-items-center">
                            <i class="bi bi-diagram-3-fill text-danger me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-0">Department</h6>
                                <p class="mb-0 fw-bold">{{ $academician->department }}</p>
                            </div>
                        </li>

                        <!-- Position -->
                        <li class="list-group-item d-flex align-items-center">
                            <i class="bi bi-briefcase-fill text-primary me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h6 class="mb-0">Position</h6>
                                <p class="mb-0 fw-bold">{{ $academician->position }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Back Button -->
            <div class="d-flex justify-content-start mt-4">
                <a href="{{ route('academicians.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left-circle"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</section>
@endsection --}}
