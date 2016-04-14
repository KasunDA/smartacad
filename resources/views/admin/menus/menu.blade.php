@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Manage Menus')

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
        <a href="{{ url('/menu-headers') }}">Menus</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Menus </h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Menus</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn green add_menu"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            {!! Form::open([
                                'method'=>'POST',
                                'class'=>'form',
                                'role'=>'form',
                                'id'=>'menu_form'
                            ])
                        !!}
                            <div class="table-responsive">
                                <table class="table table-bordered" id="menu_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 2%;">#</th>
                                        <th style="width: 20%;">Menu</th>
                                        <th style="width: 10%;">Menu Url</th>
                                        <th style="width: 15%;">Menu Icon</th>
                                        <th style="width: 15%;">Header Menu</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 20%;">Role</th>
                                        <th style="width: 4%;">Order</th>
                                        <th style="width: 4%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($menus) > 0)
                                        <?php $i = 1; ?>
                                        @foreach($menus as $menu)
                                            <tr>
                                                <td class="text-center">{{$i}} </td>
                                                <td>
                                                    {!! Form::text('menu[]', $menu->menu, ['placeholder'=>'Menu', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('menu_id[]', $menu->menu_id, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::text('menu_url[]', $menu->menu_url, ['placeholder'=>'Menu Url', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="{{$menu->icon}}"></i></span>
                                                        {!! Form::text('icon[]', $menu->icon, ['placeholder'=>'Menu Icon', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    </div>
                                                </td>
                                                <td>{!! Form::select('menu_header_id[]', $menu_header_lists, $menu->menu_header_id, ['class'=>'form-control']) !!} </td>
                                                <td>{!! Form::select('active[]', [''=>'Status', 1=>'Enable', 0=>'Disable'], $menu->active, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <select multiple class="form-control selectpicker"
                                                            name="role_id[{{$i}}][]">
                                                        @foreach($roles as $role)
                                                            @if(in_array($role->role_id, $menu->roles()->get()->lists('role_id')->toArray()))
                                                                <option selected
                                                                        value="{{ $role->role_id }}">{{ $role->display_name }}</option>
                                                            @else
                                                                <option value="{{ $role->role_id }}">{{ $role->display_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>{!! Form::text('sequence[]', $menu->sequence, ['placeholder'=>'Order By', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <button class="btn btn-danger btn-rounded btn-condensed btn-sm delete_menu">
                                                        <span class="fa fa-trash-o"></span> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td>
                                                {!! Form::text('menu[]', '', ['placeholder'=>'Menu', 'class'=>'form-control', 'required'=>'required']) !!}
                                                {!! Form::hidden('menu_id[]', '-1', ['class'=>'form-control']) !!}
                                            </td>
                                            <td>{!! Form::text('menu_url[]', '', ['placeholder'=>'Menu Url', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class=""></i></span>
                                                    {!! Form::text('icon[]', '', ['placeholder'=>'Menu Icon', 'class'=>'form-control', 'required'=>'required']) !!}
                                                </div>
                                            </td>
                                            <td>{!! Form::select('menu_header_id[]', $menu_header_lists, '', ['class'=>'form-control', 'required'=>'required']) !!} </td>
                                            <td>{!! Form::select('active[]', [''=>'Status', 1=>'Enable', 0=>'Disable'], '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>
                                                <select multiple class="form-control selectpicker" name="role_id[1][]">
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->role_id }}">{{ $role->display_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>{!! Form::text('sequence[]', '', ['placeholder'=>'Order By', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>
                                                <button class="btn btn-danger btn-rounded btn-condensed btn-sm">
                                                    <span class="fa fa-times"></span> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th style="width: 2%;">#</th>
                                        <th style="width: 20%;">Menu</th>
                                        <th style="width: 10%;">Menu Url</th>
                                        <th style="width: 15%;">Menu Icon</th>
                                        <th style="width: 15%;">Header Menu</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 20%;">Role</th>
                                        <th style="width: 4%;">Order</th>
                                        <th style="width: 4%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="form-actions noborder">
                                <button class="btn green pull-left add_menu"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                                <button type="submit" class="btn blue pull-right">Submit</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- END CONTENT BODY -->
    <div class="hide" id="new_roles">
        <select multiple class="form-control" name="role_id[][]">
            @foreach($roles as $role)
                <option value="{{ $role->role_id }}">{{ $role->display_name }}</option>
            @endforeach
        </select>
    </div>
    @endsection


    @section('layout-script')
            <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="assets/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="assets/global/scripts/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="assets/pages/scripts/ui-bootbox.min.js" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
    <script src="assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
    <script src="assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <script src="assets/custom/js/menus/menu.js" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/menus"]');
        });
    </script>
@endsection
