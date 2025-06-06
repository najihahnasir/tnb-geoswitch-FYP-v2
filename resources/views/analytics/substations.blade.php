@extends('layouts.master')

@section('title', 'Substation Analytics')
@section('page-title', 'Substation Analytics')

@section('content')
<style>
    .chart-container {
        position: relative;
        height: 320px;
        width: 100%;
    }
    .card-title {
        font-size: 1rem;
        font-weight: 600;
    }
</style>

<div class="container my-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">📊 Substation Analytics Dashboard</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('analytics.export', ['export' => 'pdf']) }}" class="btn btn-danger btn-sm">📄 Export PDF</a>
            <a href="{{ route('analytics.export', ['export' => 'excel']) }}" class="btn btn-success btn-sm">📊 Export Excel</a>
            <a href="{{ route('analytics.export', ['export' => 'csv']) }}" class="btn btn-secondary btn-sm">📑 Export CSV</a>
        </div>
    </div>

     <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-start border-4 border-success h-100">
                <div class="card-body text-center" style="height: 40px">
                    <h2 class="fw-bold text-dark">{{ $onlineCount ?? 0 }}</h2>
                    <p class="mb-0 text-muted">Online Sensors</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-start border-4 border-warning h-100">
                <div class="card-body text-center">
                    <h2 class="fw-bold text-dark">{{ $repairingCount ?? 0 }}</h2>
                    <p class="mb-0 text-muted">Repairing Sensors</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-start border-4 border-danger h-100">
                <div class="card-body text-center">
                    <h2 class="fw-bold text-dark">{{ $offlineCount ?? 0 }}</h2>
                    <p class="mb-0 text-muted">Offline Sensors</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Controls -->
    <form id="filterForm" method="GET" action="{{ route('analytics.substations') }}" class="row g-3 mb-4 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Substation</label>
            <select name="substation_id" class="form-select">
                <option value="">All Substations</option>
                @foreach($substations as $sub)
                    <option value="{{ $sub->id }}" {{ request('substation_id') == $sub->id ? 'selected' : '' }}>
                        {{ $sub->substation_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Sensor Type</label>
            <select name="sensor_type" class="form-select">
                <option value="">All</option>
                @foreach($sensorTypes as $type)
                    <option value="{{ $type }}" {{ request('sensor_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="online" {{ request('status') == 'online' ? 'selected' : '' }}>Online</option>
                <option value="repairing" {{ request('status') == 'repairing' ? 'selected' : '' }}>Repairing</option>
                <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Year</label>
            <select name="year" class="form-select">
                @foreach(range(now()->year, 2021) as $y)
                    <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Month</label>
            <select name="month" class="form-select">
                <option value="">All</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-1 d-grid">
            <button type="submit" class="btn btn-primary">🔍</button>
        </div>
    </form>

    <!-- Chart Section -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm p-3">
                <h6 class="card-title">📌 Sensor Count by Type (Stacked)</h6>
                <div class="chart-container">
                    <canvas id="sensorTypeStackedChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm p-3">
                <h6 class="card-title">📈 Uptime % Over Time</h6>
                <div class="chart-container">
                    <canvas id="uptimeTrendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card shadow-sm p-3">
                <h6 class="card-title">🚨 Top Problematic Sensors</h6>
                <small class="text-muted">Showing top 5 offline sensors from Jan to Dec {{ now()->year }}</small>
                <div class="chart-container" style="width: 100%;">
                    <canvas id="topProblemChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const sensorData = @json($sensorTypeChartData);
    const uptimeData = {
        labels: @json($uptimeTrend['labels']),
        datasets: [{
            label: 'Uptime %',
            data: @json($uptimeTrend['data']),
            borderColor: '#2ecc71',
            fill: false,
            tension: 0.3
        }]
    };
    const topProblemData = @json($topProblematicSensors);

    new Chart(document.getElementById('sensorTypeStackedChart'), {
        type: 'bar',
        data: sensorData,
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true }
            }
        }
    });

    new Chart(document.getElementById('uptimeTrendChart'), {
        type: 'line',
        data: uptimeData,
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: false }
            },
            scales: {
                y: { min: 0, max: 100, title: { display: true, text: 'Uptime %' } },
                x: { title: { display: true, text: 'Month' } }
            }
        }
    });

    new Chart(document.getElementById('topProblemChart'), {
        type: 'bar',
        data: {
            labels: topProblemData.labels,
            datasets: [{
                label: 'Offline Count',
                data: topProblemData.datasets[0].data,
                backgroundColor: '#e74c3c',
                barThickness: 20
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                title: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: { display: true, text: 'Offline Count' }
                }
            }
        }
    });
</script>
@endpush
