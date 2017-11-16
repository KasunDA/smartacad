@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/pages/css/error.css') }}" rel="stylesheet" type="text/css"/>

    <link href="{{ asset('assets/global/css/components.css') }}" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/css/plugins.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/layouts/layout/css/layout.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/layouts/layout/css/themes/light2.min.css') }}" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="{{ asset('assets/layouts/layout/css/custom.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Error:')

@section('breadcrumb')
    <li>
        <i class="fa fa-home"></i>
        <a href="/">Home</a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="/dashboard">Dashboard</a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <a href="#">Error: {{ $code }}</a>
    </li>
@endsection

@section('content')
    <div class="page-title">
        <h2><span class="fa fa-warning"></span> {{ $header }}</h2>
    </div>

    <!-- BEGIN PAGE CONTENT-->
    <div class="row">
        <div class="col-md-12 page-500">
            <div class="number">
                {{ $code }}
            </div>
            <div class="details">
                <h3>Oops! {{ $header }}</h3>
                <p>
                    {!! $message !!}
                </p>

            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT-->

    @endsection

    @section('layout-script')
            <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/dashboard"]');
        });
    </script>
@endsection