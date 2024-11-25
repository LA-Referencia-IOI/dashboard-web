@extends('adminlte::page')

@section('content')
    <div class="row">
        <div class="col-md-6 col-sm-12 col-lg-6">
            @if (session('message'))
                <div class="alert alert-{{ session('code') }} alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('message') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Attention!</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @isset($isPatientRecord)
                {{ Form::open(['route' => 'patients.store-ark', 'role' => 'form', 'class' => 'areyousure']) }}
            @else
                {{ Form::open(['route' => 'institutions.store-ark', 'role' => 'form', 'class' => 'areyousure']) }}
            @endisset
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 10px;">
                    
                        </h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h6 class="heading-small text-muted mb-4">
                            Attention!
                            <br>
                            <br>
                            All fields are mandatory.
                        </h6>
                        @include('dashboard.institution.partials.form-ark')
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        {{ Form::submit('Save Data', ['class' => 'btn btn-custom']) }}
                    </div>
                </div>
                <!-- /.box -->
            {{ Form::close() }}
        </div>
        <!-- /.col-md-8 -->
    </div>
    <!-- /.row -->
@stop