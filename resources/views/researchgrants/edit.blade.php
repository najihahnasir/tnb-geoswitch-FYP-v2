@extends('dashboard') <!-- Extending the NiceAdmin layout -->

@section('title', 'Edit Research Grant') <!-- Page Title -->

@push('css')
    <!-- Optionally add custom CSS -->
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
@endpush

@section('breadcrumbs')
<!-- Dynamic Breadcrumb -->
<x-breadcrumbs 
    title="Edit Research Grant" 
    :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Home'],
        ['url' => route('researchgrants.index'), 'label' => 'Research Grants'],
        ['url' => '', 'label' => 'Edit Research Grant']
    ]"
/>
@endsection

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h4 class="card-title"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Research Grant</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('researchgrants.update', $researchGrant->id) }}" method="POST" class="row g-4">
                        @csrf
                        @method('PUT')

                        <!-- Project Title -->
                        <div class="col-md-6">
                            <label for="project_title" class="form-label"><i class="bi bi-card-text text-primary me-2"></i>Project Title</label>
                            <input type="text" class="form-control @error('project_title') is-invalid @enderror" 
                                   id="project_title" name="project_title" 
                                   value="{{ old('project_title', $researchGrant->project_title) }}" required>
                            @error('project_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Grant Amount -->
                        <div class="col-md-6">
                            <label for="grant_amount" class="form-label"><i class="bi bi-cash-coin text-success me-2"></i>Grant Amount</label>
                            <input type="number" step="0.01" class="form-control @error('grant_amount') is-invalid @enderror" 
                                   id="grant_amount" name="grant_amount" 
                                   value="{{ old('grant_amount', $researchGrant->grant_amount) }}" required>
                            @error('grant_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Grant Provider -->
                        <div class="col-md-6">
                            <label for="grant_provider" class="form-label"><i class="bi bi-building text-info me-2"></i>Grant Provider</label>
                            <input type="text" class="form-control @error('grant_provider') is-invalid @enderror" 
                                   id="grant_provider" name="grant_provider" 
                                   value="{{ old('grant_provider', $researchGrant->grant_provider) }}" required>
                            @error('grant_provider')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-6">
                            <label for="start_date" class="form-label"><i class="bi bi-calendar-event text-warning me-2"></i>Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" 
                                   value="{{ old('start_date', $researchGrant->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6">
                            <label for="end_date" class="form-label"><i class="bi bi-calendar-x text-danger me-2"></i>End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" 
                                   value="{{ old('end_date', $researchGrant->end_date->format('Y-m-d')) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duration (Months) -->
                        <div class="col-md-6">
                            <label for="duration_month" class="form-label"><i class="bi bi-hourglass-split text-secondary me-2"></i>Duration (Months)</label>
                            <input type="number" class="form-control @error('duration_month') is-invalid @enderror" 
                                   id="duration_month" name="duration_month" 
                                   value="{{ old('duration_month', $researchGrant->duration_month) }}" required>
                            @error('duration_month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Project Leader -->
                        <div class="col-md-6">
                            <label for="leader_id" class="form-label"><i class="bi bi-person-badge text-primary me-2"></i>Project Leader</label>
                            <select class="form-select @error('leader_id') is-invalid @enderror" id="leader_id" name="leader_id" required>
                                <option value="">-- Select a Leader --</option>
                                @foreach ($academicians as $academician)
                                    <option value="{{ $academician->id }}" 
                                            {{ old('leader_id', $researchGrant->leader_id) == $academician->id ? 'selected' : '' }}>
                                        {{ $academician->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('leader_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Members -->
                        <div class="col-md-12">
                            <label for="members" class="form-label"><i class="bi bi-people-fill text-success me-2"></i>Members</label>
                            <select name="members[]" id="members" class="form-select" multiple required>
                                @foreach ($academicians as $academician)
                                    <option value="{{ $academician->id }}" 
                                            {{ in_array($academician->id, old('members', $researchGrant->academicians->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $academician->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('members')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit and Cancel Buttons -->
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-warning me-2"><i class="bi bi-save"></i> Update Research Grant</button>
                            <a href="{{ route('researchgrants.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
