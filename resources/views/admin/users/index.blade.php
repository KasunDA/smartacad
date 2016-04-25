@extends('admin.layout.default')

@section('layout-style')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ asset('assets/global/plugins/select2/css/select2.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Manage User')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <span>Manage User</span>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Manage Users</h3>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Registered</span>
                    </div>
                    <div class="tools">
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <table class="table table-striped table-bordered table-hover" id="users_datatable">
                            <thead>
                                <tr>
                                    <th style="width: 1%;">#</th>
                                    <th style="width: 25%;">Names</th>
                                    <th style="width: 10%;">Username</th>
                                    <th style="width: 19%;">Email</th>
                                    <th style="width: 20%;">User Type</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 5%;">View</th>
                                    <th style="width: 5%;">Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($users) > 0)
                                <?php $i = 1; ?>
                                @foreach($users as $user)
                                    <tr class="odd gradeX">
                                        <td class="center">{{$i++}}</td>
                                        <td>{{ $user->fullNames() }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->userType()->first()->user_type }}</td>
                                        <td>
                                            @if($user->status === 1)
                                                <button value="{{ $user->user_id }}" rel="2" class="btn btn-success btn-rounded btn-condensed btn-xs user_status">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button value="{{ $user->user_id }}" rel="1" class="btn btn-danger btn-rounded btn-condensed btn-xs user_status">
                                                    Activate
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            <a target="_blank" href="{{ url('/users/view/'.$hashIds->encode($user->user_id)) }}" class="btn btn-info btn-rounded btn-condensed btn-xs">
                                                <span class="fa fa-eye-slash"></span>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ url('/users/edit/'.$hashIds->encode($user->user_id)) }}" class="btn btn-warning btn-rounded btn-condensed btn-xs">
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
                                    <th style="width: 25%;">Names</th>
                                    <th style="width: 10%;">Username</th>
                                    <th style="width: 19%;">Email</th>
                                    <th style="width: 20%;">User Type</th>
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
    <script src="{{ asset('assets/custom/js/users/user.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/users"]');
            TableManaged.init();
        });
    </script>
@endsection
