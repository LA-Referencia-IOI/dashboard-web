<div class="card-body">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>NAAN</label>
                <input type="text" name="naan" class="form-control @error('naan') is-invalid @enderror" placeholder="Enter NAAN" value="{{ old('naan', $naan->naan ?? '') }}">
                @error('naan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Organization Name</label>
                <input type="text" name="organization_name" class="form-control @error('organization_name') is-invalid @enderror" placeholder="Enter Organization Name" value="{{ old('organization_name', $naan->organization_name ?? '') }}">
                @error('organization_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Organization Acronym</label>
                <input type="text" name="organization_acronym" class="form-control @error('organization_acronym') is-invalid @enderror" placeholder="Enter Organization Acronym" value="{{ old('organization_acronym', $naan->organization_acronym ?? '') }}">
                @error('organization_acronym')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Target URL Template</label>
                <input type="text" name="target_url_template" class="form-control @error('target_url_template') is-invalid @enderror" placeholder="Enter Target URL Template" value="{{ old('target_url_template', $naan->target_url_template ?? '') }}">
                @error('target_url_template')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Contact Name</label>
                <input type="text" name="contact_name" class="form-control @error('contact_name') is-invalid @enderror" placeholder="Enter Contact Name" value="{{ old('contact_name', $naan->contact_name ?? '') }}">
                @error('contact_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Contact Email</label>
                <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror" placeholder="Enter Contact Email" value="{{ old('contact_email', $naan->contact_email ?? '') }}">
                @error('contact_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control @error('status') is-invalid @enderror">
                    <option value="active" {{ old('status', $naan->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $naan->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Registered At</label>
                <input type="datetime-local" name="registered_at" class="form-control @error('registered_at') is-invalid @enderror" value="{{ old('registered_at', isset($naan->registered_at) ? \Carbon\Carbon::parse($naan->registered_at)->format('Y-m-d\TH:i') : '') }}">
                @error('registered_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="card-footer">
    <button type="submit" class="btn btn-dark">Submit</button>
    <a href="{{ route('naans.index') }}" class="btn btn-default">Cancel</a>
</div>
