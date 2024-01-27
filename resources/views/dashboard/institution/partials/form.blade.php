<h6 class="heading-small text-muted">Basics informations</h6>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('name', 'Name') }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('responsible', 'Responsible') }}
            {{ Form::text('responsible', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('email', 'email') }}
            {{ Form::text('email', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('phone', 'phone') }}
            {{ Form::text('phone', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<h6 class="heading-small text-muted">Geographic informations</h6>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('address', 'Address') }}
            {{ Form::text('address', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('City', 'City') }}
            {{ Form::text('City', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('city', 'City') }}
            {{ Form::text('city', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('state', 'State') }}
            {{ Form::text('state', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('latitude', 'Latitude') }}
            {{ Form::text('latitude', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('longitude', 'Longitude') }}
            {{ Form::text('longitude', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-8">
        <div class="form-group">
            {{ Form::label('balance', 'Saldo') }}
            {{ Form::text('balance', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
    <div class="form-group">
            {{ Form::label('type', 'Type') }}
            {{ Form::select('type', [
                '0' => 'Government',
                '1' => 'University',
                '2' => 'Library',
                '3' => 'Others'
            ], null, array('class' => 'form-control')) }}
        </div>
    </div>
</div>