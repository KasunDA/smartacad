@extends('admin.layout.default')

@section('layout-style')
    <!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Manage Staffs')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}"> <i class="fa fa-dashboard"></i> Dashboard</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <span>Manage Staff</span>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Manage Staffs</h3>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Registered Staffs</span>
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
                        <table class="table table-striped table-bordered table-hover" id="staff_tabledata">
                            <thead>
                            <tr role="row" class="heading">
                                <th width="2%">#</th>
                                <th width="25%">Full Name</th>
                                <th width="12%">Phone No.</th>
                                <th width="23%">Email</th>
                                <th width="10%">Gender</th>
                                <th width="10%">Dashboard</th>
                                <th width="8%">Status</th>
                                <th width="5%">View</th>
                                <th width="5%">Edit</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            <tr role="row" class="heading">
                                <th width="2%">#</th>
                                <th width="25%">Full Name</th>
                                <th width="12%">Phone No.</th>
                                <th width="23%">Email</th>
                                <th width="10%">Gender</th>
                                <th width="10%">Dashboard</th>
                                <th width="8%">Status</th>
                                <th width="5%">View</th>
                                <th width="5%">Edit</th>
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

    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/staffs"]');

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });

            var url = '/staffs/all-staffs';
            setTableDatatablesAjax($('#staff_tabledata'), url).init();
        });
    </script>
@endsection
