@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    <div class="wrapper">

        {{-- Top Navbar --}}
        @if ($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if (!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty

        {{-- Footer --}}
        @hasSection('footer')
            @include('adminlte::partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if (config('adminlte.right_sidebar'))
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>
    {{ Form::open(['method' => 'DELETE']) }}
    <div class="modal fade" id="modal-notification" tabindex="-1" role="dialog" aria-labelledby="modal-notification"
        aria-hidden="true">
        <div class="modal-dialog modal-center modal-notification modal-dialog-centered modal-" role="document">
            <div class="modal-content bg-gradient-danger">
                <div class="modal-body">
                    <div class="py-3 text-center">
                        <i class="fa fa-bell fa-3x"></i>
                        <h4 class="heading mt-4">Deseja excluir este item?</h4>
                        <p>Atenção! Essa ação não poderá ser desfeita.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Sim, quero excluir!</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">NÃO</button>
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}

    <div class="modal fade" id="modal-alert" tabindex="-1" role="dialog" aria-labelledby="modal-alert"
        aria-hidden="true">
        <div class="modal-dialog modal-center modal-notification modal-dialog-centered modal-" role="document">
            <div class="modal-content bg-gradient-danger">
                <div class="modal-body">
                    <div class="py-3 text-center">
                        <i class="fa fa-bell fa-3x"></i>
                        <h4 class="heading mt-4 title"></h4>
                        <p class="message font-18"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-name" data-dismiss="modal"></button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
