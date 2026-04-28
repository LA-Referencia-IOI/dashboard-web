
<h6 class="heading-small text-muted mb-2">Basic Information</h6>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('name', 'Name') }} <span class="text-danger">*</span>
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Institution name']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('responsible', 'Responsible') }} <span class="text-danger">*</span>
            {{ Form::text('responsible', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('email', 'Email') }} <span class="text-danger">*</span>
            {{ Form::email('email', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('phone', 'Phone') }}
            {{ Form::text('phone', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>

<h6 class="heading-small text-muted mb-2 mt-3">Geographic Information</h6>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('address', 'Address') }}
            {{ Form::text('address', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('country', 'Country') }}
            {{ Form::text('country', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('city', 'City') }}
            {{ Form::text('city', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('state', 'State') }}
            {{ Form::text('state', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('type', 'Type') }} <span class="text-danger">*</span>
            {{ Form::select('type', [
                '0' => 'Government',
                '1' => 'University',
                '2' => 'Library',
                '3' => 'Others',
            ], null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('latitude', 'Latitude') }} <span class="text-danger">*</span>
            {{ Form::text('latitude', null, ['class' => 'form-control', 'placeholder' => '-3.7319']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('longitude', 'Longitude') }} <span class="text-danger">*</span>
            {{ Form::text('longitude', null, ['class' => 'form-control', 'placeholder' => '-38.5267']) }}
        </div>
    </div>
</div>

<!-- <h6 class="heading-small text-muted mb-2 mt-3">Node Configuration</h6>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('numberNodes', 'Number of Nodes') }}
            {{ Form::text('numberNodes', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('typeNodes', 'Node Type') }}
            {{ Form::select('typeNodes', [
                '0' => 'Main dARK',
                '1' => 'External Node in Main dARK',
                '2' => 'Network Partner',
                '3' => 'External Node in Network Partner',
            ], null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('status', 'Status') }}
            {{ Form::select('status', [
                '0' => 'Enabled',
                '1' => 'Disabled',
            ], null, ['class' => 'form-control']) }}
        </div>
    </div>
</div> -->
