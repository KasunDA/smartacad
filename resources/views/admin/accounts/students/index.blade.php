@extends('admin.layout.default')

@section('layout-style')
    <!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Manage Student')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}"> <i class="fa fa-dashboard"></i> Dashboard</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <span>Manage Student</span>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Manage Student</h3>

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
                    <div class="row">
                        <table class="table table-striped table-bordered table-hover" id="student_tabledata">
                            <thead>
                                <tr>
                                    <th style="width: 1%;">#</th>
                                    <th style="width: 20%;">Full Name</th>
                                    <th style="width: 10%;">Current Class</th>
                                    <th style="width: 5%;">Gender</th>
                                    <th style="width: 19%;">Sponsor</th>
                                    <th style="width: 5%;">Status</th>
                                    {{--<th style="width: 5%;">View</th>--}}
                                    {{--<th style="width: 5%;">Edit</th>--}}
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($students) > 0)
                                <?php $i = 1; ?>
                                @foreach($students as $student)
                                    <tr class="odd gradeX">
                                        <td class="center">{{$i++}}</td>
                                        <td>{{ $student->fullNames() }}</td>
                                        <td>{!! ($student->classroom_id) ? $student->classRoom()->first()->classroom : '<span class="label label-danger">nil</span>' !!}</td>
                                        <td>{!! ($student->gender) ? $student->gender : '<span class="label label-danger">nil</span>' !!}</td>
                                        <td>
                                            @if(($student->sponsor_id))
                                                <a target="_blank" href="{{ url('/sponsors/view/'.$hashIds->encode($student->sponsor()->first()->user_id)) }}" class="btn btn-info btn-link btn-sm">
                                                    <span class="fa fa-eye-slash"></span> {{$student->sponsor()->first()->fullNames()}}
                                                </a>
                                            @else
                                                <span class="label label-danger">nil</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($student->status_id)
                                                <label class="label label-{{$student->status()->first()->label}}">{{ $student->status()->first()->status }}</label>
                                            @else
                                                <label class="label label-danger">nil</label>
                                            @endif
                                        </td>
                                        {{--<td>--}}
                                            {{--<a target="_blank" href="{{ url('/students/view/'.$hashIds->encode($student->user_id)) }}" class="btn btn-info btn-rounded btn-condensed btn-xs">--}}
                                                {{--<span class="fa fa-eye-slash"></span>--}}
                                            {{--</a>--}}
                                        {{--</td>--}}
                                        {{--<td>--}}
                                            {{--<a href="{{ url('/students/edit/'.$hashIds->encode($student->user_id)) }}" class="btn btn-warning btn-rounded btn-condensed btn-xs">--}}
                                                {{--<span class="fa fa-edit"></span>--}}
                                            {{--</a>--}}
                                        {{--</td>--}}
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th style="width: 1%;">#</th>
                                    <th style="width: 20%;">Full Name</th>
                                    <th style="width: 10%;">Current Class</th>
                                    <th style="width: 5%;">Gender</th>
                                    <th style="width: 19%;">Sponsor</th>
                                    <th style="width: 5%;">Status</th>
                                    {{--<th style="width: 5%;">View</th>--}}
                                    {{--<th style="width: 5%;">Edit</th>--}}
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
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
{{--    <script src="{{ asset('assets/custom/js/accounts/students.js') }}" type="text/javascript"></script>--}}
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/students"]');
            setTableData($('#student_tabledata')).init();
        });
    </script>
@endsection
