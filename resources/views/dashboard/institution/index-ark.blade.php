@extends('adminlte::page')

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif
    <a href="{{ route('institutions.create-ark') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">NEW ARK REGISTRATION</a>
    <a href="{{ route('institutions.export-ark-txt') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">EXPORT TXT</a>

    <div class="box">
    <div class="box-header with-border">
        <div class="box-header with-border">
            <h4 class="box-title">Institutions registrated List </h4>
        </div>
        <div class="box-body no-padding">
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>Who</th>
                            <th>What</th>
                            <th>When</th>
                            <th>Where</th>
                            <th>How</th>
                            <th>Why</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($arks as $ark)
                            <tr>
                                <td>{{ $ark->who }}</td>
                                <td>{{ $ark->what }}</td>
                                <td>{{ $ark->when }}</td>
                                <td>{{ $ark->where }}</td>
                                <td>{{ $ark->how }}</td>
                                <td>{{ $ark->why }}</td>
                                <td>{{ $ark->contact }}</td>
                                <td>{{ $ark->address }}</td>
                                <td>                        
                                    <button alt="Delete" title="Delete" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $ark->id }}, '{{ route('institutions.destroy-ark', ['ark' => $ark->id]) }}')"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer clearfix">
              {{ ($arks != null)? $arks->links(): null }}
        </div>
    </div>
@stop