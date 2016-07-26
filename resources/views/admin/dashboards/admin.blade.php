@extends('admin.layout.default')

@section('layout-style')
    <link href="../assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Admin Dashboard')

@section('breadcrumb')
    <li>
        <a href="/">Dashboard</a>
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
                    <div class="tools">
                        <a href="javascript:;" class="collapse"> </a>
                        <a href="javascript:;" class="fullscreen"> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="student_gender" class="chart"> </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <!-- BEGIN CHART PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-bar-chart font-green-haze"></i>
                        <span class="caption-subject bold uppercase font-green-haze"> Student Class Level</span>
                        <span class="caption-helper">Students in {{ AcademicYear::activeYear()->academic_year }} Academic Year</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"> </a>
                        <a href="javascript:;" class="fullscreen"> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="student_classlevel" class="chart" style="height: 400px;"> </div>
                </div>
            </div>
            <!-- END CHART PORTLET-->
        </div>
        @if(count($unmarked) > 0)
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN ACCORDION PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-book"> </i><span class="caption-subject font-white bold uppercase">Staffs Outstanding (Unmarked Assessments) for {{ AcademicTerm::activeTerm()->academic_term }} Academic Term.</span> </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse"> </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="panel-group accordion scrollable" id="accordion1">
                                <?php $i = 1;?>
                                @foreach($unmarked as $unmark)
                                    {{--@if($unmark->tutor)--}}
                                        <?php $collapse = ($i == 1) ? 'in' : 'collapse'; ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1_{{$i}}"> ({{$i}}) {{ $unmark->tutor }}:  {{ $unmark->subjects }} Unmarked</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_1_{{$i++}}" class="panel-collapse {{ $collapse }}">
                                                <div class="panel-body" style="height:200px; overflow-y:auto;">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-bordered table-striped">
                                                            <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Academic Term</th>
                                                                <th>Subject Name</th>
                                                                <th>Class Room</th>
                                                                <th>Description</th>
                                                                <th>Number</th>
                                                                <th>Due Date</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                                $j = 1;
                                                                $assessments = DB::table('subjects_assessmentsviews')
                                                                    ->select('subject', 'classroom', 'academic_term', 'description', 'number', 'submission_date')
                                                                    ->where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)
                                                                    ->where('tutor_id', $unmark->tutor_id)->where(function ($query) { $query->whereNull('assessment_id')->orWhere('marked', 2); })->get();
                                                            ?>
                                                            @foreach($assessments as $assessment)
                                                                <tr class="odd gradeX">
                                                                    <td class="center">{{$j++}}</td>
                                                                    <td>{{ $assessment->academic_term }}</td>
                                                                    <td>{{ $assessment->subject }}</td>
                                                                    <td>{{ $assessment->classroom }}</td>
                                                                    <td>{!! (isset($assessment->description)) ? $assessment->description : '<span class="label label-danger">nill</span>' !!}</td>
                                                                    <td>{!! (isset($assessment->number)) ? Assessment::formatPosition($assessment->number) : '<span class="label label-danger">nil</span>' !!}</td>
                                                                    <td>{!! (isset($assessment->submission_date)) ? $assessment->submission_date : '<span class="label label-danger">nill</span>' !!}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    {{--@endif--}}
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- END ACCORDION PORTLET-->
                </div>
            </div>
        @endif
        @if(Auth::user()->subjectClassRooms()->where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)->count() > 0)
            <div class="row">
                <div class="col-md-10">
                    <!-- BEGIN CHART PORTLET-->
                    <div class="portlet light bordered">
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
                    </div>
                    <!-- END CHART PORTLET-->
                </div>
            </div>
        @endif
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
