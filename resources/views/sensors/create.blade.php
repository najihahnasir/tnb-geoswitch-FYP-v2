@extends('layouts.master')
@section('title', 'Register New Sensor')
@section('page-title', 'Register Sensor')

@section('content')
<div class="container my-2">
    <div class="card shadow-sm p-4">
        <h5 class="mb-4">📡 Register Sensor</h5>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-4" id="sensorTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="form-tab" data-bs-toggle="tab" data-bs-target="#form" type="button" role="tab">Register Manually</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab">Bulk Upload via CSV</button>
            </li>
        </ul>

        {{-- Tab Contents --}}
        <div class="tab-content" id="sensorTabContent">
            {{-- Manual Registration Form --}}
            <div class="tab-pane fade show active" id="form" role="tabpanel">
                <form method="POST" action="{{ route('sensors.store') }}">
                    @csrf

                    <!-- Substation -->
                <div class="mb-3">
                    <label for="substation_id" class="form-label">Substation</label>
                    <select name="substation_id" id="substation_id" class="form-select" required>
                        <option value="">-- Select Substation --</option>
                        @foreach($substations as $substation)
                            <option value="{{ $substation->id }}"
                                {{ (old('substation_id') ?? $selectedSubstation?->id) == $substation->id ? 'selected' : '' }}>
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
        @if(isset($selectedSubstation))
            @foreach($selectedSubstation->switchgears as $swg)
                @php
                    $panelName = $swg->panel_name ?? 'Panel';
                    $compartment = $swg->compartment ?? 'Compartment';
                    $lineup = $swg->lineup ?? 'N/A';
                    $panelCode = $swg->panel_code ?? 'X';
                    $compCode = $swg->compartment_code ?? 'X';
                    $label = "{$panelName} – {$compartment} (Lineup: {$lineup}, Code: {$panelCode}_{$compCode})";
                @endphp
                <option value="{{ $swg->id }}"
                    {{ (old('switchgear_id') ?? $selectedSwitchgear?->id) == $swg->id ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        @endif
    </select>
    @error('switchgear_id')<small class="text-danger">{{ $message }}</small>@enderror
</div>



                    {{-- <div class="mb-3">
                        <label for="switchgear_id" class="form-label">Switchgear</label>
                        <input type="hidden" name="switchgear_id" id="switchgear_id" />

                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" data-bs-toggle="dropdown">
                                <span id="switchgearLabel">-- Select Switchgear --</span>
                            </button>

                            <ul class="dropdown-menu w-100" style="max-height: 300px; overflow-y: auto;">
                                @foreach($substations as $substation)
                                    @foreach($substation->switchgears as $swg)
                                        @php
                                            $label = ($swg->panel_name ?? 'Panel') . ' – ' . ($swg->compartment ?? 'Compartment');
                                            $lineup = $swg->lineup ?? 'N/A';
                                            $code = ($swg->panel_code ?? 'X') . '_' . ($swg->compartment_code ?? 'X');
                                            $comp = strtoupper($swg->compartment_code ?? '');

                                            // Color badge logic
                                            $badgeColor = match($comp) {
                                                'CBT' => 'bg-warning text-dark',     // CB-TOP
                                                'CBB' => 'bg-danger text-white',     // CB-BOTTOM
                                                'MBSB' => 'bg-primary text-white',   // Main Busbar
                                                'RSBS' => 'bg-secondary text-white', // Reserve Busbar
                                                'CBL' => 'bg-info text-dark',        // Cable Compartment
                                                default => 'bg-light text-dark',     // Fallback
                                            };
                                        @endphp

                                        <li>
                                            <a href="#" class="dropdown-item d-flex justify-content-between align-items-center"
                                                onclick="selectSwitchgear({{ $swg->id }}, '{{ $label }} (Lineup: {{ $lineup }}, Code: {{ $code }})')">
                                                <div>
                                                    <div><strong>{{ $label }}</strong></div>
                                                    <small class="text-muted">Lineup: {{ $lineup }}, Code: {{ $code }}</small>
                                                </div>
                                                <span class="badge {{ $badgeColor }} ms-2">{{ $comp ?: 'UNK' }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    </div> --}}




                    {{-- Sensor tag --}}
                    <div class="mb-3">
                        <label for="tag" class="form-label">Sensor Tag</label>
                        <p class="text-muted">Sensor tag will be auto-generated based on substation, panel, and compartment.</p>
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

                    {{-- Brand Name --}}
                    <div class="mb-3">
                        <label for="brand_name" class="form-label">Brand Name</label>
                        <input type="text" name="brand_name" class="form-control" id="brand_name">
                    </div>

                    {{-- SKU --}}
                    <div class="mb-3">
                        <label for="sku" class="form-label">SKU / Model No.</label>
                        <input type="text" name="sku" class="form-control" id="sku">
                    </div>

                    {{-- Warranty --}}
                    <div class="mb-3">
                        <label for="warranty" class="form-label">Warranty</label>
                        <input type="text" name="warranty" class="form-control" id="warranty" placeholder="e.g., 2 years or until 2027">
                    </div>

                    <button type="submit" class="btn btn-primary">➕ Register Sensor</button>
                </form>
            </div>

            {{-- CSV Upload --}}
            <div class="tab-pane fade" id="upload" role="tabpanel">
                <form method="POST" action="{{ route('sensors.upload') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Upload CSV File</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                    </div>

                    <p class="text-muted small">
                        Required CSV headers: <code>switchgear_id, tag, type, status, brand_name, sku, warranty</code>
                    </p>

                    <button type="submit" class="btn btn-success">📤 Upload Sensors</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
    const substations = @json($substations);

    document.getElementById('substation_id').addEventListener('change', function () {
        const selectedId = this.value;
        const switchgearSelect = document.getElementById('switchgear_id');

        switchgearSelect.innerHTML = '<option value="">-- Select Switchgear --</option>';

        const substation = substations.find(s => s.id == selectedId);
        if (!substation || !substation.switchgears) return;

        substation.switchgears.forEach(sw => {
            const label = `${sw.panel_code ?? 'N/A'} - ${sw.compartment_code ?? 'N/A'} (Lineup: ${sw.lineup ?? 'N/A'})`;
            switchgearSelect.innerHTML += `<option value="${sw.id}">${label}</option>`;
        }); 
    });
</script>
@endpush
