@extends('admin.layout.default')

@section('title', 'Student Assessments Details')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="#">Student Details</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-academic_year">Student Assessments Details</h3>
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
                            @if($setup_details)
                                <a target="_blank" href="{{ url('/assessments/print-report/'.$hashIds->encode($student->student_id).
                                    '/'.$hashIds->encode($term->academic_term_id)) }}" class="btn btn-link btn-xs">
                                    <span class="fa fa-print fa-2x"></span> Print
                                </a>
                            @endif
                        </span>
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
                                    <a target="_blank" href="{{ url('/students/view/'.$hashIds->encode($student->student_id)) }}" class="btn btn-link btn-xs sbold">
                                        <span style="font-size: 16px">{{ $student->student_no }}</span>
                                    </a>
                                </td>
                                <th> Gender </th>
                                <td> {{ $student->gender }} </td>
                            </tr>
                            <tr>
                                <th> Student Status </th>
                                <td>
                                    @if($student->status_id)
                                        <label class="label label-{{$student->status()->first()->label}}">{{ $student->status()->first()->status }}</label>
                                    @else
                                        <label class="label label-danger">nil</label>
                                    @endif
                                </td>
                                <th> Class Room </th>
                                <td> {{ $classroom->classroom }} </td>
                            </tr>
                            <tr>
                                <th> Assessments </th>
                                <td> {{ ($setup_details) ? $setup_details->count() : 0 }} </td>
                                <th> Class Level </th>
                                <td> {{ $classroom->classLevel()->first()->classlevel }} </td>
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
                @if($setup_details)
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-gears font-green"></i>
                            <span class="caption-subject font-green bold uppercase">
                                Assessments Details By Subject.
                                <a target="_blank" href="{{ url('/assessments/print-report/'.$hashIds->encode($student->student_id).
                                    '/'.$hashIds->encode($term->academic_term_id)) }}" class="btn btn-link btn-xs">
                                    <span class="fa fa-print fa-2x"></span> Print
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
                                    <th class="text-center" colspan="{{ $setup_details->count() }}">Student Scores [Points]</th>
                                    <th class="text-center">Total</th>
                                </tr>
                                <tr>
                                    <th>#</th>
                                    <th>Subject Name</th>
                                    <?php $su = 0;?>
                                    @foreach($setup_details->get() as $setup)
                                        <th>{{ Assessment::formatPosition($setup->number) }} C. A. [{{ $setup->weight_point }}]</th>
                                        <?php $su += $setup->weight_point; ?>
                                    @endforeach
                                    <th>Total ({{ $su }})</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($subjectClasses->count() > 0)
                                <?php $j = 1; ?>
                                @foreach($subjectClasses as $sub)
                                    <?php $total = 0; ?>
                                    <tr class="odd gradeX">
                                        <td class="center">{{$j++}}</td>
                                        <td>{{ $sub->subjectClassroom->subject->subject }}</td>
                                        @foreach($assessments as $assessment)
                                            <?php $check = 1; ?>
                                            @if($assessment->subject_classroom_id == $sub->subject_classroom_id)
                                                @for($i=1; $i <= $setup_details->count(); $i++)
                                                    @if($i == $assessment->number)
                                                        <td>{{$assessment->score}}</td>
                                                        <?php $total += $assessment->score; ?>
                                                    @endif
                                                    <?php $check++; ?>
                                                @endfor

                                            {{--TODO:: logical error-- @elseif($check <= $setup_details->count())--}}
                                                {{--<td><span class="label label-danger">nil</span></td>--}}
                                            @endif
                                        @endforeach
                                        <td>{!! number_format($total, 0) !!}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><th colspan="{{ $setup_details->count() + 3 }}">No Record Found</th></tr>
                            @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Subject Name</th>
                                    <?php $su = 0;?>
                                    @foreach($setup_details->get() as $setup)
                                        <th>{{ Assessment::formatPosition($setup->number) }} C. A. [{{ $setup->weight_point }}]</th>
                                        <?php $su += $setup->weight_point; ?>
                                    @endforeach
                                    <th>Total ({{ $su }})</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-globe theme-font hide"></i>
                            <span class="caption-subject font-red bold uppercase">
                                No Assessment taken for {{ $term->academic_term }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END CONTENT BODY -->
@endsection


@section('layout-script')
    <script src="{{ asset('assets/custom/js/assessments/exam.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/assessments"]');
        });
    </script>
@endsection
