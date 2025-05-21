@extends('layouts.master')
@section('title', 'Register New Sensor')
@section('page-title', 'Register Sensor')

@section('content')
<div class="container my-4">
    <div class="card shadow-sm p-4">
        <h5 class="mb-4">📡 Register New Sensor</h5>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('sensors.store') }}">
            @csrf

            {{-- Substation dropdown --}}
            <div class="mb-3">
                <label for="substation_id" class="form-label">Substation</label>
                <select id="substation_id" class="form-select" required>
                    <option value="">-- Select Substation --</option>
                    @foreach($substations as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->substation_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Switchgear dropdown (dependent) --}}
            <div class="mb-3">
                <label for="switchgear_id" class="form-label">Switchgear</label>
                <select name="switchgear_id" id="switchgear_id" class="form-select" required>
                    <option value="">-- Select Switchgear --</option>
                </select>
            </div>

            {{-- Sensor tag --}}
            <div class="mb-3">
                <label for="tag" class="form-label">Sensor Tag</label>
                <input type="text" name="tag" class="form-control" id="tag" required>
            </div>

            {{-- Sensor type --}}
            <div class="mb-3">
                <label for="type" class="form-label">Sensor Type</label>
                <select name="type" id="type" class="form-select" required>
                    <option value="Partial Discharge">Partial Discharge</option>
                    <option value="Thermal">Thermal</option>
                </select>
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="online">Online</option>
                    <option value="repairing">Repairing</option>
                    <option value="offline">Offline</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">➕ Register</button>
        </form>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const substationSelect = document.getElementById('substation_id');
        const switchgearSelect = document.getElementById('switchgear_id');

        substationSelect.addEventListener('change', function () {
            const substationId = this.value;
            switchgearSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`/api/switchgears/by-substation/${substationId}`)
                .then(response => response.json())
                .then(data => {
                    switchgearSelect.innerHTML = '<option value="">-- Select Switchgear --</option>';
                    data.forEach(sg => {
                        switchgearSelect.innerHTML += `<option value="${sg.id}">${sg.panel}</option>`;
                    });
                });
        });
    });
</script>
@endpush
