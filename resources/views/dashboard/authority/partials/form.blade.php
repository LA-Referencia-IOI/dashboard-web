<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('name', 'Name') }} <span class="text-danger">*</span>
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Authority name']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('responsible', 'Responsible') }} <span class="text-danger">*</span>
            {{ Form::text('responsible', null, ['class' => 'form-control', 'placeholder' => 'Responsible person']) }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('email', 'Email') }} <span class="text-danger">*</span>
            {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'contact@institution.org']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('phone', 'Phone') }}
            {{ Form::text('phone', null, ['class' => 'form-control', 'placeholder' => '+55 (00) 00000-0000']) }}
        </div>
    </div>
</div>
