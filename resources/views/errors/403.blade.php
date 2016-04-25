@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/pages/css/error.css') }}" rel="stylesheet" type="text/css"/>

    <link href="{{ asset('assets/global/css/components.css') }}" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/css/plugins.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/layouts/layout/css/layout.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/layouts/layout/css/themes/light2.min.css') }}" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="{{ asset('assets/layouts/layout/css/custom.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Error: 403')

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
        <a href="#">Error: 403</a>
    </li>
@endsection

@section('content')
    <div class="page-title">
        <h2><span class="fa fa-key"></span> 403 Access Denial</h2>
    </div>

    <!-- BEGIN PAGE CONTENT-->
    <div class="row">
        <div class="col-md-12 page-500">
            <div class="number">
                403
            </div>
            <div class="details">
                <h3>Oops! Access Denial.</h3>
                <p>
                    You have no access / privilege to perform such action / operation<br/>
                    Kindly Contact your system administrator for assistance<br/>
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
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/dashboard"]');
        });
    </script>
@endsection