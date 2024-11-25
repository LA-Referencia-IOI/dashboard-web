

<h6 class="heading-small text-muted">Basics informations (all is mandatory)</h6>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('who', 'Who') }}
            {{ Form::text('who', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('what', 'What') }}
            {{ Form::text('what', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('when', 'When') }}
            {{ Form::text('when', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('where', 'Where') }}
            {{ Form::text('where', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('how', 'How') }}
            {{ Form::text('how', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('why', 'Why') }}
            {{ Form::text('why', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('contact', 'Contact') }}
            {{ Form::text('contact', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {{ Form::label('address', 'Address') }}
            {{ Form::text('address', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
    