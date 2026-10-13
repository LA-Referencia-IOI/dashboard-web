@extends('adminlte::page')

@section('title', 'Create NAAN')

@section('css')
    @include('dashboard.partials.registry-design')
@stop

@section('content')
<div class="authority-page">
    <nav class="authority-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('naans.index') }}">NAANs</a> <span class="mx-2">/</span> New NAAN</nav>
    <header class="authority-page-heading"><h1>New NAAN</h1><p>Register a namespace and its organization contact.</p></header>
    <div class="row">
        <div class="col-lg-9">
            <div class="authority-panel">
                <div class="authority-panel-heading">
                    <span class="authority-section-icon" aria-hidden="true"><i class="fas fa-hashtag"></i></span>
                    <div><h2>Namespace details</h2><p>Organization, contact and registration information.</p></div>
                </div>
                <form action="{{ route('naans.store') }}" method="POST">
                    @csrf
                    @include('dashboard.naan.partials.form')
                </form>
            </div>
        </div>
    </div>
</div>
@stop
