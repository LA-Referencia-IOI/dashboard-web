@extends('adminlte::page')

@section('title', 'Workers')

@section('content_header')
    <h1>
        <i class="fas fa-cogs mr-2"></i> Workers
        <button class="btn btn-sm btn-outline-secondary ml-2" id="btn-refresh">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </h1>
@stop

@section('content')

    {{-- Overall status alert --}}
    <div id="overall-alert" class="alert d-none" role="alert">
        <i id="overall-icon" class="fas mr-2"></i>
        <strong id="overall-label"></strong>
        <span id="overall-message" class="ml-1"></span>
    </div>

    {{-- Infrastructure --}}
    <div class="row">
        <div class="col-md-4">
            <div class="info-box" id="rpc-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-network-wired"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">RPC</span>
                    <span class="info-box-number" id="rpc-state"><i class="fas fa-spinner fa-spin"></i></span>
                    <span class="progress-description" id="rpc-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box" id="storage-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-database"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Storage</span>
                    <span class="info-box-number" id="storage-state"><i class="fas fa-spinner fa-spin"></i></span>
                    <span class="progress-description" id="storage-peers"></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box" id="chain-cap-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-link"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Chain Capacity</span>
                    <span class="info-box-number" id="chain-cap-state"><i class="fas fa-spinner fa-spin"></i></span>
                    <span class="progress-description" id="chain-cap-txpool"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Workers --}}
    <div class="row" id="workers-row">
        <div class="col-md-12 text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
        </div>
    </div>

    {{-- ARKs by State + Errors --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-sitemap mr-1"></i> ARKs by State</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>State</th>
                                <th class="text-right">Count</th>
                            </tr>
                        </thead>
                        <tbody id="arks-table">
                            <tr><td colspan="2" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-1"></i> Errors</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <tbody id="errors-table">
                            <tr><td colspan="2" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
<script>
const apiUrl = '{{ route("workers.apiData") }}?detail=full';

function fmt(val) {
    if (val === null || val === undefined) return '<span class="text-muted">—</span>';
    if (typeof val === 'number') return new Intl.NumberFormat().format(val);
    return val;
}

function row(label, value) {
    return `<tr><td class="font-weight-bold" style="width:55%">${label}</td><td>${value}</td></tr>`;
}

function stateBadge(state) {
    const map = {
        available: 'success', healthy: 'success', running: 'success', idle: 'info',
        backlogged: 'warning', degraded: 'warning',
        unavailable: 'danger', critical: 'danger', dead: 'danger',
    };
    const color = map[state] || 'secondary';
    return `<span class="badge badge-${color}">${state ?? '—'}</span>`;
}

function infraColor(state) {
    if (['available','healthy'].includes(state)) return 'bg-gradient-success';
    if (['degraded','backlogged'].includes(state)) return 'bg-gradient-warning';
    return 'bg-gradient-danger';
}

function workerCard(name, w, queues) {
    const lc    = w.last_cycle  || {};
    const queue = w.queue || (queues[name] || {});
    const stats = w.stats || {};
    const alive = w.alive !== undefined ? w.alive : (w.running === true && !w.stale);
    const stateColor = { idle: 'info', running: 'success', backlogged: 'warning', dead: 'danger' };
    const headerColor = stateColor[w.state] || 'secondary';

    return `
    <div class="col-md-6">
        <div class="card card-${headerColor}">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cog mr-1"></i>
                    ${name.charAt(0).toUpperCase() + name.slice(1)} Worker
                </h3>
                <div class="card-tools">
                    ${stateBadge(w.state)}
                    <span class="badge badge-${alive ? 'success' : 'danger'} ml-1">${alive ? 'alive' : 'dead'}</span>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <tbody>
                        <tr><th colspan="2" class="bg-dark text-white-50 small py-1 px-2">Queue</th></tr>
                        ${row('Pending',         fmt(queue.pending ?? queue.pending_total))}
                        ${row('Ready',           fmt(queue.ready ?? queue.ready_now))}
                        ${row('Delayed',         fmt(queue.delayed ?? queue.delayed_by_backoff))}
                        <tr><th colspan="2" class="bg-dark text-white-50 small py-1 px-2">Last Cycle</th></tr>
                        ${row('Processed',       fmt(lc.processed !== undefined ? lc.processed : stats.total_processed))}
                        ${row('Succeeded',       fmt(lc.succeeded !== undefined ? lc.succeeded : stats.total_succeeded))}
                        ${row('Failed',          fmt(lc.failed !== undefined ? lc.failed : stats.total_failed))}
                        ${row('Duration (s)',    lc.duration_seconds !== undefined ? lc.duration_seconds.toFixed(3) : '—')}
                        ${row('Page size',       fmt(lc.page_size))}
                        ${row('Full page',       lc.full_page !== undefined ? (lc.full_page ? '<span class="badge badge-warning">Yes</span>' : '<span class="badge badge-secondary">No</span>') : '—')}
                        ${row('Next action',     lc.next_action ? `<code>${lc.next_action}</code>` : '—')}
                        <tr><th colspan="2" class="bg-dark text-white-50 small py-1 px-2">Health</th></tr>
                        ${row('Status',          w.status ?? '—')}
                        ${row('Activity',        w.activity ?? '—')}
                        ${row('Last heartbeat',  w.last_heartbeat_seconds !== undefined ? w.last_heartbeat_seconds.toFixed(1) + 's ago' : (w.heartbeat_age_seconds !== undefined ? w.heartbeat_age_seconds.toFixed(1) + 's ago' : '—'))}
                        ${row('Last cycle',      w.last_cycle_at || w.reconciliation?.last_run_at || '—')}
                    </tbody>
                </table>
            </div>
        </div>
    </div>`;
}

function load(refresh = false) {
    fetch(apiUrl + (refresh ? '&refresh=1' : ''))
        .then(r => r.json())
        .then(d => {
            if (d.error) {
                document.getElementById('overall-alert').className = 'alert alert-danger';
                document.getElementById('overall-icon').className  = 'fas fa-times-circle mr-2';
                document.getElementById('overall-label').textContent  = 'Unavailable';
                document.getElementById('overall-message').textContent = d.error;
                return;
            }

            // Overall alert
        const overallMap = {
                ok:       { cls: 'alert-success', icon: 'fa-check-circle',      label: 'OK' },
                degraded: { cls: 'alert-warning', icon: 'fa-exclamation-circle', label: 'Degraded' },
                down:     { cls: 'alert-danger',  icon: 'fa-times-circle',       label: 'Down' },
            };
            const ov = overallMap[d.overall] || { cls: 'alert-secondary', icon: 'fa-question-circle', label: d.overall };
            const alertEl = document.getElementById('overall-alert');
            alertEl.className = `alert ${ov.cls}`;
            document.getElementById('overall-icon').className    = `fas ${ov.icon} mr-2`;
            document.getElementById('overall-label').textContent = ov.label;
            document.getElementById('overall-message').textContent = d.message || '';

            // RPC
            const rpc = d.rpc || {};
            document.getElementById('rpc-box').className    = `info-box ${infraColor(rpc.state)}`;
            document.getElementById('rpc-state').textContent  = rpc.state  || '—';
            document.getElementById('rpc-block').textContent  = rpc.block_number ? `Block #${new Intl.NumberFormat().format(rpc.block_number)}` : '';

            // Storage
            const st = d.storage || {};
            document.getElementById('storage-box').className      = `info-box ${infraColor(st.state)}`;
            document.getElementById('storage-state').textContent   = st.state   || '—';
            document.getElementById('storage-peers').textContent   = st.available_peers !== undefined
                ? `${st.backend} · ${st.available_peers}/${st.min_peers} peers` : '';

            // Chain capacity
            const cc = d.chain_capacity || {};
            document.getElementById('chain-cap-box').className       = `info-box ${infraColor(cc.state)}`;
            document.getElementById('chain-cap-state').textContent    = cc.state  || '—';
            document.getElementById('chain-cap-txpool').textContent   = cc.txpool_pending !== undefined
                ? `txpool pending: ${new Intl.NumberFormat().format(cc.txpool_pending)}` : '';

            // Workers
            const workers = d.workers || {};
            const workersHtml = Object.entries(workers).map(([name, w]) => workerCard(name, w, d.queues || {})).join('');
            document.getElementById('workers-row').innerHTML = workersHtml || '<div class="col-12 text-muted text-center py-3">No workers found</div>';

            // ARKs by state
            const arks = d.by_state || d.arks || {};
            const arkColors = { reserved: 'secondary', draft: 'warning', update: 'info', published: 'success', tombstone: 'dark' };
            document.getElementById('arks-table').innerHTML = Object.entries(arks).map(([state, count]) =>
                `<tr>
                    <td><span class="badge badge-${arkColors[state] || 'secondary'}">${state}</span></td>
                    <td class="text-right font-weight-bold">${new Intl.NumberFormat().format(count)}</td>
                </tr>`
            ).join('');

            // Errors
            const e = d.errors || {};
            document.getElementById('errors-table').innerHTML = [
                row('Recoverable', `<span class="badge badge-${(e.recoverable ?? e.total?.recoverable ?? 0) > 0 ? 'warning' : 'secondary'}">${fmt(e.recoverable ?? e.total?.recoverable ?? 0)}</span>`),
                row('Permanent', `<span class="badge badge-${(e.permanent ?? e.total?.permanent ?? 0) > 0 ? 'danger'  : 'secondary'}">${fmt(e.permanent ?? e.total?.permanent ?? 0)}</span>`),
            ].join('');
        })
        .catch(err => {
            document.getElementById('overall-alert').className = 'alert alert-danger';
            document.getElementById('overall-icon').className  = 'fas fa-times-circle mr-2';
            document.getElementById('overall-label').textContent   = 'Connection failed';
            document.getElementById('overall-message').textContent = err.message || '';
        });
}

load();
document.getElementById('btn-refresh').addEventListener('click', () => load(true));
</script>
@endpush
