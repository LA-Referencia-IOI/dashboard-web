

<h6 class="heading-small text-muted">Basics informations</h6>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('organization_name', 'Name') }}
            {{ Form::text('organization_name', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('contact_email', 'Email') }}
            {{ Form::text('contact_email', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<div class="row">
    
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('naan', 'Naan') }}
            {{ Form::text('naan', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('institution_id', 'Institution ID') }}
            {{ Form::text('institution_id', null, ['class' => 'form-control']) }}
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

