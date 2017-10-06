@extends('admin.layout.default')

@section('layout-style')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Manage User')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="{{ url('/users') }}">Manage User</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Manage Users</h3>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Registered</span>
                    </div>
                    <div class="tools">
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <div class="table-actions-wrapper">
                            <span> </span>
                            Search: <input type="text" class="form-control input-inline input-small input-sm" id="search_param"/>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="users_datatable">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width="2%">#</th>
                                    <th width="28%">Names</th>
                                    <th width="12%">Phone No.</th>
                                    <th width="21%">Email</th>
                                    <th width="12%">User Type</th>
                                    <th width="10%">Status</th>
                                    <th width="5%">View</th>
                                    <th width="5%">Edit</th>
                                    <th width="5%">Delete</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr role="row" class="heading">
                                    <th width="2%">#</th>
                                    <th width="28%">Names</th>
                                    <th width="12%">Phone No.</th>
                                    <th width="21%">Email</th>
                                    <th width="12%">User Type</th>
                                    <th width="10%">Status</th>
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
    <script src="{{ asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('layout-script')
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('assets/custom/js/users/user.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/users"]');
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });

//            App.alert({
//                type: 'danger',
//                icon: 'warning',
//                message: 'No record selected',
//                container: grid.getTableWrapper(),
//                place: 'prepend'
//            });
//            setTableData($('#users_datatable')).init();
            TableDatatablesAjax.init();
        });
    </script>
@endsection
