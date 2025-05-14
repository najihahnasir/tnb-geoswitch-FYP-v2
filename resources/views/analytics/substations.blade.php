@extends('layouts.master')
@section('title', 'Substation Analytics')
@section('page-title', 'Substation Analytics')

@section('content')
<div class="container-fluid py-3">

    {{-- FILTERS --}}
    <form method="GET" action="{{ route('analytics.substations') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label fw-semibold">Substation</label>
            <select name="substation" class="form-select">
                <option value="">All</option>
                @foreach($substations as $s)
                    <option value="{{ $s->id }}" {{ request('substation') == $s->id ? 'selected' : '' }}>
                        {{ $s->substation_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">Year</label>
            <select name="year" class="form-select">
                @foreach(range(now()->year, 2021) as $y)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">Month</label>
            <select name="month" class="form-select">
                <option value="">All</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
        </div>
    </form>

    {{-- CHART CARDS --}}
    @php
        $charts = [
            ['id' => 'sensorCountChart', 'title' => 'Total Sensors per Substation'],
            ['id' => 'failureTrendChart', 'title' => 'Sensor Failure Trend'],
            ['id' => 'uptimeChart', 'title' => 'Uptime vs Downtime'],
            ['id' => 'problemSensorsChart', 'title' => 'Top Problematic Sensors'],
            ['id' => 'avgDowntimeChart', 'title' => 'Average Downtime Duration (Minutes)'],
            ['id' => 'rankingChart', 'title' => 'Substation Performance Ranking']
        ];
    @endphp

    @foreach ($charts as $chart)
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $chart['title'] }}</h5>
            <button class="btn btn-sm btn-outline-dark" data-bs-toggle="collapse" data-bs-target="#{{ $chart['id'] }}Wrapper">
                <i class="fas fa-angle-down"></i> Toggle
            </button>
        </div>
        <div id="{{ $chart['id'] }}Wrapper" class="collapse show card-body">
            <canvas id="{{ $chart['id'] }}" height="140"></canvas>
        </div>
    </div>
    @endforeach

</div>
@endsection

@push('after-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartConfig = {
        sensorCountChart: {
            type: 'bar',
            data: {
                labels: {!! json_encode($sensorCounts['labels']) !!},
                datasets: [{
                    label: 'Sensor Count',
                    data: {!! json_encode($sensorCounts['data']) !!},
                    backgroundColor: '#3498db'
                }]
            }
        },
        failureTrendChart: {
            type: 'line',
            data: {
                labels: {!! json_encode($failureTrend['labels']) !!},
                datasets: [{
                    label: 'Failures',
                    data: {!! json_encode($failureTrend['data']) !!},
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            }
        },
        uptimeChart: {
            type: 'doughnut',
            data: {
                labels: ['Uptime', 'Downtime'],
                datasets: [{
                    data: [{{ $uptimeVsDowntime['uptime'] }}, {{ $uptimeVsDowntime['downtime'] }}],
                    backgroundColor: ['#2ecc71', '#e67e22']
                }]
            }
        },
        problemSensorsChart: {
            type: 'bar',
            data: {
                labels: {!! json_encode($topProblematicSensors['labels']) !!},
                datasets: [{
                    label: 'Offline Count',
                    data: {!! json_encode($topProblematicSensors['data']) !!},
                    backgroundColor: '#e74c3c'
                }]
            }
        },
        avgDowntimeChart: {
            type: 'bar',
            data: {
                labels: {!! json_encode($avgDowntime['labels']) !!},
                datasets: [{
                    label: 'Avg Downtime (mins)',
                    data: {!! json_encode($avgDowntime['data']) !!},
                    backgroundColor: '#f39c12'
                }]
            }
        },
        rankingChart: {
            type: 'bar',
            data: {
                labels: {!! json_encode($ranking['labels']) !!},
                datasets: [{
                    label: 'Performance Score',
                    data: {!! json_encode($ranking['data']) !!},
                    backgroundColor: '#8e44ad'
                }]
            },
            options: {
                indexAxis: 'y',
            }
        }
    };

    Object.keys(chartConfig).forEach(chartId => {
        const ctx = document.getElementById(chartId).getContext('2d');
        new Chart(ctx, chartConfig[chartId]);
    });
</script>
@endpush
