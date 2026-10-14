@extends('adminlte::page')

@section('title', 'Authority — ' . $authority->name)
@section('plugins.Chartjs', true)

@section('css')
    @include('dashboard.authority.partials.design')
    <style>
        .authority-identifier { padding: 20px; margin-bottom: 24px; border: 1px solid #cbdcf1; border-radius: 8px; background: #f3f7fd; }
        .authority-identifier label { display: block; margin-bottom: 10px; color: #305d92; font-weight: 700; }
        .authority-identifier-controls { display: flex; align-items: center; gap: 12px; }
        .authority-identifier-controls input { flex: 1; min-width: 0; width: 100%; border: 0; background: transparent; color: #203c60; font-family: monospace; font-size: 20px; font-weight: 600; }
        .authority-identifier-controls .btn { flex-shrink: 0; }
        #authority-copy-status:not(:empty) { display: block; margin-top: 10px; }
        @media (max-width: 575px) {
            .authority-identifier-controls { flex-direction: column; align-items: stretch; }
            .authority-identifier-controls input { font-size: 15px; }
        }
    </style>
@stop

@section('content')
<div class="authority-page">
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('message') }}
        </div>
    @endif
    <nav class="authority-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('authorities.index') }}">Authorities</a> <span class="mx-2">/</span> {{ $authority->name }}
    </nav>
    <header class="authority-page-heading">
        <h1>Authority settings</h1>
        <p>{{ $authority->name }} <span class="ml-2">
                @switch($authority->status)
                    @case('active') <span class="badge badge-success ml-2">Active</span> @break
                    @case('pending') <span class="badge badge-warning ml-2">Pending</span> @break
                    @case('disabled') <span class="badge badge-secondary ml-2">Inactive</span> @break
                    @default <span class="badge badge-secondary ml-2">Unconfirmed</span>
                @endswitch
        </span></p>
    </header>
    <section id="authority-information" class="authority-panel">
        <div class="authority-panel-heading">
                <span class="authority-section-icon" aria-hidden="true"><i class="fas fa-building"></i></span>
                <div><h2>General information</h2><p>Contact details and identifiers for this authority.</p></div>
            </div>
        <div class="authority-panel-body">
            <div class="authority-identifier">
                <label for="authority-uuid">Authority UUID</label>
                <div class="authority-identifier-controls">
                    <input id="authority-uuid" type="text" value="{{ $authority->id }}" readonly spellcheck="false">
                    <button id="copy-authority-uuid" type="button" class="btn btn-outline-primary"><i class="far fa-copy mr-2" aria-hidden="true"></i>Copy UUID</button>
                </div>
                <span id="authority-copy-status" class="small" role="status" aria-live="polite"></span>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <dl><dt>Responsible</dt><dd>{{ $authority->responsible }}</dd>
                    <dt>Email</dt><dd>{{ $authority->email }}</dd></dl>
                </div>
                <div class="col-md-6">
                    <dl><dt>Phone</dt><dd>{{ $authority->phone ?? '—' }}</dd>
                    <dt>Created</dt><dd>{{ $authority->created_at->format('d/m/Y H:i') }}</dd></dl>
                </div>
            </div>
            <a href="{{ route('authorities.edit', $authority->id) }}" class="btn btn-outline-primary btn-sm">Edit details</a>
            @if (!$authority->wallet_address && $authority->status === 'pending')
                <div class="alert alert-warning">Registration is pending. Complete registration to enable operations.</div>
                <form action="{{ route('authorities.register', $authority->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-info">Complete registration</button>
                </form>
            @endif
        </div>
    </section>

    <section id="authority-permissions" class="authority-panel">
                <div class="authority-panel-heading">
                <span class="authority-section-icon" aria-hidden="true"><i class="fas fa-hashtag"></i></span>
                <div><h2>NAAN permissions</h2><p>Manage the registered namespaces this authority can operate.</p></div>
            </div>
                <div class="authority-panel-body">
                    <div class="authority-subheading">Authorized NAANs</div>
                    <div id="walletNaans">
                        @if($authority->isRegistered())
                            <span class="text-muted">Loading authorized NAANs…</span>
                        @else
                            <span class="text-muted">Authority is not active.</span>
                        @endif
                    </div>
                    <i class="fas fa-spinner fa-spin text-muted" id="naanSpinner" style="display:none;"></i>
                </div>
                <div class="authority-panel-footer">
                    <a href="{{ route('authorities.addNaans', $authority->id) }}" class="btn btn-outline-primary btn-sm">Add NAANs</a>
                </div>
            </section>
    <section id="authority-balance" class="authority-panel">
        <div class="authority-panel-heading">
                <span class="authority-section-icon" aria-hidden="true"><i class="fas fa-wallet"></i></span>
                <div><h2>Wallet & balance</h2><p>Funds available for blockchain operations.</p></div>
            </div>
        <div class="authority-panel-body">
            @if($authority->wallet_address || $authority->isRegistered())
                <div class="authority-subheading">Current balance</div>
                <p id="walletBalance">{{ $authority->balance ?? '0' }} dark</p>
                <i class="fas fa-spinner fa-spin text-muted" id="walletSpinner" style="display:none;"></i>
                <div class="authority-subheading">Wallet address</div><p class="small"> <code id="walletAddress" style="overflow-wrap:anywhere">{{ $authority->wallet_address ?? 'Loading...' }}</code></p>
                <p class="small text-muted">Funds are transferred from the master wallet to this authority.</p>
                @if($authority->isRegistered())
                    <details class="authority-editor" @if($errors->has('amount')) open @endif>
                        <summary class="btn btn-outline-primary btn-sm">Add funds</summary>
                        <form action="{{ route('authorities.fund', $authority->id) }}" method="POST" id="authority-fund-form">
                            @csrf
                            <p class="small">Recipient: <strong>{{ $authority->name }}</strong></p>
                            <div class="form-group">
                                <label for="fund-amount">Amount (dark)</label>
                                <input id="fund-amount" name="amount" type="number" step="0.0001" min="0.0001" required
                                    value="{{ old('amount') }}" placeholder="0.05"
                                    class="form-control @error('amount') is-invalid @enderror">
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <p class="small text-muted">Confirming transfers this amount from the master wallet.</p>
                            <button type="submit" class="btn btn-primary">Confirm transfer</button>
                        </form>
                    </details>
                @endif
                @if($authority->isRegistered())
                    <div class="border-top pt-3 mt-3">
                        <div class="authority-subheading">Balance history</div>
                        <div style="height:200px;">
                            <canvas id="balanceChart" style="max-height:200px;"></canvas>
                        </div>
                    </div>
                @endif
            @else
                <p class="text-muted">Complete registration to create the authority wallet.</p>
            @endif
        </div>
    </section>
    <section id="authority-minter">
        @include('dashboard.authority.partials.client-key')
    </section>
    @include('dashboard.authority.partials.lifecycle')
</div>
@stop

@section('js')
<script>
    document.getElementById('copy-authority-uuid').addEventListener('click', async function () {
        const field = document.getElementById('authority-uuid');
        const status = document.getElementById('authority-copy-status');
        const button = this;
        function fallbackCopy() {
            field.focus();
            field.select();
            field.setSelectionRange(0, field.value.length);
            return document.execCommand('copy');
        }
        button.disabled = true;
        try {
            let copied = false;
            if (window.isSecureContext && navigator.clipboard && navigator.clipboard.writeText) {
                try { await navigator.clipboard.writeText(field.value); copied = true; }
                catch (error) { copied = fallbackCopy(); }
            } else { copied = fallbackCopy(); }
            status.textContent = copied ? 'UUID copied to clipboard.' : 'Select the UUID and copy it manually.';
            if (copied) { button.focus(); }
        } catch (error) {
            field.focus(); field.select();
            status.textContent = 'Select the UUID and copy it manually.';
        } finally { button.disabled = false; }
    });
</script>
<script>document.querySelectorAll('.authority-lifecycle-form').forEach(form => form.addEventListener('submit', () => { const button = form.querySelector('button[type="submit"]'); button.disabled = true; button.textContent = 'Deactivating…'; }));</script>
<script>
    $(document).ready(function() {
        var isRegistered = {{ $authority->isRegistered() ? 'true' : 'false' }};
        var uuid = '{{ $authority->id }}';
        var authorityApiDataUrl = @json(route('authorities.apiData', ['authority' => '__AUTHORITY__']));
        var authorityBalanceHistoryUrl = @json(route('authorities.balanceHistoryData', ['authority' => '__AUTHORITY__']));
        var authorityUrl = function(template) { return template.replace('__AUTHORITY__', uuid); };

        function fetchWalletData() {
            $('#walletSpinner, #naanSpinner').show();
            if ($('#walletAddress').text() === 'Loading...') {
                $('#walletAddress').html('<i class="fas fa-spinner fa-spin"></i>');
            }

            $.ajax({
                url: authorityUrl(authorityApiDataUrl),
                type: 'GET',
                success: function(response) {
                    $('#walletSpinner, #naanSpinner').hide();
                    if (!response.error) {
                        $('#walletAddress').text(response.wallet_address || 'Not found');
                        $('#walletBalance').html('<strong>' + response.balance + '</strong>');

                        var naansHtml = response.naans.length > 0
                            ? response.naans.map(function(n) { return '<span class="badge badge-dark mr-1">' + n + '</span>'; }).join('')
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

        $('#authority-fund-form').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true).text('Transferring…');
        });

        // Chart Logic
        var balanceChart;
        function fetchChartData() {
            $.ajax({
                url: authorityUrl(authorityBalanceHistoryUrl),
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
