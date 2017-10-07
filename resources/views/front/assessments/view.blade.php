@extends('front.layout.default')

@section('layout-style')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Student Assessments')

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
        <span>Assessments Records</span>
    </li>
@stop


@section('page-title')
    <h1> Student Profile | Assessments Records</h1>
@endsection

@section('content')
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('front.layout.partials.student-nav', ['active' => 'assessment'])
                <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light ">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Student Assessments Records</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="portlet sale-summary">
                                <div class="portlet-body">
                                    <div class="table-container">
                                        <div class="table-actions-wrapper">
                                            <span> </span>
                                            Search: <input type="text" class="form-control input-inline input-small input-sm"/><br>
                                        </div>
                                        <table class="table table-striped table-bordered table-hover" id="attendance_tabledata">
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
                                            <?php $j = 1; ?>
                                            @foreach($assessments as $assessment)
                                                <tr>
                                                    <td>{{$j++}} </td>
                                                    <td>{{ $assessment->academic_term }}</td>
                                                    <td>{{ $assessment->classroom }}</td>
                                                    <td>{{ $assessment->ca_weight_point }}</td>
                                                    <td>
                                                        <a href="{{ url('/wards-assessments/details/'.
                                                        $hashIds->encode($student->student_id)).'/'.$hashIds->encode($assessment->academic_term_id) }}"
                                                           class="btn btn-warning btn-rounded btn-condensed btn-xs">
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
    <script src="{{ asset('assets/pages/scripts/profile.min.js') }}" type="text/javascript"></script>
@endsection
@section('layout-script')
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/wards-assessments"]');

            setTableData($('#attendance_tabledata')).init();
        });
    </script>
@endsection
