@extends('admin.layout.default')

@section('title', 'Exam Input Scores')

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
                                <td> {{ $subject->tutor()->first()->fullNames() }} </td>
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
                    {!! Form::open([
                            'method'=>'POST',
                            'class'=>'form',
                            'role'=>'form',
                            'id'=>'scores-form'
                        ])
                    !!}
                    <div class="table-responsive">
                        {!! Form::hidden('weight_point', $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->exam_weight_point, ['class'=>'form-control', 'id'=>'weight_point']) !!}
                        {!! Form::hidden('exam_id', $exam->exam_id, ['class'=>'form-control']) !!}
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Student Number</th>
                                <th>Student Name</th>
                                <th>Gender</th>
                                <th>C.A Score ({{ $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->ca_weight_point }})</th>
                                <th>Exam Score ({{ $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->exam_weight_point }})</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($exam)
                                @if($exam->examDetails()->count() > 0)
                                    <?php $i = 1; ?>
                                    @foreach($exam->examDetails()->get() as $detail)
                                        <tr class="odd gradeX">
                                            <td class="center">{{$i++}}</td>
                                            <td>{{ $detail->student()->first()->student_no }}</td>
                                            <td>{{ $detail->student()->first()->fullNames() }}</td>
                                            <td>
                                                {{ $detail->student()->first()->gender }}
                                                {!! Form::hidden('exam_detail_id[]', $detail->exam_detail_id, ['class'=>'form-control']) !!}
                                            </td>
                                            <td>
                                                {{ $detail->ca }}
                                            </td>
                                            <td>
                                                {!! Form::text('exam[]', $detail->exam, ['class'=>'form-control scores', 'size'=>4, 'required'=>'required']) !!}
                                                <span></span>
                                            </td>
                                        </tr>
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
                                <th>C.A Score ({{ $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->ca_weight_point }})</th>
                                <th>Exam Score ({{ $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->exam_weight_point }})</th>
                            </tr>
                            </tfoot>
                        </table>
                        <div class="form-actions noborder">
                            <button type="submit" class="btn blue pull-right">Save Scores</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END CONTENT BODY -->
    @endsection


    @section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/assessments/exam.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/exams"]');
        });
    </script>
@endsection
