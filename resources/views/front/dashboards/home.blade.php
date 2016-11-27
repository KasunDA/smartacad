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
                <h4 class="widget-thumb-heading">Staffs</h4>
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
                <div class="col-md-10">
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
                                                        <th>Class Room</th>
                                                        <th>Status</th>
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
                                                                {!! ($stud->currentClass(AcademicTerm::activeTerm()->academic_year_id))
                                                                ? $stud->currentClass(AcademicTerm::activeTerm()->academic_year_id)->classroom
                                                                : '<span class="label label-danger">nil</span>' !!}
                                                            </td>
                                                            <td>
                                                                {!! (ResultChecker::where('student_id', $stud->student_id)->where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)
                                                                    ->where('classroom_id', $stud->currentClass(AcademicTerm::activeTerm()->academic_year_id)->classroom_id)->count() > 0)
                                                                ? '<small class="label label-success">Activated</small>' : '<small class="label label-danger">Not Activated</small>' !!}
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
                                                                <td>{!! ($subject->exam_status_id == 2) ? '<span class="label label-success">Marked</span>' : '<span class="label label-danger">Unmarked</span>' !!}</td>
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
    <div id="result_checker_modal" class="modal fade bs-modal-lg" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title text-center font-blue" id="manage-title-text">
                        This result need to be activated with a SCRATCH CARD PIN to view/print result<br>
                        <small class="font-red">kindly enter the scratch card <i>Serial Number and Secret PIN Number</i> or contact your school for one</small>
                    </h4>
                </div>
                <form method="POST" action="#" class="form" role="form" id="result_checker_form">
                    {!! csrf_field() !!}
                    {!! Form::hidden('student_id', '', ['id'=>'student_id']) !!}
                    {!! Form::hidden('academic_term_id', '', ['id'=>'term_id']) !!}
                    <div class="modal-body">
                        <div id="msg_box_modal"></div>
                        <div class="scroller" style="height:220px;" data-always-visible="1" data-rail-visible1="1">
                            <div class="row">
                                <div class="form-body">
                                    <div class="form-group col-md-8">
                                        <label>Serial Number: <small class="font-red">*</small></label>
                                        <input type="text" maxlength="8" class="form-control" id="serial_number" required name="serial_number" placeholder="Card Serial Number">
                                    </div>
                                    <div class="form-group last col-md-8">
                                        <label>PIN Number: <small class="font-red">*</small></label>
                                        <input type="text" maxlength="12" class="form-control" id="pin_number" required name="pin_number" placeholder="Card PIN Number">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close / Cancel</button>
                        <button type="submit" class="btn green">Proceed to Result Checking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
