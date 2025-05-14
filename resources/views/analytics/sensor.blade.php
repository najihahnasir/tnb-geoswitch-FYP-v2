@extends('layouts.master')

@section('title', 'Sensor Analytics')
@section('page-title', 'Sensor Analytics')


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
    {{-- <h5 class="mb-4 mt-2">📊 Sensor Analytics Dashboard</h5> --}}
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <h5 class="mb-0">📊 Sensor Analytics Dashboard</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('analytics.export', ['export' => 'pdf']) }}" class="btn btn-danger btn-sm">📄 Export PDF</a>
            <a href="{{ route('analytics.export', ['export' => 'excel']) }}" class="btn btn-success btn-sm">📊 Export Excel</a>
            <a href="{{ route('analytics.export', ['export' => 'csv']) }}" class="btn btn-secondary btn-sm">📑 Export CSV</a>
            <button onclick="downloadChartAsPDF('totalSensorsChart')" class="btn btn-outline-primary btn-sm">🖼️ Chart PDF</button>
            <button onclick="viewChartAsPDF('totalSensorsChart')" class="btn btn-outline-secondary btn-sm">👁️ View Chart PDF</button>
        </div>
    </div>

    <!-- 🔹 Summary Cards -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
        <div class="col">
            <div class="card text-center shadow-sm p-3">
                <h6 class="card-title">Total Sensors</h6>
                <h3 class="text-primary">{{ $totalSensors }}</h3>
            </div>
        </div>
        <div class="col">
            <div class="card text-center shadow-sm p-3">
                <h6 class="card-title">Active Substations</h6>
                <h3 class="text-success">{{ $activeSubstations }}</h3>
            </div>
        </div>
        <div class="col">
            <div class="card text-center shadow-sm p-3">
                <h6 class="card-title">Offline Sensors</h6>
                <h3 class="text-danger">{{ $offlineSensors }}</h3>
            </div>
        </div>
    </div>

    <!-- 🔹 Filter Form -->
    {{-- <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <select name="sensor_type" class="form-select">
                <option value="">-- Select Sensor Type --</option>
                @foreach ($sensorTypes as $type)
                    <option value="{{ $type }}" {{ request('sensor_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="substation_id" class="form-select">
                <option value="">-- Select Substation --</option>
                @foreach ($substations as $s)
                    <option value="{{ $s->id }}" {{ request('substation_id') == $s->id ? 'selected' : '' }}>{{ $s->substation_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="year" class="form-control" placeholder="Year" value="{{ request('year') }}">
        </div>
        <div class="col-md-2">
            <input type="number" name="month" class="form-control" placeholder="Month" value="{{ request('month') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form> --}}

    <!-- 🔹 Filter Form (Dynamic) -->
    <form id="filterForm" method="GET" class="row g-3 mb-4 align-items-end">
        <div class="col-md-3">
            <label for="sensorTypeFilter" class="form-label">Sensor Type</label>
            <select name="sensor_type" id="sensorTypeFilter" class="form-select">
                <option value="">Select Sensor Type</option>
                @foreach ($sensorTypes as $type)
                    <option value="{{ $type }}" {{ request('sensor_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="substationFilter" class="form-label">Substation</label>
            <select name="substation_id" id="substationFilter" class="form-select">
                <option value="">Select Substation</option>
                @foreach ($substations as $s)
                    <option value="{{ $s->id }}" {{ request('substation_id') == $s->id ? 'selected' : '' }}>{{ $s->substation_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label for="yearFilter" class="form-label">Year</label>
            <select name="year" id="yearFilter" class="form-select">
                <option value="">Select Year </option>
                @for ($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <label for="monthFilter" class="form-label">Month</label>
            <input type="number" name="month" id="monthFilter" class="form-control" placeholder="Month" min="1" max="12" value="{{ request('month') }}">
        </div>

        <div class="col-md-2 d-flex gap-2">
            <a href="{{ route(\Illuminate\Support\Facades\Route::currentRouteName()) }}" class="btn btn-outline-secondary w-100">🔄 Reset</a>
        </div>
    </form>

    <!-- 🔹 Chart Grid -->
    <div class="row g-2 align-items-start mb-2">
        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h6 class="card-title mb-2">📅 Yearly Trend</h6>
                <div class="chart-container"><canvas id="trendChart"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h6 class="card-title mb-2">📊 Total Sensors by Type</h6>
                <div class="chart-container"><canvas id="totalSensorsChart"></canvas></div>
            </div>
        </div>
        {{-- <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h6 class="card-title mb-2">⚠️ Failure Frequency</h6>
                <div class="chart-container"><canvas id="failureChart"></canvas></div>
            </div>
        </div> --}}
        <div class="row g-4">
            <!-- Failure Frequency: wider space -->
            <div class="col-md-8">
                <div class="card p-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title mb-0">⚠️ Failure Frequency</h6>
                        <select id="failureFilter" class="form-select form-select-sm w-auto">
                            <option value="all">All Substations</option>
                            <option value="top5">Top 5 Most Offline</option>
                            <option value="withOffline">Only with Offline</option>
                        </select>
                    </div>
                    <div class="chart-container" id="failureChartContainer">
                        <canvas id="failureChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Status Pie Chart: smaller column -->
            <div class="col-md-4">
                <div class="card p-3 shadow-sm">
                    <h6 class="card-title mb-2">📶 Status Distribution</h6>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    async function downloadChartAsPDF(canvasId) {
        const { jsPDF } = window.jspdf;
        const canvas = document.getElementById(canvasId);
        const imgData = canvas.toDataURL("image/png");

        const pdf = new jsPDF();
        pdf.addImage(imgData, 'PNG', 10, 10, 180, 120);
        pdf.save(canvasId + '.pdf');
    }
        async function viewChartAsPDF(canvasId) {
        const { jsPDF } = window.jspdf;
        const canvas = document.getElementById(canvasId);
        const imgData = canvas.toDataURL("image/png");

        const pdf = new jsPDF();
        pdf.addImage(imgData, 'PNG', 10, 10, 180, 120);

        const blob = pdf.output("blob");
        const url = URL.createObjectURL(blob);
        window.open(url); // opens PDF in new tab
    }
</script>

<!-- Dynamic Filter Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filterForm');
        const filters = ['sensorTypeFilter', 'substationFilter', 'yearFilter', 'monthFilter'];

        filters.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', () => {
                    form.submit();
                });
            }
        });
    });
</script>

<script>
    const histories = @json($sensorHistories);
    const allSubstations = @json($substationNames);
    const normalize = val => val.trim();

    const sensorTypes = [...new Set(histories.map(h => normalize(h.type)))];

    const substationTypes = {};

    allSubstations.forEach(sub => {
        substationTypes[sub] = {};
        sensorTypes.forEach(type => substationTypes[sub][type] = 0);
    });

    histories.forEach(h => {
        const sub = h.substation_name;
        const type = normalize(h.type);
        if (substationTypes[sub]) {
            substationTypes[sub][type] += 1;
        }
    });

    const totalSensorsData = sensorTypes.map((type, idx) => ({
        label: type,
        data: allSubstations.map(sub => substationTypes[sub][type]),
        backgroundColor: idx === 0 ? '#0d6efd' : '#ff6384',
        stack: 'sensorType'
    }));

    const failureCounts = {};
    histories.forEach(h => {
        if (h.status === 'offline') {
            const sub = h.substation_name;
            failureCounts[sub] = (failureCounts[sub] || 0) + h.failures;
        }
    });

    const failureData = {
        labels: allSubstations,
        data: allSubstations.map(sub => failureCounts[sub] || 0)
    };

    const statusCounts = {};
    histories.forEach(h => {
        const key = h.status;
        statusCounts[key] = (statusCounts[key] || 0) + h.failures;
    });

    const statusData = Object.entries(statusCounts).map(([status, count]) => ({
        status,
        count
    }));

    const trendLabels = [...new Set(histories.map(h => `${h.month}/${h.year}`))].sort();
    const trendData = sensorTypes.map(type => {
        return {
            label: type,
            data: trendLabels.map(label => {
                const [month, year] = label.split('/');
                return histories
                    .filter(h =>
                        normalize(h.type) === type &&
                        h.month == month &&
                        h.year == year
                    )
                    .reduce((a, b) => a + b.failures, 0);
            }),
            fill: false,
            tension: 0.3
        };
    });

    // Total Sensors by Type per Substation (Stacked Bar)
    new Chart(document.getElementById('totalSensorsChart'), {
        type: 'bar',
        data: {
            labels: allSubstations,
            datasets: totalSensorsData
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Total Sensors by Type per Substation'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            return value === 0
                                ? `${context.dataset.label}: No data available`
                                : `${context.dataset.label}: ${value}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    title: { display: true, text: 'Substation' }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: { display: true, text: 'Sensor Count' }
                }
            }
        }
    });

    // Failure Frequency (Horizontal Bar)
    // new Chart(document.getElementById('failureChart'), {
    //     type: 'bar',
    //     data: {
    //         labels: failureData.labels,
    //         datasets: [{
    //             label: 'Offline Failures',
    //             backgroundColor: '#dc3545',
    //             data: failureData.data
    //         }]
    //     },
    //     options: {
    //         indexAxis: 'y',
    //         responsive: true,
    //         plugins: {
    //             title: { display: true, text: 'Failure Frequency per Substation' }
    //         },
    //         scales: {
    //             x: {
    //                 beginAtZero: true,
    //                 title: { display: true, text: 'Failure Count' }
    //             },
    //             y: {
    //                 title: { display: true, text: 'Substation' }
    //             }
    //         }
    //     }
    // });
    const failureRawData = failureData.labels.map((label, idx) => ({
        label,
        value: failureData.data[idx]
    }));

    const failureChartRef = new Chart(document.getElementById('failureChart'), {
        type: 'bar',
        data: {
            labels: failureRawData.map(d => d.label),
            datasets: [{
                label: 'Offline Failures',
                backgroundColor: '#dc3545',
                data: failureRawData.map(d => d.value)
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                title: { display: true, text: 'Failure Frequency per Substation' }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: { display: true, text: 'Failure Count' }
                },
                y: {
                    title: { display: true, text: 'Substation' }
                }
            }
        }
    });

    document.getElementById('failureFilter').addEventListener('change', function () {
        let filtered = [...failureRawData];
        const val = this.value;

        if (val === 'top5') {
            filtered = filtered.sort((a, b) => b.value - a.value).slice(0, 5);
        } else if (val === 'withOffline') {
            filtered = filtered.filter(d => d.value > 0);
        }

        failureChartRef.data.labels = filtered.map(d => d.label);
        failureChartRef.data.datasets[0].data = filtered.map(d => d.value);
        failureChartRef.update();
    });

    // Status Distribution (Pie)
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusData.map(s => s.status),
            datasets: [{
                label: 'Status Distribution',
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                data: statusData.map(s => s.count)
            }]
        },
        options: {
            plugins: {
                title: { display: true, text: 'Sensor Status Distribution' }
            }
        }
    });

    // Yearly Trend (Line)
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: trendData
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Sensor Failures Over Time' }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Month/Year' }
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Failure Count' }
                }
            }
        }
    });
</script>


@endsection
