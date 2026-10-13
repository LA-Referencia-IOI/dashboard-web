@extends('adminlte::page')
@section('title', 'PID ownership — ' . $authority->name)
@section('css')
    @include('dashboard.authority.partials.design')
@stop
@section('content')
<div class="authority-page">
    <nav class="authority-breadcrumb"><a href="{{ route('authorities.index') }}">Authorities</a><span class="mx-2">/</span><a href="{{ route('authorities.show', $authority->id) }}">{{ $authority->name }}</a><span class="mx-2">/</span>PID ownership</nav>
    <header class="authority-page-heading"><h1>PID ownership</h1><p>Transfer published PIDs to an active authority authorized for their NAANs.</p></header>
    @if(session('message'))<div class="alert alert-{{ session('code') }}">{{ session('message') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @if(session('transfer_results'))
        <section class="authority-panel"><div class="authority-panel-heading"><div><h2>Transfer results</h2><p>Each PID is confirmed separately on blockchain. A partial batch is not rolled back.</p></div></div><div class="authority-panel-body">
            @foreach(session('transfer_results') as $result)<p><strong>{{ $result['ark'] }}</strong> — {{ $result['status'] }}<br><small>@if($result['transaction_hash'])<br>Transaction: <code>{{ $result['transaction_hash'] }}</code>@endif</small></p>@endforeach
        </div></section>
    @endif
    @if(!$ownership)
        <div class="alert alert-warning">Ownership inventory is unavailable. Transfers and deactivation are blocked.</div>
    @else
        <section class="authority-panel"><div class="authority-panel-heading"><div><h2>Associated PIDs</h2><p>{{ $ownership['on_chain_count'] }} owned on blockchain. Local Minter queues are not checked.</p></div></div>
            <form action="{{ route('authorities.transferPids', $authority->id) }}" method="POST" id="pid-transfer-form">
                @csrf
                @if(old('arks'))
                    <div class="alert alert-info mx-3 mt-3">Previous selection retained. Retry the same destination to reconcile incomplete transfers. Completed items remain applied.</div>
                    @foreach(old('arks', []) as $ark)<input type="hidden" name="arks[]" value="{{ $ark }}">@endforeach
                @endif
                <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Select</th><th>PID</th><th>State</th><th>Transfer eligibility</th></tr></thead><tbody>
                    @forelse($ownership['records'] as $record)
                        <tr><td><input type="checkbox" name="arks[]" value="{{ $record['ark'] }}" @if(!$record['transferable'] || old('arks')) disabled @endif aria-label="Select {{ $record['ark'] }}"></td><td><code>{{ $record['ark'] }}</code></td><td>On-chain</td><td>{{ $record['transferable'] ? 'On-chain; local pending work may exist' : 'Blocked — resolve lifecycle or pending work' }}</td></tr>
                    @empty<tr><td colspan="4" class="text-muted">No PIDs owned on blockchain on this page.</td></tr>@endforelse
                </tbody></table></div>
                <div class="authority-panel-body">
                    <div class="form-group"><label for="transfer-target">Destination authority</label><select id="transfer-target" name="target_authority_id" class="form-control"><option value="">Choose a listed authority, or enter its UUID below</option>@foreach($targets as $target)<option value="{{ $target->id }}" @if(old('target_authority_id') === $target->id) selected @endif>{{ $target->name }}</option>@endforeach</select><small class="text-muted">Admin rechecks active status and NAAN permissions on-chain.</small></div>
                    <div class="form-group"><label for="external-transfer-target">Authority UUID from another Dashboard</label><input id="external-transfer-target" name="external_target_authority_id" class="form-control" value="{{ old('external_target_authority_id', $targets->contains('id', old('target_authority_id')) ? '' : old('target_authority_id')) }}" placeholder="Destination authority UUID"><small class="text-muted">Use this field when the authority is not listed here. Admin validates registration, active status and NAAN permissions on blockchain.</small></div>
                    <div class="form-group"><label for="transfer-reason">Administrative reason</label><textarea id="transfer-reason" name="reason" class="form-control" maxlength="2000" required>{{ old('reason') }}</textarea></div>
                    <p class="small text-muted">Identifiers, targets, metadata and creation dates are preserved. The source authority is not deactivated automatically.</p>
                </div>
                <div class="authority-panel-footer"><button class="btn btn-primary" type="submit">Transfer selected PIDs</button><a class="btn btn-outline-secondary ml-2" href="{{ route('authorities.show', $authority->id) }}">Back to authority</a></div>
            </form>
        </section>
        <div class="d-flex justify-content-between mb-4">
            @if($page > 1)<a href="{{ route('authorities.pids', ['authority' => $authority->id, 'page' => $page-1]) }}">Previous page</a>@else<span></span>@endif
            @if($page * $ownership['page_size'] < $ownership['on_chain_count'])<a href="{{ route('authorities.pids', ['authority' => $authority->id, 'page' => $page+1]) }}">Next page</a>@endif
        </div>
    @endif
</div>
@stop
@section('js')
<script>
document.getElementById('pid-transfer-form')?.addEventListener('submit', function () {
    const button = this.querySelector('button[type="submit"]');
    if (button.disabled) { return; }
    button.disabled = true;
    button.textContent = 'Transferring PIDs…';
});
</script>
@stop
