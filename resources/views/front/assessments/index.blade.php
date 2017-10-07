@extends('front.layout.default')

@section('title', 'Students Assessments')

@section('breadcrumb')
    <li>
        <a href="{{ url('/home') }}">Home</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/wards') }}">Students</a>
        <i class="fa fa-users"></i>
    </li>
    <li>
        <span>Assessments</span>
    </li>
@stop

@section('page-title')
    <h1> Assessments Summary</h1>
@endsection

@section('content')
    <div class="row widget-row" style="margin-top: 20px;">
        @if(count($students) > 0)
            <div class="row">
                <div class="col-md-10">
                    <!-- BEGIN ACCORDION PORTLET-->
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-user"> </i>
                                <span class="caption-subject font-white bold uppercase">Available Students</span>
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
                                        $assessments = AssessmentDetailView::orderBy('assessment_id', 'desc')
                                                ->where('student_id', $student->student_id)
                                                ->groupBy(['student_id', 'academic_term'])
                                                ->get();
                                    ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1_{{$i}}">
                                                    ({{$i}}) {{ $student->fullNames() }}
                                                    {{ ($student->currentClass(AcademicTerm::activeTerm()->academic_year_id))
                                                        ? 'in: ' . $student->currentClass(AcademicTerm::activeTerm()->academic_year_id)->classroom : ''
                                                    }}
                                                    {{ ' || Assessment Taken' }}
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
                                                                <th>Academic Term</th>
                                                                <th>Class Room</th>
                                                                <th>Weight Point</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tfoot>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Academic Term</th>
                                                                <th>Class Room</th>
                                                                <th>Weight Point</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </tfoot>
                                                        <tbody>
                                                            @foreach($assessments as $assessment)
                                                                <tr>
                                                                    <td>{{$j++}} </td>
                                                                    <td>{{ $assessment->academic_term }}</td>
                                                                    <td>{{ $assessment->classroom }}</td>
                                                                    <td>{{ $assessment->ca_weight_point }}</td>
                                                                    <td>
                                                                        <a href="{{ url('/wards-assessments/details/'.
                                                                        $hashIds->encode($student->student_id)).'/'.$hashIds->encode($assessment->academic_term_id) }}"
                                                                           target="_blank" class="btn btn-warning btn-rounded btn-condensed btn-xs">
                                                                            <span class="fa fa-eye"></span> Details
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            @if(empty($assessments))
                                                                <tr>
                                                                    <th colspan="5">No Record Found</th>
                                                                </tr>
                                                            @endif
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
        @else
            <div class="col-md-6 col-md-offset-5">
                <h2>No Record</h2>
            </div>
        @endif
    </div>
<!-- END CONTENT BODY -->
@endsection

@section('page-level-js')

@endsection

@section('layout-script')
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/wards-assessments"]');
        });
    </script>
@endsection
