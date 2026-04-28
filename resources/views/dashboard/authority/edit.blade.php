@extends('adminlte::page')

@section('title', 'Edit Authority')

@section('content')
    <div class="row">
        <div class="col-md-6 col-sm-12">
            @if (session('message'))
                <div class="alert alert-{{ session('code') }} alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    {{ session('message') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>Attention!</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ Form::model($authority, ['route' => ['authorities.update', $authority->id], 'method' => 'PUT']) }}
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top:10px;">
                            <i class="fas fa-shield-alt mr-2"></i> Edit Authority
                        </h3>
                        <small class="text-muted">UUID: {{ $authority->id }}</small>
                    </div>
                    <div class="box-body">
                        @include('dashboard.authority.partials.form')
                    </div>
                    <div class="box-footer">
                        {{ Form::submit('Save Changes', ['class' => 'btn btn-custom']) }}
                        <a href="{{ route('authorities.show', $authority->id) }}" class="btn btn-default ml-2">Cancel</a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
@stop
