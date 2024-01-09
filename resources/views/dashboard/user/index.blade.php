@extends('adminlte::page')

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif
    <a href="{{ route('users.create') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">NEW USER</a>
    <div class="box">
        <div class="box-header with-border">
            <h4 class="box-title">User List</h4>
            <div class="pull-right col-md-4" style="padding: 0px;">
                {{ Form::open(['method' => 'GET', 'route' => ['users.index'], 'style' => 'display: inline']) }}
                    <div class="input-group input-group-sm">
                        <input value="{{ $s }}" type="text" class="form-control input-sm" name="s" placeholder="Enter user name snippet to search...">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-custom btn-flat"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
        <div class="box-body no-padding">
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>Access Profile</th>
                            <th>Name</th>
                            <th>Registration Date</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->getProfileDescriptionAttribute() }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{!! $user->getRegistration() !!}</td>
                                <td>{{ $user->last_login_at }}</td>
                                <td>
                                    <a href="{{ route('users.edit', $user->id) }}" alt="Edit" title="Edit" class="btn btn-primary btn-sm"><i class="fa fa-pencil-alt"></i></a>
                                    <button alt="Delete" title="Delete" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $user->id }}, '{{ route('users.destroy', ['user' => $user->id]) }}')"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer clearfix">
              {{ ($users != null)? $users->links(): null }}
        </div>
    </div>
@stop
