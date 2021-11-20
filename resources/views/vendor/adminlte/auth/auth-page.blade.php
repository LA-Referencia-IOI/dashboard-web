@extends('adminlte::master')

@php($dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home'))

@if (config('adminlte.use_route_url', false))
    @php($dashboard_url = $dashboard_url ? route($dashboard_url) : '')
@else
    @php($dashboard_url = $dashboard_url ? url($dashboard_url) : '')
@endif

@section('adminlte_css')
    @stack('css')
    @yield('css')
    <link href="{{ asset('vendor/custom/auth-page.css') }}" rel="stylesheet">
@stop

{{-- @section('classes_body'){{ ($auth_type ?? 'login') . '-page' }}@stop --}}

@section('body')
    <main class="d-flex align-items-center min-vh-100 py-3 py-md-0">
        <div class="container">
            <div class="card login-card">
                <div class="row no-gutters">
                    @yield('auth_body')
                </div>
            </div>
        </div>
    </main>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
