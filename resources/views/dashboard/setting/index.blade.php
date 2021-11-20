@extends('adminlte::page')

@section('content')
    <div class="row pt-4">
        <div class="col-md-8 col-sm-12 col-lg-8">
            @if (session('message'))
                <div class="alert alert-{{ session('code') }} alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('message') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Atenção!</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Informações do Perfil</h3>
                </div>
                {{ Form::model(Auth::user(), ['route' => ['settings.update', Auth::user()->id], 'method' => 'PUT', 'role' => 'form']) }}
                <div class="card-body">
                    @include('dashboard.user.partials.form', [
                    'showPasswordTip' => true
                    ])
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-secondary">Salvar dados</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
