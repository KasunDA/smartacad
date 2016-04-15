@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Assign Permission')

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
        <a href="{{ url('/permissions/roles-permissions') }}">Permission to roles</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Permissions to roles</h3>

    <div class="row">
        <div class="col-md-7 margin-bottom-10">
            <form method="post" action="/permissions/roles" role="form" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-md-3 control-label">Roles</label>

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

                <div class="form-group">
                    <div class="col-md-10">
                        <h3 class="text-center">Permissions in: <span
                                    class="text-danger">{{ $role->display_name }}</span> Role</h3>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <form method="post" action="/permissions/roles-permissions" role="form" class="form-horizontal">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-list font-green"></i>
                            <label style="font-size: small" class="pull-left"><input type="checkbox"
                                                                                     class="permissions_all" value="0"
                                                                                     name=""/>
                                <span>SELECT ALL PERMISSIONS</span>
                            </label>
                        </div>
                    </div>
                    <div class="portlet-body">
                        {!! csrf_field() !!}
                        {!! Form::hidden('role_id', $role->role_id, ['class'=>'form-control']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mt-element-list">
                                    <div class="mt-list-container list-simple ext-1">
                                        <ul>
                                            @for($i=0; $i<count($permissions); $i+=3)
                                                <li class="mt-list-item done">
                                                    <div class="list-icon-container">
                                                        ({{$i+1}})
                                                    </div>
                                                    <div class="pull-right">
                                                        <label>
                                                            @if( in_array($permissions[$i]->permission_id, array_values($role->perms()->get()->lists('permission_id')->toArray())))
                                                                <input checked type="checkbox" name="permission_id[]"
                                                                       value="{{ $permissions[$i]->permission_id }}"
                                                                       class="permissions_check_box color_border"/>
                                                                <span class="label label-danger">Remove</span>
                                                            @else
                                                                <input type="checkbox" name="permission_id[]"
                                                                       value="{{ $permissions[$i]->permission_id }}"
                                                                       class="permissions_check_box"/>
                                                                <span class="label label-success">Add</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                    <div class="list-item-content">
                                                        <h3 class="uppercase">
                                                            <a href="javascript:;">{{$permissions[$i]->display_name}}</a>
                                                        </h3>
                                                    </div>
                                                </li>
                                            @endfor
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mt-element-list">
                                    <div class="mt-list-container list-simple ext-1">
                                        <ul>
                                            @for($j=1; $j<count($permissions); $j+=3)
                                                <li class="mt-list-item done">
                                                    <div class="list-icon-container">
                                                        ({{$j+1}})
                                                    </div>
                                                    <div class="pull-right">
                                                        <label>
                                                            @if( in_array($permissions[$j]->permission_id, array_values($role->perms()->get()->lists('permission_id')->toArray())))
                                                                <input checked type="checkbox" name="permission_id[]"
                                                                       value="{{ $permissions[$j]->permission_id }}"
                                                                       class="permissions_check_box color_border"/>
                                                                <span class="label label-danger">Remove</span>
                                                            @else
                                                                <input type="checkbox" name="permission_id[]"
                                                                       value="{{ $permissions[$j]->permission_id }}"
                                                                       class="permissions_check_box"/>
                                                                <span class="label label-success">Add</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                    <div class="list-item-content">
                                                        <h3 class="uppercase">
                                                            <a href="javascript:;">{{$permissions[$j]->display_name}}</a>
                                                        </h3>
                                                    </div>
                                                </li>
                                            @endfor
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mt-element-list">
                                    <div class="mt-list-container list-simple ext-1">
                                        <ul>
                                            @for($k=2; $k<count($permissions); $k+=3)
                                                <li class="mt-list-item done">
                                                    <div class="list-icon-container">
                                                        ({{$k+1}})
                                                    </div>
                                                    <div class="pull-right">
                                                        <label>
                                                            @if( in_array($permissions[$k]->permission_id, array_values($role->perms()->get()->lists('permission_id')->toArray())))
                                                                <input checked type="checkbox" name="permission_id[]"
                                                                       value="{{ $permissions[$k]->permission_id }}"
                                                                       class="permissions_check_box color_border"/>
                                                                <span class="label label-danger">Remove</span>
                                                            @else
                                                                <input type="checkbox" name="permission_id[]"
                                                                       value="{{ $permissions[$k]->permission_id }}"
                                                                       class="permissions_check_box"/>
                                                                <span class="label label-success">Add</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                    <div class="list-item-content">
                                                        <h3 class="uppercase">
                                                            <a href="javascript:;">{{$permissions[$k]->display_name}}</a>
                                                        </h3>
                                                    </div>
                                                </li>
                                            @endfor
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary pull-right" type="submit"><i class="fa fa-save"></i> Save
                    record
                </button>
            </form>
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
    <script src="{{ asset('assets/custom/js/roles-permissions/permissions.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/permissions/roles-permissions/"]');
        });
    </script>
@endsection
