<h6 class="heading-small text-muted">ARK Information</h6>

{{-- ======================= BASIC INFO ======================= --}}
<div class="card mb-3 p-3">
    <h6 class="text-muted">Basic Information</h6>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('what', 'What') }}
                {{ Form::text('what', old('what', $ark->what ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('when', 'When') }}
                {{ Form::text('when', old('when', $ark->when ?? $currentDateTime), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('where', 'Where') }}
                {{ Form::text('where', old('where', $ark->where ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('how', 'How') }}
                {{ Form::text('how', old('how', $ark->how ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('why', 'Why') }}
                {{ Form::text('why', old('why', $ark->why ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>

{{-- ======================= WHO ======================= --}}
<div class="card mb-3 p-3">
    <h6 class="text-muted">Who</h6>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('who_name', 'Who Name') }}
                {{ Form::text('who_name', old('who_name', $ark->who_name ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('who_name_native', 'Who Name Native') }}
                {{ Form::text('who_name_native', old('who_name_native', $ark->who_name_native ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('who_acronym', 'Who Acronym') }}
                {{ Form::text('who_acronym', old('who_acronym', $ark->who_acronym ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('who_location_lat', 'Who Location (LAT)') }}
                {{ Form::text('who_location_lat', old('who_location_lat', $ark->who_location_lat ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('who_location_lon', 'Who Location (LON)') }}
                {{ Form::text('who_location_lon', old('who_location_lon', $ark->who_location_lon ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('who_address', 'Who Address') }}
                {{ Form::text('who_address', old('who_address', $ark->who_address ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>

{{-- ======================= TARGET ======================= --}}
<div class="card mb-3 p-3">
    <h6 class="text-muted">Target</h6>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('target_url', 'Target URL') }}
                {{ Form::text('target_url', old('target_url', $ark->target_url ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('target_http_code', 'Target HTTP Code') }}
                {{ Form::number('target_http_code', old('target_http_code', $ark->target_http_code ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>

{{-- ======================= NA POLICY ======================= --}}
<div class="card mb-3 p-3">
    <h6 class="text-muted">NA Policy</h6>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('na_orgtype', 'Org Type') }}
                {{ Form::text('na_orgtype', old('na_orgtype', $ark->na_orgtype ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('na_policy', 'Policy') }}
                {{ Form::text('na_policy', old('na_policy', $ark->na_policy ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('na_tenure', 'Tenure') }}
                {{ Form::text('na_tenure', old('na_tenure', $ark->na_tenure ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('na_policy_url', 'Policy URL') }}
                {{ Form::text('na_policy_url', old('na_policy_url', $ark->na_policy_url ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>

{{-- ======================= CONTACT ======================= --}}
<div class="card mb-3 p-3">
    <h6 class="text-muted">Contact</h6>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('contact_name', 'Contact Name') }}
                {{ Form::text('contact_name', old('contact_name', $ark->contact_name ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('contact_unit', 'Unit') }}
                {{ Form::text('contact_unit', old('contact_unit', $ark->contact_unit ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('contact_tenure', 'Tenure') }}
                {{ Form::text('contact_tenure', old('contact_tenure', $ark->contact_tenure ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('contact_email', 'Email') }}
                {{ Form::text('contact_email', old('contact_email', $ark->contact_email ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('contact_phone', 'Phone') }}
                {{ Form::text('contact_phone', old('contact_phone', $ark->contact_phone ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>

{{-- ======================= OTHERS ======================= --}}
<div class="card mb-3 p-3">
    <h6 class="text-muted">Others</h6>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('alternate_contact', 'Alternate Contact') }}
                {{ Form::text('alternate_contact', old('alternate_contact', $ark->alternate_contact ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('provider', 'Provider') }}
                {{ Form::text('provider', old('provider', $ark->provider ?? null), ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('comments', 'Comments') }}
                {{ Form::textarea('comments', old('comments', $ark->comments ?? null), ['class' => 'form-control', 'rows' => 2]) }}
            </div>
        </div>
    </div>
</div>
{{-- ======================= END OF FORM ======================= --}}