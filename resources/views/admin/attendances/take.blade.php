@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'View Order Items')

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
    <h3 class="page"> Take/Adjust Attendance</h3>
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
                                <td>{{$classMaster->classroom->studentClasses->count()}}</td>
                            </tr>
                            <tr>
                                <th> Academic Term </th>
                                <td>{{ AcademicTerm::activeTerm()->academic_term }}</td>
                                <th> Class Room </th>
                                <td> {{ $classMaster->classRoom->classroom }} </td>
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
                    {!! Form::open([
                            'method'=>'POST',
                            'class'=>'form-horizontal',
                            'id' => 'take_attendance_form'
                        ])
                    !!}
                    <div class="form-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover table-checkable">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th><input type="checkbox" class="group-checkable check-all"> </th>
                                        <th>#</th>
                                        <th>Full Name</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i=1; ?>
                                    @foreach($studentClasses as $studentClass)
                                        <tr>
                                            <td><input type="checkbox" class="check-one" name="student_id[]" value="{{$studentClass->student_id}}"></td>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $studentClass->student->fullNames() }}</td>
                                            <td><input type="text" class="form-control" name="reason[]" placeholder="Reason for being absent if any"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Attendance Date:</label>
                                    <div class="input-icon">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" class="form-control date-picker" data-date-format='yyyy-mm-dd'
                                               placeholder="Attendance Date Taken" name="attendance_date" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions noborder">
                                <button type="submit" class="btn blue pull-right">
                                    <i class="fa fa-send"></i> Submit
                                </button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END CONTENT BODY -->
@endsection


@section('layout-script')
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/attendances/attendance.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {

            $('.date-picker').datepicker();
            setTabActive('[href="/attendances"]');

//            setTableData($('#view_student_datatable')).init();
        });
    </script>
@endsection
