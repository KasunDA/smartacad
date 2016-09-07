@extends('front.layout.default')

@section('layout-style')
    <!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Manage Students')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}"> <i class="fa fa-dashboard"></i> Dashboard</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <span>Manage Students</span>
    </li>
@stop


@section('page-title')
    <h1> Manage My Wards (Students) </h1>
@endsection

@section('content')
    <div class="col-md-12">

        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-list font-green"></i>
                            <span class="caption-subject font-green bold uppercase">Registered Students</span>
                        </div>
                        <div class="tools">
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container">
                            <div class="table-actions-wrapper">
                                <span> </span>
                                Search: <input type="text" class="form-control input-inline input-small input-sm"/><br>
                            </div>
                            <table class="table table-striped table-bordered table-hover" id="student_tabledata">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th width="2%">#</th>
                                        <th width="28%">Full Name</th>
                                        <th width="15%">Current Class</th>
                                        <th width="10%">Gender</th>
                                        <th width="10%">Age</th>
                                        <th width="15%">Date Of Birth</th>
                                        <th width="10%">Status</th>
                                        <th width="5%">View</th>
                                        <th width="5%">Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($students as $student)
                                        <tr>
                                            <td>{{$i++}} </td>
                                            <td>{{ $student->fullNames() }}</td>
                                            <td>{!! ($student->classroom_id) ? $student->classRoom()->first()->classroom : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{!! ($student->gender) ? $student->gender : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{!! ($student->dob) ? $student->dob->age . ' Years' : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{!! ($student->dob) ? $student->dob->format('jS M, Y') : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>
                                                @if($student->status_id)
                                                    <label class="label label-{{$student->status()->first()->label}}">{{ $student->status()->first()->status }}</label>
                                                @else
                                                    <label class="label label-danger">nil</label>
                                                @endif
                                            </td>
                                            <td>
                                                <a target="_blank" href="{{ url('/my-wards/view/'.$hashIds->encode($student->student_id)) }}" class="btn btn-info btn-rounded btn-condensed btn-xs">
                                                    <span class="fa fa-eye-slash"></span>
                                                </a>
                                            </td>
                                            <td>
                                                <a target="_blank" href="{{ url('/my-wards/edit/'.$hashIds->encode($student->student_id)) }}" class="btn btn-warning btn-rounded btn-condensed btn-xs">
                                                    <span class="fa fa-edit"></span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr role="row" class="heading">
                                        <th width="2%">#</th>
                                        <th width="28%">Full Name</th>
                                        <th width="15%">Current Class</th>
                                        <th width="10%">Gender</th>
                                        <th width="10%">Age</th>
                                        <th width="15%">Date Of Birth</th>
                                        <th width="10%">Status</th>
                                        <th width="5%">View</th>
                                        <th width="5%">Update</th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
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
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/accounts/students.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/my-wards"]');

            setTableData($('#student_tabledata')).init();

            {{--$.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });--}}
        });
    </script>
@endsection
