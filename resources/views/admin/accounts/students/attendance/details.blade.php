@extends('admin.layout.default')

@section('layout-style')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Student Attendance Details')

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
        <a href="{{ url('/attendances') }}">Attendances</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <span>Attendance Details</span>
    </li>
@stop

@section('content')
    <h3 class="page-title">Student Profile | Attendance Records Details</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.student-nav', ['active' => 'attendance'])
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
                            Attendance Information
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-scrollable">
                                <table class="table table-hover table-striped">
                                    <tr>
                                        <th> Academic Term </th>
                                        <td>{{ isset($attendances[0]) ? $attendances[0]->academicTerm->academic_term : ''}}</td>
                                        <th> Class Room </th>
                                        <td>{{ isset($attendances[0]) ? $attendances[0]->classRoom->classroom : ''}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END SAMPLE TABLE PORTLET-->
                </div>
                <div class="col-md-11">
                    <div class="portlet light ">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Student Attendance Details</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="portlet sale-summary">
                                <div class="portlet-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover" id="view_attendance_datatable">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Reason</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 1; $present = 0;?>
                                            @foreach( $attendances as $attendance )
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $attendance->attendance_date->format( 'D jS, M Y' ) }}</td>
                                                    <td>
                                                        @if( isset($attendance->details[0]) && $attendance->details[0]->status )
                                                            {!! LabelHelper::success('Present')  !!}
                                                            <?php $present++; ?>
                                                        @else
                                                            {!! LabelHelper::danger('Absent')  !!}
                                                        @endif
                                                    <td>{!! (isset($attendance->details[0])) ? $attendance->details[0]->reason : '' !!}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th></th>
                                                <th>Present(Total): {{ $present }}</th>
                                                <td></td>
                                                <th>Absent(Total): {{ count($attendances) - $present }}</th>
                                            </tr>
                                            </tfoot>
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
            setTabActive('[href="/students"]');

            setTableData($('#view_attendance_datatable')).init();
        });
    </script>
@endsection
