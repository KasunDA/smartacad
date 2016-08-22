@extends('admin.layout.default')

@section('layout-style')
    <link href="../assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Staff Dashboard')

@section('breadcrumb')
    <li>
        <a href="/">Dashboard</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <div class="row widget-row" style="margin-top: 30px;">
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
        @if(Auth::user()->classMasters()->where('academic_year_id', AcademicYear::activeYear()->academic_year_id)->count() > 0)
            <div class="row">
                <div class="col-md-6">
                    <!-- BEGIN CHART PORTLET-->
                    <div class="portlet light bordered">
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
                    </div>
                    <!-- END CHART PORTLET-->
                </div>
            </div>
        @endif
        <?php $j = 1; ?>
        @if(count($assessments) > 0)
            <div class="row">
                <div class="col-md-10">
                    <!-- BEGIN CHART PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-book font-green"></i>
                                <span class="caption-subject font-red bold uppercase">Assessments Outstanding (Unmarked) for {{ AcademicTerm::activeTerm()->academic_term }} Academic Term.</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-responsive">
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
                    <!-- END CHART PORTLET-->
                </div>
            </div>
        @endif
        @if(Auth::user()->assessments()->where('marked', 1)->count() > 0)
            <div class="row">
                <div class="col-md-10">
                    <!-- BEGIN CHART PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-bar-chart font-green"></i>
                                <span class="caption-subject font-green bold uppercase">Assessments Marked for {{ AcademicTerm::activeTerm()->academic_term }} Academic Term.</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Academic Term</th>
                                        <th>Subject Name</th>
                                        <th>Class Room</th>
                                        <th>Description</th>
                                        <th>No.</th>
                                        <th>Due Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1; ?>
                                    @foreach(Auth::user()->assessments()->where('marked', 1)->get() as $assessment)
                                        <tr class="odd gradeX">
                                            <td class="center">{{$i++}}</td>
                                            <td>{{ $assessment->subjectClassroom->academicTerm->academic_term }}</td>
                                            <td>{{ $assessment->subjectClassroom->subject->subject }}</td>
                                            <td>{{ $assessment->subjectClassroom->classRoom->classroom }}</td>
                                            <td>{{ $assessment->assessmentSetupDetail->description }}</td>
                                            <td>{{ Assessment::formatPosition($assessment->assessmentSetupDetail->number) }}</td>
                                            <td>{{ $assessment->assessmentSetupDetail->submission_date->format('jS M, Y') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
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
