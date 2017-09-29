@extends('admin.layout.default')

@section('layout-style')
@endsection

@section('title', 'Adjust Attendances')

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
    <h3 class="page"> Adjust Attendance</h3>
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
                                <td>{{ $classMaster->user->fullNames() }}</td>
                                <th> Number of Students </th>
                                <td>
                                    {{$classMaster->classroom
                                        ->studentClasses
                                        ->where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                        ->count()
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <th> Academic Term </th>
                                <td>{{ AcademicTerm::activeTerm()->academic_term }}</td>
                                <th> Class Room </th>
                                <td> {{ $classMaster->classRoom->classroom }} </td>
                            </tr>
                            <tr>
                                <th> Total Attendance Taken </th>
                                <td>{{ count($attendances) }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
        <div class="col-md-6 margin-bottom-10">
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
                        <table class="table table-striped table-bordered table-hover" id="adjust_attendance_datatable">
                            <thead>
                                <tr role="row" class="heading">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr role="row" class="heading">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php $i=1; ?>
                                @foreach($attendances as $attendance)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $attendance->attendance_date->format('D jS, M Y') }}</td>
                                        <td>{{ $attendance->details()->present()->count() }}</td>
                                        <td>{{ $attendance->details()->absent()->count() }}</td>
                                        <td>
                                            <a class="btn btn-warning btn-xs" href="{{ route('initiateAttendance',
                                                ['classId'=>$hashIds->encode($attendance->classroom_id), 'attendId'=>$hashIds->encode($attendance->id)]) }}">
                                                <i class="fa fa-edit"></i>Edit
                                            </a>
                                        </td>
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
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {

            setTabActive('[href="/attendances"]');

            setTableData($('#adjust_attendance_datatable')).init();
        });
    </script>
@endsection
