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
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
            <div class="inner">
                <h3>Instituition<sup style="font-size: 20px"></sup> </h3>
                <p>3</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw fa-cubes"></i>
            </div>
            <a href="" class="small-box-footer">See <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
            <div class="inner">
                <h3>Nodes <sup style="font-size: 20px"> dARK Main</sup> </h3>

                <p>5</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw fa-battery-three-quarters"></i>
            </div>
            <a href="#" class="small-box-footer">see <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
            <div class="inner">
                <h3>Nodes <sup style="font-size: 20px">dARK private</sup> </h3>

                <p>2</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw fa-rss"></i>
            </div>
            <a href="#" class="small-box-footer">see <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
            <div class="inner">
                <h3>Errors<sup style="font-size: 20px"></sup></h3>
                <p>0</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw  fa-wrench"></i>
            </div>
            <a href="" class="small-box-footer">see <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
</div>

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
        maxZoom: 6,
    }).addTo(map);

    L.marker([lat, lon]).addTo(map);

    L.marker([lat, lon]).addTo(map)
        .bindPopup("<b>IBICT</b>: <small>Instituto Brasileiro de Informação em Ciência e Tecnologia </small> ").openPopup();
</script>
@endpush
