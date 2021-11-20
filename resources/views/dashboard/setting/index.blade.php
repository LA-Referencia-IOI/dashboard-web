@extends('adminlte::page')

@section('content')
    <div class="row pt-4">
        <div class="col-md-4 col-sm-12 col-lg-4">
            <div class="card card-widget widget-user-2">
                <div class="widget-user-header bg-secondary">
                    <div class="widget-user-image">
                        <img class="img-circle" src="{{ Auth::user()->getProfilePicture() }}"
                            alt="{{ Auth::user()->name }}" title="{{ Auth::user()->name }}">
                    </div>
                    <h3 class="widget-user-username">{{ Auth::user()->name }}</h3>
                    <h5 class="widget-user-desc">{{ Auth::user()->getProfileDescriptionAttribute() }}</h5>
                </div>
                <div class="card-footer p-0">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                E-mail <span class="float-right">{{ Auth::user()->email }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                Último acesso <span class="float-right">{{ Auth::user()->last_login_at }}</span>
                            </a>
                        </li>
                    </ul>
                    {{-- <div class="row">
                        {{ Form::model(Auth::user(), ['route' => ['image.upload', Auth::user()->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'frmUploadImage']) }}
                        {{ Form::hidden('model', 'User') }}
                        {{ Form::hidden('userId', Auth::user()->id) }}
                        {{ Form::hidden('highlight', 1) }}
                        <div class="custom-file">
                            <input type="file" style="display: none" class="custom-file-input"
                                onchange="uploadImage('#frmUploadImage')" name="file" id="fileUser"
                                accept="image/x-png,image/jpeg">
                            <div class="{{ Auth::user()->getIdFromProfilePicture() ? 'col-md-9' : 'col-md-12' }}">
                                <label class="btn btn-primary btn-block" for="fileUser" data-toggle="tooltip"
                                    data-placement="left" title="Resolução ideal: 800x800">Escolher imagem do
                                    perfil</label>
                            </div>
                            <div class="col-md-3">
                                @if (Auth::user()->getIdFromProfilePicture())
                                    <label class="btn btn-danger btn-block" data-toggle="tooltip" data-placement="right"
                                        title="Excluir imagem"
                                        onclick="confirmDelete({{ Auth::user()->id }}, '{{ route('image.destroy', ['id' => Auth::user()->getIdFromProfilePicture()]) }}', 'User')"><i
                                            class="fa fa-trash"></i></label>
                                @endif
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="col-md-8 col-sm-12 col-lg-8">
            @if (session('message'))
                <div class="alert alert-{{ session('code') }} alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('message') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Atenção!</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Informações do Perfil</h3>
                </div>
                {{ Form::model(Auth::user(), ['route' => ['settings.update', Auth::user()->id], 'method' => 'PUT', 'role' => 'form']) }}
                <div class="card-body">
                    @include('dashboard.user.partials.form', [
                    'showPasswordTip' => true
                    ])
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-secondary">Salvar dados</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
