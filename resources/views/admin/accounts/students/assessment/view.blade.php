@extends('admin.layout.default')

@section('layout-style')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Student Assessment View')

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
        <a href="{{ url('/assessments') }}">Assessments</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <span>Assessment Records</span>
    </li>
@stop

@section('content')
    <h3 class="page-title">Student Profile | Assessment Records</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.student-nav', ['active' => 'assessment'])
                <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light ">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Student Assessment Records</span>
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
                                        <table class="table table-striped table-bordered table-hover" id="assessment_tabledata">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th>#</th>
                                                <th>Academic Term</th>
                                                <th>Class Room</th>
                                                <th>Weight Point</th>
                                                <th>Details</th>
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr role="row" class="heading">
                                                <th>#</th>
                                                <th>Academic Term</th>
                                                <th>Class Room</th>
                                                <th>Weight Point</th>
                                                <th>Details</th>
                                            </tr>
                                            </tfoot>
                                            <tbody>
                                            <?php $i = 1; ?>
                                            @foreach($assessments as $assessment)
                                                <tr>
                                                    <td>{{$i++}} </td>
                                                    <td>{{ $assessment->academic_term }}</td>
                                                    <td>{{ $assessment->classroom }}</td>
                                                    <td>{{ $assessment->ca_weight_point }}</td>
                                                    <td>
                                                        <a href="{{ url('/assessments/details/'.$hashIds->encode($student->student_id)).'/'.$hashIds->encode($assessment->academic_term_id) }}"
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
@endsection
@section('layout-script')
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/students"]');

            setTableData($('#assessment_tabledata')).init();
        });
    </script>
@endsection
