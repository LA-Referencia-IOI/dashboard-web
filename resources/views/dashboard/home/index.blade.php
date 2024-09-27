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
            <div class="small-box bg-purple">
            <div class="inner">
                <h3>Instituition<sup style="font-size: 20px"></sup> </h3>
                <p>{!!$countInstitutions!!}</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw fa-building"></i>
            </div>
            <a href="{{route('institutions.index')}}" class="small-box-footer">See <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-purple">
            <div class="inner">
                <h3>dARKs<sup style="font-size: 20px"> pids</sup> </h3>

                <p>{!!$numberDark!!}</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw  fa-flag-checkered"></i>
            </div>
            <a href="{{route('blockchains.index')}}" class="small-box-footer">see <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-purple">
            <div class="inner">
                <h3>Block<sup style="font-size: 20px"> last</sup> </h3>

                <p>{!!$blockNumber!!}</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw fa-spinner"></i>
            </div>
            <a href="{{route('blockchains.index')}}" class="small-box-footer">see <i class="fas fa-arrow-circle-right"></i></a>
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
    const latitude = '{{ isset($latitude) ? $latitude : -15.8040207 }}';
    const longitude = '{{ isset($longitude) ? $longitude : -47.8857621 }}';
    
    var map = L.map('mapid').setView([latitude, longitude], 11);

    var circle = L.circle([latitude, longitude], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.5,
        radius: 300
    }).addTo(map);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© Version 0.2 dARK',
        maxZoom: 7,
    }).addTo(map);

    L.marker([latitude, longitude]).addTo(map);

    L.marker([latitude, longitude]).addTo(map)
        .bindPopup("<b>IBICT</b>: <small>Instituto Brasileiro de Informação em Ciência e Tecnologia </small> ").openPopup();

    const locations = @json($locations);
    console.log(locations);

    for (var i = 0; i < locations.length; i++) {
        console.log(locations[i]["latitude"]);
        L.marker([locations[i]["latitude"], locations[i]["longitude"]]).addTo(map)
        .bindPopup("<b>Institution: </b>"+ locations[i]["name"] +"\n"+ "<b>Resp: </b>"+ locations[i]["responsible"]+ "\n" +
          "<b>Email: </b>"+ locations[i]["email"]+"\n"+ locations[i]["typeNodes"]+"\n").openPopup();

        var circle = L.circle([locations[i]["latitude"],locations[i]["longitude"]], {
              color: 'red',
              fillColor: '#f03',
              fillOpacity: 0.5,
              radius: 300 // raio em metros
        }).addTo(map);
    }
</script>
@endpush
