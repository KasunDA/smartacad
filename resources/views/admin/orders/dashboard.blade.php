@extends('admin.layout.default')

@section('layout-style')
    <link href="../assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Orders Dashboard')

@section('breadcrumb')
    <li>
        <i class="fa fa-home"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
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
        <a class="active" href="#">Dashboard</a>
    </li>
@stop


@section('content')
    <div class="row widget-row" style="margin-top: 30px;">
        <div class="col-md-10 col-md-offset-1 margin-bottom-10">
            <form method="post" action="/orders/dashboard" role="form" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                    <div class="col-md-12">
                        <label class="col-md-2 control-label">Academic Year</label>
                        <div class="col-md-4">
                            <div class="col-md-10">
                                {!! Form::select('academic_year_id', $academic_years,  $academic_year->academic_year_id,
                                    ['class'=>'form-control', 'id'=>'academic_year_id', 'required'=>'required'])
                                !!}
                            </div>
                        </div>
                        <label class="col-md-2 control-label">Academic Term</label>
                        <div class="col-md-4">
                            <div class="col-md-10">
                                {!! Form::select('academic_term_id', AcademicTerm::where('academic_year_id', $academic_term->academic_year_id)
                                        ->orderBy('term_type_id')
                                        ->lists('academic_term', 'academic_term_id')
                                        ->prepend('- Academic Term -', ''),
                                    $academic_term->academic_term_id,
                                    ['class'=>'form-control', 'id'=>'academic_term_id', 'required'=>'required'])
                                !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-8">
                        <h4 class="text-center">Orders(Item) Analytics:
                            <span class="text-danger">{{ ($academic_term) ? $academic_term->academic_term : 'All' }}</span> Academic Year
                        </h4>
                    </div>
                    <div class="col-md-2 pull left">
                        <button class="btn btn-primary pull-right" type="submit">Filter</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-3">
                <div class="dashboard-stat red">
                    <div class="visual">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div class="details">
                        <div class="number">{{ CurrencyHelper::format($pendingAmount, 0, true) }}</div>
                        <div class="desc">Pending Payments (Not Paid)</div>
                    </div>
                    <a class="more" target="_blank" href="{{route('notPaidOrders', ['termId'=>$hashIds->encode($academic_term->academic_term_id)])}}">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                    </a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-3">
                <div class="dashboard-stat purple">
                    <div class="visual">
                        <i class="fa fa-globe"></i>
                    </div>
                    <div class="details">
                        <div class="number">{{ CurrencyHelper::format($totalAmount, 0, true) }}</div>
                        <div class="desc">Expected Amount (All Orders)</div>
                    </div>
                    <a class="more" target="_blank" href="{{route('allOrders', ['termId'=>$hashIds->encode($academic_term->academic_term_id)])}}">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                    </a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-3">
                <div class="dashboard-stat yellow">
                    <div class="visual">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="details">
                        <div class="number">{{ $studentCount }}</div>
                        <div class="desc">Students Count</div>
                    </div>
                    <a class="more" href="#">View more <i class="m-icon-swapright m-icon-white"></i></a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-3">
                <div class="dashboard-stat green">
                    <div class="visual">
                        <i class="fa fa-bar-chart-o"></i>
                    </div>
                    <div class="details">
                        <div class="number">{{ CurrencyHelper::format($paidAmount, 0, true) }}</div>
                        <div class="desc">Total Income (Paid)</div>
                    </div>
                    <a class="more" target="_blank" href="{{route('paidOrders', ['termId'=>$hashIds->encode($academic_term->academic_term_id)])}}">
                        View more <i class="m-icon-swapright m-icon-white"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <!-- BEGIN CHART PORTLET-->
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-bar-chart font-purple"></i>
                        <span class="caption-subject bold uppercase font-purple">Expected Order Items</span>
                        <span class="caption-helper">Listing for {{ $academic_term->academic_term }}</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"> </a>
                        <a href="javascript:;" class="fullscreen"> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="order_items_expected_stats_loading" class="text-center">
                        <img src="/assets/global/img/loading.gif" alt="loading"/>
                    </div>
                    <div id="order_items_expected_stats_content" class="display-none">
                        <div id="order_items_expected" class="chart" style="height: 350px;"> </div>
                    </div>
                </div>
            </div>
            <!-- END CHART PORTLET-->
        </div>
        <div class="col-md-5">
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Orders</span>
                        <span class="caption-helper">Percentage for {{ $academic_term->academic_term }}</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"> </a>
                        <a href="javascript:;" class="fullscreen"> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="orders_percentage_stats_loading" class="text-center">
                        <img src="/assets/global/img/loading.gif" alt="loading"/>
                    </div>
                    <div id="orders_percentage_stats_content" class="display-none">
                        <div id="orders_percentage" class="chart" style="height: 350px;"> </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- BEGIN CHART PORTLET-->
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-bar-chart font-green-haze"></i>
                        <span class="caption-subject bold uppercase font-green-haze">Paid Order Items</span>
                        <span class="caption-helper">Listing for {{ $academic_term->academic_term }}</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"> </a>
                        <a href="javascript:;" class="fullscreen"> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="order_items_stats_loading" class="text-center">
                        <img src="/assets/global/img/loading.gif" alt="loading"/>
                    </div>
                    <div id="order_items_stats_content" class="display-none">
                        <div id="order_items" class="chart" style="height: 350px;"> </div>
                    </div>
                </div>
            </div>
            <!-- END CHART PORTLET-->
        </div>
        <div class="col-md-6">
            <!-- BEGIN CHART PORTLET-->
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-bar-chart font-red"></i>
                        <span class="caption-subject bold uppercase font-red">Pending Order Items</span>
                        <span class="caption-helper">Listing for {{ $academic_term->academic_term }}</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"> </a>
                        <a href="javascript:;" class="fullscreen"> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="order_items_pending_stats_loading" class="text-center">
                        <img src="/assets/global/img/loading.gif" alt="loading"/>
                    </div>
                    <div id="order_items_pending_stats_content" class="display-none">
                        <div id="order_items_pending" class="chart" style="height: 350px;"> </div>
                    </div>
                </div>
            </div>
            <!-- END CHART PORTLET-->
        </div>
    </div>

@endsection


@section('layout-script')
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/plugins/flot/jquery.flot.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/flot/jquery.flot.resize.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/flot/jquery.flot.categories.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/flot/jquery.flot.pie.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('assets/global/plugins/morris/morris.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/morris/raphael-min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('assets/global/plugins/amcharts/amcharts/amcharts.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/amcharts/amcharts/serial.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/amcharts/amcharts/themes/light.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->

    <script src="{{ asset('assets/global/plugins/counterup/jquery.waypoints.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/counterup/jquery.counterup.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/orders/dashboard.js') }}" type="text/javascript"></script>

    <script>
        jQuery(document).ready(function () {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });
            setTabActive('[href="/orders/dashboard"]');

            ChartsOrderPercent.initPieCharts('{{$academic_term->academic_term_id}}');
            ChartsPaidItems.init('{{$academic_term->academic_term_id}}');
            ChartsPendingItems.init('{{$academic_term->academic_term_id}}');
            ChartsExpectedItems.init('{{$academic_term->academic_term_id}}');
        });
    </script>
@endsection
