@extends('layouts.niceadmin')

@section('content')
    <div class="pagetitle">
        <h1>Create Milestone</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('researchgrants.index') }}">Research Grants</a></li>
                <li class="breadcrumb-item active">Create Milestone</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <!-- Create Milestone Form Card -->
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-plus me-2"></i>Create New Milestone for 
                            <span class="text-primary">{{ $researchGrant->project_title }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('researchgrants.milestones.store', $researchGrant->id) }}" method="POST">
                            @csrf

                            <div class="row mb-3">
                                <label for="milestone_name" class="col-md-3 col-form-label fw-bold d-flex align-items-center">
                                    <i class="bi bi-flag-fill text-primary me-2" style="font-size: 1.2rem;"></i>Milestone Name
                                </label>
                                <div class="col-md-9">
                                    <input type="text" name="milestone_name" id="milestone_name"
                                        class="form-control @error('milestone_name') is-invalid @enderror"
                                        value="{{ old('milestone_name') }}" placeholder="Enter milestone name" required>
                                    @error('milestone_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="completion_date" class="col-md-3 col-form-label fw-bold d-flex align-items-center">
                                    <i class="bi bi-calendar-check-fill text-success me-2" style="font-size: 1.2rem;"></i>Completion Date
                                </label>
                                <div class="col-md-9">
                                    <input type="date" name="completion_date" id="completion_date"
                                        class="form-control @error('completion_date') is-invalid @enderror"
                                        value="{{ old('completion_date') }}" required>
                                    @error('completion_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="deliverable" class="col-md-3 col-form-label fw-bold d-flex align-items-center">
                                    <i class="bi bi-box-seam-fill text-warning me-2" style="font-size: 1.2rem;"></i>Deliverable
                                </label>
                                <div class="col-md-9">
                                    <input type="text" name="deliverable" id="deliverable"
                                        class="form-control @error('deliverable') is-invalid @enderror"
                                        value="{{ old('deliverable') }}" placeholder="Enter deliverable details" required>
                                    @error('deliverable')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="status" class="col-md-3 col-form-label fw-bold d-flex align-items-center">
                                    <i class="bi bi-hourglass-split text-secondary me-2" style="font-size: 1.2rem;"></i>Status
                                </label>
                                <div class="col-md-9">
                                    <select name="status" id="status"
                                        class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="Not Started" {{ old('status') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                        <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="remark" class="col-md-3 col-form-label fw-bold d-flex align-items-center">
                                    <i class="bi bi-chat-dots-fill text-info me-2" style="font-size: 1.2rem;"></i>Remark
                                </label>
                                <div class="col-md-9">
                                    <textarea name="remark" id="remark"
                                        class="form-control @error('remark') is-invalid @enderror" rows="4"
                                        placeholder="Enter any additional remarks">{{ old('remark') }}</textarea>
                                    @error('remark')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="grant_title" class="col-md-3 col-form-label fw-bold d-flex align-items-center">
                                    <i class="bi bi-bookmark-fill text-danger me-2" style="font-size: 1.2rem;"></i>Research Grant
                                </label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" id="grant_title" value="{{ $researchGrant->project_title }}" disabled>
                                    <input type="hidden" name="grant_id" value="{{ $researchGrant->id }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Create Milestone
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
