@extends('front.layout.default')

@section('layout-style')
        <!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Assessments Details')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/home') }}">Home</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="#">Assessment Details</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('page-title')
    <h1> Student Profile | Assessments Details</h1>
@endsection

@section('content')
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('front.layout.partials.student-nav', ['active' => 'assessment'])
        <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-11 margin-bottom-10">
                    <!-- BEGIN SAMPLE TABLE PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-book font-green"></i>
                        <span class="caption-subject font-green bold uppercase">
                            Student Details
                            <a target="_blank" href="{{ url('/wards-assessments/print-report/'.$hashIds->encode($student->student_id).
                                '/'.$hashIds->encode($term->academic_term_id)) }}" class="btn btn-link btn-xs">
                                <span class="fa fa-print fa-2x"></span> Print
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
                                            <a target="_blank" href="{{ url('/wards/view/'.$hashIds->encode($student->student_id)) }}" class="btn btn-link btn-xs sbold">
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
                <div class="col-md-12">
                    <div class="portlet light ">
                        @if($setup_details)
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-gears font-green"></i>
                            <span class="caption-subject font-green bold uppercase">
                                Assessments Details By Subject.
                                <a target="_blank" href="{{ url('/wards-assessments/print-report/'.$hashIds->encode($student->student_id).
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
                                                <?php $check = $total = 0; ?>
                                                <tr class="odd gradeX">
                                                    <td class="center">{{$j++}}</td>
                                                    <td>{{ $sub->subjectClassroom->subject->subject }}</td>
                                                    @foreach($assessments as $assessment)
                                                        @if($assessment->subject_classroom_id == $sub->subject_classroom_id and $check < $setup_details->count())
                                                            @for($i=1; $i <= $setup_details->count(); $i++)
                                                                @if($i == $assessment->number)
                                                                    <td>{{$assessment->score}}</td>
                                                                    <?php $total += $assessment->score; ?>
                                                                @endif
                                                            @endfor
                                                            <?php $check++; ?>
                                                        @endif
                                                    @endforeach
                                                    <td>{!! ($total) ? number_format($total, 0) : '<span class="label label-danger">nil</span>' !!}</td>
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
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY -->
@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/profile.min.js') }}" type="text/javascript"></script>
@endsection
@section('layout-script')
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/wards-assessments"]');

            setTableData($('#view_attendance_datatable')).init();
        });
    </script>
@endsection
