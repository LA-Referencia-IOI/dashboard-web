@extends('adminlte::page')

@section('title', 'Institutions')

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('message') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Institutions <span class="badge badge-secondary">{{ $total }}</span></h4>
        <a href="{{ route('authorities.index') }}" class="btn btn-default btn-sm">
            <i class="fas fa-shield-alt mr-1"></i> Manage via Authorities
        </a>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <form method="GET" action="{{ route('institutions.index') }}" class="d-flex">
                <input type="text" name="s" value="{{ $s }}" class="form-control form-control-sm mr-2"
                       placeholder="Search by name..." style="max-width:260px">
                <button class="btn btn-sm btn-default">Search</button>
            </form>
        </div>
        <div class="box-body no-padding">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Authority</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Responsible</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($institutions as $institution)
                            <tr>
                                <td>{{ $institution->id }}</td>
                                <td><strong>{{ $institution->name }}</strong></td>
                                <td>
                                    @if ($institution->authority)
                                        <a href="{{ route('authorities.show', $institution->authority_id) }}">
                                            {{ $institution->authority->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $institution->getTypeAlias() }}</td>
                                <td>{{ $institution->email }}</td>
                                <td>{{ $institution->responsible }}</td>
                                <td>{!! $institution->getStatus() !!}</td>
                                <td>
                                    <a href="{{ route('institutions.create-ark-institution', $institution->id) }}"
                                       class="btn btn-warning btn-sm" title="ARK">
                                        <i class="fas fa-ship"></i>
                                    </a>
                                    <a href="{{ route('institutions.edit', $institution->id) }}"
                                       class="btn btn-primary btn-sm" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No institutions found.
                                    <a href="{{ route('authorities.index') }}">Create via an Authority</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer clearfix">
            {{ $institutions->links() }}
        </div>
    </div>
@stop