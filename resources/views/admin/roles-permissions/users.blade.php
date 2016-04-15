@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Assign Users Roles')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/roles') }}">Roles</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Manage user Roles</h3>
    <div class="row">
        <div class="col-md-7 margin-bottom-10">
            <form method="post" action="/roles/roles" role="form" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-md-3 control-label">Filter By Roles</label>
                    <div class="col-md-6">
                        <div class="col-md-9">
                            <select class="form-control selectpicker" name="role_id" id="role_id">
                                @foreach($roles as $key => $value)
                                    @if($key === $role->role_id)
                                        <option selected value="{{$key}}">{{$value}}</option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary pull-right" type="submit">Enter</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Users in: <span class="text-danger">{{ $role->display_name }}</span> Role</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <form method="post" action="/roles/users-roles/{{$encodeId}}" role="form" class="form-horizontal">
                        {!! csrf_field() !!}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-actions datatable">
                                <thead>
                                <tr>
                                    <th style="width: 1%;">#</th>
                                    <th style="width: 16%;">Names</th>
                                    <th style="width: 16%;">Email</th>
                                    <th style="width: 5%;">Gender</th>
                                    <th style="width: 13%;">User Type</th>
                                    <th style="width: 26%;">Roles</th>
                                    <th style="width: 5%;">View</th>
                                    <th style="width: 5%;">Edit</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($users) > 0)
                                    <?php $i = 1; ?>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{$i++}} </td>
                                            <td>{{ $user->fullNames() }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{!! ($user->gender) ? $user->gender : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>{{ $user->userType()->first()->user_type }}</td>
                                            <td>
                                                <select multiple class="form-control selectpicker" name="role_id[role{{$user->user_id}}][]">
                                                    @foreach($roles as $key => $value)
                                                        @if($user->roles() && in_array($key, $user->roles()->get()->lists('role_id')->toArray()))
                                                            <option selected value="{{$key}}">{{$value}}</option>
                                                        @else
                                                            <option value="{{$key}}">{{$value}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <input name="user_id[]" type="hidden" value="{{$user->user_id}}">
                                            </td>
                                            <td>
                                                <a target="_blank" href="{{ url('/users/show/'.$hashIds->encode($user->user_id)) }}" class="btn btn-info btn-rounded btn-condensed btn-xs">
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
                            </table>
                            <div class="form-actions noborder">
                                <button type="submit" class="btn blue pull-right">Save Record</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/roles-permissions/roles.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/roles/users-roles"]');
        });
    </script>
@endsection
