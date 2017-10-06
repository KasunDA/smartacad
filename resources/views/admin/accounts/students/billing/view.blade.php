@extends('admin.layout.default')

@section('layout-style')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Student Billings')

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
        <a href="{{ url('/orders') }}">Billings</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <span>Billings Records</span>
    </li>
@stop

@section('content')
    <h3 class="page-title">Student Profile | Billings Records</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.student-nav', ['active' => 'billing'])
                <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light ">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Student Billings Records</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="portlet sale-summary">
                                <div class="portlet-body">
                                    <div class="table-container">
                                        <div class="table-actions-wrapper">
                                            <span> </span>
                                            Search: <input type="text" class="form-control input-inline input-small input-sm"/><br>
                                        </div>
                                        <table class="table table-striped table-bordered table-hover" id="billing_tabledata">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th>#</th>
                                                <th>Academic Term</th>
                                                <th>Class Room</th>
                                                <th>Item(s)</th>
                                                <th>Details</th>
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr role="row" class="heading">
                                                <th>#</th>
                                                <th>Academic Term</th>
                                                <th>Class Room</th>
                                                <th>Item(s)</th>
                                                <th>Action</th>
                                            </tr>
                                            </tfoot>
                                            <tbody>
                                            <?php $i = 1; ?>
                                            @foreach($orders as $order)
                                                <tr>
                                                    <td>{{$i++}} </td>
                                                    <td>{{ $order->academic_term }}</td>
                                                    <td>{{ $order->classroom }}</td>
                                                    <td>{{ $order->item_count }}</td>
                                                    <td>
                                                        <a href="{{ url('/billings/details/'.$hashIds->encode($student->student_id)).'/'.$hashIds->encode($order->order_id) }}"
                                                            class="btn btn-warning btn-rounded btn-condensed btn-xs">
                                                            <span class="fa fa-eye"></span> Details
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if(empty($orders))
                                                <tr>
                                                    <th colspan="5">No Record Found</th>
                                                </tr>
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
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
@endsection
@section('layout-script')
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/students"]');

            setTableData($('#billing_tabledata')).init();
        });
    </script>
@endsection
