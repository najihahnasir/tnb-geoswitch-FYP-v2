@push('after-styles')
<style>
    #substationChart, 
    #severityChart {
        display: block;
        width: 100% !important;
        height: 100% !important;
    }
    .col-md-4 {
        flex: 0 0 30%;
        max-width: 30%;
    }
    .col-md-8 {
        flex: 0 0 66%;
        max-width: 66%;
    }
    @media (max-width: 768px) {
        .col-md-4,
        .col-md-8 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
@endpush
<div class="row align-items-start mb-0" style="margin-top: 0px;">
    <!-- LEFT: Bar Chart -->
    <div class="col-md-8 ">
        <div class="chart-card mb-0">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="chart-title mb-0">Substation Sensor Overview</h5>
                <div class="ms-2"> <!-- Added margin start -->
                    <select id="filterMode" class="form-select form-select-sm d-inline-block" style="width: 180px;">
                        <option value="top5">Top 5 Most Offline</option>
                        <option value="offline">Only with Offline</option>
                        <option value="all">All Substations</option>
                    </select>
                </div>
            </div>
            <div style="max-height: 230px; overflow-y: auto;">
                <canvas id="substationChart" style="width: 100%; height: auto;"></canvas>
            </div>
        </div>
    </div>

    <!-- RIGHT: Pie Chart -->
    <div class="col-md-4">
        <div class="chart-box">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="chart-title mb-0">Sensor Severity</h5>
            </div>
            <div style="position: relative; height: 235px; width: 100%;">
                <canvas id="severityChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- @push('after-scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('severityChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Onine', 'Repairing', 'Offline'],
            datasets: [{
                data: [
                    {{ $severityStats['online'] }},
                    {{ $severityStats['repairing'] }},
                    {{ $severityStats['offline'] }}
                ],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',  // Changed from 'right' to 'left'
                    labels: {
                        boxWidth: 15,
                        padding: 15,
                        font: {
                            size: 12
                        },
                        // Add percentage display in legend
                        generateLabels: function(chart) {
                            const data = chart.data;
                            const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                            return data.labels.map((label, i) => {
                                const value = data.datasets[0].data[i];
                                const percentage = ((value / total) * 100).toFixed(1) + '%';
                                return {
                                    text: `${label}   ${percentage}`,  // "Low 64.6%"
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    hidden: false,
                                    index: i
                                };
                            });
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            // Remove cutout for pure pie chart
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });

    const overviewData = @json($overviewData);
    let substationChart;

    // Set default filter mode
    const DEFAULT_FILTER_MODE = 'top5'; 

    function getFilteredData(mode) {

        let data = [...overviewData];

        if (mode === 'offline') {
            data = data.filter(item => item.offline > 0);
        } else if (mode === 'top5') {
            data = data.filter(item => item.offline > 0)  // First filter offline only
                    .sort((a, b) => b.offline - a.offline)
                    .slice(0, 5);
        } else {
            data = data.sort((a, b) => b.offline - a.offline); 
        }

        return {
            labels: data.map(d => d.label),
            online: data.map(d => d.online),
            repairing: data.map(d => d.repairing),
            offline: data.map(d => d.offline)
        };
    }

    function renderSubstationChart(mode = DEFAULT_FILTER_MODE) {  // Set default here
        const { labels, online, repairing, offline } = getFilteredData(mode);  // ✅ defines labels
        console.log('LABELS:', labels);
        console.log('ONLINE:', online);
        console.log('REPAIRING:', repairing);
        console.log('OFFLINE:', offline);

        if (substationChart) substationChart.destroy();

        const substationCtx = document.getElementById('substationChart').getContext('2d');
        substationChart = new Chart(substationCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Online',
                        data: online,
                        backgroundColor: '#4CAF50',
                        barThickness: 12,
                        maxBarThickness: 12,
                    },
                    {
                        label: 'Repairing',
                        data: repairing,
                        backgroundColor: '#FFC107',
                        barThickness: 12,
                        maxBarThickness: 12,
                    },
                    {
                        label: 'Offline',
                        data: offline,
                        backgroundColor: '#F44336',
                        barThickness: 12,
                        maxBarThickness: 12,
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { display: false }
                    },
                    y: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { autoSkip: false }
                    }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    document.getElementById('filterMode').addEventListener('change', function () {
        renderSubstationChart(this.value);
    });

    renderSubstationChart();


</script>
@endpush --}}

@push('after-scripts')
{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('severityChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Online', 'Repairing', 'Offline'],
            datasets: [{
                data: [
                    {{ $severityStats['online'] }},
                    {{ $severityStats['repairing'] }},
                    {{ $severityStats['offline'] }}
                ],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 15,
                        padding: 15,
                        font: {
                            size: 12
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                            return data.labels.map((label, i) => {
                                const value = data.datasets[0].data[i];
                                const percentage = ((value / total) * 100).toFixed(1) + '%';
                                return {
                                    text: `${label}   ${percentage}`,
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    hidden: false,
                                    index: i
                                };
                            });
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });

    const overviewData = @json($overviewData);
    let substationChart;

    const DEFAULT_FILTER_MODE = 'all';

    function getFilteredData(mode) {
        let data = [...overviewData];

        if (mode === 'offline') {
            data = data.filter(item => item.offline > 0);
        } else if (mode === 'top5') {
            data = data.filter(item => item.offline > 0)
                       .sort((a, b) => b.offline - a.offline)
                       .slice(0, 5);
        } else {
            data = data.sort((a, b) => b.offline - a.offline);
        }

        // 🛡️ fallback if no data found
        if (data.length === 0) {
            return {
                labels: ['No Data'],
                online: [0],
                repairing: [0],
                offline: [0]
            };
        }

        return {
            labels: data.map(d => d.label),
            online: data.map(d => d.online),
            repairing: data.map(d => d.repairing),
            offline: data.map(d => d.offline)
        };
    }

    function renderSubstationChart(mode = DEFAULT_FILTER_MODE) {
        const { labels, online, repairing, offline } = getFilteredData(mode);

        if (substationChart) substationChart.destroy();

        const substationCtx = document.getElementById('substationChart').getContext('2d');
        substationChart = new Chart(substationCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Online',
                        data: online,
                        backgroundColor: '#4CAF50',
                        barThickness: 12,
                        maxBarThickness: 12,
                    },
                    {
                        label: 'Repairing',
                        data: repairing,
                        backgroundColor: '#FFC107',
                        barThickness: 12,
                        maxBarThickness: 12,
                    },
                    {
                        label: 'Offline',
                        data: offline,
                        backgroundColor: '#F44336',
                        barThickness: 12,
                        maxBarThickness: 12,
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { display: false }
                    },
                    y: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { autoSkip: false }
                    }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    document.getElementById('filterMode').addEventListener('change', function () {
        renderSubstationChart(this.value);
    });

    // Initial render
    renderSubstationChart();
</script>
@endpush

