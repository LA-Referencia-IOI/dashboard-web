@extends('adminlte::page')

@section('title', 'Workers')

@section('content_header')
    <h1><i class="fas fa-cogs mr-2"></i>Workers <button class="btn btn-sm btn-outline-secondary ml-2" id="refresh"><i class="fas fa-sync-alt"></i> Refresh</button></h1>
@stop

@section('content')
    <div id="overall" class="alert d-none"></div>
    <div class="row" id="workers"><div class="col-12 text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div></div>
    <div class="row">
        <div class="col-md-6"><div class="card card-dark"><div class="card-header"><h3 class="card-title">Infrastructure</h3></div><div class="card-body" id="infrastructure">Loading…</div></div></div>
        <div class="col-md-6"><div class="card card-dark"><div class="card-header"><h3 class="card-title">Permanent failures</h3></div><div class="card-body" id="errors">Loading…</div></div></div>
    </div>
@stop

@push('js')
<script>
const url = '{{ route("workers.apiData") }}?detail=full';
const number = value => new Intl.NumberFormat().format(value || 0);
const ago = seconds => seconds === null || seconds === undefined ? '—' : `${Math.round(seconds)} s ago`;
const when = value => value ? new Date(value).toLocaleTimeString() : '—';
function row(label, value) { return `<tr><th style="width:52%">${label}</th><td>${value}</td></tr>`; }
function processBadge(state) {
    const colors = {RUNNING:'success', SLEEPING:'info', SLEEPING_UNTIL_DUE:'info', PAUSED:'warning', DOWN:'danger', DISABLED:'secondary'};
    return `<span class="badge badge-${colors[state] || 'secondary'}">${state || '—'}</span>`;
}
function workerCard(name, process, work) {
    const state = process.process_state || 'DOWN';
    const color = {RUNNING:'success', SLEEPING:'info', sleeping_until_due:'info', PAUSED:'warning', DOWN:'danger', DISABLED:'secondary'}[state] || 'secondary';
    const cycle = process.last_cycle || {};
    const reasons = Object.entries(work.waiting_reasons || {}).map(([reason, count]) => `${reason}: ${number(count)}`).join('<br>') || '—';
    const stages = work.by_stage || {};
    const availability = stages.availability || {};
    const replicationRows = name === 'replication'
        ? `${row('First pins: ready now', number(availability.ready_now))}
           ${row('First pins: waiting', number(availability.waiting))}
           ${row('Published: awaiting durability', number(work.maintenance_ready))}
           ${row('Maintenance blocked by', work.maintenance_blocked_by || '—')}`
        : `${row('Ready now', number(work.ready_now))}
           ${row('Waiting', number(work.waiting))}
           ${row('Next action', when(work.next_action_at))}`;
    const statusText = process.stalled_suspected
        ? `No progress for ${number(process.no_progress_cycles)} attempted cycles; inspect dependency/error detail`
        : (state === 'SLEEPING' || state === 'SLEEPING_UNTIL_DUE')
        ? (name === 'replication'
            ? (work.maintenance_blocked_by ? `First-pin work has priority (${work.maintenance_blocked_by})` : `${number(work.maintenance_ready || 0)} published records awaiting durability`)
            : (work.ready_now ? `${number(work.ready_now)} ready; wakes in ${Math.ceil(process.wake_in_seconds || 0)} s` : `No ready work; wakes in ${Math.ceil(process.wake_in_seconds || 0)} s`))
        : state === 'PAUSED' ? `Paused: ${process.pause_reason || 'dependency unavailable'}`
        : state === 'RUNNING' ? 'Executing a worker cycle' : state;
    return `<div class="col-md-4"><div class="card card-${color}"><div class="card-header"><h3 class="card-title"><i class="fas fa-cog mr-1"></i>${name.charAt(0).toUpperCase()+name.slice(1)}</h3><div class="card-tools">${processBadge(state)}</div></div><div class="card-body p-0"><table class="table table-sm table-striped mb-0"><tbody>
        ${row('Process', statusText)}
        ${row('Alive', process.alive ? 'yes' : 'no')}
        ${row('Last heartbeat', ago(process.heartbeat_age_seconds))}
        ${row('No-progress cycles', number(process.no_progress_cycles))}
        ${replicationRows}
        ${row('Why records are waiting', reasons)}
        ${row('Permanent failures', number(work.failed))}
        <tr><th colspan="2" class="bg-dark text-white-50 small">Last cycle</th></tr>
        ${row('Finished', when(process.last_cycle_at))}
        ${row('Observed / advanced / waiting / repaired / failed', `${number(cycle.observed)} / ${number(cycle.advanced)} / ${number(cycle.waiting)} / ${number(cycle.repaired)} / ${number(cycle.failed)}`)}
        ${row('Duration', cycle.duration_seconds === null || cycle.duration_seconds === undefined ? '—' : `${cycle.duration_seconds.toFixed(3)} s`)}
    </tbody></table></div></div></div>`;
}
function infrastructure(data) {
    const rpc = data.rpc || {}, storage = data.storage || {};
    const duplicate = storage.duplicate_ipfs_peer_ids ? '<br><span class="text-danger">Invalid topology: Cluster peers share a Kubo identity.</span>' : '';
    return `<p><strong>RPC:</strong> ${rpc.available ? 'available' : 'unavailable'}${rpc.block_number ? ` · block #${number(rpc.block_number)}` : ''}</p><p class="mb-0"><strong>Storage:</strong> ${storage.available ? 'available' : 'unavailable'}${storage.available_peers !== undefined ? ` · ${number(storage.available_peers)} peers` : ''}${duplicate}</p>`;
}
function render(data) {
    const overall = document.getElementById('overall');
    const cls = data.overall === 'ok' ? 'alert-success' : data.overall === 'degraded' ? 'alert-warning' : 'alert-danger';
    overall.className = `alert ${cls}`;
    overall.textContent = data.overall === 'ok'
        ? 'Workers healthy. Waiting for a Cluster pin is normal; durability runs only when the critical pipeline is clear.'
        : `Worker health: ${data.overall}`;
    const work = data.workload || {}, workers = data.workers || {};
    document.getElementById('workers').innerHTML = Object.entries(workers).map(([name, p]) => workerCard(name, p, work[name] || {ready_now:0,waiting:0,failed:0})).join('');
    document.getElementById('infrastructure').innerHTML = infrastructure(data.infrastructure || {});
    const errors = data.errors || {};
    document.getElementById('errors').innerHTML = `${number(errors.total)} records require administrative review.`;
}
function load(refresh=false) { fetch(url + (refresh ? '&refresh=1' : '')).then(r => r.json()).then(render).catch(error => { const e=document.getElementById('overall'); e.className='alert alert-danger'; e.textContent=error.message; }); }
document.getElementById('refresh').addEventListener('click', () => load(true));
load();
</script>
@endpush
