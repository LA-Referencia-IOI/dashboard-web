@extends('adminlte::page')

@section('title', 'Edit Institution')

@section('content')
    <div class="col-md-7 col-sm-12">
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

        {{ Form::model($institution, ['route' => ['institutions.update', $institution->id], 'method' => 'PUT']) }}
            {{-- authority_id must always be preserved --}}
            {{ Form::hidden('authority_id', $institution->authority_id) }}

            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title" style="margin-top:10px;">
                        <i class="fas fa-university mr-2"></i> Edit Institution
                    </h3>
                    @if ($institution->authority)
                        <div class="box-tools">
                            <small class="text-muted">
                                Authority: <strong>{{ $institution->authority->name }}</strong>
                            </small>
                        </div>
                    @endif
                </div>
                <div class="box-body">
                    @include('dashboard.institution.partials.form')
                </div>
                <div class="box-footer">
                    {{ Form::submit('Save Changes', ['class' => 'btn btn-custom']) }}
                    <a href="{{ route('authorities.show', $institution->authority_id) }}" class="btn btn-default ml-2">Cancel</a>
                </div>
            </div>
        {{ Form::close() }}
    </div>
@endsection