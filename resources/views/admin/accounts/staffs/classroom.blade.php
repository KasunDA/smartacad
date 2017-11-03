@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Staff Class Rooms')

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
        <span>Staff Class Rooms</span>
    </li>
@stop



@section('content')
    <h3 class="page-title">Staff Profile | Class Rooms</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.staff-nav', ['active' => 'classroom'])
        <!-- END BEGIN PROFILE SIDEBAR -->
        <div class="profile-content">
            <?php $j = 1; ?>
            <div class="row">
                <div class="col-md-10">
                    <!-- BEGIN CHART PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-book font-green"></i>
                                <span class="caption-subject font-green bold uppercase">Lists of Class Rooms Assigned.</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-responsive">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered table-striped" id="classroom_table">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Academic Year</th>
                                            <th>Class Room</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($classrooms as $classroom)
                                            <tr class="odd gradeX">
                                                <td class="center">{{$j++}}</td>
                                                <td>{{ $classroom->academicYear->academic_year }}</td>
                                                <td>{{ $classroom->classRoom->classroom }}</td>
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

            setTableData($('#classroom_table')).init();
        });
    </script>
@endsection
