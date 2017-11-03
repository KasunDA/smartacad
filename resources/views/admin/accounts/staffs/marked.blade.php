@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Staff Subjects Marked')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/staffs') }}">Staffs</a>
        <i class="fa fa-users"></i>
    </li>
    <li>
        <span>Staff Dashboard</span>
    </li>
@stop



@section('content')
    <h3 class="page-title">Staff Profile | Marked Subjects</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.staff-nav', ['active' => 'marked'])
        <!-- END BEGIN PROFILE SIDEBAR -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN ACCORDION PORTLET-->
                    <div class="portlet box green">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-book"> </i>
                                <span class="caption-subject font-white bold uppercase">
                                    Assessments Marked for {{ AcademicTerm::activeTerm()->academic_term }} Academic Year.
                                </span>
                            </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse"> </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="panel-group accordion scrollable" id="accordion1">
                                <?php $i = 1;?>
                                @foreach($marked as $mark)
                                    <?php $collapse = ($i == 1) ? 'in' : 'collapse'; ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1_{{$i}}">
                                                    ({{$i}}) {{ $mark->subject }}: {{ AcademicTerm::activeTerm()->academic_term }}
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_1_{{$i++}}" class="panel-collapse {{ $collapse }}">
                                            <div class="panel-body" style="height:200px; overflow-y:auto;">
                                                <div class="table-responsive">
                                                    <table class="table table-hover table-bordered table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Class Room</th>
                                                            <th>Description</th>
                                                            <th>No.</th>
                                                            <th>Due Date</th>
                                                        </tr>
                                                        </thead>
                                                        <tfoot>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Class Room</th>
                                                            <th>Description</th>
                                                            <th>No.</th>
                                                            <th>Due Date</th>
                                                        </tr>
                                                        </tfoot>
                                                        <tbody>
                                                        <?php
                                                        $j = 1;
                                                        $assessments = DB::table('subjects_assessmentsviews')
                                                                ->select('subject', 'classroom', 'academic_term', 'description', 'number', 'submission_date')
                                                                ->where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)
                                                                ->where('tutor_id', $mark->tutor_id)
                                                                ->where('subject_id', $mark->subject_id)
                                                                ->where('marked', 1)
                                                                ->get();
                                                        ?>
                                                        @foreach($assessments as $assessment)
                                                            <tr class="odd gradeX">
                                                                <td class="center">{{$j++}}</td>
                                                                <td>{{ $assessment->classroom }}</td>
                                                                <td>{!! (isset($assessment->description)) ? $assessment->description : '<span class="label label-danger">nill</span>' !!}</td>
                                                                <td>{!! (isset($assessment->number)) ? Assessment::formatPosition($assessment->number) : '<span class="label label-danger">nil</span>' !!}</td>
                                                                <td>{!! (isset($assessment->submission_date)) ? $assessment->submission_date : '<span class="label label-danger">nill</span>' !!}</td>
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
                            @if(count($marked) == 0)
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                         No Assessment marked yet
                                    </h4>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- END ACCORDION PORTLET-->
                </div>
            </div>
        </div>
    </div>

@endsection


@section('layout-script')
            <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/staffs"]');

        });
    </script>
@endsection
