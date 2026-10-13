@extends('adminlte::page')

@section('title', 'Authorities')

@section('css')
    @include('dashboard.partials.registry-design')
@stop

@section('content')
<div class="authority-page">
    <nav class="authority-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a> <span class="mx-2">/</span> Authorities</nav>
    <header class="authority-page-heading"><h1>Authorities</h1><p>Manage authority details, NAAN permissions and Minter access.</p></header>
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif

    <div class="authority-panel">
        <div class="authority-panel-heading">
            <span class="authority-section-icon" aria-hidden="true"><i class="fas fa-building"></i></span>
            <div><h2>Registered authorities <span class="badge badge-light ml-2">{{ $total }}</span></h2><p>Open an authority to manage its permissions, public key and balance.</p></div>
        </div>
        <div class="authority-panel-body">
            <a href="{{ route('authorities.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> New authority</a>
        </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Responsible</th>
                            <th>NAANs</th>
                            <th>Wallet Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($authorities as $authority)
                            <tr>
                                <td><a href="{{ route('authorities.show', $authority->id) }}"><strong>{{ $authority->name }}</strong></a></td>
                                <td>{{ $authority->responsible }}</td>
                                <td class="auth-naans" data-uuid="{{ $authority->id }}">
                                    @if($authority->isRegistered())
                                        <i class="fas fa-spinner fa-spin text-muted"></i>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="auth-balance" data-uuid="{{ $authority->id }}">
                                    @if($authority->isRegistered())
                                        <i class="fas fa-spinner fa-spin text-muted"></i>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{!! $authority->getStatusBadge() !!}</td>
                                <td>
                                    <a href="{{ route('authorities.show', $authority->id) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No authorities registered yet.
                                    <a href="{{ route('authorities.create') }}">Create the first one</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        <div class="authority-panel-footer">
            {{ $authorities->links() }}
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    const authorityApiDataUrl = @json(route('authorities.apiData', ['authority' => '__AUTHORITY__']));
    const authorityUrl = function(template, uuid) { return template.replace('__AUTHORITY__', uuid); };

    $(document).ready(function() {
        $('.auth-naans').each(function() {
            let tdNaans = $(this);
            let uuid = tdNaans.data('uuid');
            let tdBalance = $('.auth-balance[data-uuid="' + uuid + '"]');

            // Only fetch if it shows a spinner (meaning it's registered)
            if (tdNaans.find('.fa-spinner').length > 0) {
                fetchApiData(uuid, tdNaans, tdBalance);
            }
        });

        function fetchApiData(uuid, tdNaans, tdBalance) {
            $.ajax({
                url: authorityUrl(authorityApiDataUrl, uuid),
                type: 'GET',
                success: function(response) {
                    if (response.error) {
                        tdNaans.html('<span class="text-danger" title="' + response.error + '">?</span>');
                        tdBalance.html('<span class="text-muted">' + response.balance + ' <i class="fas fa-exclamation-triangle text-warning" title="' + response.error + '"></i></span>');
                    } else {
                        tdNaans.html('<span class="badge badge-dark">' + response.naans_count + '</span>');
                        tdBalance.html('<strong>' + response.balance + '</strong>');
                    }
                },
                error: function() {
                    tdNaans.html('<span class="text-danger">Error</span>');
                    tdBalance.html('<span class="text-danger">Error</span>');
                }
            });
        }
    });
</script>
@stop
