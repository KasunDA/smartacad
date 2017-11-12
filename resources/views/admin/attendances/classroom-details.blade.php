@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Attendances Class Room Details')

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
@stop


@section('content')
    <h3 class="page"> Attendance Class Room Details</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8 margin-bottom-10">
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
                                <th> Head Tutor </th>
                                <td>{{ $attendance->takenBy->fullNames() }}</td>
                                <th> Number of Students </th>
                                <td>{{$attendance->details->count()}}</td>
                            </tr>
                            <tr>
                                <th> Academic Term </th>
                                <td>{{ $attendance->academicTerm->academic_term }}</td>
                                <th> Class Room </th>
                                <td> {{ $attendance->classRoom->classroom }} </td>
                            </tr>
                            <tr>
                                <th> Present </th>
                                <td>{{ $attendance->details()->present()->count() }}</td>
                                <th> Absent </th>
                                <td>{{ $attendance->details()->absent()->count() }}</td>
                            </tr>
                            <tr>
                                <th> Date Taken </th>
                                <td>{{ $attendance->attendance_date->format( 'D jS, M Y' ) }}</td>
                                <th> </th>
                                <td> </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
        <div class="col-md-8 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-money font-green"></i>
                        <span class="caption-subject font-green bold uppercase">
                            Attendance Details
                        </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="view_attendance_datatable">
                            <thead>
                                <tr role="row" class="heading">
                                    <th>#</th>
                                    <th>Full Name</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr role="row" class="heading">
                                    <th>#</th>
                                    <th>Full Name</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php $i=1; ?>
                                @foreach($details as $detail)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $detail->student->fullNames() }}</td>
                                        <td>{{ $detail->reason }}</td>
                                        <td>{!! ($detail->status) ? '<span class="label label-success">Present</span>' : '<span class="label label-danger">Absent</span>' !!}</td>
                                    </tr>
                                @endforeach
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
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/custom/js/attendances/attendance.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {

            setTabActive('[href="/attendances"]');

            setTableData($('#view_attendance_datatable')).init();
        });
    </script>
@endsection
