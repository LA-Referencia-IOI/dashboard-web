@extends('adminlte::page')

@section('title', 'New Authority')

@section('content')
    <div class="row">
        <div class="col-md-6 col-sm-12">
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

            {{ Form::open(['route' => 'authorities.store', 'method' => 'POST']) }}
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top:10px;">
                            <i class="fas fa-shield-alt mr-2"></i> New Authority
                        </h3>
                    </div>
                    <div class="box-body">
                        <h6 class="heading-small text-muted mb-3">
                            Fields marked with <span class="text-danger">*</span> are required.
                        </h6>
                        @include('dashboard.authority.partials.form')
                    </div>
                    <div class="box-footer">
                        {{ Form::submit('Create Authority', ['class' => 'btn btn-custom']) }}
                        <a href="{{ route('authorities.index') }}" class="btn btn-default ml-2">Cancel</a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
@stop
