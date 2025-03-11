@extends('dashboard') <!-- Extending the NiceAdmin layout -->

@section('title', 'Create Academician') <!-- Page Title -->

@push('css')
    <!-- Optionally add custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
@endpush

@section('breadcrumbs')
<!-- Dynamic Breadcrumb -->
<x-breadcrumbs 
    title="New Academician" 
    :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Home'],
        ['url' => route('academicians.index'), 'label' => 'Academicians Index'],
        ['url' => '', 'label' => 'New Academician']
    ]"
/>
@endsection

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-plus-fill me-2"></i>Create New Academician
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('academicians.store') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="row mb-3">
                            <label for="name" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-person-fill text-primary me-2" style="font-size: 1.2rem;"></i>Name
                            </label>
                            <div class="col-sm-9">
                                <input type="text" name="name" id="name" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    value="{{ old('name') }}" placeholder="Enter full name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Staff Number -->
                        <div class="row mb-3">
                            <label for="staff_number" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-card-list text-success me-2" style="font-size: 1.2rem;"></i>Staff Number
                            </label>
                            <div class="col-sm-9">
                                <input type="text" name="staff_number" id="staff_number" 
                                    class="form-control @error('staff_number') is-invalid @enderror" 
                                    value="{{ old('staff_number') }}" placeholder="Enter staff number" required>
                                @error('staff_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="row mb-3">
                            <label for="email" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-envelope-fill text-warning me-2" style="font-size: 1.2rem;"></i>Email
                            </label>
                            <div class="col-sm-9">
                                <input type="email" name="email" id="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    value="{{ old('email') }}" placeholder="Enter email address" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- College -->
                        <div class="row mb-3">
                            <label for="college" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-building-fill text-info me-2" style="font-size: 1.2rem;"></i>College
                            </label>
                            <div class="col-sm-9">
                                <input type="text" name="college" id="college" 
                                    class="form-control @error('college') is-invalid @enderror" 
                                    value="{{ old('college') }}" placeholder="Enter college name" required>
                                @error('college')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="row mb-3">
                            <label for="department" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-diagram-3-fill text-secondary me-2" style="font-size: 1.2rem;"></i>Department
                            </label>
                            <div class="col-sm-9">
                                <input type="text" name="department" id="department" 
                                    class="form-control @error('department') is-invalid @enderror" 
                                    value="{{ old('department') }}" placeholder="Enter department name" required>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Position -->
                        <div class="row mb-3">
                            <label for="position" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-person-workspace text-danger me-2" style="font-size: 1.2rem;"></i>Position
                            </label>
                            <div class="col-sm-9">
                                <select name="position" id="position" 
                                    class="form-select @error('position') is-invalid @enderror" required>
                                    <option value="">Select Position</option>
                                    <option value="Professor" {{ old('position') == 'Professor' ? 'selected' : '' }}>Professor</option>
                                    <option value="Assoc Prof" {{ old('position') == 'Assoc Prof' ? 'selected' : '' }}>Assoc Prof</option>
                                    <option value="Senior Lecturer" {{ old('position') == 'Senior Lecturer' ? 'selected' : '' }}>Senior Lecturer</option>
                                    <option value="Lecturer" {{ old('position') == 'Lecturer' ? 'selected' : '' }}>Lecturer</option>
                                </select>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Academician
                            </button>
                            <a href="{{ route('academicians.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left-circle"></i> Back to Academicians
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
