@extends('adminlte::page')
@section('plugins.Chartjs', true)

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif
    <a href="{{ route('blockchains.create') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">NEW NETWORK</a>
    <a href="{{ route('blockchains.log') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">BLOCKCHAIN LOG</a>

    {{-- ── Master Wallet Evolution Chart ───────────────────────────────── --}}
    <div class="box box-solid">
        <div class="box-header with-border">
            <h4 class="box-title"><i class="fas fa-wallet mr-1"></i> Master Wallet Evolution</h4>
            <div class="box-tools pull-right">
                <span class="badge badge-success" id="masterBalanceBadge">Loading...</span>
            </div>
        </div>
        <div class="box-body">
            <div class="chart">
                <canvas id="masterBalanceChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Transfer Ledger ─────────────────────────────────────────────── --}}
    <div class="box box-solid">
        <div class="box-header with-border">
            <h4 class="box-title"><i class="fas fa-list-alt mr-1"></i> Transfer Ledger</h4>
        </div>
        <div class="box-body no-padding">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Date</th>
                            <th>Authority</th>
                            <th>Amount (dark)</th>
                            <th>Transaction Hash</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                            <tr>
                                <td>{{ $transfer->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $transfer->authority->name ?? 'Unknown Authority' }}</td>
                                <td class="text-success font-weight-bold">-{{ number_format($transfer->amount, 4) }}</td>
                                <td>
                                    @if($transfer->tx_hash && $transfer->tx_hash !== 'registration-funding')
                                        <code>{{ Str::limit($transfer->tx_hash, 20) }}...</code>
                                    @elseif($transfer->tx_hash === 'registration-funding')
                                        <span class="badge badge-info">Initial Funding</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No transfers recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box">
        <div class="box-header with-border">
            <h4 class="box-title">blockchains</h4>
        </div>
        <div class="box-body no-padding">
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>Institution ID</th>
                            <th>Type</th>
                            <th>Nº Nodes</th>
                            <th>Local</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Liveness</th> <!-- Nova coluna "Liveness" -->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blockchains as $blockchain)
                            <tr>
                                <td>{{ $blockchain->institution_id }}</td>
                                <td>{{ $blockchain->type }}</td>
                                <td>{{ $blockchain->number_nodes }}</td>
                                <td>{{ $blockchain->local }}</td>
                                <td>{{ $blockchain->status }}</td>
                                <td>{{ $blockchain->description }}</td>
                                @if($blockchain->url)
                                    <td class="liveness" data-url="{{ $blockchain->url }}">Checking...</td> <!-- Modificado para usar URL -->
                                @else
                                    <td> --- </td>
                                @endif
                                
                                <td>
                                    <button alt="Delete" title="Delete" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $blockchain->id }}, '{{ route('blockchains.destroy', ['blockchain' => $blockchain->id]) }}')"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No blockchains found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer clearfix">
              {{ ($blockchains != null)? $blockchains->links(): null }}
        </div>
    </div>
@stop

@section('js')
    <script>
        function checkLiveness() {
            $('.liveness').each(function() {
                var row = $(this);
                var originalUrl = row.data('url');

                // Validate the original URL
                if (!originalUrl.startsWith('http://') && !originalUrl.startsWith('https://')) {
                    console.error("Invalid URL:", originalUrl);
                    row.text('Invalid URL').removeClass('status-up').addClass('status-down');
                    return;
                }

                // Function to attempt AJAX request with a given URL
                function attemptRequest(url) {
                    $.ajax({
                        url: url,
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status && response.status === 'UP') {
                                row.text('Up').removeClass('status-down').addClass('status-up');
                            } else {
                                row.text('Down').removeClass('status-up').addClass('status-down');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching liveness status:", status, error);
                            // Switch protocol if the current attempt failed
                            if (url.startsWith('http://')) {
                                attemptRequest(url.replace('http://', 'https://'));
                            } else if (url.startsWith('https://')) {
                                attemptRequest(url.replace('https://', 'http://'));
                            } else {
                                row.text('Error').removeClass('status-up').addClass('status-down');
                            }
                        }
                    });
                }

                // Append ':8545/liveness' to the URL
                var urlWithPath = originalUrl; // + '/liveness';

                // Start the first attempt
                attemptRequest(urlWithPath);
            });
        }





        $(document).ready(function() {
            // Check liveness every minute (60000 milliseconds)
            setInterval(checkLiveness, 60000);
            // Initial check
            checkLiveness();

            // Fetch Master Wallet Data
            $.ajax({
                url: '{{ route("blockchains.masterWalletData") }}',
                type: 'GET',
                success: function(response) {
                    $('#masterBalanceBadge').text(response.current_balance);
                    renderMasterChart(response.labels, response.data);
                },
                error: function() {
                    $('#masterBalanceBadge').removeClass('badge-success').addClass('badge-danger').text('API Error');
                }
            });
        });

        var masterChart;
        function renderMasterChart(labels, data) {
            var ctx = document.getElementById('masterBalanceChart').getContext('2d');
            
            var gradient = ctx.createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(40, 167, 69, 0.5)'); // Greenish for Master
            gradient.addColorStop(1, 'rgba(40, 167, 69, 0)');

            masterChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Master Balance (dark)',
                        data: data,
                        borderColor: '#28a745',
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: '#28a745',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: false,
                                callback: function(value) {
                                    return value + ' dark';
                                }
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }]
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.yLabel + ' dark';
                            }
                        }
                    }
                }
            });
        }
    </script>
@stop
@section('css')
<style>
    .status-up {
        color: green;
        font-weight: bold;
    }

    .status-down {
        color: red;
        font-weight: bold;
    }
</style>
@stop
