@extends('dashboard') <!-- Extending the NiceAdmin layout -->

@section('title', 'Edit Milestone') <!-- Page Title -->

@push('css')
    <!-- Optionally add custom CSS -->
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
@endpush

@section('breadcrumbs')
<!-- Dynamic Breadcrumb -->
<x-breadcrumbs 
    title="Edit Milestone" 
    :breadcrumbs="[
        ['url' => route('dashboard'), 'label' => 'Home'],
        ['url' => route('researchgrants.index'), 'label' => 'Research Grants'],
        ['url' => route('researchgrants.milestones.index', $researchGrant->id), 'label' => 'Milestones'],
        ['url' => '', 'label' => 'Edit Milestone']
    ]"
/>
@endsection

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h4 class="card-title"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Milestone</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('researchgrants.milestones.update', [$researchGrant->id, $milestone->id]) }}" method="POST" class="row g-4">
                        @csrf
                        @method('PUT')

                        <!-- Milestone Name -->
                        <div class="col-md-6">
                            <label for="milestone_name" class="form-label"><i class="bi bi-card-text text-primary me-2"></i>Milestone Name</label>
                            <input type="text" class="form-control @error('milestone_name') is-invalid @enderror" 
                                   id="milestone_name" name="milestone_name" 
                                   value="{{ old('milestone_name', $milestone->milestone_name) }}" required>
                            @error('milestone_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Completion Date -->
                        <div class="col-md-6">
                            <label for="completion_date" class="form-label"><i class="bi bi-calendar-check text-success me-2"></i>Completion Date</label>
                            <input type="date" class="form-control @error('completion_date') is-invalid @enderror" 
                                   id="completion_date" name="completion_date" 
                                   value="{{ old('completion_date', $milestone->completion_date->format('Y-m-d')) }}" required>
                            @error('completion_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deliverable -->
                        <div class="col-md-12">
                            <label for="deliverable" class="form-label"><i class="bi bi-box-seam text-info me-2"></i>Deliverable</label>
                            <input type="text" class="form-control @error('deliverable') is-invalid @enderror" 
                                   id="deliverable" name="deliverable" 
                                   value="{{ old('deliverable', $milestone->deliverable) }}" required>
                            @error('deliverable')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="status" class="form-label"><i class="bi bi-list-task text-warning me-2"></i>Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="Not Started" {{ old('status', $milestone->status) == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                <option value="In Progress" {{ old('status', $milestone->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ old('status', $milestone->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remark -->
                        <div class="col-md-6">
                            <label for="remark" class="form-label"><i class="bi bi-chat-left-dots text-secondary me-2"></i>Remark</label>
                            <textarea class="form-control @error('remark') is-invalid @enderror" 
                                      id="remark" name="remark">{{ old('remark', $milestone->remark) }}</textarea>
                            @error('remark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit and Cancel Buttons -->
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-warning me-2"><i class="bi bi-save"></i> Update Milestone</button>
                            <a href="{{ route('researchgrants.milestones.index', $researchGrant->id) }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
