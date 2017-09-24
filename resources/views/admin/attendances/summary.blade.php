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
                        <table class="table table-striped table-bordered table-hover" id="paid_orders_datatable">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width="1%">#</th>
                                    <th width="12%">Order No.</th>
                                    <th width="10%">Amount</th>
                                    <th width="4%">Status</th>
                                    <th width="22%">Student</th>
                                    <th width="22%">Sponsor</th>
                                    <th width="10%">Gender</th>
                                    <th width="15%">Class Room</th>
                                    <th width="4%">Backend</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr role="row" class="heading">
                                    <th width="1%">#</th>
                                    <th width="12%">Order No.</th>
                                    <th width="10%">Amount</th>
                                    <th width="4%">Status</th>
                                    <th width="22%">Student</th>
                                    <th width="22%">Sponsor</th>
                                    <th width="10%">Gender</th>
                                    <th width="15%">Class Room</th>
                                    <th width="4%">Backend</th>
                                </tr>
                            </tfoot>
                            <tbody>
                            <?php $i=1; ?>
                            @foreach($orders as $order)
                                <tr role="row" class="heading">
                                    <td>{{ $i++ }}</td>
                                    <td>
                                        <a target="_blank" href="{{ action('Admin\Orders\OrdersController@getItems',
                                        ['studentId' => $hashIds->encode($order->student_id), 'termId' => $hashIds->encode($term->academic_term_id)]) }}"
                                           class="btn btn-link btn-xs sbold"><span style="font-size: 14px">{{ $order->number }}</span>
                                        </a>
                                    </td>
                                    <td>{{ CurrencyHelper::format($order->amount, 0, true) }}</td>
                                    <td><span class="label label-sm label-{!! ($order->paid==1) ? 'success' : 'danger' !!}">{{strtoupper($order->status)}}</span></td>
                                    <td>
                                        <a target="_blank" href="{{ url('/students/view/'.$hashIds->encode($order->student_id)) }}" class="btn btn-link btn-xs sbold">
                                            <span style="font-size: 14px">{{ $order->fullname }}</span>
                                        </a>
                                    </td>
                                    <td>
                                        <a target="_blank" href="{{ url('/sponsors/view/'.$hashIds->encode($order->sponsor_id)) }}" class="btn btn-link btn-xs sbold">
                                            <span style="font-size: 14px">{{ $order->sponsor->simpleName() }}</span>
                                        </a>
                                    </td>
                                    <td>{{ $order->gender }}</td>
                                    <td>{{ $order->classroom }}</td>
                                    <td>
                                        {!! ($order->backend==1)
                                            ? '<span class="label label-sm label-success">Yes</span>'
                                            : '<span class="label label-sm label-danger">No</span>'
                                        !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>


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
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/orders/{{strtolower($type)}}"]');
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });

            setTableData($('#paid_orders_datatable')).init();
        });
    </script>
@endsection
