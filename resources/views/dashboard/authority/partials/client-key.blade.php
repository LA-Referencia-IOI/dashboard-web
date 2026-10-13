<div class="authority-panel">
    <div class="authority-panel-heading">
        <span class="authority-section-icon" aria-hidden="true"><i class="fas fa-key"></i></span>
        <div><h2>Minter access</h2><p>Public key used to verify requests from this authority.</p></div>
    </div>
    <div class="authority-panel-body">
        @if($clientKeyState['status'] === 'unavailable')
            <p class="text-warning">The current public key could not be checked. Reload this page before replacing it.</p>
        @elseif($clientKeyState['status'] === 'pending')
            <p class="text-muted">Authority registration is pending. The key below will be submitted when registering.</p>
        @elseif($clientKeyState['status'] === 'inactive')
            <p class="text-muted">This authority is inactive.</p>
        @elseif($clientKeyState['status'] === 'missing')
            <p class="text-muted">No public key is registered. A key is needed when the Minter requires signed requests.</p>
        @else
            <p class="text-success"><i class="fas fa-check-circle mr-1"></i> Public key registered on blockchain</p>
        @endif
        @if($clientKeyState['public_key'])
            <details class="mb-3">
                <summary>View public key @if($clientKeyState['status'] === 'pending')(pending registration)@endif</summary>
                <div class="authority-key mt-2"><code>{{ $clientKeyState['public_key'] }}</code></div>
            </details>
            @if($clientKeyState['key_id'])
                <small class="text-muted d-block">Fingerprint (SHA-256):</small>
                <code class="d-block mb-2" style="overflow-wrap:anywhere">{{ $clientKeyState['key_id'] }}</code>
            @endif
        @endif
        @if(in_array($clientKeyState['status'], ['registered', 'missing', 'pending']))
            <details class="authority-editor" @if($errors->has('client_public_key') || (old('client_public_key') !== null && session('code') === 'danger')) open @endif>
                <summary class="btn btn-outline-primary btn-sm">{{ $clientKeyState['status'] === 'registered' ? 'Replace public key' : ($clientKeyState['status'] === 'pending' ? 'Edit key for registration' : 'Register public key') }}</summary>
                <form action="{{ route('authorities.clientKey', $authority->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('dashboard.authority.partials.client-key-input', ['required' => true])
                <p class="small text-muted">One key per authority. Replacing it preserves the wallet and PID ownership.</p>
                <button type="submit" class="btn btn-primary">
                    {{ $clientKeyState['status'] === 'registered' ? 'Replace public key' : ($clientKeyState['status'] === 'pending' ? 'Save key for registration' : 'Register public key') }}
                </button>
                </form>
            </details>
        @endif
    </div>
</div>
