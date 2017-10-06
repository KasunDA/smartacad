@extends('admin.layout.default')

@section('title', 'Assessments Subject Details')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="#">Assessments Details</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-academic_year">Subjects Assessment Details</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-7 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-book font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Subject Assessment Info.</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover">
                            <tr>
                                <th> Academic Term </th>
                                <td> {{ $subject->academicTerm()->first()->academic_term }} </td>
                            </tr>
                            <tr>
                                <th> Subject </th>
                                <td> {{ $subject->subject()->first()->subject }} </td>
                            </tr>
                            <tr>
                                <th> Class Room </th>
                                <td> {{ $subject->classRoom()->first()->classroom }} </td>
                            </tr>
                            <tr>
                                <th> Class Level </th>
                                <td> {{ $subject->classRoom()->first()->classLevel()->first()->classlevel }} </td>
                            </tr>
                            <tr>
                                <th> Assessments Assigned </th>
                                <td> {{ $assessment_setup->assessment_no }} </td>
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
                        <span class="caption-subject font-green bold uppercase">Assessment Details By Subject.</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Due Date</th>
                                    <th>Weight Point</th>
                                    <th>Number</th>
                                    <th>Percentage</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($assessment_setup->assessmentSetupDetails()->count() > 0)
                                <?php $i = 1;
                                    $now = Carbon\Carbon::now('Africa/Lagos');
                                    $diff = $now->diffInDays(AcademicTerm::activeTerm()->term_ends, false);
                                ?>
                                @foreach($assessment_setup->assessmentSetupDetails()->get() as $detail)
                                    <tr class="odd gradeX">
                                        <td class="center">{{$i++}}</td>
                                        <td>{{ $detail->submission_date->format('jS M, Y') }}</td>
                                        <td>{{ $detail->weight_point }}</td>
                                        <td>{{ Assessment::formatPosition($detail->number) }}</td>
                                        <td>{{ $detail->percentage }} %</td>
                                        <td>{{ $detail->description }}</td>
                                        <td>
                                            {!! ($detail->assessments()->count() > 0 and $detail->assessments()->where('subject_classroom_id', $subject->subject_classroom_id)->count() > 0)
                                                ? (($detail->assessments()->where('subject_classroom_id', $subject->subject_classroom_id)->first()->marked == 1)
                                                    ? '<label class="label label-success">Marked</label>' : '<label class="label label-danger">Not Marked</label>')
                                                : '<label class="label label-danger">Not Marked</label>' !!}
                                        </td>
                                        <td>
                                            @if($diff < 0 && !Auth::user()->hasRole([Role::DEVELOPER, Role::SUPER_ADMIN]))
                                                <a href="{{ url('/assessments/input-scores/'.$hashIds->encode($detail->assessment_setup_detail_id).'/'.$hashIds->encode($subject->subject_classroom_id).'/View') }}" class="btn btn-link btn-xs">
                                                    <span class="fa fa-eye"></span> View Scores
                                                </a>
                                            @else
                                                @if($detail->assessments()->count() > 0 && $detail->assessments()->where('subject_classroom_id', $subject->subject_classroom_id)->count() > 0
                                                && $detail->assessments()->where('subject_classroom_id', $subject->subject_classroom_id)->first()->marked == 1)
                                                    <a href="{{ url('/assessments/input-scores/'.$hashIds->encode($detail->assessment_setup_detail_id).'/'.$hashIds->encode($subject->subject_classroom_id)) }}" class="btn btn-link btn-xs">
                                                        <span class="fa fa-edit"></span> Edit Scores
                                                    </a>
                                                @else
                                                    <a href="{{ url('/assessments/input-scores/'.$hashIds->encode($detail->assessment_setup_detail_id).'/'.$hashIds->encode($subject->subject_classroom_id)) }}" class="btn btn-link btn-xs">
                                                        <span class="fa fa-check-square"></span> Input Scores
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><th colspan="8">No Record Found</th></tr>
                            @endif
                            </tbody>
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
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/assessments"]');
        });
    </script>
@endsection
