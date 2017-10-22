@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'View Order Items')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="{{ url('/billings/view') }}">View/Adjust Order Items</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page">Student Order Items Billings</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cart-plus font-green"></i>
                        <span class="caption-subject font-green bold uppercase">
                            Student Order Information
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th> Student Name </th>
                                <td>{{ $student->fullNames() }}</td>
                                <th> Academic Term </th>
                                <td> {{ $term->academic_term }} </td>
                            </tr>
                            <tr>
                                <th> Student No. </th>
                                <td>
                                    <a target="_blank" href="{{ url('/students/view/'.$hashIds->encode($student->student_id)) }}" class="btn btn-link btn-xs sbold">
                                        <span style="font-size: 16px">{{ $student->student_no }}</span>
                                    </a>
                                </td>
                                <th> Gender </th>
                                <td> {{ $student->gender }} </td>
                            </tr>
                            <tr>
                                <th> Student Status </th>
                                <td>
                                    @if($student->status_id)
                                        <label class="label label-{{$student->status()->first()->label}}">{{ $student->status()->first()->status }}</label>
                                    @else
                                        <label class="label label-danger">nil</label>
                                    @endif
                                </td>
                                <th> Sponsor </th>
                                <td>
                                    <a target="_blank" href="{{ url('/sponsors/view/'.$hashIds->encode($student->sponsor_id)) }}" class="btn btn-link btn-xs sbold">
                                        <span style="font-size: 16px">{{ $student->sponsor->fullNames() }}</span>
                                    </a>
                                </td>
                            </tr>
                            @if(!empty($order))
                                <tr>
                                    <th> Academic Term </th>
                                    <td> {{ $order->academicTerm->academic_term }} </td>
                                    <th> Class Room </th>
                                    <td> {{ $order->classRoom->classroom }} </td>
                                </tr>
                                <tr>
                                    <th> Order No. </th>
                                    <td> {{ $order->number }} </td>
                                    <th> Status </th>
                                    <td>{!! ($order->paid) ? LabelHelper::success(strtoupper($order->status)) : LabelHelper::danger(strtoupper($order->status))!!}</td>
                                </tr>
                                <tr>
                                    <th> Total Amount </th>
                                    <th>{{CurrencyHelper::NAIRA}} {{ $order->amount(true) }}</th>
                                    <th> Item(s) </th>
                                    <td> {{ count($items) }} </td>
                                </tr>
                                <tr>
                                    <th>Update Status</th>
                                    <td>
                                        @if(!$order->paid)
                                            <button  data-confirm-text="Yes, Confirm Payment" data-name="{{$order->number}}" data-title="Order Status Update Confirmation"
                                                     data-message="Are you sure Order: <b>{{$order->number}}</b> meant for <b>{{$student->simpleName()}} has being PAID, for {{$term->academic_term}}?</b>"
                                                     data-statusText="{{$order->number}} Order status updated to PAID" data-confirm-button="#44b6ae"
                                                     data-action="/orders/status/{{$order->id}}" data-status="Updated"
                                                     class="btn btn-success btn-xs confirm-delete-btn">
                                                <span class="fa fa-save"></span> Update
                                            </button>
                                        @else
                                            <button  data-confirm-text="Yes, Undo Payment" data-name="{{$order->number}}" data-title="Order Status Update Confirmation"
                                                     data-message="Are you sure Order: <b>{{$order->number}}</b> meant for <b>{{$student->simpleName()}} has NOT being PAID, for {{$term->academic_term}}?</b>"
                                                     data-statusText="{{$order->number}} Order status updated to NOT-PAID"
                                                     data-action="/orders/status/{{$order->id}}" data-status="Updated"
                                                     class="btn btn-warning btn-xs confirm-delete-btn">
                                                <span class="fa fa-undo"></span> Undo
                                            </button>
                                        @endif
                                    </td>
                                    <th>Source</th>
                                    <td>{!! ($order->backend) ? LabelHelper::info('Admin') : LabelHelper::default('Sponsor') !!}</td>
                                </tr>
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
        <div class="col-md-6 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-money font-green"></i>
                        <span class="caption-subject font-green bold uppercase">
                            Order Billings By Items.
                        </span>
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
                                    <th>Amount ({{CurrencyHelper::NAIRA}})</th>
                                    @if(!$order->paid)
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if($items && count($items) > 0)
                                    <?php $i = 1; $total = 0;?>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item->item->name }}</td>
                                            <td>{{ CurrencyHelper::format($item->amount) }}</td>
                                            @if(!$order->paid)
                                                <td>
                                                    <a href="#" data-id="{{$item->id}}" data-item="{{$item->item->name}}"
                                                       data-amount="{{ intval($item->amount) }}" class="btn btn-warning btn-xs item-edit">
                                                        <span class="fa fa-edit"></span> Edit
                                                    </a>
                                                </td>
                                                <td>
                                                    <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$item->item->name}}" data-title="Delete Confirmation"
                                                         data-action="/orders/delete-item/{{$item->id}}" class="btn btn-danger btn-xs btn-sm confirm-delete-btn">
                                                        <span class="fa fa-trash-o"></span> Delete
                                                    </button>
                                                </td>
                                            @endif
                                        </tr>
                                        <?php $total += $item->amount; ?>
                                    @endforeach
                                    <tr>
                                        <th></th>
                                        <th>Sum Total:</th>
                                        <th>{{CurrencyHelper::NAIRA}} {{CurrencyHelper::format($total, 2) }}</th>
                                        @if(!$order->paid)
                                            <th></th>
                                            <th></th>
                                        @endif
                                    </tr>
                                @else
                                    <tr><th colspan="{{ (!$order->paid) ? 3 : 5 }}" class="text-center">No Orders Billed Yet</th></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END CONTENT BODY -->

    <!-- modal -->
    <div id="edit_item_modal" class="modal fade bs-modal-lg" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title text-center text-primary" id="modal-title-text">Edit Item Amount Form</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 col-sm-12 col-md-offset-2">
                            <form method="POST" action="#" class="form" role="form" id="edit_item_form">
                                {!! csrf_field() !!}
                                {!! Form::hidden('order_item_id', '', ['id'=>'order_item_id']) !!}
                                <div class="form-group">
                                    <label class="control-label">Amount ({{CurrencyHelper::NAIRA}}): <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-money"></i></span>
                                        {!! Form::text('amount', '', ['id'=>'amount', 'placeholder'=>'Amount', 'class'=>'form-control', 'required'=>true]) !!}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                                    <button type="submit" class="btn green">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal -->
@endsection


@section('layout-script')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/custom/js/orders/orders.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/orders"]');
        });
    </script>
@endsection
