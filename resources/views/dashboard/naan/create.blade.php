@extends('adminlte::page')

@section('title', 'Create NAAN')

@section('content_header')
    <h1>Create NAAN</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">NAAN Details</h3>
                </div>
                <form action="{{ route('naans.store') }}" method="POST">
                    @csrf
                    @include('dashboard.naan.partials.form')
                </form>
            </div>
        </div>
    </div>
@stop
