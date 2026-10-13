@extends('adminlte::page')

@section('title', 'NAANs')

@section('css')
    @include('dashboard.partials.registry-design')
@stop

@section('content')
<div class="authority-page">
    <nav class="authority-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a> <span class="mx-2">/</span> NAANs</nav>
    <header class="authority-page-heading"><h1>NAAN registry</h1><p>Manage registered namespaces and their organization details.</p></header>
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                    {{ session('success') }}
                </div>
            @endif

            <div class="authority-panel">
                <div class="authority-panel-heading">
                    <span class="authority-section-icon" aria-hidden="true"><i class="fas fa-hashtag"></i></span>
                    <div><h2>Registered NAANs</h2><p>Namespaces available for assignment to authorities.</p></div>
                </div>
                <div class="authority-panel-body">
                    <a href="{{ route('naans.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> New NAAN</a>
                </div>
                <!-- /.card-header -->
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>NAAN</th>
                                <th>Organization</th>
                                <th>Acronym</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($naans as $naan)
                                <tr>
                                    <td><a href="{{ route('naans.edit', $naan->id) }}"><strong>{{ $naan->naan }}</strong></a></td>
                                    <td>{{ $naan->organization_name }}</td>
                                    <td>{{ $naan->organization_acronym ?: '—' }}</td>
                                    <td>{{ $naan->contact_name ?: '—' }}</td>
                                    <td>
                                        @if($naan->status == 'active')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($naan->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('naans.edit', $naan->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('naans.destroy', $naan->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this NAAN?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No NAANs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@stop
