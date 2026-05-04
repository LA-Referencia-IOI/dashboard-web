@extends('adminlte::page')

@section('title', 'Server Resources')

@section('plugins.Chartjs', true)

@section('content_header')
    <h1><i class="fas fa-server mr-2"></i> Server Resources</h1>
@stop

@section('content')
    <div class="row">
        {{-- CPU Box --}}
        <div class="col-md-4">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-microchip"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">CPU Load (1 min)</span>
                    <span class="info-box-number" id="cpu-load">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                    <div class="progress">
                        <div class="progress-bar" id="cpu-progress" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RAM Box --}}
        <div class="col-md-4">
            <div class="info-box bg-gradient-success">
                <span class="info-box-icon"><i class="fas fa-memory"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">RAM Usage</span>
                    <span class="info-box-number" id="ram-usage">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                    <div class="progress">
                        <div class="progress-bar" id="ram-progress" style="width: 0%"></div>
                    </div>
                    <span class="progress-description" id="ram-desc">
                        Calculating...
                    </span>
                </div>
            </div>
        </div>

        {{-- Disk Box --}}
        <div class="col-md-4">
            <div class="info-box bg-gradient-warning">
                <span class="info-box-icon"><i class="fas fa-hdd"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Disk Free Space</span>
                    <span class="info-box-number" id="disk-usage">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                    <div class="progress">
                        <div class="progress-bar" id="disk-progress" style="width: 0%"></div>
                    </div>
                    <span class="progress-description" id="disk-desc">
                        Calculating...
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Disk History Chart --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-chart-area mr-1"></i>
                        Disk Space Consumption History
                    </h3>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <canvas id="diskChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        var diskChart;

        function fetchServerData() {
            $.ajax({
                url: '{{ route("server-resources.apiData") }}',
                type: 'GET',
                success: function(response) {
                    // Update CPU
                    $('#cpu-load').text(response.cpu.load);
                    let loadVal = parseFloat(response.cpu.load);
                    if(!isNaN(loadVal)) {
                        let width = (loadVal * 100).toString() + '%';
                        $('#cpu-progress').css('width', width);
                    }

                    // Update RAM
                    $('#ram-usage').text(response.ram.used + ' / ' + response.ram.total);
                    $('#ram-progress').css('width', response.ram.percent + '%');
                    $('#ram-desc').text(response.ram.percent + '% Used');

                    // Update Disk
                    $('#disk-usage').text(response.disk.available_gb + ' GB free / ' + response.disk.total_gb + ' GB');
                    $('#disk-progress').css('width', response.disk.percent + '%');
                    $('#disk-desc').text(response.disk.percent + '% Used (' + response.disk.used_gb + ' GB)');

                    // Update Chart
                    renderChart(response.chart.labels, response.chart.data_used, response.chart.data_available);
                },
                error: function() {
                    console.error("Failed to fetch server resources.");
                }
            });
        }

        function renderChart(labels, dataUsed, dataAvailable) {
            var ctx = document.getElementById('diskChart').getContext('2d');
            
            if (diskChart) {
                diskChart.data.labels = labels;
                diskChart.data.datasets[0].data = dataUsed;
                diskChart.data.datasets[1].data = dataAvailable;
                diskChart.update();
                return;
            }

            diskChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Used (GB)',
                            data: dataUsed,
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.2)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Available (GB)',
                            data: dataAvailable,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.2)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value) {
                                    return value + ' GB';
                                }
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }]
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return data.datasets[tooltipItem.datasetIndex].label + ': ' + tooltipItem.yLabel + ' GB';
                            }
                        }
                    }
                }
            });
        }

        // Fetch data immediately, then every 5 seconds
        fetchServerData();
        setInterval(fetchServerData, 5000);
    });
</script>
@stop
