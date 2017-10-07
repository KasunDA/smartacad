@extends('admin.layout.default')

@section('layout-style')
    <!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Manage Student')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}"> <i class="fa fa-dashboard"></i> Dashboard</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <span>Manage Student</span>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Manage Student</h3>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Registered Students</span>
                    </div>
                    <div class="tools">
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <div class="table-actions-wrapper">
                            <span> </span>
                            Search: <input type="text" class="form-control input-inline input-small input-sm" id="search_param"/><br>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="student_tabledata">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width="1%">#</th>
                                    <th width="33%">Full Name</th>
                                    <th width="5%">I.D</th>
                                    <th width="16%">Sponsor</th>
                                    <th width="12%">Current Class</th>
                                    <th width="10%">Gender</th>
                                    <th width="8%">Status</th>
                                    <th width="5%">View</th>
                                    <th width="5%">Edit</th>
                                    <th width="5%">Delete</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td colspan="4"></td>
                                    <td>
                                        {!! Form::select('classroom_id', $classrooms, '', ['class'=>'form-control input-inline input-sm search-params', 'id'=>'classroom_id']) !!}
                                    </td>
                                    <td>
                                        {!! Form::select('gender', [''=>'- Gender -', 'Male'=>'Male', 'Female'=>'Female'], '',
                                        ['id'=>'gender', 'class'=>'form-control input-inline input-xsmall input-sm search-params']) !!}
                                    </td>
                                    <td>
                                        {!! Form::select('status_id', $status, '', ['class'=>'form-control input-inline input-xsmall input-sm search-params', 'id'=>'status_id']) !!}
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr role="row" class="heading">
                                    <th width="1%">#</th>
                                    <th width="33%">Full Name</th>
                                    <th width="5%">I.D</th>
                                    <th width="16%">Sponsor</th>
                                    <th width="12%">Current Class</th>
                                    <th width="10%">Gender</th>
                                    <th width="8%">Status</th>
                                    <th width="5%">View</th>
                                    <th width="5%">Edit</th>
                                    <th width="5%">Delete</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('page-level-js')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('layout-script')
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/accounts/students.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/students"]');

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });

            TableDatatablesAjax.init();
        });
    </script>
@endsection
