@extends('admin.layout.default')

@section('layout-style')
        <!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/select2/css/select2.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Manage Schools')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <span>Manage Schools</span>
    </li>
@stop

@section('content')
    <h3 class="page-title"> Manage Schools</h3>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Registered Schools</span>
                    </div>
                    <div class="tools">
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <table class="table table-striped table-bordered table-hover" id="schools_datatable">
                            <thead>
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th style="width: 34%;">School Name</th>
                                <th style="width: 10%;">Mobile No.</th>
                                <th style="width: 15%;">Email</th>
                                <th style="width: 15%;">Website</th>
                                <th style="width: 5%;">D.B</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 5%;">View</th>
                                <th style="width: 5%;">Edit</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($schools) > 0)
                                <?php $i = 1; ?>
                                @foreach($schools as $school)
                                    <tr class="odd gradeX">
                                        <td class="center">{{$i++}}</td>
                                        <td>{{ $school->full_name }}</td>
                                        <td>{{ $school->phone_no }}</td>
                                        <td>{!! ($school->email) ? $school->email : '<span class="label label-danger">nil</span>' !!}</td>
                                        <td>{!! ($school->website) ? $school->website : '<span class="label label-danger">nil</span>' !!}</td>
                                        <td>
                                            <a href="{{ url('/schools/db-config/'.$hashIds->encode($school->schools_id)) }}" class="btn btn-default btn-rounded btn-condensed btn-xs">
                                                <span class="fa fa-gears"></span>
                                            </a>
                                        </td>
                                        <td>
                                            @if($school->status_id === 1)
                                                <button value="{{ $school->schools_id }}" rel="2" class="btn btn-success btn-rounded btn-condensed btn-xs school_status">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button value="{{ $school->schools_id }}" rel="1" class="btn btn-danger btn-rounded btn-condensed btn-xs school_status">
                                                    Activate
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            <a target="_blank" href="{{ url('/schools/view/'.$hashIds->encode($school->schools_id)) }}" class="btn btn-info btn-rounded btn-condensed btn-xs">
                                                <span class="fa fa-eye-slash"></span>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ url('/schools/edit/'.$hashIds->encode($school->schools_id)) }}" class="btn btn-warning btn-rounded btn-condensed btn-xs">
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
                                <th style="width: 34%;">School Name</th>
                                <th style="width: 10%;">Mobile No.</th>
                                <th style="width: 15%;">Email</th>
                                <th style="width: 15%;">Website</th>
                                <th style="width: 5%;">D.B</th>
                                <th style="width: 10%;">Status</th>
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
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('layout-script')
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/schools/school.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/schools"]');
            setTableData($('#schools_datatable')).init();
        });
    </script>
@endsection
