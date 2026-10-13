<div class="authority-panel-body">
    <div class="row">
        <div class="col-sm-6">
            <h3 class="authority-subheading mb-3">Namespace & organization</h3>
            <div class="form-group">
                <label for="naan-naan">NAAN</label>
                <input type="text" id="naan-naan" name="naan" class="form-control @error('naan') is-invalid @enderror" placeholder="Enter NAAN" value="{{ old('naan', $naan->naan ?? '') }}">
                @error('naan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="naan-organization_name">Organization Name</label>
                <input type="text" id="naan-organization_name" name="organization_name" class="form-control @error('organization_name') is-invalid @enderror" placeholder="Enter Organization Name" value="{{ old('organization_name', $naan->organization_name ?? '') }}">
                @error('organization_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="naan-organization_acronym">Organization Acronym</label>
                <input type="text" id="naan-organization_acronym" name="organization_acronym" class="form-control @error('organization_acronym') is-invalid @enderror" placeholder="Enter Organization Acronym" value="{{ old('organization_acronym', $naan->organization_acronym ?? '') }}">
                @error('organization_acronym')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="naan-target_url_template">Target URL Template</label>
                <input type="text" id="naan-target_url_template" name="target_url_template" class="form-control @error('target_url_template') is-invalid @enderror" placeholder="Enter Target URL Template" value="{{ old('target_url_template', $naan->target_url_template ?? '') }}">
                @error('target_url_template')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <h3 class="authority-subheading mb-3">Contact & registration</h3>
            <div class="form-group">
                <label for="naan-contact_name">Contact Name</label>
                <input type="text" id="naan-contact_name" name="contact_name" class="form-control @error('contact_name') is-invalid @enderror" placeholder="Enter Contact Name" value="{{ old('contact_name', $naan->contact_name ?? '') }}">
                @error('contact_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="naan-contact_email">Contact Email</label>
                <input type="email" id="naan-contact_email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror" placeholder="Enter Contact Email" value="{{ old('contact_email', $naan->contact_email ?? '') }}">
                @error('contact_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="naan-status">Status</label>
                <select id="naan-status" name="status" class="form-control @error('status') is-invalid @enderror">
                    <option value="active" {{ old('status', $naan->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $naan->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="naan-registered_at">Registered At</label>
                <input type="datetime-local" id="naan-registered_at" name="registered_at" class="form-control @error('registered_at') is-invalid @enderror" value="{{ old('registered_at', isset($naan->registered_at) ? \Carbon\Carbon::parse($naan->registered_at)->format('Y-m-d\TH:i') : '') }}">
                @error('registered_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="authority-panel-footer">
    <button type="submit" class="btn btn-primary">Save NAAN</button>
    <a href="{{ route('naans.index') }}" class="btn btn-default">Cancel</a>
</div>
