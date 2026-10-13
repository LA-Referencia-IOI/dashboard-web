@extends('adminlte::page')

@section('title', 'Add NAANs — ' . $authority->name)

@section('css')
    @include('dashboard.authority.partials.design')
@stop

@section('content')
<div class="authority-page">
    <header class="authority-page-heading"><h1>Add NAANs</h1><p>Select registered namespaces for {{ $authority->name }}.</p></header>
    @if(session('message'))
        <div class="alert alert-{{ session('code') }}">{{ session('message') }}</div>
    @endif
    <a href="{{ route('authorities.show', $authority->id) }}" class="d-inline-block mb-3">&larr; Back to {{ $authority->name }}</a>
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif
    <div class="authority-panel">
        <div class="authority-panel-heading"><span class="authority-section-icon"><i class="fas fa-hashtag"></i></span><div><h2>Registered NAANs</h2><p>Choose one or more namespaces from the registry.</p></div></div>
        <div class="authority-panel-body">
            <p class="text-muted mb-0">Select NAANs from the registry to assign to this authority.</p>
        </div>
                    <form action="{{ route('authorities.syncNaans', $authority->id) }}" method="POST">
                        @csrf
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">Select</th>
                                        <th>NAAN</th>
                                        <th>Organization</th>
                                        <th>Contact</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($availableNaans as $naan)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="naans[]" value="{{ $naan->id }}">
                                            </td>
                                            <td><strong>{{ $naan->naan }}</strong></td>
                                            <td>{{ $naan->organization_name }}</td>
                                            <td>{{ $naan->contact_name }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No registered NAANs available to assign. Register a NAAN in the NAAN registry first.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($availableNaans->count() > 0)
                            <div class="p-3 bg-light text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-link mr-1"></i> Add selected NAANs
                                </button>
                            </div>
                        @endif
                    </form>
        <div class="authority-panel-footer">
            <a href="{{ route('authorities.show', $authority->id) }}" class="btn btn-default btn-sm">Cancel</a>
        </div>
    </div>
</div>
@stop
