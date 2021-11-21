@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@php($password_email_url = View::getSection('password_email_url') ?? config('adminlte.password_email_url', 'password/email'))
@php($login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login'))

@if (config('adminlte.use_route_url', false))
    @php($login_url = $login_url ? route($login_url) : '')
    @php($password_email_url = $password_email_url ? route($password_email_url) : '')
@else
    @php($login_url = $login_url ? url($login_url) : '')
    @php($password_email_url = $password_email_url ? url($password_email_url) : '')
@endif

@section('auth_header', __('adminlte::adminlte.password_reset_message'))

@section('css')
    <style>
        body {
            background-color: {{ config('seed.reset_background_color') }};
        }

    </style>
@endsection

@section('auth_body')
    <div class="col-md-5">
        <img src="{{ asset('assets/images/reset.jpg') }}" alt="login" class="login-card-img">
    </div>
    <div class="col-md-7">
        <div class="card-body">
            <div class="brand-wrapper">
                <div class="{{ $auth_type ?? 'login' }}-logo">
                    <img src="{{ asset(config('adminlte.logo_img')) }}" class="logo">
                    {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
                </div>
            </div>
            <p class="login-card-description text-center">Informe o seu e-mail para redefinir a sua senha</p>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ $password_email_url }}" method="post">
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
                <button type="submit"
                    class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-share-square"></span>
                    {{ __('adminlte::adminlte.send_password_reset_link') }}
                </button>
            </form>
            <a href="{{ $login_url }}" class="forgot-password-link">{{ __('adminlte::adminlte.sign_in') }}</a>
            @include('vendor.adminlte.auth.footer-nav')
        </div>
    </div>
@stop
