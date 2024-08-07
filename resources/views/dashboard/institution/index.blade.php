@extends('adminlte::page')

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif
    <a href="{{ route('institutions.create') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">NEW INSTITUTION</a>
    <div class="box">
    <div class="box-header with-border">
        <div class="box-header with-border">
            <h4 class="box-title">Institutions List</h4>
        </div>
        <div class="box-body no-padding">
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Responsible</th>
                            <th>Nº nodes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($institutions as $institution)
                            <tr>
                                <td>{{ $institution->id }}</td>
                                <td>{{ $institution->name }}</td>
                                <td>{{ $institution->getTypeAlias() }}</td>
                                <td>{{ $institution->email }}</td>
                                <td>{{ $institution->responsible }}</td>
                                <td>{{ $institution->numberNodes }}</td>
                                <td>{!! $institution->getStatus() !!}</td>
                                <td>
                                @if (!$institution->code && $institution->typeNodes == 0 || $institution->typeNodes == 1)
                                    <a href="{{ route('institutions.add_id_blockchain', $institution->id) }}" alt="key dARK" title="key dARK" class="btn btn-success btn-sm"><i class="fa  fa-key"></i></a>
                                @endif 
                                    <a href="{{ route('institutions.edit', $institution->id) }}" alt="Edit" title="Edit" class="btn btn-primary btn-sm"><i class="fa fa-pencil-alt"></i></a>
                                    <button alt="Delete" title="Delete" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $institution->id }}, '{{ route('institutions.destroy', ['institution' => $institution->id]) }}')"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer clearfix">
              {{ ($institutions != null)? $institutions->links(): null }}
        </div>
    </div>
@stop