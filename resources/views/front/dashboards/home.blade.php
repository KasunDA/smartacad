@extends('front.layout.default')

@section('page-level-css')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/pages/css/profile-2.min.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/global/plugins/morris/morris.css') }}" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Dashboard')

@section('breadcrumb')
    <li>
        <a href="{{ url('/home') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <span>Dashboard</span>
    </li>
@stop

@section('page-title')
    <h1> Dashboard / Home Page
        <small>Summary of activities</small>
    </h1>
@endsection

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
                <h4 class="widget-thumb-heading">Students</h4>
                <div class="widget-thumb-wrap">
                    <i class="widget-thumb-icon bg-blue icon-users"></i>
                    <div class="widget-thumb-body">
                        <span class="widget-thumb-subtitle">Active</span>
                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ number_format($active_students_count) }}">0</span>
                    </div>
                </div>
            </div>
            <!-- END WIDGET THUMB -->
        </div>
        <div class="col-md-4">
            <!-- BEGIN WIDGET THUMB -->
            <div class="widget-thumb widget-bg-color-white text-uppercase margin-bottom-20 bordered">
                <h4 class="widget-thumb-heading">Students</h4>
                <div class="widget-thumb-wrap">
                    <i class="widget-thumb-icon bg-red icon-users"></i>
                    <div class="widget-thumb-body">
                        <span class="widget-thumb-subtitle">Inactive</span>
                        <span class="widget-thumb-body-stat" data-counter="counterup" data-value="{{ number_format($inactive_students_count) }}">0</span>
                    </div>
                </div>
            </div>
            <!-- END WIDGET THUMB -->
        </div>
        @if(count($students) > 0)
            <div class="row">
                <div class="col-md-11">
                    <!-- BEGIN ACCORDION PORTLET-->
                    <div class="portlet box green">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-book"> </i>
                                <span class="caption-subject font-white bold uppercase">Students Result Checker for: {{ AcademicTerm::activeTerm()->academic_term }} Academic Term.</span>
                            </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse"> </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="panel-group accordion scrollable" id="accordion1">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1_0">
                                                Check results for {{ count($students) }} Students in: {{ AcademicTerm::activeTerm()->academic_term }} Academic Term
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_1_0" class="panel-collapse in">
                                        <div class="panel-body" style="height:180px; overflow-y:auto;">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Student No.</th>
                                                        <th>Full Name</th>
                                                        <th>Student Status</th>
                                                        <th>Class Room</th>
                                                        <th>Result Status</th>
                                                        <th>Check Result</th>
                                                        <th>Print</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $k = 1;?>
                                                    @foreach($students as $stud)
                                                        <?php $hashed = $hashIds->encode($stud->student_id).'/'.$hashIds->encode(AcademicTerm::activeTerm()->academic_term_id);?>
                                                        <tr class="odd gradeX">
                                                            <td class="center">{{$k++}}</td>
                                                            <td>{{ $stud->student_no }}</td>
                                                            <td>{{ $stud->fullNames() }}</td>
                                                            <td>
                                                                {!! (isset($stud->status_id))
                                                                ? '<label class="label label-'.$stud->status()->first()->label.'">'.$stud->status()->first()->status.'</label>'
                                                                : '<label class="label label-danger">nil</label>'  !!}
                                                            </td>
                                                            <td>
                                                                {!! ($stud->currentClass(AcademicTerm::activeTerm()->academic_year_id))
                                                                ? $stud->currentClass(AcademicTerm::activeTerm()->academic_year_id)->classroom
                                                                : '<span class="label label-danger">nil</span>' !!}
                                                            </td>
                                                            <td>
                                                                <?php
                                                                    $re = ResultChecker::where('student_id', $stud->student_id)->where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)
                                                                        ->where(function ($query) use ($stud) {
                                                                            if($stud->currentClass(AcademicTerm::activeTerm()->academic_year_id))
                                                                                $query->where('classroom_id', $stud->currentClass(AcademicTerm::activeTerm()->academic_year_id)->classroom_id);
                                                                        })->count();
                                                                ?>
                                                                {!! ($re > 0) ? '<small class="label label-success">Activated</small>' : '<small class="label label-danger">Not Activated</small>' !!}
                                                            </td>
                                                            <td><button class="btn btn-link check-result" rel="view" value="{{$hashed}}"> <i class="fa fa-bookmark"></i> Proceed</button></td>
                                                            <td><button class="btn btn-link check-result" rel="print" value="{{$hashed}}"> <i class="fa fa-print"></i> Print</button></td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END ACCORDION PORTLET-->
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <!-- BEGIN ACCORDION PORTLET-->
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-book"> </i>
                                <span class="caption-subject font-white bold uppercase">Students Subjects offered for: {{ AcademicTerm::activeTerm()->academic_term }} Academic Term.</span>
                            </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse"> </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="panel-group accordion scrollable" id="accordion1">
                                <?php $i = 1;?>
                                @foreach($students as $student)
                                    <?php $collapse = ($i == 1) ? 'in' : 'collapse'; ?>
                                    <?php
                                        $j = 1;
                                        $subjects = $student->subjectClassRooms()->where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)->get();
                                    ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1_{{$i}}">
                                                    ({{$i}}) {{ $student->fullNames() }}
                                                    {{ ($student->currentClass(AcademicTerm::activeTerm()->academic_year_id)) ? 'in:' . $student->currentClass(AcademicTerm::activeTerm()->academic_year_id)->classroom : '' }}
                                                    {{ ' || ' . count($subjects) . ' Subjects' }}
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_1_{{$i++}}" class="panel-collapse {{ $collapse }}">
                                            <div class="panel-body" style="height:300px; overflow-y:auto;">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-bordered table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Subject Name</th>
                                                            <th>Tutor</th>
                                                            <th>Exam Status</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($subjects as $subject)
                                                            <tr class="odd gradeX">
                                                                <td class="center">{{$j++}}</td>
                                                                <td>{{ $subject->subject->subject }}</td>
                                                                <td>{!! (isset($subject->tutor_id)) ? $subject->tutor->fullNames() : '<span class="label label-danger">nil</span>' !!}</td>
                                                                <td>{!! ($subject->exam_status_id == 1) ? '<span class="label label-success">Marked</span>' : '<span class="label label-danger">Unmarked</span>' !!}</td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- END ACCORDION PORTLET-->
                </div>
            </div>
        @endif
    </div>
    <!-- modal -->
    @include('front.layout.partials.result-checker')
    <!-- /.modal -->
<!-- END CONTENT BODY -->
@endsection

@section('page-level-js')

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
    <script src="{{ asset('assets/custom/js/front/exam.js') }}" type="text/javascript"></script>

    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/home"]');

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });
        });
    </script>
@endsection
