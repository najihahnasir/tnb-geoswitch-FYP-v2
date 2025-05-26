@extends('layouts.master')
@section('title', 'Edit Status Sensor new')
@section('page-title', 'Register Sensor')

@section('content')
<div class="container">
    <h2>Update Sensor Status for Report #{{ $report->id }}</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('log-reports.updateStatus', $report->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Sensor Tag</label>
            <input type="text" class="form-control" value="{{ $report->sensor->tag }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Current Status</label>
            <input type="text" class="form-control" value="{{ ucfirst($report->sensor->status) }}" disabled>
        </div>

        <div class="mb-3">
            <label for="sensor_status" class="form-label">Update Status</label>
            <select class="form-select @error('sensor_status') is-invalid @enderror" name="sensor_status" id="sensor_status" required>
                <option value="">-- Select New Status --</option>
                <option value="online" {{ old('sensor_status') == 'online' ? 'selected' : '' }}>Online</option>
                <option value="offline" {{ old('sensor_status') == 'offline' ? 'selected' : '' }}>Offline</option>
                <option value="repairing" {{ old('sensor_status') == 'repairing' ? 'selected' : '' }}>Repairing</option>
            </select>
            @error('sensor_status')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Duration Sensor Has Been Down</label>
            <input type="text" class="form-control" value="{{ $downDuration }}" disabled>
        </div>

        <button type="submit" class="btn btn-primary">Update Status</button>
        <a href="{{ route('log-reports.show', $report->id) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
