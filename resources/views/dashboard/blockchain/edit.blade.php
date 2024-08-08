@extends('adminlte::page')

@section('title', 'Edition')

@section('content')
    <div class="col-md-6 col-sm-12 col-lg-10">
        @if (session('message'))
            <div class="alert alert-{{ session('code') }} alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('message') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Atenção!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{ Form::model($blockchain, ['route' => ['blockchains.update', $blockchain->id], 'method' => 'PUT']) }}
            <div class="card-body">
                <h6 class="heading-small text-muted mb-4">
                    <strong>Atenção!</strong>
                    <br>
                    Todos os campos obrigatórios.
                </h6>
                @include('dashboard.blockchain.partials.form')
            </div>
            <div class="card-footer">
                {{ Form::submit('Salvar dados', ['class' => 'btn btn-custom']) }}
            </div>
        {{ Form::close() }}  
    </div>
@endsection