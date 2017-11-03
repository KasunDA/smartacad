@extends('front.layout.default')

@section('layout-style')
        <!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Billings Details')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/home') }}">Home</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="#">Billings Details</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('page-title')
    <h1> Student Profile | Billings Details</h1>
@endsection

@section('content')
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('front.layout.partials.student-nav', ['active' => 'billing'])
        <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-11 margin-bottom-10">
                    <!-- BEGIN SAMPLE TABLE PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-cart-plus font-green"></i>
                                <span class="caption-subject font-green bold uppercase">
                                    Billing Information
                                </span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-scrollable">
                                <table class="table table-hover table-striped">
                                    <tr>
                                        <th> Student No. </th>
                                        <td> {{ $student->student_no }} </td>
                                        <th> Sponsor </th>
                                        <td>
                                            <a target="_blank" href="{{ url('/sponsors/view/'.$hashIds->encode($student->sponsor_id)) }}" class="btn btn-link btn-xs sbold">
                                                <span style="font-size: 16px">{{ $student->sponsor->fullNames() }}</span>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th> Gender </th>
                                        <td> {{ $student->gender }} </td>
                                        <th> Student Status </th>
                                        <td>
                                            @if($student->status_id)
                                                <label class="label label-{{$student->status()->first()->label}}">{{ $student->status()->first()->status }}</label>
                                            @else
                                                {{ LabelHelper::danger() }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if(!empty($order))
                                        <tr>
                                            <th> Term </th>
                                            <td> {{ $order->academicTerm->academic_term }} </td>
                                            <th> Class Room </th>
                                            <td> {{ $order->classRoom->classroom }} </td>
                                        </tr>
                                        <tr>
                                            <th> Order No. </th>
                                            <td> {{ $order->number }} </td>
                                            <th> Status </th>
                                            <td>{!! LabelHelper::label(Order::STATUSES[$order->paid]['label'], Order::STATUSES[$order->paid]['title']) !!}</td>
                                        </tr>
                                        <tr>
                                            <th> Discount </th>
                                            <th>{{CurrencyHelper::format($order->total_amount - $order->amount, 1)}} ({{$order->discount}}%)</th>
                                            <th> Item(s) </th>
                                            <td> {{ $order->item_count }} </td>
                                        </tr>
                                        <tr>
                                            <th> Total Amount </th>
                                            <th>{{ CurrencyHelper::format($order->total_amount, 2, true) }}</th>
                                            <th> Amount Payable </th>
                                            <th>{{ CurrencyHelper::format($order->amount, 2, true) }}</th>
                                        </tr>
                                        <th>Payment Type</th>
                                        <td>
                                            @if($order->is_part_payment)
                                                {!! LabelHelper::primary(PartPayment::PAYMENT_TYPES[$order->is_part_payment]) !!}
                                            @else
                                                {!! LabelHelper::success(PartPayment::PAYMENT_TYPES[$order->is_part_payment]) !!}
                                            @endif
                                        </td>
                                        <th>Source</th>
                                        <td>{!! ($order->backend) ? LabelHelper::info('Admin') : LabelHelper::default('Sponsor') !!}</td>
                                        @if($order->is_part_payment)
                                            <tr><th colspan="4" class="text-center">Part Payments Summary</th></tr>
                                            <tr>
                                                <th>Installment(s)</th>
                                                <td>{{ $order->partPayments->count() }}</td>
                                                <th>Add Amount</th>
                                                <td><button class="btn btn-success btn-xs add-part-payment"><span class="fa fa-plus"></span> Add</button></td>
                                            </tr>
                                            <tr>
                                                <th>Amount Paid: </th>
                                                <td>{{ CurrencyHelper::format($order->partPayments()->lists('amount')->sum(), 2, true) }}</td>
                                                <th>Outstanding: </th>
                                                <td>{{ CurrencyHelper::format($order->amount - $order->partPayments()->lists('amount')->sum(), 2, true) }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <th>Amount Paid: </th>
                                                <td>{{ CurrencyHelper::format($order->amount_paid, 2, true) }}</td>
                                                <th>Outstanding: </th>
                                                <td>{{ CurrencyHelper::format($order->amount - $order->amount_paid, 2, true) }}</td>
                                            </tr>
                                        @endif
                                    @else
                                        <tr>
                                            <th colspan="2">No Order initiated yet for the Student</th>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END SAMPLE TABLE PORTLET-->
                </div>
                <div class="col-md-11">
                    <div class="portlet light ">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Student Billings Details</span>
                            </div>
                            <div class="pull-right">
                                <a target="_blank" href="{{ url('/invoices/order/'.$hashIds->encode($order->id)) }}" class="btn btn-default btn-xs sbold">
                                    <i class="fa fa-print"></i> View / Print
                                </a>
                                <a target="_blank" href="{{ url('/invoices/pdf/'.$hashIds->encode($order->id)) }}" class="btn btn-info btn-xs sbold">
                                    <i class="fa fa-file"></i> PDF
                                </a>
                                <a target="_blank" href="{{ url('/invoices/download/'.$hashIds->encode($order->id)) }}" class="btn btn-primary btn-xs sbold">
                                    <i class="fa fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Name</th>
                                        <th>Item Description</th>
                                        <th>Amount ({{CurrencyHelper::NAIRA}})</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($items && count($items) > 0)
                                        <?php $i = 1; $total = 0;?>
                                        @foreach($items as $item)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $item->item->name }}</td>
                                                <td>{{ $item->item->description }}</td>
                                                <td>{{ CurrencyHelper::format($item->amount) }}</td>
                                            </tr>
                                            <?php $total += $item->amount; ?>
                                        @endforeach
                                        <tr>
                                            <th>#</th>
                                            <th>Item Name</th>
                                            <th>Item Description</th>
                                            <th>{{CurrencyHelper::NAIRA}} {{CurrencyHelper::format($total, 2) }}</th>
                                        </tr>
                                    @else
                                        <tr><th colspan="4" class="text-center">No Orders Placed Yet</th></tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY -->
@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/profile.min.js') }}" type="text/javascript"></script>
@endsection
@section('layout-script')
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/wards-assessments"]');

            setTableData($('#view_attendance_datatable')).init();
        });
    </script>
@endsection
