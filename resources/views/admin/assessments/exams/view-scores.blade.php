@extends('admin.layout.default')

@section('layout-style')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Exam Display Scores')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="#">Exams Scores</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-academic_year">Exam Detail Scores</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-7 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-book font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Examination Info.</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th> Academic Term </th>
                                <td> {{ $subject->academicTerm()->first()->academic_term }} </td>
                                <th> Subject </th>
                                <td> {{ $subject->subject()->first()->subject }} </td>
                            </tr>
                            <tr>
                                <th> Class Room </th>
                                <td> {{ $subject->classRoom()->first()->classroom }} </td>
                                <th> Class Level </th>
                                <td> {{ $subject->classRoom()->first()->classLevel()->first()->classlevel }} </td>
                            </tr>
                            <tr>
                                <th> Weight Point C.A </th>
                                <td> {{ $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->ca_weight_point }} </td>
                                <th> Weight Point Exam </th>
                                <td> {{ $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->exam_weight_point }} </td>
                            </tr>
                            <tr>
                                <th> Marked Status </th>
                                <td> {!! ($exam->marked == 1) ? '<span class="label label-success">Marked</span>' : '<span class="label label-danger">Not Marked</span>' !!} </td>
                                <th> Tutor </th>
                                <td> {!! (isset($subject->tutor_id)) ? $subject->tutor()->first()->fullNames() : '<span class="label label-danger">nil</span>' !!} </td>
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
                        <span class="caption-subject font-green bold uppercase">Exam Scores By Subject.</span>
                    </div>
                </div>
                <div id="error-div"></div>
                <div class="portlet-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="scores_datatable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Student Number</th>
                                <th>Student Name</th>
                                <th>Gender</th>
                                <th>C.A ({{ $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->ca_weight_point }})</th>
                                <th>Exam ({{ $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->exam_weight_point }})</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($exam)
                                @if($exam->examDetails()->count() > 0)
                                    <?php $i = 1; ?>
                                    @foreach($exam->examDetails()->get() as $detail)
                                        @if($detail->student()->first())
                                            <tr class="odd gradeX">
                                                <td class="center">{{$i++}}</td>
                                                <td>{{ $detail->student()->first()->student_no }}</td>
                                                <td>{{ $detail->student()->first()->fullNames() }}</td>
                                                <td>{!! ($detail->student()->first()) ? $detail->student()->first()->gender : '<span class="label label-danger">nil</span>' !!}</td>
                                                <td>{{ $detail->ca }}</td>
                                                <td>{{ $detail->exam }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr><th colspan="6">No Record Found</th></tr>
                                @endif
                            @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Student Number</th>
                                <th>Student Name</th>
                                <th>Gender</th>
                                <th>C.A ({{ $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->ca_weight_point }})</th>
                                <th>Exam ({{ $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->exam_weight_point }})</th>
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
