@extends('admin.layout.default')

@section('layout-style')
    <link href="../assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Dashboard')

@section('breadcrumb')
    <li>
        <a href="/">Dashobard</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <div class="row widget-row" style="margin-top: 30px;">
        <div class="col-md-4">
            <!-- BEGIN WIDGET THUMB -->
            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                <h4 class="widget-thumb-heading">Students</h4>
                <div class="widget-thumb-wrap">
                    <i class="widget-thumb-icon bg-green icon-user"></i>
                    <div class="widget-thumb-body">
                        <span class="widget-thumb-subtitle">Registered</span>
                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ number_format($students_count) }}">0</span>
                    </div>
                </div>
            </div>
            <!-- END WIDGET THUMB -->
        </div>
        <div class="col-md-4">
            <!-- BEGIN WIDGET THUMB -->
            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                <h4 class="widget-thumb-heading">Sponsors</h4>
                <div class="widget-thumb-wrap">
                    <i class="widget-thumb-icon bg-blue icon-users"></i>
                    <div class="widget-thumb-body">
                        <span class="widget-thumb-subtitle">Registered</span>
                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ number_format($sponsors_count) }}">0</span>
                    </div>
                </div>
            </div>
            <!-- END WIDGET THUMB -->
        </div>
        <div class="col-md-4">
            <!-- BEGIN WIDGET THUMB -->
            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                <h4 class="widget-thumb-heading">Staffs</h4>
                <div class="widget-thumb-wrap">
                    <i class="widget-thumb-icon bg-red icon-users"></i>
                    <div class="widget-thumb-body">
                        <span class="widget-thumb-subtitle">Registered</span>
                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ number_format($staff_count) }}">0</span>
                    </div>
                </div>
            </div>
            <!-- END WIDGET THUMB -->
        </div>
        <div class="col-md-5">
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Students Gender</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="student_gender" class="chart"> </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase"> Student Class Levels</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="student-classlevel" style="height:400px;"></div>
                </div>
            </div>
        </div>
        {{--<div class="row">--}}
            {{--<div class="col-md-12">--}}
                {{--<!-- BEGIN CHART PORTLET-->--}}
                {{--<div class="portlet light bordered">--}}
                    {{--<div class="portlet-title">--}}
                        {{--<div class="caption">--}}
                            {{--<i class="icon-bar-chart font-green-haze"></i>--}}
                            {{--<span class="caption-subject bold uppercase font-green-haze"> 3D Chart</span>--}}
                            {{--<span class="caption-helper">3d cylinder chart</span>--}}
                        {{--</div>--}}
                        {{--<div class="tools">--}}
                            {{--<a href="javascript:;" class="collapse"> </a>--}}
                            {{--<a href="#portlet-config" data-toggle="modal" class="config"> </a>--}}
                            {{--<a href="javascript:;" class="reload"> </a>--}}
                            {{--<a href="javascript:;" class="fullscreen"> </a>--}}
                            {{--<a href="javascript:;" class="remove"> </a>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="portlet-body">--}}
                        {{--<div id="chart_5" class="chart" style="height: 400px;"> </div>--}}
                        {{--<div class="well margin-top-20">--}}
                            {{--<div class="row">--}}
                                {{--<div class="col-sm-3">--}}
                                    {{--<label class="text-left">Top Radius:</label>--}}
                                    {{--<input class="chart_5_chart_input" data-property="topRadius" type="range" min="0" max="1.5" value="1" step="0.01" /> </div>--}}
                                {{--<div class="col-sm-3">--}}
                                    {{--<label class="text-left">Angle:</label>--}}
                                    {{--<input class="chart_5_chart_input" data-property="angle" type="range" min="0" max="89" value="30" step="1" /> </div>--}}
                                {{--<div class="col-sm-3">--}}
                                    {{--<label class="text-left">Depth:</label>--}}
                                    {{--<input class="chart_5_chart_input" data-property="depth3D" type="range" min="1" max="120" value="40" step="1" /> </div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<!-- END CHART PORTLET-->--}}
            {{--</div>--}}
        {{--</div>--}}
    </div>

@endsection


@section('layout-script')
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/plugins/flot/jquery.flot.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/flot/jquery.flot.resize.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/flot/jquery.flot.categories.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/flot/jquery.flot.pie.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('assets/global/plugins/morris/morris.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/morris/raphael-min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('assets/global/plugins/amcharts/amcharts/amcharts.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/amcharts/amcharts/serial.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/amcharts/amcharts/themes/light.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->

    <script src="{{ asset('assets/global/plugins/counterup/jquery.waypoints.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/counterup/jquery.counterup.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/dashboards/dashboard.js') }}" type="text/javascript"></script>

    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/dashboard"]');

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });
        });
    </script>
@endsection
