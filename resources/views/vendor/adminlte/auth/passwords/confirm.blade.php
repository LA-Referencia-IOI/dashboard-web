@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@php($password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset'))
@php($dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home'))

@if (config('adminlte.use_route_url', false))
    @php($password_reset_url = $password_reset_url ? route($password_reset_url) : '')
    @php($dashboard_url = $dashboard_url ? route($dashboard_url) : '')
@else
    @php($password_reset_url = $password_reset_url ? url($password_reset_url) : '')
    @php($dashboard_url = $dashboard_url ? url($dashboard_url) : '')
@endif

@section('css')
    <style>
        body {
            background-color: {{ config('seed.lock_background_color') }};
        }

    </style>
@endsection

@section('auth_body')
    <div class="col-md-5">
        <img src="{{ asset('vendor/custom/images/lock.jpg') }}" alt="login" class="login-card-img">
    </div>
    <div class="col-md-7">
        <div class="card-body">
            <div class="brand-wrapper">
                <div class="{{ $auth_type ?? 'login' }}-logo">
                    <img src="{{ asset(config('adminlte.logo_img')) }}" class="logo">
                    {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
                </div>
            </div>
            <p class="login-card-description text-center">
                {{ isset(Auth::user()->name) ? Auth::user()->name : Auth::user()->email }}</p>
            <p class="login-card-description text-center text-muted">Informe sua senha para entrar</p>
            <form action="{{ route('password.confirm') }}" method="post">
                @csrf
                <div class="form-group mb-4">
                    <label for="password">
                        {{ __('adminlte::adminlte.password') }}
                        @error('password')
                            <span class="invalid-feedback d-inline" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </label>
                    <input id="password" type="password" name="password"
                        class="form-control @error('password')mb-0 is-invalid @enderror"
                        placeholder="{{ __('adminlte::adminlte.password') }}">
                </div>
                <button type=submit
                    class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-arrow-right text-muted"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </form>
            @include('vendor.adminlte.auth.footer-nav')
        </div>
    </div>
@stop
