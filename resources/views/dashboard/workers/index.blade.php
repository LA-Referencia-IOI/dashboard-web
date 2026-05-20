@extends('adminlte::page')

@section('title', 'Workers')

@section('content_header')
    <h1><i class="fas fa-cogs mr-2"></i> Workers</h1>
@stop

@section('content')

    {{-- Status banner --}}
    <div class="row">
        <div class="col-md-4">
            <div class="info-box" id="status-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-cog fa-spin"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Status</span>
                    <span class="info-box-number" id="worker-status"><i class="fas fa-spinner fa-spin"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Last Heartbeat</span>
                    <span class="info-box-number" id="heartbeat-age"><i class="fas fa-spinner fa-spin"></i></span>
                    <span class="progress-description" id="heartbeat-at"></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-gradient-warning">
                <span class="info-box-icon"><i class="fas fa-layer-group"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending in Queue</span>
                    <span class="info-box-number" id="queue-pending"><i class="fas fa-spinner fa-spin"></i></span>
                    <span class="progress-description" id="queue-ready"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Identity & Config --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-id-badge mr-1"></i> Worker Identity</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-outline-light" id="btn-refresh">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <tbody id="identity-table">
                            <tr><td colspan="2" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-sliders-h mr-1"></i> Configuration</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <tbody id="config-table">
                            <tr><td colspan="2" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Processing Stats</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <tbody id="stats-table">
                            <tr><td colspan="2" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-sitemap mr-1"></i> ARKs by State</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <tbody id="states-table">
                            <tr><td colspan="2" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Errors & Queue detail --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-1"></i> Errors</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <tbody id="errors-table">
                            <tr><td colspan="2" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list-ol mr-1"></i> Queue Detail</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <tbody id="queue-table">
                            <tr><td colspan="2" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
<script>
    const apiUrl = '{{ route("workers.apiData") }}';

    function fmt(val) {
        if (val === null || val === undefined) return '<span class="text-muted">—</span>';
        if (typeof val === 'number') return new Intl.NumberFormat().format(val);
        return val;
    }

    function row(label, value) {
        return `<tr><td class="font-weight-bold" style="width:55%">${label}</td><td>${value}</td></tr>`;
    }

    function badge(val, trueColor, falseColor) {
        const color = val ? trueColor : falseColor;
        const text  = val ? 'Yes' : 'No';
        return `<span class="badge badge-${color}">${text}</span>`;
    }

    function load() {
        fetch(apiUrl)
            .then(r => r.json())
            .then(d => {
                if (d.error) {
                    document.getElementById('worker-status').innerHTML = '<span class="text-danger">Unavailable</span>';
                    return;
                }

                // Status banner
                const isRunning = d.running && !d.stale;
                const statusBox = document.getElementById('status-box');
                statusBox.className = 'info-box ' + (isRunning ? 'bg-gradient-success' : 'bg-gradient-danger');
                document.getElementById('worker-status').textContent = d.status || '—';
                document.getElementById('heartbeat-age').textContent =
                    d.heartbeat_age_seconds !== undefined
                        ? d.heartbeat_age_seconds.toFixed(1) + 's ago'
                        : '—';
                document.getElementById('heartbeat-at').textContent = d.last_heartbeat_at
                    ? new Date(d.last_heartbeat_at).toLocaleString()
                    : '';
                document.getElementById('queue-pending').textContent =
                    fmt(d.queue?.pending_total ?? 0);
                document.getElementById('queue-ready').textContent =
                    'Ready now: ' + fmt(d.queue?.ready_now ?? 0);

                // Identity
                document.getElementById('identity-table').innerHTML = [
                    row('Name',        d.worker_name),
                    row('Instance ID', `<code>${d.instance_id}</code>`),
                    row('Host',        d.host),
                    row('PID',         d.pid),
                    row('Enabled',     badge(d.enabled, 'success', 'secondary')),
                    row('Running',     badge(d.running, 'success', 'danger')),
                    row('Stale',       badge(d.stale, 'danger', 'success')),
                    row('Source',      d.source),
                    row('Started at',  d.started_at ? new Date(d.started_at).toLocaleString() : '—'),
                    row('Last cycle',  d.last_cycle_at ? new Date(d.last_cycle_at).toLocaleString() : '—'),
                    row('Last error',  d.last_error ?? '<span class="text-muted">None</span>'),
                ].join('');

                // Config
                const cfg = d.config || {};
                document.getElementById('config-table').innerHTML = [
                    row('Interval (s)',        fmt(cfg.worker_interval_seconds)),
                    row('Batch size',          fmt(cfg.worker_batch_size)),
                    row('Max retries',         fmt(cfg.worker_max_retries)),
                    row('Retry backoff base',  fmt(cfg.worker_retry_backoff_base)),
                    row('Stale after (s)',     fmt(d.stale_after_seconds)),
                ].join('');

                // Stats
                const s = d.stats || {};
                document.getElementById('stats-table').innerHTML = [
                    row('Total processed',          fmt(s.total_processed)),
                    row('Total succeeded',          fmt(s.total_succeeded)),
                    row('Total failed',             fmt(s.total_failed)),
                    row('Permanent failures',       fmt(s.total_permanent_failures)),
                ].join('');

                // By state
                const bs = d.by_state || {};
                document.getElementById('states-table').innerHTML = [
                    row('Reserved',  fmt(bs.reserved)),
                    row('Draft',     fmt(bs.draft)),
                    row('Update',    fmt(bs.update)),
                    row('Published', fmt(bs.published)),
                    row('Tombstone', fmt(bs.tombstone)),
                ].join('');

                // Errors
                const e = d.errors || {};
                document.getElementById('errors-table').innerHTML = [
                    row('Retrying',         fmt(e.retrying)),
                    row('Permanent',        fmt(e.permanent)),
                    row('Oldest error at',  e.oldest_error_at ? new Date(e.oldest_error_at).toLocaleString() : '<span class="text-muted">None</span>'),
                ].join('');

                // Queue detail
                const q = d.queue || {};
                document.getElementById('queue-table').innerHTML = [
                    row('Pending total',       fmt(q.pending_total)),
                    row('Ready now',           fmt(q.ready_now)),
                    row('Delayed by backoff',  fmt(q.delayed_by_backoff)),
                    row('Oldest pending at',   q.oldest_pending_at ? new Date(q.oldest_pending_at).toLocaleString() : '<span class="text-muted">None</span>'),
                ].join('');
            })
            .catch(() => {
                document.getElementById('worker-status').innerHTML = '<span class="text-danger">Connection failed</span>';
            });
    }

    load();
    document.getElementById('btn-refresh').addEventListener('click', load);
</script>
@endpush
