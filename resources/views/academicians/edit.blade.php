@extends('dashboard') <!-- Extending the NiceAdmin layout -->

@section('title', 'Edit Academician') <!-- Page Title -->

@push('css')
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet"> <!-- NiceAdmin Template CSS -->
@endpush

@section('breadcrumbs')
<!-- Dynamic Breadcrumb -->
<x-breadcrumbs 
    title="Edit Academician" 
    :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Home'],
        ['url' => route('academicians.index'), 'label' => 'Academicians'],
        ['url' => '', 'label' => 'Edit Academician']
    ]"
/>
@endsection

@section('content')

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="card-title"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Academician</h5>
                </div>
                <div class="card-body">
                    <!-- Form Start -->
                    <form action="{{ route('academicians.update', $academician->id) }}" method="POST" class="row g-4">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                <i class="bi bi-person-fill text-info me-2"></i>Name
                            </label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name', $academician->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Staff Number -->
                        <div class="col-md-6">
                            <label for="staff_number" class="form-label">
                                <i class="bi bi-card-checklist text-warning me-2"></i>Staff Number
                            </label>
                            <input type="text" name="staff_number" id="staff_number" 
                                class="form-control @error('staff_number') is-invalid @enderror" 
                                value="{{ old('staff_number', $academician->staff_number) }}" required>
                            @error('staff_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope-fill text-success me-2"></i>Email
                            </label>
                            <input type="email" name="email" id="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email', $academician->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- College -->
                        <div class="col-md-6">
                            <label for="college" class="form-label">
                                <i class="bi bi-building text-secondary me-2"></i>College
                            </label>
                            <input type="text" name="college" id="college" 
                                class="form-control @error('college') is-invalid @enderror" 
                                value="{{ old('college', $academician->college) }}" required>
                            @error('college')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Department -->
                        <div class="col-md-6">
                            <label for="department" class="form-label">
                                <i class="bi bi-diagram-3-fill text-danger me-2"></i>Department
                            </label>
                            <input type="text" name="department" id="department" 
                                class="form-control @error('department') is-invalid @enderror" 
                                value="{{ old('department', $academician->department) }}" required>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Position -->
                        <div class="col-md-6">
                            <label for="position" class="form-label">
                                <i class="bi bi-briefcase-fill text-primary me-2"></i>Position
                            </label>
                            <select name="position" id="position" 
                                class="form-select @error('position') is-invalid @enderror" required>
                                <option value="Professor" {{ $academician->position == 'Professor' ? 'selected' : '' }}>Professor</option>
                                <option value="Assoc Prof" {{ $academician->position == 'Assoc Prof' ? 'selected' : '' }}>Assoc Prof</option>
                                <option value="Senior Lecturer" {{ $academician->position == 'Senior Lecturer' ? 'selected' : '' }}>Senior Lecturer</option>
                                <option value="Lecturer" {{ $academician->position == 'Lecturer' ? 'selected' : '' }}>Lecturer</option>
                            </select>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-save-fill me-2"></i>Update
                            </button>
                            <a href="{{ route('academicians.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle-fill me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                    <!-- Form End -->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
