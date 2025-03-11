@extends('layouts.niceadmin')

@section('content')
    <div class="pagetitle">
        <h1>Research Grant Details</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('researchgrants.index') }}">Research Grants</a></li>
                <li class="breadcrumb-item active">Grant Details</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <!-- Research Grant Details Card -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text-fill me-2"></i>Grant Information</h5>
                        <span class="badge bg-primary text-uppercase">{{ $researchGrant->project_title }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Grant Amount -->
                            <div class="col-md-6 mb-4">
                                <div class="info-box d-flex align-items-center">
                                    <i class="bi bi-cash-coin text-success me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-0">Grant Amount</h6>
                                        <p class="mb-0 fw-bold">RM {{ number_format($researchGrant->grant_amount, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                
                            <!-- Grant Provider -->
                            <div class="col-md-6 mb-4">
                                <div class="info-box d-flex align-items-center">
                                    <i class="bi bi-building text-info me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-0">Grant Provider</h6>
                                        <p class="mb-0 fw-bold">{{ $researchGrant->grant_provider }}</p>
                                    </div>
                                </div>
                            </div>
                
                            <!-- Start Date -->
                            <div class="col-md-6 mb-4">
                                <div class="info-box d-flex align-items-center">
                                    <i class="bi bi-calendar-check text-warning me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-0">Start Date</h6>
                                        <p class="mb-0 fw-bold">
                                            {{ $researchGrant->start_date ? \Carbon\Carbon::parse($researchGrant->start_date)->format('d-m-Y') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                
                            <!-- End Date -->
                            <div class="col-md-6 mb-4">
                                <div class="info-box d-flex align-items-center">
                                    <i class="bi bi-calendar-x text-danger me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-0">End Date</h6>
                                        <p class="mb-0 fw-bold">
                                            {{ $researchGrant->end_date ? \Carbon\Carbon::parse($researchGrant->end_date)->format('d-m-Y') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                
                            <!-- Duration -->
                            <div class="col-md-6 mb-4">
                                <div class="info-box d-flex align-items-center">
                                    <i class="bi bi-hourglass-split text-secondary me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-0">Duration</h6>
                                        <p class="mb-0 fw-bold">{{ $researchGrant->duration_month }} months</p>
                                    </div>
                                </div>
                            </div>
                
                            <!-- Project Leader -->
                            <div class="col-md-6 mb-4">
                                <div class="info-box d-flex align-items-center">
                                    <i class="bi bi-person-badge text-primary me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-0">Project Leader</h6>
                                        <p class="mb-0 fw-bold text-primary">
                                            {{ $researchGrant->leader ? $researchGrant->leader->name : 'No leader assigned' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Members List -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title"><i class="bi bi-people-fill me-2"></i>Project Members</h5>
                    </div>
                    <div class="card-body">
                        @if ($researchGrant->academicians->where('pivot.role', 'member')->count() > 0)
                            <ul class="list-group">
                                @foreach ($researchGrant->academicians as $academician)
                                    @if ($academician->pivot->role === 'member')
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $academician->name }}
                                            <span class="badge bg-primary">{{ $academician->email }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No members assigned to this research grant.</p>
                        @endif
                    </div>
                </div><!-- End Members List -->

                <!-- Action Buttons -->
                <div class="d-flex justify-content-start mt-4">
                    <a href="{{ route('researchgrants.edit', ['researchgrant' => $researchGrant->id]) }}" class="btn btn-warning me-2">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <form action="{{ route('researchgrants.destroy', ['researchgrant' => $researchGrant->id]) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger me-2">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                    
                    @if ($researchGrant->getLeader())
                    <a href="{{ route('researchgrants.milestones.create', ['researchgrant' => $researchGrant->id]) }}" class="btn btn-primary">
                        Create Milestone
                    </a>
                    @endif
        
                    <a href="{{ route('researchgrants.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Back to Research Grants
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

