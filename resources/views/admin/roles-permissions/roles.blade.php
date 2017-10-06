@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Manage Roles')

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
    <h3 class="page-title"> Roles</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Roles</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn btn-sm green add_role"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            {!! Form::open([
                                   'method'=>'POST',
                                   'class'=>'form',
                                   'role'=>'form',
                                   'id'=>'menu_header_form'
                                ])
                            !!}
                            <table class="table table-bordered table-striped table-actions" id="role_table">
                                <thead>
                                <tr>
                                    <th style="width: 1%;">s/no</th>
                                    <th style="width: 10%;">Role (Unique)</th>
                                    <th style="width: 22%;">Display Name</th>
                                    <th style="width: 25%;">Description</th>
                                    <th style="width: 15%;">User Type</th>
                                    <th style="width: 5%;">Actions</th>
                                </tr>
                                </thead>
                                @if(count($roles) > 0)
                                    <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($roles as $role)
                                        <tr>
                                            <td class="text-center">{{$i++}}</td>
                                            <td>
                                                {!! Form::text('name[]', $role->name, ['placeholder'=>'Role Name (Unique)', 'class'=>'form-control', 'required'=>'required']) !!}
                                                {!! Form::hidden('role_id[]', $role->role_id, ['class'=>'form-control']) !!}
                                            </td>
                                            <td>{!! Form::text('display_name[]', $role->display_name  , ['placeholder'=>'Role Display name', 'class'=>'form-control','required'=>'required']) !!} </td>
                                            <td>{!! Form::text('description[]', $role->description  , ['placeholder'=>'Role Description', 'class'=>'form-control']) !!} </td>
                                            <td>{!! Form::select('user_type_id[]', $user_types, $role->user_type_id, ['class'=>'form-control selectpicker']) !!} </td>
                                            <td>
                                                <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$role->name}}" data-title="Delete Confirmation"
                                                         data-message="Are you sure you want to delete <b>{{$role->name}}?</b>"
                                                         data-action="/roles/delete/{{$role->role_id}}" class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
                                                    <span class="fa fa-trash-o"></span> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                @else
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>
                                            {!! Form::text('name[]', '', ['placeholder'=>'Role Name (Unique)', 'class'=>'form-control', 'required'=>'required']) !!}
                                            {!! Form::hidden('role_id[]', '-1', ['class'=>'form-control']) !!}
                                        </td>
                                        <td>{!! Form::text('display_name[]', ''  , ['placeholder'=>'Role Display name', 'class'=>'form-control','required'=>'required']) !!} </td>
                                        <td>{!! Form::text('description[]', ''  , ['placeholder'=>'Role Description', 'class'=>'form-control']) !!} </td>
                                        <td>{!! Form::select('user_type_id[]', $user_types, '', ['class'=>'form-control selectpicker']) !!} </td>
                                        <td>
                                            <button class="btn btn-danger btn-rounded btn-condensed btn-sm">
                                                <span class="fa fa-times"></span> Remove
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                            <div class="col-md-12 margin-bottom-10 pull-left">
                                <div class="btn-group">
                                    <button class="btn btn-sm green add_role"> Add New
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-actions noborder">
                                <button type="submit" class="btn blue pull-right">Submit</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hide" id="new_user_types">
        <select class="form-control" name="user_type_id[]">
            <option value="">Select User Type</option>
            @foreach($user_types as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>
    <!-- END CONTENT BODY -->
    @endsection
@section('page-level-js')
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    @endsection

@section('layout-script')
            <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/custom/js/roles-permissions/roles.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/roles"]');
            setTableData($('#role_table')).init();
        });
    </script>
@endsection
