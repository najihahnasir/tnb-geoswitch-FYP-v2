    {{-- <div class="col-md-8">
        <div class="chart-card">
            <div class="chart-title">Substation Sensor Overview</div>
            <canvas id="substationChart" width="100%" height="320"></canvas>
        </div>
    </div>

@push('after-script')
<script>
    const overviewData = @json($overviewData);

    const substationLabels = overviewData.map(item => item.label);
    const online = overviewData.map(item => item.online);
    const repairing = overviewData.map(item => item.repairing);
    const offline = overviewData.map(item => item.offline);

    const substationCtx = document.getElementById('substationChart').getContext('2d');
    new Chart(substationCtx, {
        type: 'bar',
        data: {
            labels: substationLabels,
            datasets: [
                {
                    label: 'Online',
                    data: online,
                    backgroundColor: '#4CAF50',
                    barThickness: 12,
                },
                {
                    label: 'Repairing',
                    data: repairing,
                    backgroundColor: '#FFC107',
                    barThickness: 12,

                },
                {
                    label: 'Offline',
                    data: offline,
                    backgroundColor: '#F44336',
                    barThickness: 12,

                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            scales: {
                x: {
                    stacked: true,
                    beginAtZero: true
                },
                y: {
                    stacked: true
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush --}}