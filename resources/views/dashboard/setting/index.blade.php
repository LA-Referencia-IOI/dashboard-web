@extends('adminlte::page')

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-12 col-lg-4">
            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box box-solid box-body box-profile">
                    <img class="profile-user-img img-responsive img-circle" src="{{ Auth::user()->getProfilePicture() }}"
                        alt="{{ Auth::user()->name }}" title="{{ Auth::user()->name }}">
                    <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>E-mail</b> <a class="pull-right">{{ Auth::user()->email }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Telefone</b> <a class="pull-right">{{ Auth::user()->phone }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Último acesso</b> <a class="pull-right">{{ Auth::user()->last_login_at }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Endereço</b> <a class="pull-right">{{ Auth::user()->address }}</a>
                        </li>
                    </ul>
                    <div class="row">
                        {{-- {{ Form::model(Auth::user(), ['route' => ['image.upload', Auth::user()->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'frmUploadImage']) }}
                        {{ Form::hidden('model', 'User') }}
                        {{ Form::hidden('userId', Auth::user()->id) }}
                        {{ Form::hidden('highlight', 1) }} --}}

                        <form method="POST" action="{{ route('image.upload', [Auth::user()->id]) }}"
                            accept-charset="UTF-8" enctype="multipart/form-data" id="frmUploadImage">
                            @csrf
                            @method('PUT')
                            <input name="model" type="hidden" value="User">
                            <input name="userId" type="hidden" value="{{ Auth::user()->id }}">
                            <input name="highlight" type="hidden" value="1">

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
                        </form>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-8 col-sm-12 col-lg-8">
            @if (session('message'))
                <div class="alert alert-{{ session('code') }} alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    {{ session('message') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- {{ Form::model(Auth::user(), ['route' => ['settings.update', Auth::user()->id], 'method' => 'PUT', 'class' => 'areyousure']) }} --}}

            <form method="POST" action="{{ route('settings.upload', [Auth::user()->id]) }}">
                @csrf
                @method('PUT')
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 10px;">Informações do perfil</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <h6 class="heading-small text-muted mb-4">Informações de acesso ao sistema</h6>
                        @include('dashboard.user.partials.form', [
                        'showPasswordTip' => true
                        ])
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <input class="btn" type="submit" value="Salvar dados">
                    </div>
                </div>
                <!-- /.box -->
            </form>
        </div>
        <!-- /.col-md-8 -->
    </div>
    <!-- /.row -->
@stop
