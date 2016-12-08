@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Manage Menu Level Two')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <a href="{{ url('/menus') }}">Menus</a>
        <i class="fa fa-circle"></i>
    </li>
    <li><span class="active">Level Two</span></li>
@stop


@section('content')
    <!-- BEGIN PAGE BASE CONTENT -->
    <h3 class="page-title"> Manage Menu: Level Two</h3>
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-8 col-md-offset-2 margin-bottom-20 margin-top-20">
                <form method="post" action="/menus/filter" role="form" class="form-horizontal">
                    {!! csrf_field() !!}
                    {!! Form::hidden('level', 2, ['class'=>'form-control']) !!}
                    <div class="form-group">
                        <label class="col-md-3 control-label">Filter By Parent Menu</label>
                        <div class="col-md-6">
                            <div class="col-md-9">
                                <select class="form-control selectpicker" name="menu_id" required>
                                    @foreach($parents as $key => $value)
                                        @if($key === $menu_id)
                                            <option selected value="{{$key}}">{{$value}}</option>
                                        @else
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endif
                                    @endforeach
                                    <option value="all">ALL LEVEL TWO</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary pull-right" type="submit">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-tree font-green"></i>
                            <span class="caption-subject font-green bold uppercase">Menu Level Two</span>
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
                                    ])
                                !!}
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="menu_table">
                                        <thead>
                                        <tr>
                                            <th width="1%" class="text-center">#</th>
                                            <th width="20%">Name</th>
                                            <th width="12%">Parent</th>
                                            <th width="10%">Icon</th>
                                            <th width="10%">URL</th>
                                            <th width="9%">Status</th>
                                            <th width="30%">Role</th>
                                            <th width="4%">Type</th>
                                            <th width="4%">Sort</th>
                                            <th width="5%">Actions</th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                            <th width="1%" class="text-center">#</th>
                                            <th width="20%">Name</th>
                                            <th width="12%">Parent</th>
                                            <th width="10%">Icon</th>
                                            <th width="10%">URL</th>
                                            <th width="9%">Status</th>
                                            <th width="30%">Role</th>
                                            <th width="4%">Type</th>
                                            <th width="4%">Sort</th>
                                            <th width="5%">Actions</th>
                                        </tr>
                                        </tfoot>
                                        <tbody>
                                            @if(count($menus) > 0)
                                                <?php $i = 1; ?>
                                                @foreach($menus as $parent_menu)
                                                    @foreach($parent_menu->getImmediateDescendants() as $menu)
                                                        <tr>
                                                            <td class="text-center">{{$i}} </td>
                                                            <td>
                                                                {!! Form::text('name[]', $menu->name, ['placeholder'=>'Menu Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                                                {!! Form::hidden('menu_id[]', $menu->menu_id, ['class'=>'form-control']) !!}
                                                            </td>
                                                            <td>{!! Form::select('parent_id[]', $parents, $menu->parent_id, ['class'=>'form-control']) !!} </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-addon"><i class="{{$menu->icon}}"></i></span>
                                                                    {!! Form::text('icon[]', $menu->icon, ['placeholder'=>'Icon', 'class'=>'form-control']) !!}
                                                                </div>
                                                            </td>
                                                            <td>{!! Form::text('url[]', $menu->url, ['placeholder'=>'Url', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                            <td> {!! Form::select('active[]', [''=>'Status', 1=>'Enable', 0=>'Disable'], $menu->active, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                            <td>
                                                                <select multiple class="form-control selectpicker" name="role_id[{{$i}}][]">
                                                                    @foreach($roles as $role)
                                                                        @if(in_array($role->role_id, $menu->roles()->get(['menus_roles.role_id'])->pluck('role_id')->toArray()))
                                                                            <option selected value="{{ $role->role_id }}">{{ $role->display_name }}</option>
                                                                        @else
                                                                            <option value="{{ $role->role_id }}">{{ $role->display_name }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>{!! Form::text('type[]', $menu->type, ['placeholder'=>'Type', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                            <td>{!! Form::text('sequence[]', $menu->sequence, ['placeholder'=>'Sort', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                            <td>
                                                                <button class="btn btn-danger btn-rounded btn-condensed btn-xs delete_menu">
                                                                    <span class="fa fa-trash-o"></span> Delete
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <?php $i++; ?>
                                                    @endforeach
                                                @endforeach
                                                @if($sub < 1)
                                                    <tr>
                                                        <td class="text-center">1</td>
                                                        <td>
                                                            {!! Form::text('name[]', '', ['placeholder'=>'Menu Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                                            {!! Form::hidden('menu_id[]', '-1', ['class'=>'form-control']) !!}
                                                        </td>
                                                        <td>{!! Form::select('parent_id[]', $parents, '', ['class'=>'form-control']) !!} </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-addon"><i class=""></i></span>
                                                                {!! Form::text('icon[]', '', ['placeholder'=>'Icon', 'class'=>'form-control']) !!}
                                                            </div>
                                                        </td>
                                                        <td>{!! Form::text('url[]', '', ['placeholder'=>'Url', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                        <td>{!! Form::select('active[]', [''=>'Status', 1=>'Enable', 0=>'Disable'],'', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                        <td>
                                                            <select multiple class="form-control selectpicker" name="role_id[1][]">
                                                                @foreach($roles as $role)
                                                                    <option value="{{ $role->role_id }}">{{ $role->display_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>{!! Form::text('type[]', '', ['placeholder'=>'Type', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                        <td>{!! Form::text('sequence[]', '', ['placeholder'=>'Sort', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                        <td>
                                                            <button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_menu">
                                                                <span class="fa fa-times"></span> Remove
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif
                                        </tbody>
                                    </table>
                                    <div class="form-actions noborder">
                                        <button class="btn green pull-left add_menu"> Add New
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <button type="submit" class="btn blue pull-right">Submit</button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
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
    <!-- END PAGE BASE CONTENT -->
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
    <script src="{{ asset('assets/custom/js/menus/sub-menus.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/menus/level-2"]');
        });
    </script>
@endsection