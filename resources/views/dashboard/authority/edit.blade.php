@extends('adminlte::page')

@section('title', 'Edit Authority Details')

@section('css')
    @include('dashboard.authority.partials.design')
@stop

@section('content')
<div class="authority-page">
    <nav class="authority-breadcrumb"><a href="{{ route('authorities.show', $authority->id) }}">{{ $authority->name }}</a> <span class="mx-2">/</span> Edit details</nav>
    <header class="authority-page-heading"><h1>Edit authority details</h1><p>Update the name and contact information for {{ $authority->name }}.</p></header>
    <div class="row">
        <div class="col-lg-8 col-sm-12">
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
                <div class="authority-panel">
                    <div class="authority-panel-heading d-block">
                        <h2>
                            <i class="fas fa-shield-alt mr-2"></i> General information
                        </h2>
                        <small class="text-muted">UUID: {{ $authority->id }}</small>
                    </div>
                    <div class="authority-panel-body">
                        @include('dashboard.authority.partials.form')
                    </div>
                    <div class="authority-panel-footer">
                        {{ Form::submit('Save Changes', ['class' => 'btn btn-custom']) }}
                        <a href="{{ route('authorities.show', $authority->id) }}" class="btn btn-default ml-2">Cancel</a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop
