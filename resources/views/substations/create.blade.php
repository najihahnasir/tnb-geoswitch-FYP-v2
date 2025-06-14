@extends('layouts.master')
@section('title', 'Register New Substation')
@section('page-title', 'Register Substation')

@section('content')
<div class="container my-2">
    <div class="card shadow-sm p-4">
        <h5 class="mb-4">🆕 Register New Substation</h5>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('substations.store') }}">
            @csrf

            <div class="mb-3">
                <label for="substation_name" class="form-label">Substation Name</label>
                <input type="text" class="form-control" id="substation_name" name="substation_name" required>
            </div>

            <div class="mb-3">
                <label for="code" class="form-label">Code</label>
                <input type="text" class="form-control" id="code" name="code" 
                    value="{{ old('code') ?? 'AUTO_GENERATED' }}" readonly>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="2" required></textarea>
            </div>

            <div class="mb-3">
                <label for="substation_location" class="form-label">Substation Location</label>
                <input type="text" class="form-control" id="substation_location" name="substation_location" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="number" step="0.000001" class="form-control" id="latitude" name="latitude" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="number" step="0.000001" class="form-control" id="longitude" name="longitude" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="Main" selected>Main</option>
                        <option value="Backup">Backup</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">➕ Register</button>
        </form>
    </div>
</div>
@endsection
@push('after-script')
<script>
    document.getElementById('substation_name').addEventListener('input', function () {
        let name = this.value.trim().toUpperCase();
        let prefix = name.includes('PMU') ? 'PMU' : 'PPU';
        let parts = name.split(' ');
        let suffix = parts[parts.length - 1].replace(/\s+/g, '_');
        document.getElementById('code').value = prefix + '_' + suffix;
    });
</script>
@endpush
