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
        @if(count($unmarked) > 0)
            <div class="row">
                <div class="col-md-10">
                    <!-- BEGIN CHART PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-book font-green"></i>
                                <span class="caption-subject font-red bold uppercase">Assessments Outstanding (Unmarked) for {{ AcademicTerm::activeTerm()->academic_term }} Academic Year.</span>
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
                                            <th>No.</th>
                                            <th>Due Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($unmarked as $assessment)
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
        @if(count($marked) > 0)
            <div class="row">
                <div class="col-md-10">
                    <!-- BEGIN ACCORDION PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-book"> </i>
                                <span class="caption-subject font-white bold uppercase">
                                    Assessments Marked for {{ AcademicTerm::activeTerm()->academic_term }} Academic Year.
                                </span>
                            </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse"> </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="panel-group accordion scrollable" id="accordion1">
                                <?php $i = 1;?>
                                @foreach($marked as $mark)
                                    <?php $collapse = ($i == 1) ? 'in' : 'collapse'; ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1_{{$i}}">
                                                    ({{$i}}) {{ $mark->subject }}: {{ AcademicTerm::activeTerm()->academic_term }}
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_1_{{$i++}}" class="panel-collapse {{ $collapse }}">
                                            <div class="panel-body" style="height:200px; overflow-y:auto;">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-bordered table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Class Room</th>
                                                            <th>Description</th>
                                                            <th>No.</th>
                                                            <th>Due Date</th>
                                                        </tr>
                                                        </thead>
                                                        <tfoot>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Class Room</th>
                                                            <th>Description</th>
                                                            <th>No.</th>
                                                            <th>Due Date</th>
                                                        </tr>
                                                        </tfoot>
                                                        <tbody>
                                                        <?php
                                                        $j = 1;
                                                        $assessments = DB::table('subjects_assessmentsviews')
                                                                ->select('subject', 'classroom', 'academic_term', 'description', 'number', 'submission_date')
                                                                ->where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)
                                                                ->where('tutor_id', $mark->tutor_id)
                                                                ->where('subject_id', $mark->subject_id)
                                                                ->where('marked', 1)
                                                                ->get();
                                                        ?>
                                                        @foreach($assessments as $assessment)
                                                            <tr class="odd gradeX">
                                                                <td class="center">{{$j++}}</td>
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
            setTabActive('[href="/dashboard"]');

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });

            ChartsStudentGender.initPieCharts();
            ChartsAmcharts.init('{{ Auth::user()->user_id }}');
        });
    </script>
@endsection
