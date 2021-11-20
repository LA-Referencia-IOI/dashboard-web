<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('email', 'Email') }}
            {{ Form::email('email', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>

@if (isset($showPasswordTip) && $showPasswordTip)
    <h6 class="heading-small text-muted">Deseja mudar a senha? Preencha os campos Senha e Confirmação de senha logo
        abaixo. Não esqueça de clicar em Salvar.</h6>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('password', 'Senha') }}
            {{ Form::password('password', ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('password', 'Confirmação de senha') }}
            {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
        </div>
    </div>
</div>

<h6 class="heading-small text-muted">Informações básicas</h6>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {{ Form::label('name', 'Nome') }}
            {{ Form::text('name', null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>
