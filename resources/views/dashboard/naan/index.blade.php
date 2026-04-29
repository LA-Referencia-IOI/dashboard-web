@extends('adminlte::page')

@section('title', 'NAANs')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">NAANs</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Home</a></li>
                <li class="breadcrumb-item active">NAANs</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">List of NAANs</h3>
                    <div class="card-tools">
                        <a href="{{ route('naans.create') }}" class="btn btn-dark btn-sm">
                            <i class="fas fa-plus"></i> New NAAN
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
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
                                    <td>{{ $naan->naan }}</td>
                                    <td>{{ $naan->organization_name }}</td>
                                    <td>{{ $naan->organization_acronym }}</td>
                                    <td>{{ $naan->contact_name }}</td>
                                    <td>
                                        @if($naan->status == 'active')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($naan->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('naans.edit', $naan->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('naans.destroy', $naan->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this NAAN?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No NAANs found.</td>
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
@stop
