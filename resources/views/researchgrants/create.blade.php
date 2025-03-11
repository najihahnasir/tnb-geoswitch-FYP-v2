@extends('dashboard') <!-- Extending the NiceAdmin layout -->

@section('title', 'Create New Research Grant') <!-- Page Title -->

@push('css')
    <!-- Optionally add custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
@endpush

@section('breadcrumbs')
<!-- Dynamic Breadcrumb -->
<x-breadcrumbs 
    title="Create New Research Grant" 
    :breadcrumbs="[ 
        ['url' => route('dashboard'), 'label' => 'Home'], 
        ['url' => route('researchgrants.index'), 'label' => 'Research Grants'], 
        ['url' => '', 'label' => 'Create New Research Grant']
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
                        <i class="bi bi-folder-plus me-2"></i>Create New Research Grant
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('researchgrants.store') }}" method="POST" class="row g-3">
                        @csrf

                        <!-- Project Title -->
                        <div class="row mb-3">
                            <label for="project_title" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-file-earmark-text-fill text-primary me-2" style="font-size: 1.2rem;"></i>Project Title
                            </label>
                            <div class="col-sm-9">
                                <input type="text" name="project_title" id="project_title"
                                    class="form-control @error('project_title') is-invalid @enderror"
                                    value="{{ old('project_title') }}" placeholder="Enter project title" required>
                                @error('project_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Grant Amount -->
                        <div class="row mb-3">
                            <label for="grant_amount" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-cash-coin text-success me-2" style="font-size: 1.2rem;"></i>Grant Amount
                            </label>
                            <div class="col-sm-9">
                                <input type="number" step="0.01" name="grant_amount" id="grant_amount"
                                    class="form-control @error('grant_amount') is-invalid @enderror"
                                    value="{{ old('grant_amount') }}" placeholder="Enter grant amount" required>
                                @error('grant_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Grant Provider -->
                        <div class="row mb-3">
                            <label for="grant_provider" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-building-fill text-info me-2" style="font-size: 1.2rem;"></i>Grant Provider
                            </label>
                            <div class="col-sm-9">
                                <input type="text" name="grant_provider" id="grant_provider"
                                    class="form-control @error('grant_provider') is-invalid @enderror"
                                    value="{{ old('grant_provider') }}" placeholder="Enter grant provider" required>
                                @error('grant_provider')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div class="row mb-3">
                            <label for="start_date" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-calendar-event-fill text-warning me-2" style="font-size: 1.2rem;"></i>Start Date
                            </label>
                            <div class="col-sm-9">
                                <input type="date" name="start_date" id="start_date"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="row mb-3">
                            <label for="end_date" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-calendar2-check-fill text-secondary me-2" style="font-size: 1.2rem;"></i>End Date
                            </label>
                            <div class="col-sm-9">
                                <input type="date" name="end_date" id="end_date"
                                    class="form-control @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Duration (Months) -->
                        <div class="row mb-3">
                            <label for="duration_month" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-hourglass-split text-danger me-2" style="font-size: 1.2rem;"></i>Duration (Months)
                            </label>
                            <div class="col-sm-9">
                                <input type="number" name="duration_month" id="duration_month"
                                    class="form-control @error('duration_month') is-invalid @enderror"
                                    value="{{ old('duration_month') }}" placeholder="Enter duration in months" required>
                                @error('duration_month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Project Leader -->
                        <div class="row mb-3">
                            <label for="leader_id" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-person-badge-fill text-primary me-2" style="font-size: 1.2rem;"></i>Project Leader
                            </label>
                            <div class="col-sm-9">
                                <select name="leader_id" id="leader_id"
                                    class="form-select @error('leader_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select Project Leader</option>
                                    @foreach ($academicians as $academician)
                                        <option value="{{ $academician->id }}" {{ old('leader_id') == $academician->id ? 'selected' : '' }}>
                                            {{ $academician->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('leader_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Project Members -->
                        <div class="row mb-3">
                            <label for="members" class="col-sm-3 col-form-label fw-bold d-flex align-items-center">
                                <i class="bi bi-people-fill text-success me-2" style="font-size: 1.2rem;"></i>Project Members
                            </label>
                            <div class="col-sm-9">
                                <select name="members[]" id="members" multiple
                                    class="form-select @error('members') is-invalid @enderror" required>
                                    @foreach ($academicians as $academician)
                                        <option value="{{ $academician->id }}" {{ collect(old('members'))->contains($academician->id) ? 'selected' : '' }}>
                                            {{ $academician->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('members')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit and Cancel Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Save Research Grant
                            </button>
                            <a href="{{ route('researchgrants.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left-circle"></i> Back to Research Grants
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
