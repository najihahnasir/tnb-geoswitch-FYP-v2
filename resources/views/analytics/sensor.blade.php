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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">📊 Sensor Analytics Dashboard</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('analytics.export', ['export' => 'pdf']) }}" class="btn btn-danger btn-sm">📄 Export PDF</a>
            <a href="{{ route('analytics.export', ['export' => 'excel']) }}" class="btn btn-success btn-sm">📊 Export Excel</a>
            <a href="{{ route('analytics.export', ['export' => 'csv']) }}" class="btn btn-secondary btn-sm">📑 Export CSV</a>
            {{-- <button onclick="downloadChartAsPDF('totalSensorsChart')" class="btn btn-outline-primary btn-sm">🖼️ Download Chart</button>
            <button onclick="viewChartAsPDF('totalSensorsChart')" class="btn btn-outline-secondary btn-sm">👁️ Preview Chart</button> --}}
        </div>
    </div>

    <!-- Summary Cards -->
    {{-- <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
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
    </div> --}}
{{-- @php
    dump([
        'onlineSensors' => $onlineSensors ?? 'NOT SET',
        'repairingSensors' => $repairingSensors ?? 'NOT SET',
        'offlineSensors' => $offlineSensors ?? 'NOT SET',
    ]);
@endphp

@include('partial-dashboard.popup-sensor') --}}
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





    <!-- Filter Form -->
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
                <option value="">Select Year</option>
                @for ($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <label for="monthFilter" class="form-label">Month</label>
            <input type="number" name="month" id="monthFilter" class="form-control" placeholder="Month" min="1" max="12" value="{{ request('month') }}">
        </div>

        <div class="col-md-2 d-grid">
            <a href="{{ route(\Illuminate\Support\Facades\Route::currentRouteName()) }}" class="btn btn-outline-secondary">🔄 Reset</a>
        </div>
    </form>

    <!-- Charts Row -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm p-3">
                <h6 class="card-title">📅 Yearly Trend</h6>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm p-3">
                <h6 class="card-title">📊 Total Sensors by Type</h6>
                <div class="chart-container">
                    <canvas id="totalSensorsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-8">
            <div class="card shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">⚠️ Failure Frequency</h6>
                    <select id="failureFilter" class="form-select form-select-sm w-auto">
                        <option value="all">All Substations</option>
                        <option value="top5">Top 5 Most Offline</option>
                        <option value="withOffline">Only with Offline</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="failureChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h6 class="card-title">📶 Status Distribution</h6>
                <div class="chart-container" style="height: 325px">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-12">
                <div class="card shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">📅 Sensor Status Timeline</h6>
                        <!--  Status Filter Dropdown -->
                        <form method="GET" class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 me-2 small">Status:</label>
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="online" {{ request('status') == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="repairing" {{ request('status') == 'repairing' ? 'selected' : '' }}>Repairing</option>
                                <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                            </select>
                        </form>
                    </div>

                    <div class="chart-container" style="height: 500px;">
                        @if($statusTimelineData->isNotEmpty())
                            <canvas id="statusTimelineChart"></canvas>
                        @else
                            <p class="text-muted">No timeline data available for this filter.</p>
                        @endif
                    </div>

                    <!-- Custom Color Legend -->
                    <div class="mt-3 small text-muted d-flex gap-4 align-items-center">
                        <span><i style="background:#198754;width:12px;height:12px;display:inline-block;border-radius:2px;"></i> Online</span>
                        <span><i style="background:#ffc107;width:12px;height:12px;display:inline-block;border-radius:2px;"></i> Repairing</span>
                        <span><i style="background:#dc3545;width:12px;height:12px;display:inline-block;border-radius:2px;"></i> Offline</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Chart.js -->
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-doughnutlabel@1.0.3/dist/chartjs-plugin-doughnutlabel.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3"></script>

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
    const statusData = @json($statusData);
    const totalSensors = @json($totalSensors);

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

    // const statusCounts = {};
    // histories.forEach(h => {
    //     const key = h.status;
    //     statusCounts[key] = (statusCounts[key] || 0) + h.failures;
    // });

    // const statusData = Object.entries(statusCounts).map(([status, count]) => ({
    //     status,
    //     count
    // }));

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
    const centerTextPlugin = {
        id: 'centerTextPlugin',
        afterDraw(chart) {
            const { ctx, chartArea: { left, right, top, bottom } } = chart;
            const centerX = (left + right) / 2;
            const centerY = (top + bottom) / 2;

            ctx.save();
            ctx.font = 'bold 20px sans-serif';
            ctx.fillStyle = '#333';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(totalSensors.toString(), centerX, centerY - 10);

            ctx.font = '12px sans-serif';
            ctx.fillStyle = '#888';
            ctx.fillText('Total Sensors', centerX, centerY + 10);
            ctx.restore();
        }
    };

    // Setup the chart
    const ctx = document.getElementById('statusChart').getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(s => s.status),
            datasets: [{
                backgroundColor: ['#198754', '#ffc107','#dc3545'],
                data: statusData.map(s => s.count)
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Sensor Status Distribution'
                },
                legend: {
                    position: 'bottom'
                }
            }
        },
        plugins: [centerTextPlugin]
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

    // const timelineDataRaw = @json($statusTimelineData);

    // const barData = {
    //     labels: timelineDataRaw.map(d => d.tag + ' (' + d.substation_name + ')'),
    //     datasets: [{
    //         label: 'Status Timeline',
    //         data: timelineDataRaw.map(d => ({
    //             x: [new Date(d.created_at), new Date(d.resolved_at)],
    //             y: d.tag + ' (' + d.substation_name + ')',
    //             status: d.new_status
    //         })),
    //         backgroundColor: timelineDataRaw.map(d =>
    //             d.new_status === 'offline' ? '#dc3545' :
    //             d.new_status === 'repairing' ? '#ffc107' :
    //             '#198754'
    //         ),
    //         borderWidth: 1,
    //         borderColor: '#fff'
    //     }]
    // };

    // new Chart(document.getElementById('statusTimelineChart'), {
    //     type: 'bar',
    //     data: barData,
    //     options: {
    //         indexAxis: 'y',
    //         responsive: true,
    //         plugins: {
    //             tooltip: {
    //                 callbacks: {
    //                     label: ctx => {
    //                         const val = ctx.raw;
    //                         return `From ${new Date(val.x[0]).toLocaleString()} to ${new Date(val.x[1]).toLocaleString()}`;
    //                     }
    //                 }
    //             },
    //             legend: { display: false },
    //             title: { display: false }
    //         },
    //         scales: {
    //             x: {
    //                 type: 'time',
    //                 time: {
    //                     unit: 'day',
    //                     tooltipFormat: 'dd MMM yyyy HH:mm'
    //                 },
    //                 title: { display: true, text: 'Time Range' }
    //             },
    //             y: { title: { display: true, text: 'Sensor (Substation)' } }
    //         }
    //     }
    // });

    const timelineDataRaw = @json($statusTimelineData);
    console.log(timelineDataRaw);

    // Convert date range to duration (in ms) and midpoint
    // const dataset = timelineDataRaw.map((d, idx) => {
    //     const start = new Date(d.created_at);
    //     const end = new Date(d.resolved_at);
    //     const mid = new Date((start.getTime() + end.getTime()) / 2);
    //     const duration = (end.getTime() - start.getTime()) / (1000 * 60 * 60); // hours

    //     return {
    //         x: mid,
    //         y: d.tag + ' (' + d.substation_name + ')',
    //         duration,
    //         status: d.new_status
    //     };
    // });

    //new 

    // const dataset = timelineDataRaw.map(d => {
    //     const start = new Date(d.created_at).getTime();
    //     const end = new Date(d.resolved_at || Date.now()).getTime(); // handle ongoing
    //     const duration = (end - start) / (1000 * 60 * 60); // hours

    //     return {
    //         x: [start, end], // ⬅️ use range
    //         y: d.tag + ' (' + d.substation_name + ')',
    //         status: d.new_status,
    //         duration,
    //         start,
    //         end
    //     };
    // });

    // new Chart(document.getElementById('statusTimelineChart'), {
    //     type: 'bar',
    //     data: {
    //         labels: [...new Set(dataset.map(d => d.y))],
    //         datasets: [{
    //             label: 'Status Duration',
    //             data: dataset,
    //             backgroundColor: dataset.map(d =>
    //                 d.status === 'offline' ? '#dc3545' :
    //                 d.status === 'repairing' ? '#ffc107' :
    //                 '#198754'
    //             ),
    //             parsing: {
    //                 xAxisKey: 'x',
    //                 yAxisKey: 'y'
    //             },
    //             barThickness: 12
    //         }]
    //     },
    //     options: {
    //         indexAxis: 'y',
    //         responsive: true,
    //         plugins: {
    //             tooltip: {
    //                 callbacks: {
    //                     label: ctx => {
    //                         const data = ctx.raw;
    //                         return `Status: ${data.status}\nFrom: ${new Date(data.x.getTime() - data.duration * 60 * 60 * 1000).toLocaleString()}\nTo: ${new Date(data.x).toLocaleString()}`;
    //                     }
    //                 }
    //             },
    //             legend: { display: false },
    //             title: { display: true, text: 'Sensor Status Timeline' }
    //         },
    //         scales: {
    //             x: {
    //                 type: 'time',
    //                 time: {
    //                     unit: 'day',
    //                     tooltipFormat: 'dd MMM yyyy HH:mm'
    //                 },
    //                 title: { display: true, text: 'Date Range' }
    //             },
    //             y: {
    //                 title: { display: true, text: 'Sensor (Substation)' },
    //                 ticks: { autoSkip: false }
    //             }
    //         }
    //     }
    // });

    //new
    // new Chart(document.getElementById('statusTimelineChart'), {
    //     type: 'bar',
    //     data: {
    //         labels: [...new Set(dataset.map(d => d.y))],
    //         datasets: [{
    //             label: 'Status Period',
    //             data: dataset,
    //             backgroundColor: dataset.map(d =>
    //                 d.status === 'offline' ? '#dc3545' :
    //                 d.status === 'repairing' ? '#ffc107' :
    //                 '#198754'
    //             ),
    //             parsing: {
    //                 xAxisKey: 'x',
    //                 yAxisKey: 'y'
    //             },
    //             barPercentage: 1,
    //             categoryPercentage: 1
    //         }]
    //     },
    //     options: {
    //         indexAxis: 'y',
    //         responsive: true,
    //         plugins: {
    //             tooltip: {
    //                 callbacks: {
    //                     label: ctx => {
    //                         const d = ctx.raw;
    //                         const from = new Date(d.start).toLocaleString();
    //                         const to = new Date(d.end).toLocaleString();
    //                         return `Status: ${d.status}\nFrom: ${from}\nTo: ${to}\nDuration: ${d.duration.toFixed(1)} hrs`;
    //                     }
    //                 }
    //             },
    //             legend: { display: false },
    //             title: { display: true, text: 'Sensor Status Timeline' }
    //         },
    //         scales: {
    //             x: {
    //                 type: 'linear',
    //                 min: Math.min(...dataset.map(d => d.start)),
    //                 max: Math.max(...dataset.map(d => d.end)),
    //                 ticks: {
    //                     callback: val => new Date(val).toLocaleDateString()
    //                 },
    //                 title: { display: true, text: 'Date Range' }
    //             },
    //             y: {
    //                 title: { display: true, text: 'Sensor (Substation)' },
    //                 ticks: { autoSkip: false }
    //             }
    //         }
    //     }
    // });

    document.getElementById('substationFilter').addEventListener('change', function () {
        const selected = this.value;

        const filtered = selected
            ? dataset.filter(d => d.substation === selected)
            : dataset;

        chart.data.labels = [...new Set(filtered.map(d => d.y))];
        chart.data.datasets[0].data = filtered;
        chart.update();
    });

    const raw = @json($statusTimelineData); // Blade or your JS variable

    // Convert and enhance data
    const now = new Date();
    const last30Days = new Date();
    last30Days.setDate(now.getDate() - 30);

    const dataset = raw
        .map(d => {
            const start = new Date(d.created_at).getTime();
            const end = d.resolved_at ? new Date(d.resolved_at).getTime() : now.getTime();
            return {
                x: [start, end],
                y: `${d.tag} (${d.substation_name})`,
                status: d.new_status,
                duration: (end - start) / 3600000, // in hours
                start,
                end,
                substation: d.substation_name
            };
        })
        .filter(d => d.end >= last30Days.getTime()) // ✅ Limit to last 30 days
        .sort((a, b) => {
            // ✅ Sort by substation first, then tag
            const subCompare = a.substation.localeCompare(b.substation);
            if (subCompare !== 0) return subCompare;
            return a.y.localeCompare(b.y);
        });

    const chart = new Chart(document.getElementById('statusTimelineChart'), {
        type: 'bar',
        data: {
            labels: [...new Set(dataset.map(d => d.y))], // Unique y labels
            datasets: [{
                label: 'Sensor Status Timeline',
                data: dataset,
                backgroundColor: dataset.map(d =>
                    d.status === 'offline' ? '#dc3545' :
                    d.status === 'repairing' ? '#ffc107' :
                    '#198754'
                ),
                parsing: {
                    xAxisKey: 'x',
                    yAxisKey: 'y'
                },
                borderRadius: 2,
                barThickness: 16
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const d = ctx.raw;
                            const from = new Date(d.start).toLocaleString();
                            const to = new Date(d.end).toLocaleString();
                            return [
                                ` Status: ${d.status}`,
                                `📅 From: ${from}`,
                                `📅 To: ${to}`,
                                `⏱️ Duration: ${d.duration.toFixed(1)} hrs`
                            ];
                        }
                    },
                    bodyFont: { size: 12 },
                    titleFont: { weight: 'bold' }
                },
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Sensor Status Timeline',
                    font: { size: 16 }
                }
            },
            scales: {
                x: {
                    type: 'linear',
                    min: Math.min(...dataset.map(d => d.start)),
                    max: Math.max(...dataset.map(d => d.end)),
                    ticks: {
                        callback: val => new Date(val).toLocaleDateString()
                    },
                    title: {
                        display: true,
                        text: 'Date Range'
                    }
                },
                y: {
                    ticks: {
                        autoSkip: false,
                        padding: 6
                    },
                    title: {
                        display: true,
                        text: 'Sensor (Substation)'
                    }
                }
            }
        }
    });



</script>


@endsection
