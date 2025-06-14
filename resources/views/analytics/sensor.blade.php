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
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">📅 Yearly Trend</h6>
                    <select id="trendRange" name="range" class="form-select form-select-sm w-auto">
                        <option value="">All Time</option>
                        <option value="rolling">Rolling 12 Months</option>
                        <option value="custom">Custom Year/Month</option>
                    </select>
                </div>
                <div class="chart-container" style="height: 345px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm p-3">
                <h6 class="card-title mb-1">📊 Sensor Status Distribution by Type per Substation</h6>
                <p class="text-muted" style="font-size: 0.85rem;">
                    Note: Each substation has a fixed total of 15 sensors – 3 Partial Discharge (PD) and 12 Thermal.
                </p>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="statusByTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <!-- 🔻 Failure Frequency -->
        <div class="col-md-8">
            <div class="card shadow-sm p-2">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0">⚠️ Failure Frequency</h6>
                    <select id="substationScope" class="form-select form-select-sm w-auto">
                        <option value="all">All</option>
                        <option value="withFailures">Only with Failures</option>
                        <option value="top5count">Top 5 by Count</option>
                        <option value="top5rate">Top 5 by Rate (%)</option>
                    </select>
                </div>
                <div class="chart-container mb-3" style="height: 300px;">
                    <canvas id="failureChart"></canvas>
                </div>
            </div>
        </div>
        <!-- 🔻 Status Distribution -->
        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h6 class="card-title">📶 Status Distribution</h6>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="card shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">📅 Sensor Status Timeline</h6>
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

                <div class="mt-3 small text-muted d-flex gap-4 align-items-center">
                    <span><i style="background:#198754;width:12px;height:12px;display:inline-block;border-radius:2px;"></i> Online</span>
                    <span><i style="background:#ffc107;width:12px;height:12px;display:inline-block;border-radius:2px;"></i> Repairing</span>
                    <span><i style="background:#dc3545;width:12px;height:12px;display:inline-block;border-radius:2px;"></i> Offline</span>
                </div>
            </div>
        </div>
       
    </div> --}}
</div>


<!-- Chart.js -->
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-doughnutlabel@1.0.3/dist/chartjs-plugin-doughnutlabel.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-chart-error-bars"></script>

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
    const totalSensorsBySub = @json($totalSensorsBySub);
</script>

<!-- Substation Analytics Blade Chart Script -->
{{-- @php
    $sensorSummary = $substations->map(function ($sub) use ($sensorTypes) {
        $totals = [];
        foreach ($sensorTypes as $type) {
            $count = $sub->switchgears->flatMap->sensors
                ->filter(fn($s) => $s->formatted_type === $type)
                ->count();

            $totals[$type] = $count;
        }
        return [
            'substation' => $sub->substation_name,
            'data' => $totals
        ];
    });
@endphp --}}



<script>
    const statusData = @json($statusData);
    const totalSensors = @json($totalSensors);
    const histories = @json($sensorHistories);
    const timelineHistories = @json($statusTimelineData); // ✅ Use your full data
    const allSubstations = @json($substationNames);
    const normalize = val => val.trim();
    const latestStatusSummary = @json($latestStatusSummary);
    
    // Total Sensors by Type
    const sensorSummary = @json($sensorSummary);
    const sensorTypes = @json($sensorTypes);

    const stackedSensorData = {
        labels: sensorSummary.map(d => d.substation),
        datasets: sensorTypes.map((type, idx) => ({
            label: type,
            data: sensorSummary.map(d => d.data[type]),
            backgroundColor: idx === 0 ? '#0d6efd' : '#ff6384',
            stack: 'sensors'
        }))
    };


    // Failure Count by Substation
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

    // 🧠 Build failureMeta for latest offline timestamps
    const failureMeta = {};
    histories.forEach(h => {
        if (h.status === 'offline') {
            const sub = h.substation_name;
            const current = failureMeta[sub];
            const reportedAt = `${h.year}-${String(h.month).padStart(2, '0')}-${h.day?.toString().padStart(2, '0') || '01'}`;
            if (!current || current < reportedAt) {
                failureMeta[sub] = reportedAt;
            }
        }
    });

    // 🔍 Log check
    console.log("📊 Sensor Histories:", histories);
    console.log("🧠 Offline counts by substation:", failureCounts);

    // 📈 Enhanced Chart
    const failureRawData = failureData.labels.map((label, i) => ({
        label,
        value: failureData.data[i],
        total: totalSensorsBySub[label] || 0,
        rate: totalSensorsBySub[label] ? (failureData.data[i] / totalSensorsBySub[label]) : 0
    }));
    const totalFailures = failureRawData.reduce((sum, d) => sum + d.value, 0);


    const failureChartRef = new Chart(document.getElementById('failureChart'), {
        type: 'bar',
        data: {
            labels: failureRawData.map(d => d.label),
            datasets: [{
                label: 'Offline Failures',
                data: failureRawData.map(d => d.value),
                backgroundColor: failureRawData.map(d => {
                    const rate = totalSensorsBySub[d.label] ? d.value / totalSensorsBySub[d.label] : 0;
                    return rate >= 0.3 ? '#8B0000' :       // dark red
                        rate >= 0.2 ? '#dc3545' :       // red
                        rate >= 0.1 ? '#ff6b6b' :       // light red
                                        '#ffc9c9';         // very light red
                }),
                borderRadius: 4,
                barThickness: 18
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Failure Frequency per Substation',
                    font: { size: 16, weight: 'bold' }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const count = ctx.raw;
                            const label = ctx.label;
                            const total = totalSensorsBySub[label] || 0;
                            const percent = total ? ((count / total) * 100).toFixed(1) : 0;
                            const last = failureMeta[label] || 'N/A';

                            // Get sensorType filter from dropdown (assuming it exists)
                            const sensorType = document.getElementById('sensorTypeFilter')?.value || 'All';

                            return [
                                `📍 ${label}`,
                                `🔧 Failures: ${count}`,
                                `📊 Failure Rate: ${percent}%`,
                                `🎯 Total Sensors: ${total}`,
                                `🔌 Sensor Type: ${sensorType}`,
                                `🕓 Last Failure: ${last}`
                            ];
                        }
                    }
                },
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 12 }
                    },
                    title: {
                        display: true,
                        text: 'Failure Count',
                        font: { size: 13, weight: 'bold' }
                    }
                },
                y: {
                    ticks: {
                        font: { size: 12 },
                        autoSkip: false
                    },
                    title: {
                        display: true,
                        text: 'Substation',
                        font: { size: 13, weight: 'bold' }
                    }
                }
            }
        }
    });

    document.getElementById('substationScope').addEventListener('change', function () {
        let filtered = [...failureRawData];
        const val = this.value;

        if (val === 'withFailures') {
            filtered = filtered.filter(d => d.value > 0);
        } else if (val === 'top5count') {
            filtered = filtered.sort((a, b) => b.value - a.value).slice(0, 5);
        } else if (val === 'top5rate') {
            filtered = filtered
                .filter(d => d.total > 0)
                .sort((a, b) => b.rate - a.rate)
                .slice(0, 5);
        }

        failureChartRef.data.labels = filtered.map(d => d.label);
        failureChartRef.data.datasets[0].data = filtered.map(d => d.value);
        failureChartRef.data.datasets[0].backgroundColor = filtered.map(d => {
            const rate = d.total ? d.value / d.total : 0;
            return rate >= 0.3 ? '#8B0000' :
                rate >= 0.2 ? '#dc3545' :
                rate >= 0.1 ? '#ff6b6b' : '#ffc9c9';
        });
        failureChartRef.update();
    });
        document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('substationScope').dispatchEvent(new Event('change'));
    });

    // Yearly Trend Labels
    const trendLabels = [...new Set(histories.map(h => `${h.year}-${String(h.month).padStart(2, '0')}`))]
        .sort()
        .map(label => {
            const [year, month] = label.split('-');
            return `${parseInt(month)}/${year}`;
        });
    const trendData = sensorTypes.map(type => {
        return {
            label: type,
            data: trendLabels.map(label => {
                const [month, year] = label.split('/');
                return histories
                    .filter(h => normalize(h.type) === type && h.month == month && h.year == year)
                    .reduce((a, b) => a + b.failures, 0);
            }),
            fill: false,
            tension: 0.3
        };
    });

    // Sensor Status Distribution Chart
    const statusColorMap = {
        'Online': '#198754',     // Green
        'Offline': '#dc3545',    // Red
        'Repairing': '#ffc107'   // Yellow
    };
    const ctx = document.getElementById('statusChart').getContext('2d');

    const statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(s => s.status),
            datasets: [{
                backgroundColor: statusData.map(s => statusColorMap[s.status]),
                data: statusData.map(s => s.count)
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 500, // 🔥 force animation so onComplete is called
                onComplete: function () {
                    const chart = this;
                    const { ctx, chartArea: { left, right, top, bottom } } = chart;
                    const x = (left + right) / 2;
                    const y = (top + bottom) / 2;

                    const total = chart.data.datasets[0].data.reduce((sum, val) => sum + val, 0);
                    ctx.save();
                    ctx.font = 'bold 18px Arial';
                    ctx.fillStyle = '#333';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(`Total: ${total}`, x, y);
                    ctx.restore();
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Sensor Status Distribution'
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    });


    // Total Sensors by Type per Substation
    // Sensor Status by Type per Substation (DYNAMIC using histories)
    const typeStatusDataset = [];
    const statuses = ['Online', 'Repairing', 'Offline']; // ✅ Exact match
    const colors = {
        Online: {
            Thermal: '#0d6efd',            // Blue
            'Partial Discharge': '#2ecc71'// Green
        },
        Repairing: {
            Thermal: '#fd7e14',            // Orange
            'Partial Discharge': '#f39c12'// Amber
        },
        Offline: {
            Thermal: '#6f42c1',            // Purple
            'Partial Discharge': '#e74c3c'// Red
        }
    };



    const statusChartSubstations = Object.keys(latestStatusSummary);
    console.log("🧠 latestStatusSummary:", latestStatusSummary);


    sensorTypes.forEach(type => {
        statuses.forEach(status => {
            typeStatusDataset.push({
                label: `${status.charAt(0).toUpperCase() + status.slice(1)} (${type})`,
                backgroundColor: colors[status][type],
                data: statusChartSubstations.map(sub => {
                    const subData = latestStatusSummary[sub] || {};
                    const typeData = subData[type] || {};
                    return typeData[status] || 0;
                }),
                stack: type
            });
        });
    });

    new Chart(document.getElementById('statusByTypeChart'), {
        type: 'bar',
        data: {
                labels: statusChartSubstations,            
                datasets: typeStatusDataset
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Sensor Status Distribution by Type per Substation'
                },
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.dataset.label}: ${ctx.raw}`
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'Substation'
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    max: 12,  // For Thermal only
                    suggestedMax: 12,  // Let it auto-scale if needed // If 12 Thermal sensors max; change to 15 if needed
                    title: {
                        display: true,
                        text: 'Sensor Count (Per Type)'
                    }
                }
            }
        }
    });

    // Yearly Trend Chart
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: trendData
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        title: tooltipItems => `📅 ${tooltipItems[0].label}`,
                        label: item => `🔧 ${item.dataset.label}: ${item.formattedValue} failures`
                    },
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 6
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
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

    //timeline chart
    const rawTimelineData = @json($statusTimelineData);

    const dataset = rawTimelineData.map(d => {
        const start = new Date(d.created_at);
        const end = new Date(d.resolved_at || new Date());

        return {
            x: start,
            x2: end,
            y: `${d.tag} (${d.substation_name})`, // ← ✅ This ensures grouped rows
            status: d.new_status,
            backgroundColor:
                d.new_status === 'offline' ? '#dc3545' :
                d.new_status === 'repairing' ? '#ffc107' :
                '#198754'
        };
    });

    new Chart(document.getElementById('statusTimelineChart'), {
        type: 'barWithErrorBars',
        data: {
            datasets: [{
                label: 'Sensor Status Timeline',
                data: dataset,
                parsing: {
                    xAxisKey: 'x',
                    x2AxisKey: 'x2',
                    yAxisKey: 'y'
                },
                backgroundColor: ctx => ctx.raw.backgroundColor,
                borderSkipped: false,
                borderRadius: 2,
                barPercentage: 1.0,
                categoryPercentage: 1.0
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const r = ctx.raw;
                            return `Status: ${r.status}\nFrom: ${new Date(r.x).toLocaleString()}\nTo: ${new Date(r.x2).toLocaleString()}`;
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Sensor Status Timeline'
                },
                legend: { display: false }
            },
            scales: {
                x: {
                    type: 'time',
                    min: new Date("2023-01-01"),
                    title: {
                        display: true,
                        text: 'Date Range'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Sensor (Substation)'
                    },
                    ticks: {
                        autoSkip: false
                    }
                }
            }
        }
    });



    
</script>



@endsection
