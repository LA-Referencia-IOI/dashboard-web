<section class="authority-panel">
    <div class="authority-panel-heading">
        <span class="authority-section-icon" aria-hidden="true"><i class="fas fa-exchange-alt"></i></span>
        <div><h2>PID ownership & retirement</h2><p>Transfer responsibility before retiring this authority.</p></div>
    </div>
    <div class="authority-panel-body">
        @if($ownership)
            <dl class="mb-2"><dt>Owned on blockchain</dt><dd>{{ $ownership['on_chain_count'] }}</dd></dl>
            @if(!$ownership['can_deactivate'])
                <p class="small text-muted">Deactivation is blocked while this authority owns PIDs on blockchain. Transfer them first. Local records and pending work are not checked by Admin.</p>
            @else
                <p class="small text-muted">No PIDs owned on blockchain. Deactivation retains this authority's record and history.</p>
            @endif
        @else
            <div class="alert alert-warning mb-0">Ownership inventory is unavailable. Deactivation is blocked until it can be checked.</div>
        @endif
    </div>
    <div class="authority-panel-footer">
        <a href="{{ route('authorities.pids', $authority->id) }}" class="btn btn-outline-primary btn-sm">Manage PID ownership</a>
        @if($authority->status === 'active')
            @if($ownership && $ownership['can_deactivate'])
                <details class="authority-editor mt-3"><summary class="btn btn-outline-danger btn-sm">Deactivate authority</summary>
                    <form action="{{ route('authorities.deactivate', $authority->id) }}" method="POST" class="mt-3 authority-lifecycle-form">
                        @csrf
                        <p class="small">Stop this authority’s Minter activity before deactivation. Admin rechecks blockchain ownership; local reservations and jobs are not inventoried.</p>
                        <label class="d-block"><input type="checkbox" name="confirmation" value="deactivate" required> I confirm deactivation.</label>
                        <button type="submit" class="btn btn-danger btn-sm">Confirm deactivation</button>
                    </form>
                </details>
            @else
                <button class="btn btn-outline-secondary btn-sm" disabled>Deactivate authority</button>
            @endif
        @endif
    </div>
</section>
