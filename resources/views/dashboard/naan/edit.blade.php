@extends('adminlte::page')

@section('title', 'Edit NAAN')

@section('content_header')
    <h1>Edit NAAN</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">NAAN Details</h3>
                </div>
                <form action="{{ route('naans.update', $naan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('dashboard.naan.partials.form', ['naan' => $naan])
                </form>
            </div>
        </div>
    </div>
@stop
