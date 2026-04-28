@extends('adminlte::page')

@section('title', 'Authorities')

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Authorities <span class="badge badge-secondary">{{ $total }}</span></h4>
        <a href="{{ route('authorities.create') }}" class="btn btn-custom btn-sm">
            <i class="fas fa-plus mr-1"></i> NEW AUTHORITY
        </a>
    </div>

    <div class="box">
        <div class="box-body no-padding">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:260px">UUID</th>
                            <th>Name</th>
                            <th>Responsible</th>
                            <th>Email</th>
                            <th>Institutions</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($authorities as $authority)
                            <tr>
                                <td><small class="text-muted">{{ $authority->id }}</small></td>
                                <td><strong>{{ $authority->name }}</strong></td>
                                <td>{{ $authority->responsible }}</td>
                                <td>{{ $authority->email }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $authority->institutions_count }}</span>
                                </td>
                                <td>{!! $authority->getStatusBadge() !!}</td>
                                <td>{{ $authority->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('authorities.show', $authority->id) }}"
                                       class="btn btn-secondary btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('authorities.edit', $authority->id) }}"
                                       class="btn btn-primary btn-sm" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm" title="Delete"
                                        onclick="confirmDelete('{{ $authority->id }}', '{{ route('authorities.destroy', $authority->id) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No authorities registered yet.
                                    <a href="{{ route('authorities.create') }}">Create the first one</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer clearfix">
            {{ $authorities->links() }}
        </div>
    </div>

    @include('dashboard.partials.confirm-delete')
@stop
