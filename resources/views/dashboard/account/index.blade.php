@extends('adminlte::page')

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif
    <a href="{{ route('accounts.create') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">NEW ACCOUNT</a>
    <div class="box">
        <div class="box-header with-border">
            <h4 class="box-title">accounts List</h4>
        </div>
        <div class="box-body no-padding">
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Naan</th>
                            <th>Shoulder</th>
                            <th>Balance</th>
                            <th>Wallet</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            <tr>
                                <td>{{ $account->organization_name }}</td>
                                <td>{{ $account->contact_email }}</td>
                                <td>{{ $account->naan }}</td>
                                <td>{{ $account->shoulder }}</td>
                                <td>{{ $account->balance }}</td>
                                <td>{{ $account->wallet }}</td>
                                <td>
                                    <button alt="Delete" title="Delete" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $account->id }}, '{{ route('accounts.destroy', ['account' => $account->id]) }}')"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer clearfix">
              {{ ($accounts != null)? $accounts->links(): null }}
        </div>
    </div>
@stop