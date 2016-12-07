@extends('admin.layout.default')

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
                        <span class="caption-subject font-green bold uppercase">
                            Student Details
                            <a target="_blank" href="{{ url('/exams/print-student-terminal-result/'.$hashIds->encode($student->student_id).
                                '/'.$hashIds->encode($term->academic_term_id)) }}" class="btn btn-link btn-xs">
                                <span class="fa fa-print fa-3x"></span> Print
                            </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th> Student Name </th>
                                <td>{{ $student->fullNames() }}</td>
                                <th> Academic Term </th>
                                <td> {{ $term->academic_term }} </td>
                            </tr>
                            <tr>
                                <th> Student No. </th>
                                <td>
                                    <a target="_blank" href="{{ url('/students/view/'.$hashIds->encode($student->student_id)) }}" class="btn btn-link btn-xs">
                                        {{ $student->student_no }}
                                    </a>
                                </td>
                                <th> Gender </th>
                                <td> {{ $student->gender }} </td>
                            </tr>
                            <tr>
                                <th> Class Room </th>
                                <td> {{ $classroom->classroom }} </td>
                                <th> Class Level </th>
                                <td> {{ $classroom->classLevel()->first()->classlevel }} </td>
                            </tr>
                            @if(isset($position->student_sum_total))
                                <tr>
                                    <th> Student Total Score </th>
                                    <td> {{ $position->student_sum_total }}</td>
                                    <th> Assessment Perfect Score </th>
                                    <td> {{ $position->exam_perfect_score }} </td>
                                </tr>
                                <tr>
                                    <th> Class Average </th>
                                    <td>{{ $position->class_average }}</td>
                                    <th></th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th> Number of Students (Out of) </th>
                                    <td>{{ $position->class_size }}</td>
                                    <th></th><td></td>
                                    <!-- TODO:: Wrong class position-->
                                    {{--<th> Class Position </th>--}}
                                    {{--<td>{{ Assessment::formatPosition($position->class_position) }}</td>--}}
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>

        <?php $domain = $student->domainAssessment()->where('academic_term_id', $term->academic_term_id);?>
        @if($domain->count() > 0)
            <div class="col-md-4 margin-bottom-10">
                <table class="table table-bordered">
                    <thead>
                    <tr style="font-weight:bold; background-color:#CCCCCC; !important;">
                        <th width="50%">Affective Domains</th>
                        <th width="10%">5</th>
                        <th width="10%">4</th>
                        <th width="10%">3</th>
                        <th width="10%">2</th>
                        <th width="10%">1</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($domain->first()->domainDetails()->get() as $detail)
                        <tr>
                            <td width="50%" style="background-color: #F2F0F0 !important; font-size: 11px;">{{ $detail->domain()->first()->domain }}:</td>
                            {!! ($detail->option == 5) ? '<td width="10%" style="background-color:#F2F0F0 !important;"></td>' : '<td></td>'  !!}
                            {!! ($detail->option == 4) ? '<td width="10%" style="background-color:#F2F0F0 !important;"></td>' : '<td></td>' !!}
                            {!! ($detail->option == 3) ? '<td width="10%" style="background-color:#F2F0F0 !important;"></td>' : '<td></td>' !!}
                            {!! ($detail->option == 2) ? '<td width="10%" style="background-color:#F2F0F0 !important;"></td>' : '<td></td>' !!}
                            {!! ($detail->option == 1) ? '<td width="10%" style="background-color:#F2F0F0 !important;"></td>' : '<td></td>' !!}
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <div class="col-md-10 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gears font-green"></i>
                        <span class="caption-subject font-green bold uppercase">
                            Assessments Details By Subject.
                            <a target="_blank" href="{{ url('/exams/print-student-terminal-result/'.$hashIds->encode($student->student_id).
                                '/'.$hashIds->encode($term->academic_term_id)) }}" class="btn btn-link btn-xs">
                                <span class="fa fa-print fa-3x"></span> Print
                            </a>
                        </span>
                    </div>
                </div>
                <div id="error-div"></div>
                <div class="portlet-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th colspan="2"></th>
                                    <th class="center" colspan="3">Student Scores</th>
                                    <th class="center" colspan="2">Grades</th>
                                </tr>
                                <tr>
                                    <th>#</th>
                                    <th>Subject Name</th>
                                    <th>C. A ({{$classroom->classLevel()->first()->classGroup()->first()->ca_weight_point}})</th>
                                    <th>Exam ({{$classroom->classLevel()->first()->classGroup()->first()->exam_weight_point}})</th>
                                    <th>
                                        Total ({{$classroom->classLevel()->first()->classGroup()->first()->ca_weight_point +
                                        $classroom->classLevel()->first()->classGroup()->first()->exam_weight_point}})
                                    </th>
                                    <th>Grade</th>
                                    <th>Abbr.</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($subjects->count() > 0)
                                <?php $i = 1; ?>
                                @foreach($subjects as $subjectClass)
                                    <?php
                                        $ca = ($subjectClass->examDetails()->where('student_id', $student->student_id))
                                            ? $subjectClass->examDetails()->where('student_id', $student->student_id)->first()["ca"] : null;
                                        $exam = ($subjectClass->examDetails()->where('student_id', $student->student_id))
                                            ? $subjectClass->examDetails()->where('student_id', $student->student_id)->first()["exam"] : null;
                                        $grade = $classroom->classLevel()->first()->classGroup()->first()
                                                ->grades()->where('lower_bound', '<=', ($ca+$exam))->where('upper_bound', '>=', ($ca+$exam))->first();
                                    ?>
                                    @if($exam && $subjectClass->examDetails()->where('student_id', $student->student_id)->first()->exam()->where('marked', 1)->count() > 0)
                                        <tr class="odd gradeX">
                                            <td class="center">{{$i++}}</td>
                                            <td>{{ $subjectClass->subject()->first()->subject }}</td>
                                            <td>{!! ($ca) ? number_format($ca, 1) : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{!! ($exam) ? number_format($exam, 1) : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{!! ($ca || $exam) ? number_format(($ca + $exam), 1) : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{!! ($grade) ? $grade->grade : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{!! ($grade) ? $grade->grade_abbr : '<span class="label label-danger">nil</span>' !!}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr><th colspan="7">No Record Found</th></tr>
                            @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Subject Name</th>
                                <th>C. A ({{$classroom->classLevel()->first()->classGroup()->first()->ca_weight_point}})</th>
                                <th>Exam ({{$classroom->classLevel()->first()->classGroup()->first()->exam_weight_point}})</th>
                                <th>
                                    Total ({{$classroom->classLevel()->first()->classGroup()->first()->ca_weight_point +
                                        $classroom->classLevel()->first()->classGroup()->first()->exam_weight_point}})
                                </th>
                                <th>Grade</th>
                                <th>Abbr.</th>
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
