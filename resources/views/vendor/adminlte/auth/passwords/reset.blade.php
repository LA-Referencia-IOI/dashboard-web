@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@php($password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset'))

@if (config('adminlte.use_route_url', false))
    @php($password_reset_url = $password_reset_url ? route($password_reset_url) : '')
@else
    @php($password_reset_url = $password_reset_url ? url($password_reset_url) : '')
@endif

@section('auth_header', __('adminlte::adminlte.password_reset_message'))

@section('auth_body')
    {{-- <form action="{{ $password_reset_url }}" method="post">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                placeholder="{{ trans('adminlte::adminlte.retype_password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-sync-alt"></span>
            {{ __('adminlte::adminlte.reset_password') }}
        </button>

    </form> --}}
    <div class="col-md-5">
        <img src="{{ asset('assets/css/images/reset.jpg') }}" alt="login" class="login-card-img">
    </div>
    <div class="col-md-7">
        <div class="card-body">
            <div class="brand-wrapper">
                <div class="{{ $auth_type ?? 'login' }}-logo">
                    <img src="{{ asset(config('adminlte.logo_img')) }}" class="logo">
                    {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
                </div>
            </div>
            <p class="login-card-description text-center">Informe sua nova senha</p>
            <form action="{{ $password_reset_url }}" method="post">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
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
                <div class="form-group mb-4">
                    <label for="password_confirmation">
                        {{ __('adminlte::adminlte.retype_password') }}
                        @error('password_confirmation')
                            <span class="invalid-feedback d-inline" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        class="form-control @error('password_confirmation')mb-0 is-invalid @enderror"
                        placeholder="{{ __('adminlte::adminlte.retype_password') }}">
                </div>
                <button type=submit
                    class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sync-alt"></span>
                    {{ __('adminlte::adminlte.reset_password') }}
                </button>
            </form>
            @include('vendor.adminlte.auth.footer-nav')
        </div>
    </div>
@stop
