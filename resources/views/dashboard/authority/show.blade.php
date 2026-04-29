@extends('adminlte::page')

@section('title', 'Authority — ' . $authority->name)
@section('plugins.Sweetalert2', true)
@section('plugins.Chartjs', true)

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('message') }}
        </div>
    @endif

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-shield-alt mr-2 text-primary"></i>
                {{ $authority->name }}
            </h4>
            <small class="text-muted">UUID: <code>{{ $authority->id }}</code></small>
        </div>
        <div>
            <a href="{{ route('authorities.edit', $authority->id) }}" class="btn btn-primary btn-sm mr-1">
                <i class="fas fa-pencil-alt mr-1"></i> Edit
            </a>
            <a href="{{ route('authorities.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        {{-- ── Info Card ─────────────────────────────────────────────────── --}}
        <div class="col-md-4">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fas fa-info-circle mr-1"></i> Information</h3>
                </div>
                <div class="box-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th class="text-muted" style="width:40%">Responsible</th>
                            <td>{{ $authority->responsible }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Email</th>
                            <td>{{ $authority->email }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Phone</th>
                            <td>{{ $authority->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Status</th>
                            <td>{!! $authority->getStatusBadge() !!}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Created</th>
                            <td>{{ $authority->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- ── Wallet / Blockchain Card ──────────────────────────────── --}}
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fas fa-wallet mr-1"></i> Wallet</h3>
                </div>
                <div class="box-body" id="walletInfoBox">
                    @if ($authority->wallet_address || $authority->isRegistered())
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <th class="text-muted">Address</th>
                                <td><small><code id="walletAddress">{{ $authority->wallet_address ?? 'Loading...' }}</code></small></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Balance</th>
                                <td id="walletBalance">
                                    {{ $authority->balance ?? '0' }} dark
                                    <i class="fas fa-spinner fa-spin ml-2 text-muted" id="walletSpinner" style="display:none;"></i>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted" style="vertical-align: middle;">NAANs</th>
                                <td id="walletNaans">
                                    <i class="fas fa-spinner fa-spin text-muted" id="naanSpinner" style="display:none;"></i>
                                </td>
                                <td style="width: 10%; text-align: right;">
                                    @if($authority->isRegistered())
                                        <button class="btn btn-xs btn-outline-primary btn-add-naan" title="Authorize new NAAN">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    @else
                        <p class="text-muted text-center mb-0">No wallet configured yet.</p>
                    @endif
                </div>
            </div>

            {{-- ── Blockchain Actions ────────────────────────────────────── --}}
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fas fa-link mr-1"></i> Blockchain</h3>
                </div>
                <div class="box-body">
                    @if (!$authority->isRegistered())
                        <a href="{{ route('authorities.register', $authority->id) }}"
                           class="btn btn-info btn-block"
                           onclick="return confirm('Register this Authority on the smart contract?')">
                            <i class="fas fa-sitemap mr-1"></i> Register Authority on Blockchain
                        </a>
                        <small class="text-muted d-block mt-2 text-center">
                            This will call the admin API and create the authority entry on the smart contract.
                        </small>
                    @else
                        <div class="text-center">
                            <span class="badge badge-success badge-lg p-2">
                                <i class="fas fa-check-circle mr-1"></i> Registered on Blockchain
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Balance Evolution Chart ───────────────────────────────── --}}
            @if ($authority->isRegistered())
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fas fa-chart-line mr-1"></i> Balance Evolution</h3>
                </div>
                <div class="box-body">
                    <div class="chart">
                        <canvas id="balanceChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- ── NAANs Panel ────────────────────────────────────────── --}}
        <div class="col-md-8">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fas fa-hashtag mr-1"></i>
                        Available NAANs to Assign
                    </h3>
                </div>
                <div class="box-body no-padding">
                    <form action="{{ route('authorities.syncNaans', $authority->id) }}" method="POST">
                        @csrf
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">Select</th>
                                        <th>NAAN</th>
                                        <th>Organization</th>
                                        <th>Contact</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($availableNaans as $naan)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="naans[]" value="{{ $naan->id }}">
                                            </td>
                                            <td><strong>{{ $naan->naan }}</strong></td>
                                            <td>{{ $naan->organization_name }}</td>
                                            <td>{{ $naan->contact_name }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">All NAANs have been assigned to this Authority or there are no NAANs created yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($availableNaans->count() > 0)
                            <div class="p-3 bg-light text-right">
                                <button type="submit" class="btn btn-dark">
                                    <i class="fas fa-link mr-1"></i> Assign NAAN
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.partials.confirm-delete')
@stop

@section('js')
<script>
    $(document).ready(function() {
        var isRegistered = {{ $authority->isRegistered() ? 'true' : 'false' }};
        var uuid = '{{ $authority->id }}';

        function fetchWalletData() {
            $('#walletSpinner, #naanSpinner').show();
            if ($('#walletAddress').text() === 'Loading...') {
                $('#walletAddress').html('<i class="fas fa-spinner fa-spin"></i>');
            }

            $.ajax({
                url: '/dashboard/authority/' + uuid + '/api-data',
                type: 'GET',
                success: function(response) {
                    $('#walletSpinner, #naanSpinner').hide();
                    if (!response.error) {
                        $('#walletAddress').text(response.wallet_address || 'Not found');
                        $('#walletBalance').html('<strong>' + response.balance + '</strong>');
                        
                        var naansHtml = response.naans.length > 0 
                            ? response.naans.map(n => '<span class="badge badge-dark mr-1">' + n + '</span>').join('')
                            : '<span class="text-muted">None</span>';
                        $('#walletNaans').html(naansHtml);

                        // Also refresh chart data
                        fetchChartData();
                    } else {
                        $('#walletBalance').html('<span class="text-danger">API Error</span>');
                        $('#walletNaans').html('<span class="text-danger">API Error</span>');
                    }
                },
                error: function() {
                    $('#walletSpinner, #naanSpinner').hide();
                    $('#walletBalance').html('<span class="text-danger">Error</span>');
                    $('#walletNaans').html('<span class="text-danger">Error</span>');
                }
            });
        }

        if (isRegistered) {
            fetchWalletData();
        }

        // Authorize NAAN flow
        $('.btn-add-naan').click(function() {
            Swal.fire({
                title: 'Authorize NAAN',
                html: '<div class="text-left"><label>Enter NAAN:</label>' +
                      '<input id="swal-naan" type="text" class="form-control" placeholder="e.g. 12345"></div>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Authorize',
                onOpen: function(popup) {
                    popup.querySelector('#swal-naan').focus();
                },
                preConfirm: function() {
                    let naan = Swal.getPopup().querySelector('#swal-naan').value.trim();
                    if (!naan) {
                        Swal.showValidationMessage('NAAN cannot be empty');
                    }
                    return naan;
                }
            }).then(function(result) {
                if (result.value) {
                    Swal.fire({
                        title: 'Authorizing...',
                        text: 'Please wait while the NAAN is registered on the blockchain.',
                        allowOutsideClick: false,
                        onBeforeOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '/dashboard/authority/' + uuid + '/authorize-naan',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            naan: result.value
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Success!', res.message, 'success');
                                fetchWalletData(); // Refresh Wallet UI
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Failed to authorize NAAN.', 'error');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });

        // Chart Logic
        var balanceChart;
        function fetchChartData() {
            $.ajax({
                url: '/dashboard/authority/' + uuid + '/balance-history',
                type: 'GET',
                success: function(response) {
                    renderChart(response.labels, response.data);
                }
            });
        }

        function renderChart(labels, data) {
            var ctx = document.getElementById('balanceChart').getContext('2d');
            
            if (balanceChart) {
                balanceChart.data.labels = labels;
                balanceChart.data.datasets[0].data = data;
                balanceChart.update();
                return;
            }

            var gradient = ctx.createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(0, 123, 255, 0.5)');
            gradient.addColorStop(1, 'rgba(0, 123, 255, 0)');

            balanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Balance (dark)',
                        data: data,
                        borderColor: '#007bff',
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: '#007bff',
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
                                beginAtZero: true,
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
    });
</script>
@stop
