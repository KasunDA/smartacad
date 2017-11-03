@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/morris/morris.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Profile Dashboard')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/profiles') }}">My Profile</a>
        <i class="fa fa-users"></i>
    </li>
    <li>
        <span>Profile Dashboard</span>
    </li>
@stop



@section('content')
    <h3 class="page-title">Profile | Dashboard</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.profile-nav', ['active' => 'dashboard'])
        <!-- END BEGIN PROFILE SIDEBAR -->
        <div class="profile-content">
            <div class="row widget-row">
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN CHART PORTLET-->
                        <div class="portlet light bordered">
                            @if($user->subjectClassRooms()->where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)->count() > 0)
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-book font-green-haze"></i>
                                        <span class="caption-subject bold uppercase font-green-haze"> Subjects Assigned</span>
                                        <span class="caption-helper"> in {{ AcademicTerm::activeTerm()->academic_term }} Academic Year</span>
                                    </div>
                                    <div class="tools">
                                        <a href="javascript:;" class="collapse"> </a>
                                        <a href="javascript:;" class="fullscreen"> </a>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="subject_tutor" class="chart" style="height: 450px;"> </div>
                                </div>
                            @else
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-book font-red-haze"></i>
                                        <span class="caption-subject bold uppercase font-red-haze">No Subjects Assigned</span>
                                        <span class="caption-helper"> in {{ AcademicTerm::activeTerm()->academic_term }} Academic Year</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <!-- END CHART PORTLET-->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <!-- BEGIN CHART PORTLET-->
                        <div class="portlet light bordered">
                            @if($user->classMasters()->where('academic_year_id', AcademicYear::activeYear()->academic_year_id)->count() > 0)
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-book font-green-haze"></i>
                                        <span class="caption-subject bold uppercase font-green-haze"> Class Rooms Assigned</span>
                                        <span class="caption-helper"> in {{ AcademicYear::activeYear()->academic_year }} Academic Year: as Class Teacher</span>
                                    </div>
                                    <div class="tools">
                                        <a href="javascript:;" class="collapse"> </a>
                                        <a href="javascript:;" class="fullscreen"> </a>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="class_teacher" class="chart" style="height: 450px;"> </div>
                                </div>
                            @else
                                <div class="caption">
                                    <i class="fa fa-book font-red-haze"></i>
                                    <span class="caption-subject bold uppercase font-red-haze"> No Class Rooms Assigned</span>
                                    <span class="caption-helper"> in {{ AcademicYear::activeYear()->academic_year }} Academic Year: as Class Teacher</span>
                                </div>
                            @endif
                        </div>
                        <!-- END CHART PORTLET-->
                    </div>
                </div>
            </div>
        </div>
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
    <script src="{{ asset('assets/custom/js/dashboards/dashboard.js') }}" type="text/javascript"></script>

    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/profiles"]');

            ChartsAmcharts.init('{{ $user->user_id }}');
        });
    </script>
@endsection
