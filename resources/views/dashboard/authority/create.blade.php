@extends('adminlte::page')

@section('title', 'New Authority')

@section('css')
    @include('dashboard.partials.registry-design')
@stop

@section('content')
<div class="authority-page">
    <nav class="authority-breadcrumb"><a href="{{ route('authorities.index') }}">Authorities</a> <span class="mx-2">/</span> New authority</nav>
    <header class="authority-page-heading"><h1>New authority</h1><p>Register an authority and its contact details.</p></header>
    <div class="row">
        <div class="col-lg-9 col-sm-12">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>Attention!</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ Form::open(['route' => 'authorities.store', 'method' => 'POST', 'id' => 'authority-create-form']) }}
                <input type="hidden" name="creation_token" value="{{ $creationToken }}">
                <div class="authority-panel">
                    <div class="authority-panel-heading">
                        <span class="authority-section-icon"><i class="fas fa-building"></i></span>
                        <div><h2>General information</h2><p>Fields marked with an asterisk are required.</p></div>
                    </div>
                    <div class="authority-panel-body">
                        @include('dashboard.authority.partials.form')
                        @include('dashboard.authority.partials.client-key-input', ['required' => false])
                    </div>
                    <div class="authority-panel-footer">
                        {{ Form::submit('Create Authority', ['class' => 'btn btn-primary', 'id' => 'authority-create-submit']) }}
                        <p id="authority-create-progress" class="small text-muted mt-3" role="status" hidden>Creating authority and waiting for confirmation. Please keep this page open.</p>
                        <a href="{{ route('authorities.index') }}" class="btn btn-default ml-2">Cancel</a>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop

@section('js')
<script>
(function () {
    var form = document.getElementById('authority-create-form');
    var button = document.getElementById('authority-create-submit');
    var progress = document.getElementById('authority-create-progress');
    var submitting = false;
    form.addEventListener('submit', function (event) {
        if (submitting) { event.preventDefault(); return; }
        submitting = true;
        button.disabled = true;
        button.value = 'Creating authority…';
        progress.hidden = false;
        form.setAttribute('aria-busy', 'true');
    });
    window.addEventListener('pageshow', function () {
        submitting = false;
        button.disabled = false;
        button.value = 'Create Authority';
        progress.hidden = true;
        form.removeAttribute('aria-busy');
    });
})();
</script>
@stop
