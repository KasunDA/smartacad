@extends('admin.layout.default')

@section('layout-style')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Terminal Student Assessments Details')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="#">Terminal Student Details</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-academic_year">Terminal Student Assessments Details</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-book font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Student Details</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th> Student Name </th>
                                <td> {{ $student->fullNames() }} </td>
                                <th> Academic Term </th>
                                <td> {{ $term->academic_term }} </td>
                            </tr>
                            <tr>
                                <th> Student No. </th>
                                <td> {{ $student->student_no }} </td>
                                <th> Gender </th>
                                <td> {{ $student->gender }} </td>
                            </tr>
                            <tr>
                                <th> Class Room </th>
                                <td> {{ $student->currentClass($term->academicYear->academic_year_id)->classroom }} </td>
                                <th> Class Level </th>
                                <td> {{ $student->currentClass($term->academicYear->academic_year_id)->classLevel()->first()->classlevel }} </td>
                            </tr>
                            <tr>
                                <th> Student Total Score </th>
                                <td></td>
                                <th> Assessment Perfect Score </th>
                                <td></td>
                            </tr>
                            <tr>
                                <th> Class Position </th>
                                <td></td>
                                <th> Number of Students (Out of) </th>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
        <div class="col-md-10 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gears font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Assessments Details By Subject.</span>
                    </div>
                </div>
                <div id="error-div"></div>
                <div class="portlet-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr>
                                <th colspan="2"></th>
                                <th class="center" colspan="3">Student Scores</th>
                                <th class="center" colspan="2">Grades</th>
                                <th class="center" colspan="2">Weight Point</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Subject Name</th>
                                <th>C. A</th>
                                <th>Exam</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Abbr.</th>
                                <th>C.A </th>
                                <th>Exam </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($subjects->count() > 0)
                                <?php $i = 1; ?>
                                @foreach($subjects as $subjectClass)
                                    <?php
                                        $ca = ($subjectClass->examDetails()->where('student_id', $student->student_id))
                                            ? $subjectClass->examDetails()->where('student_id', $student->student_id)->first()->ca : null;
                                        $exam = ($subjectClass->examDetails()->where('student_id', $student->student_id))
                                            ? $subjectClass->examDetails()->where('student_id', $student->student_id)->first()->exam : null;
                                    ?>
                                    @if($ca || $exam)
                                        <tr class="odd gradeX">
                                            <td class="center">{{$i++}}</td>
                                            <td>{{ $subjectClass->subject->subject }}</td>
                                            <td>{!! ($ca) !!}</td>
                                            <td>{!! ($exam) ? $exam : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{!! ($ca || $exam) ? ($ca + $exam) : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>
                                                {{ $student->currentClass($term->academicYear->academic_year_id)->classLevel()->first()->classGroup()->first()
                                                ->grades()->where('lower_bound', '<=', ($ca+$exam))->where('upper_bound', '>=', ($ca+$exam))->first()->grade }}
                                            </td>
                                            <td>
                                                {{ $student->currentClass($term->academicYear->academic_year_id)->classLevel()->first()->classGroup()->first()
                                                ->grades()->where('lower_bound', '<=', ($ca+$exam))->where('upper_bound', '>=', ($ca+$exam))->first()->grade_abbr }}
                                            </td>
                                            <td>{{ $student->currentClass($term->academicYear->academic_year_id)->classLevel()->first()->classGroup()->first()->ca_weight_point }}</td>
                                            <td>{{ $student->currentClass($term->academicYear->academic_year_id)->classLevel()->first()->classGroup()->first()->exam_weight_point }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr><th colspan="8">No Record Found</th></tr>
                            @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Subject Name</th>
                                <th>C. A</th>
                                <th>Exam</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Abbr.</th>
                                <th>C.A </th>
                                <th>Exam </th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END CONTENT BODY -->
    @endsection


    @section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>

    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/exams"]');
            setTableData($('#scores_datatable')).init();
        });
    </script>
@endsection
