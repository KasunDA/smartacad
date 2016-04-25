@extends('admin.layout.default')

@section('layout-style')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ asset('assets/global/plugins/select2/css/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Manage Sponsors')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}"> <i class="fa fa-dashboard"></i> Dashboard</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <span>Manage Sponsors</span>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Manage Sponsors</h3>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Registered Sponsors</span>
                    </div>
                    <div class="tools">
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <table class="table table-striped table-bordered table-hover" id="sponsor_datatable">
                            <thead>
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th style="width: 20%;">Full Name</th>
                                <th style="width: 15%;">Mobile</th>
                                <th style="width: 19%;">Email</th>
                                <th style="width: 15%;">Registered By</th>
                                <th style="width: 15%;">Registered On</th>
                                <th style="width: 5%;">Status</th>
                                <th style="width: 5%;">View</th>
                                <th style="width: 5%;">Edit</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($sponsors) > 0)
                                <?php $i = 1; ?>
                                @foreach($sponsors as $sponsor)
                                    <tr class="odd gradeX">
                                        <td class="center">{{$i++}}</td>
                                        <td>{{ $sponsor->fullNames() }}</td>
                                        <td>{{ $sponsor->phone_no }}</td>
                                        <td>{{ $sponsor->email }}</td>
                                        <td>{{ $sponsor->createdBy()->first()->fullNames() }}</td>
                                        <td>{{ $sponsor->created_at->format('jS M, Y') }}</td>
                                        <td>
                                            @if($sponsor->user()->first()->status === 1)
                                                <label class="label label-success">Activated</label>
                                            @else
                                                <label class="label label-danger">Deactivated</label>
                                            @endif
                                        </td>
                                        <td>
                                            <a target="_blank" href="{{ url('/sponsors/view/'.$hashIds->encode($sponsor->sponsor_id)) }}" class="btn btn-info btn-rounded btn-condensed btn-xs">
                                                <span class="fa fa-eye-slash"></span>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ url('/sponsors/edit/'.$hashIds->encode($sponsor->sponsor_id)) }}" class="btn btn-warning btn-rounded btn-condensed btn-xs">
                                                <span class="fa fa-edit"></span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th style="width: 20%;">Full Name</th>
                                <th style="width: 15%;">Mobile</th>
                                <th style="width: 19%;">Email</th>
                                <th style="width: 15%;">Registered By</th>
                                <th style="width: 15%;">Registered On</th>
                                <th style="width: 5%;">Status</th>
                                <th style="width: 5%;">View</th>
                                <th style="width: 5%;">Edit</th>
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
    <script type="text/javascript" src="{{ asset('assets/global/plugins/select2/js/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/global/plugins/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}"></script>
    <!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('layout-script')
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/accounts/sponsors.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/sponsors"]');
            TableManaged.init();
        });
    </script>
@endsection
