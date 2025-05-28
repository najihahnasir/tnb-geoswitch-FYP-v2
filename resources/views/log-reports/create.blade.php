@extends('layouts.master')
@section('title', 'Report Log Sensor')
@section('page-title', 'Register Sensor')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Create Sensor Log Report</h4>
        <a href="{{ route('log-reports.index') }}" class="btn btn-secondary">Back to Reports</a>
    </div>

    <form action="{{ route('log-reports.store') }}" method="POST">
        @csrf

        <!-- Substation -->
        <div class="mb-3">
            <label for="substation_id" class="form-label">Substation</label>
            <select name="substation_id" id="substation_id" class="form-select" required>
                <option value="">-- Select Substation --</option>
                @foreach($substations as $substation)
                    <option value="{{ $substation->id }}" {{ old('substation_id') == $substation->id ? 'selected' : '' }}>
                        {{ $substation->substation_name }}
                    </option>
                @endforeach
            </select>
            @error('substation_id')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <!-- Switchgear -->
        <div class="mb-3">
            <label for="switchgear_id" class="form-label">Switchgear</label>
            <select name="switchgear_id" id="switchgear_id" class="form-select" required>
                <option value="">-- Select Switchgear --</option>
            </select>
            @error('switchgear_id')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <!-- Sensor -->
        <div class="mb-3">
            <label for="sensor_id" class="form-label">Sensor</label>
            <select name="sensor_id" id="sensor_id" class="form-select" required>
                <option value="">-- Select Sensor --</option>
            </select>
            @error('sensor_id')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <!-- Sensor Status -->
        <div class="mb-3">
            <label for="sensor_status" class="form-label">Sensor Status</label>
            <select class="form-select" name="sensor_status" id="sensor_status" required>
                <option value="">-- Select Status --</option>
                <option value="offline">Offline</option>
                <option value="repairing">Repairing</option>
            </select>
        </div>

        <!-- Title -->
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
            @error('title')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            @error('description')<small class="text-danger">{{ $message }}</small>@enderror
        </div>

        <button type="submit" class="btn btn-primary">Submit Report</button>
    </form>
</div>
@endsection

@push('after-scripts')
<script>
    const substations = @json($substations);

    document.getElementById('substation_id').addEventListener('change', function () {
        const selectedId = this.value;
        const switchgearSelect = document.getElementById('switchgear_id');
        const sensorSelect = document.getElementById('sensor_id');

        switchgearSelect.innerHTML = '<option value="">-- Select Switchgear --</option>';
        sensorSelect.innerHTML = '<option value="">-- Select Sensor --</option>';

        const substation = substations.find(s => s.id == selectedId);
        if (!substation || !substation.switchgears) return;

        substation.switchgears.forEach(sw => {
            const label = `${sw.display_name ?? (sw.name + ' - ' + sw.compartment)} (ID: ${sw.id})`;
            switchgearSelect.innerHTML += `<option value="${sw.id}">${label}</option>`;
        });
    });

    document.getElementById('switchgear_id').addEventListener('change', function () {
        const substationId = document.getElementById('substation_id').value;
        const switchgearId = this.value;
        const sensorSelect = document.getElementById('sensor_id');
        sensorSelect.innerHTML = '<option value="">-- Select Sensor --</option>';

        const substation = substations.find(s => s.id == substationId);
        const switchgear = substation?.switchgears?.find(sw => sw.id == switchgearId);

        if (!switchgear || !switchgear.sensors) return;

        switchgear.sensors.forEach(sensor => {
            sensorSelect.innerHTML += `<option value="${sensor.id}">${sensor.tag} (${sensor.type})</option>`;
        });
    });
</script>
@endpush
