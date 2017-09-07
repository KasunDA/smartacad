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
                                    <td> {{ strtoupper($order->status) }} </td>
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
                </div>
                <div class="portlet-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Amount</th>
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
                                            <td>{{ $item->amount }}</td>
                                            @if(!$order->paid)
                                                <td>
                                                    <a href="#" class="btn btn-warning btn-rounded btn-condensed btn-xs">
                                                        <span class="fa fa-edit"></span> Edit
                                                    </a>
                                                </td>
                                                <td>
                                                    <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$item->item->name}}" data-title="Delete Confirmation"
                                                             data-action="/billings/delete-item/{{$item->id}}"
                                                             class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
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
                                        <th>{{ number_format($total, 2) }}</th>
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
@endsection


@section('layout-script')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/orders/billings.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/billings/view"]');
        });
    </script>
@endsection
