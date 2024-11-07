<h6 class="heading-small text-muted">Basics informations</h6>
<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            {{ Form::label('institution_id', 'Instution ID') }}
            {{ Form::text('institution_id', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {{ Form::label('number_nodes', 'Nº nodes') }}
            {{ Form::text('number_nodes', null, ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
    <div class="form-group">
            {{ Form::label('type', 'Type Nodes') }}
            {{ Form::select('type', [
                'Main dARK' => 'Main dARK',
                'External Fullnode ' => 'External Fullnode ',
                'External Observator Node ' => 'External Observator Node '
            ], null, array('class' => 'form-control')) }}
        </div>
    </div>
    </div>
</div>
