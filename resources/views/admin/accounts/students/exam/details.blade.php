@extends('admin.layout.default')

@section('layout-style')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Student Exams Details')

@section('breadcrumb')
    <li>
        <i class="fa fa-home"></i>
        <a href="{{ url('/dashboard') }}">Home</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <i class="fa fa-money"></i>
        <a href="{{ url('/exams') }}">Exams</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <span>Exams Details</span>
    </li>
@stop

@section('content')
    <h3 class="page-title">Student Profile | Exams Records Details</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.student-nav', ['active' => 'exam'])
                <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-11 margin-bottom-10">
                    <!-- BEGIN SAMPLE TABLE PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-cart-plus font-green"></i>
                                <span class="caption-subject font-green bold uppercase">
                                    Exams Information
                                    <a target="_blank" href="{{ url('/exams/print-student-terminal-result/'.$hashIds->encode($student->student_id).
                                        '/'.$hashIds->encode($term->academic_term_id)) }}" class="btn btn-link btn-xs">
                                        <span class="fa fa-print fa-2x"></span> Print
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-scrollable">
                                <table class="table table-hover table-striped">
                                    <tr>
                                        <th> Academic Term </th>
                                        <td> {{ $term->academic_term }} </td>
                                        <th> Class Room </th>
                                        <td> {{ $classroom->classroom }} </td>
                                    </tr>
                                    <tr>
                                        <th> Gender </th>
                                        <td> {{ $student->gender }} </td>
                                        <th> Student Status </th>
                                        <td>
                                            @if($student->status_id)
                                                <label class="label label-{{$student->status()->first()->label}}">{{ $student->status()->first()->status }}</label>
                                            @else
                                                {{ LabelHelper::danger() }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if(isset($position->student_sum_total))
                                        <tr>
                                            <th> Student Score (%) </th>
                                            <td>{!! ($position->exam_perfect_score > 0)
                                                ? number_format((($position->student_sum_total / $position->exam_perfect_score) * 100), 2) . '%'
                                                : '-' !!}
                                            <th> Perfect Score </th>
                                            <td> {{ $position->exam_perfect_score }} </td>
                                        </tr>
                                        <tr>
                                            <th> Class Average </th>
                                            <td>{{ $position->class_average }}</td>
                                            <th> Class Position </th>
                                            <td>{{ Assessment::formatPosition($position->class_position) }}</td>
                                        </tr>
                                        <tr>
                                            <th> No. in Class </th>
                                            <td>{{ $position->class_size }} Students</td>
                                            <th>Class Level</th>
                                            <td> {{ $classroom->classLevel->classlevel }} </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END SAMPLE TABLE PORTLET-->
                </div>
                <div class="col-md-12">
                    <div class="portlet light ">
                        @if(isset($position->student_sum_total))
                            <div class="portlet-title tabbable-line">
                                <div class="caption caption-md">
                                    <i class="icon-globe theme-font hide"></i>
                                    <span class="caption-subject font-blue-madison bold uppercase">
                                        Student Exams Details
                                        <a target="_blank" href="{{ url('/exams/print-student-terminal-result/'.$hashIds->encode($student->student_id).
                                            '/'.$hashIds->encode($term->academic_term_id)) }}" class="btn btn-link btn-xs">
                                            <span class="fa fa-print fa-2x"></span> Print
                                        </a>
                                    </span>
                                </div>
                            </div>
                            <div class="portlet-body">
                            <div class="portlet sale-summary">
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
                                            <?php $h = 1; $subIds = []; ?>
                                            @if($groups->count() > 0)
                                                @foreach($groups as $group)
                                                    <?php $cas = $examss = $total = $count = 0;?>
                                                    @foreach($group->getImmediateDescendants() as $subject)
                                                        @foreach($exams as $exam)
                                                            @if($subject->subject_id == $exam->subject_id)
                                                                <?php
                                                                $cas += $exam->ca;
                                                                $examss += $exam->exam;
                                                                $total += ($exam->ca + $exam->exam);
                                                                $count++;
                                                                $subIds[] = $exam->subject_id;
                                                                ?>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                    <?php
                                                    $c = ($count > 0) ? ($cas / $count) : 0;
                                                    $e = ($count > 0) ? ($examss / $count) : 0;
                                                    $to = ($count > 0) ? ($c + $e) : 0;
                                                    $grade = $classroom->classLevel()->first()->classGroup()->first()
                                                            ->grades()->where('lower_bound', '<=', ($to))->where('upper_bound', '>=', ($to))->first();
                                                    ?>
                                                    <tr>
                                                        <td class="text-center">{{$h++}} </td>
                                                        <td>{{ $group->name }}</td>
                                                        <td>{{ number_format($c, 1) }}</td>
                                                        <td>{{ number_format($e, 1) }}</td>
                                                        <td>{{ number_format($to, 1) }}</td>
                                                        <td>{!! ($grade) ? $grade->grade : '<span class="label label-danger">nil</span>' !!}</td>
                                                        <td>{!! ($grade) ? $grade->grade_abbr : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            @if(count($exams) > 0)
                                                @foreach($exams as $exam)
                                                    @if(!in_array($exam->subject_id, $subIds))
                                                        <?php
                                                        $total = $exam->ca + $exam->exam;
                                                        $grade = $classroom->classLevel()->first()->classGroup()->first()
                                                                ->grades()->where('lower_bound', '<=', ($total))->where('upper_bound', '>=', ($total))->first();
                                                        ?>
                                                        <tr>
                                                            <td class="text-center">{{$h++}}</td>
                                                            <td>{{ $exam->subjectClassroom->subject->subject }}</td>
                                                            <td>{!! ($exam->ca) ? number_format($exam->ca, 1) : '<span class="label label-danger">nil</span>' !!}</td>
                                                            <td>{!! ($exam->exam) ? number_format($exam->exam, 1) : '<span class="label label-danger">nil</span>' !!}</td>
                                                            <td>{!! ($total) ? number_format($total, 1) : '<span class="label label-danger">nil</span>' !!}</td>
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
                        @else
                            <div class="portlet-title tabbable-line">
                                <div class="caption caption-md">
                                    <i class="icon-globe theme-font hide"></i>
                                    <span class="caption-subject font-red bold uppercase">
                                        No Exam Result found for {{ $term->academic_term }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
@endsection
@section('layout-script')
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/students"]');
        });
    </script>
@endsection
