<div class="form-group mt-3">
    <label for="client_public_key">Minter public key @if($required)<span class="text-danger">*</span>@else<span class="text-muted">(optional)</span>@endif</label>
    <textarea id="client_public_key" name="client_public_key" rows="3" maxlength="4096"
              class="form-control text-monospace @error('client_public_key') is-invalid @enderror"
              placeholder="ssh-ed25519 AAAA… client-label" @if($required) required @endif>{{ old('client_public_key') }}</textarea>
    <small class="form-text text-muted">Paste an Ed25519 public key in OpenSSH or base64 format. Keep the private key on the client.</small>
    @error('client_public_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
