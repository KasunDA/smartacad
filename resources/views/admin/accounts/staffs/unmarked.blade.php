@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Staff Subjects Unmarked')

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
        <span>Staff Subjects</span>
    </li>
@stop



@section('content')
    <h3 class="page-title">Staff Profile | UnMarked Subjects</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.staff-nav', ['active' => 'unmarked'])
        <!-- END BEGIN PROFILE SIDEBAR -->
        <div class="profile-content">
            <div class="row widget-row">
                <?php $j = 1; ?>
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN CHART PORTLET-->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-book font-green"></i>
                                    <span class="caption-subject font-green bold uppercase">Assessments Outstanding (Unmarked) for {{ AcademicTerm::activeTerm()->academic_term }} Academic Year.</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered table-striped" id="unmarked_table">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Academic Term</th>
                                                <th>Subject Name</th>
                                                <th>Class Room</th>
                                                <th>Description</th>
                                                <th>No.</th>
                                                <th>Due Date</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($unmarked as $assessment)
                                                <tr class="odd gradeX">
                                                    <td class="center">{{$j++}}</td>
                                                    <td>{{ $assessment->academic_term }}</td>
                                                    <td>{{ $assessment->subject }}</td>
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
                        <!-- END CHART PORTLET-->
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
@endsection


@section('layout-script')
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/staffs"]');

            setTableData($('#unmarked_table')).init();
        });
    </script>
@endsection
