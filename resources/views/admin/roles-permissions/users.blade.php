@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Assign Users Roles')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/roles') }}">Roles to Users</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Manage user Roles</h3>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Users: <span class="text-danger">Roles</span> </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <form method="post" action="/roles/users-roles" role="form" class="form-horizontal">
                            {!! csrf_field() !!}
                            <div class="col-md-12">
                                <div class="table-container">
                                    <div class="table-actions-wrapper">
                                        <span> </span>
                                        Search: <input type="text" class="form-control input-inline input-small input-sm" id="search_param"/>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-actions datatable" id="user_role_table">
                                            <thead>
                                                <tr role="row" class="heading">
                                                    <th style="width: 1%;">#</th>
                                                    <th style="width: 16%;">Names</th>
                                                    <th style="width: 16%;">Email</th>
                                                    <th style="width: 5%;">Gender</th>
                                                    <th style="width: 13%;">User Type</th>
                                                    <th style="width: 26%;">Roles</th>
                                                    <th style="width: 5%;">View</th>
                                                    <th style="width: 5%;">Edit</th>
                                                </tr>
                                                <tr role="row" class="filter">
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        {!! Form::select('user_type_id', $user_types, '', ['class'=>'form-control input-inline input-sm search-params', 'id'=>'user_type_id']) !!}
                                                    </td>
                                                    <td>
                                                        {!! Form::select('role_id', $roles, '', ['class'=>'form-control input-inline input-sm search-params', 'id'=>'role_id']) !!}
                                                    </td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th style="width: 1%;">#</th>
                                                    <th style="width: 16%;">Names</th>
                                                    <th style="width: 16%;">Email</th>
                                                    <th style="width: 5%;">Gender</th>
                                                    <th style="width: 13%;">User Type</th>
                                                    <th style="width: 26%;">Roles</th>
                                                    <th style="width: 5%;">View</th>
                                                    <th style="width: 5%;">Edit</th>
                                                </tr>
                                            </tfoot>
                                            <tbody> </tbody>
                                        </table>
                                        <div class="form-actions noborder">
                                            <button type="submit" class="btn blue pull-right">Save Record</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/custom/js/roles-permissions/roles.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/roles/users-roles"]');
//            setTableData($('#user_role_table')).init();
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });
            TableDatatablesAjax.init();
        });
    </script>
@endsection
