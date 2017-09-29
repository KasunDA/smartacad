@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Attendances Student Details')

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
    <h3 class="page"> Attendance Student Details</h3>
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
                                <th> Student Name </th>
                                <td>
                                    <a target="_blank" href="{{ url('/students/view/'.$hashIds->encode($studentClass->student_id)) }}" class="btn btn-link btn-xs sbold">
                                        <span style="font-size: 16px">{{ $studentClass->student->fullNames() }}</span>
                                    </a>
                                </td>
                                <th> Age. </th>
                                <td>{!! ($studentClass->student->dob) ? $studentClass->student->dob->age . ' Year(s)' : 'N/A' !!}</td>
                            </tr>
                            <tr>
                                <th> Student No. </th>
                                <td>
                                    <a target="_blank" href="{{ url('/students/view/'.$hashIds->encode($studentClass->student_id)) }}" class="btn btn-link btn-xs sbold">
                                        <span style="font-size: 16px">{{ $studentClass->student->student_no }}</span>
                                    </a>
                                </td>
                                <th> Gender </th>
                                <td> {{ $studentClass->student->gender }} </td>
                            </tr>
                            <tr>
                                <th> Student Status </th>
                                <td>
                                    @if($studentClass->student->status_id)
                                        <label class="label label-sm label-{{$studentClass->student->status()->first()->label}}">{{ $studentClass->student->status()->first()->status }}</label>
                                    @else
                                        <label class="label label-danger label-sm">nil</label>
                                    @endif
                                </td>
                                <th> Sponsor </th>
                                <td>
                                    <a target="_blank" href="{{ url('/sponsors/view/'.$hashIds->encode($studentClass->student->sponsor_id)) }}" class="btn btn-link btn-xs sbold">
                                        <span style="font-size: 16px">{{ $studentClass->student->sponsor->fullNames() }}</span>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th> Academic Term </th>
                                <td>{{ $term->academic_term }}</td>
                                <th> Class Room </th>
                                <td> {{ $studentClass->classRoom->classroom }} </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
        <div class="col-md-7 margin-bottom-10">
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
                                            @if( $attendance->details[0]->status )
                                                <span class="label label-success label-sm">Present</span>
                                                <?php $present++; ?>
                                            @else
                                                <span class="label label-danger label-sm">Absent</span>
                                            @endif
                                        <td>{{ $attendance->details[0]->reason }}</td>
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
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/attendances/attendance.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {

            setTabActive('[href="/attendances"]');

            setTableData($('#view_attendance_datatable')).init();
        });
    </script>
@endsection
