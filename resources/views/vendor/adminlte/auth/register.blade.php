@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@php($login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login'))
@php($register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register'))

@if (config('adminlte.use_route_url', false))
    @php($login_url = $login_url ? route($login_url) : '')
    @php($register_url = $register_url ? route($register_url) : '')
@else
    @php($login_url = $login_url ? url($login_url) : '')
    @php($register_url = $register_url ? url($register_url) : '')
@endif

@section('auth_header', __('adminlte::adminlte.register_message'))

@section('css')
    <style>
        body {
            background-color: {{ config('seed.register_background_color') }};
        }

    </style>
@endsection

@section('auth_body')
    <div class="col-md-5">
        <img src="{{ asset('vendor/custom/images/register.jpg') }}" alt="login" class="login-card-img">
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
            <form action="{{ $register_url }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="name" class="sr-only">{{ __('adminlte::adminlte.full_name') }}</label>
                    <input type="text" name="name" class="form-control @error('name')mb-0 is-invalid @enderror"
                        value="{{ old('name') }}" placeholder="{{ __('adminlte::adminlte.full_name') }}" autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email" class="sr-only">{{ __('adminlte::adminlte.email') }}</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-4">
                    <label for="password" class="sr-only">Password</label>
                    <input type="password" name="password" class="form-control @error('password')mb-0 is-invalid @enderror"
                        placeholder="{{ __('adminlte::adminlte.password') }}">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-4">
                    <label for="password" class="sr-only">{{ __('adminlte::adminlte.retype_password') }}</label>
                    <input type="password" name="password_confirmation"
                        class="form-control @error('password_confirmation')mb-0 is-invalid @enderror"
                        placeholder="{{ __('adminlte::adminlte.retype_password') }}">
                    @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <button type=submit
                    class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-user-plus"></span>
                    {{ __('adminlte::adminlte.register') }}
                </button>
            </form>
            <a href="{{ $login_url }}"
                class="forgot-password-link">{{ __('adminlte::adminlte.i_already_have_a_membership') }}</a>
            <p class="login-card-footer-text">
                <a href="{{ $register_url }}" class="text-reset">
                    {{ __('adminlte::adminlte.register_a_new_membership') }}
                </a>
            </p>
            @include('vendor.adminlte.auth.footer-nav')
        </div>
    </div>
@stop
