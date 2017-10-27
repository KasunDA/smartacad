@extends('admin.layout.default')

@section('layout-style')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', "{$type} Orders for {$term->academic_term}")

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
        <a href="{{ url('/orders') }}">Orders</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/orders/dashboard') }}">Dashboard</a>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Orders Payments Details</h3>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">{{$type}} Orders for {{$term->academic_term}}</span>
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
                        <table class="table table-striped table-bordered table-hover" id="orders_datatable">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width="1%">#</th>
                                    <th width="12%">Order No.</th>
                                    <th width="12%">Charges</th>
                                    <th width="12%">Paid</th>
                                    <th width="5%">Type</th>
                                    <th width="5%">Status</th>
                                    <th width="23%">Student</th>
                                    <th width="20%">Class Room</th>
                                    <th width="5%">Action</th>
                                    <th width="5%">Update</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr role="row" class="heading">
                                    <th width="1%">#</th>
                                    <th width="12%">Order No.</th>
                                    <th width="12%">Charges</th>
                                    <th width="12%">Paid</th>
                                    <th width="5%">Type</th>
                                    <th width="5%">Status</th>
                                    <th width="23%">Student</th>
                                    <th width="20%">Class Room</th>
                                    <th width="5%">Action</th>
                                    <th width="5%">Update</th>
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
    <script src="{{ asset('assets/custom/js/orders/summary.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/orders/{{strtolower($type)}}"]');
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });

            TableDatatablesAjax.init();
        });
    </script>
@endsection
