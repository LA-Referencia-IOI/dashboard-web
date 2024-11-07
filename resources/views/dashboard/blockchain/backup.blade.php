@extends('adminlte::page')

@section('title', 'Backup dARK Network')

@section('content_header')
    <h5>
        Welcome {{ \Auth::user()->name }} || Today: {{ now()->format('d/m/Y') }}
    </h5>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet/dist/leaflet.css" />
@endsection
@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif
    <a href="{{ route('blockchains.create') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">NEW BACKUP</a>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>Nº backups<sup style="font-size: 20px"></sup> </h3>
                    <p>{{$totalFiles}}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fw fa-database"></i>
                </div>
            </div>
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>Last Update<sup style="font-size: 20px"></sup> </h3>

                    <p>{{$lastModified}}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fw  fa-spinner"></i>
                </div>
            </div>
            <div class="small-box bg-purple">
                <div class="inner"> 
                    <h3>Used Memory<sup style="font-size: 20px"> </sup> </h3>

                    <p>{{$folderSizeMB}} Gb</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fw fa-archive"></i>
                </div>
            </div>
        </div>
    </div>
@stop

