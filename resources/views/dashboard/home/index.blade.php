@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h5>
        Welcome {{ \Auth::user()->name }} || Today: {{ now()->format('d/m/Y') }}
    </h5>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet/dist/leaflet.css" />
@endsection

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <h4>Atenção!</h4>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="far fa-comments"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">Features this dashboard</span>
                    <span class="info-box-text">
                    <ul>
                        <li>Register new institutions.</li>
                        <li>Create access keys for new institutions</li>
                        <li>Create new dARK wallets</li>
                        <li>See and load dARK balances</li>
                    </ul>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row">
        <div class="col-md-12">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="far fa-user"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">Simulation</span>
                    <span class="info-box-text">
                        In this section, you can simulate the location of an end-user device and analyze the coverage.
                    </span>
                </div>
            </div>
        </div>
    </div> -->
    <div class="row">
        <div class="col-md-12">
            <div class="info-box">
                <div class="info-box-content">
                    <div class="pad">
                        <!-- Map will be created here -->
                        <div id="mapid" style="height: 600px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/leaflet/dist/leaflet.js"></script>
<script>
    const lat = '{{ isset($lat) ? $lat : -15.8040207 }}';
    const lon = '{{ isset($lon) ? $lon : -47.8857621 }}';
    
    var map = L.map('mapid').setView([lat, lon], 11);

    var circle = L.circle([lat, lon], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.5,
        radius: 500
    }).addTo(map);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© Version 0.1 dARK',
        maxZoom: 4,
    }).addTo(map);

    L.marker([lat, lon]).addTo(map);

    L.marker([lat, lon]).addTo(map)
        .bindPopup("<b>IBICT</b>: <small>Instituto Brasileiro de Informação em Ciência e Tecnologia </small> ").openPopup();
</script>
@endpush
