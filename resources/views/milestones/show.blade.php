@extends('layouts.niceadmin')

@section('content')
    <div class="pagetitle">
        <h1>Milestone Details</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('researchgrants.index') }}">Research Grants</a></li>
                <li class="breadcrumb-item"><a href="{{ route('researchgrants.milestones.index', $researchGrant->id) }}">Milestones</a></li>
                <li class="breadcrumb-item active">Milestone Details</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <!-- Milestone Details Card -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-flag-fill me-2"></i>Milestone Information</h5>
                        <span class="badge bg-success text-uppercase">{{ $milestone->status }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Milestone Name -->
                            <div class="col-md-6 mb-4">
                                <div class="info-box d-flex align-items-center">
                                    <i class="bi bi-bookmark text-info me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-0">Milestone Name</h6>
                                        <p class="mb-0 fw-bold">{{ $milestone->milestone_name }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Completion Date -->
                            <div class="col-md-6 mb-4">
                                <div class="info-box d-flex align-items-center">
                                    <i class="bi bi-calendar-check text-warning me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-0">Completion Date</h6>
                                        <p class="mb-0 fw-bold">{{ $milestone->completion_date->format('d-m-Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Deliverable -->
                            <div class="col-md-6 mb-4">
                                <div class="info-box d-flex align-items-center">
                                    <i class="bi bi-box-seam text-primary me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-0">Deliverable</h6>
                                        <p class="mb-0 fw-bold">{{ $milestone->deliverable }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Remark -->
                            <div class="col-md-6 mb-4">
                                <div class="info-box d-flex align-items-center">
                                    <i class="bi bi-chat-left-text text-secondary me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-0">Remark</h6>
                                        <p class="mb-0 fw-bold">{{ $milestone->remark }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-start mt-4">
                    @can('update-milestone', $researchGrant)
                        <a href="{{ route('milestones.edit', $milestone->id) }}" class="btn btn-warning me-2">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    @endcan
                    @can('admin-only')
                        <form action="{{ route('milestones.destroy', $milestone->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger me-2">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    @endcan
                    <a href="{{ route('researchgrants.milestones.index', $researchGrant->id) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Back to Milestones
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
