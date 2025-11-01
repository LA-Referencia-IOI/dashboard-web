

<h6 class="heading-small text-muted">Basics informations</h6>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('organization_name', 'Name') }}
            {{ Form::text(
                'organization_name',
                old('organization_name', $institution->name ?? null),
                ['class' => 'form-control', 'readonly' => isset($institution)]
            ) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('contact_email', 'Email') }}
            {{ Form::email(
                'contact_email',
                old('contact_email', $institution->email ?? null),
                ['class' => 'form-control', 'readonly' => isset($institution)]
            ) }}
        </div>
    </div>
</div>
<div class="row">
    
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('naan', 'Naan') }}
            {{ Form::text(
                'naan',
                old('naan', $institution->getNaan()),
                ['class' => 'form-control']
            ) }}

        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('institution_id', 'Institution ID') }}
            {{ Form::text(
                'institution_id',
                old('institution_id', $institution->id ?? null),
                ['class' => 'form-control', 'readonly' => isset($institution)]
            ) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
                {{ Form::label('payload_schema', 'Payload Schema') }}
                {{ Form::select('payload_schema', [
                    'basic2024a' => 'basic2024a'
                ], null, array('class' => 'form-control')) }}
            </div>
        </div>
    </div>
</div>

