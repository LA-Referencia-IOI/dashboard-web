@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php($login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login'))
@php($register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register'))
@php($password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset'))

@if (config('adminlte.use_route_url', false))
    @php($login_url = $login_url ? route($login_url) : '')
    @php($register_url = $register_url ? route($register_url) : '')
    @php($password_reset_url = $password_reset_url ? route($password_reset_url) : '')
@else
    @php($login_url = $login_url ? url($login_url) : '')
    @php($register_url = $register_url ? url($register_url) : '')
    @php($password_reset_url = $password_reset_url ? url($password_reset_url) : '')
@endif

@section('auth_header', __('adminlte::adminlte.login_message'))

@section('css')
    <style>
        body {
            background-color: {{ config('seed.login_background_color') }};
        }

    </style>
@endsection

@section('auth_body')
    <div class="col-md-5">
        <img src="{{ asset('assets/css/images/login.jpg') }}" alt="login" class="login-card-img">
    </div>
    <div class="col-md-7">
        <div class="card-body">
            <div class="brand-wrapper">
                <div class="{{ $auth_type ?? 'login' }}-logo">
                    <img src="{{ asset(config('adminlte.logo_img')) }}" class="logo">
                    {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
                </div>
            </div>
            <p class="login-card-description text-center">Informe suas credências para entrar</p>
            <form action="{{ $login_url }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="email">{{ __('adminlte::adminlte.email') }}
                        @error('email')
                            <span class="invalid-feedback d-inline" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </label>
                    <input id="email" type="email" name="email"
                        class="form-control @error('email')mb-0 is-invalid @enderror" value="{{ old('email') }}"
                        placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>
                </div>
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
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </form>
            <a href="{{ $password_reset_url }}"
                class="forgot-password-link">{{ __('adminlte::adminlte.i_forgot_my_password') }}</a>
            <p class="login-card-footer-text">
                <a href="{{ $register_url }}" class="text-reset">
                    {{ __('adminlte::adminlte.register_a_new_membership') }}
                </a>
            </p>
            @include('vendor.adminlte.auth.footer-nav')
        </div>
    </div>
@stop
