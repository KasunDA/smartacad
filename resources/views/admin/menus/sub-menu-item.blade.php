@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Manage Sub Menu Items')

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
        <a href="{{ url('/menu-headers') }}">Sub Menu Item</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Sub Menu Item</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-7 margin-bottom-10">
            <form method="post" action="/sub-menu-items/menu-item" role="form" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-md-3 control-label">Filter By Menu Item</label>
                    <div class="col-md-9">
                        <div class="col-md-9">
                            <select class="form-control selectpicker" name="menu_item_id" id="menu_select">
                                @foreach($menu_item_lists as $key => $value)
                                    @if($menu_item_id === $key)
                                        <option selected value="{{$key}}">{{$value}}</option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach
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
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Sub Menu items</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn green add_sub_menu_item"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <form method="post" action="/sub-menu-items" id="sub_menu_item_form" role="form"
                                  class="form-horizontal">
                                {!! csrf_field() !!}
                                <div id="menu_container">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="sub_menu_item_table">
                                            <thead>
                                            <tr>
                                                <th style="width: 1%;">#</th>
                                                <th style="width: 10%;">Title</th>
                                                <th style="width: 10%;">Url</th>
                                                <th style="width: 9%;">Icon</th>
                                                <th style="width: 15%;">Parent Menu</th>
                                                <th style="width: 10%;">Status</th>
                                                <th style="width: 25%;">Role</th>
                                                <th style="width: 5%;">Order</th>
                                                <th style="width: 5%;">actions</th>
                                            </tr>
                                            </thead>
                                            <tbody id="sub_menu_item_tbody">
                                            @if(count($sub_menu_items) > 0)
                                                <?php $i = 1; ?>
                                                @foreach($sub_menu_items as $sub_menu_item)
                                                    <tr>
                                                        <td class="text-center">{{$i}}</td>
                                                        <td>
                                                            {!! Form::text('sub_menu_item[]', $sub_menu_item->sub_menu_item, ['placeholder'=>'Sub Menu Item', 'class'=>'form-control', 'required'=>'required']) !!}
                                                            {!! Form::hidden('sub_menu_item_id[]', $sub_menu_item->sub_menu_item_id, ['class'=>'form-control']) !!}
                                                        </td>
                                                        <td>{!! Form::text('sub_menu_item_url[]', $sub_menu_item->sub_menu_item_url, ['placeholder'=>'Sub Menu Item Url', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                        <td>
                                                            <div class="input-group">
                                                        <span class="input-group-addon"><i
                                                                    class="{{$sub_menu_item->sub_menu_item_icon}}"></i></span>
                                                                {!! Form::text('sub_menu_item_icon[]', $sub_menu_item->sub_menu_item_icon, ['placeholder'=>'Sub Menu Icon', 'class'=>'form-control', 'required'=>'required']) !!}
                                                            </div>
                                                        </td>
                                                        <td>{!! Form::select('menu_item_id[]', $menu_item_lists, $sub_menu_item->menu_item_id, ['class'=>'form-control']) !!} </td>
                                                        <td> {!! Form::select('active[]', [''=>'Status', 1=>'Enable', 0=>'Disable'], $sub_menu_item->active, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                        <td>
                                                            <select multiple class="form-control selectpicker"
                                                                    name="role_id[{{$i}}][]">
                                                                @foreach($roles as $role)
                                                                    @if(in_array($role->role_id, $sub_menu_item->roles()->get()->lists('role_id')->toArray()))
                                                                        <option selected
                                                                                value="{{ $role->role_id }}">{{ $role->display_name }}</option>
                                                                    @else
                                                                        <option value="{{ $role->role_id }}">{{ $role->display_name }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>{!! Form::text('sequence[]', $sub_menu_item->sequence, ['placeholder'=>'Order By', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                        <td>
                                                            <button class="btn btn-danger btn-rounded btn-condensed btn-sm delete_sub_menu_item">
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
                                                        {!! Form::text('sub_menu_item[]', '', ['placeholder'=>'Sub Menu Item', 'class'=>'form-control', 'required'=>'required']) !!}
                                                        {!! Form::hidden('sub_menu_item_id[]', '-1', ['class'=>'form-control']) !!}
                                                    </td>
                                                    <td>{!! Form::text('sub_menu_item_url[]', '', ['placeholder'=>'Sub Menu Item Url', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i class=""></i></span>
                                                            {!! Form::text('sub_menu_item_icon[]', '', ['placeholder'=>'Sub Menu Item Icon', 'class'=>'form-control', 'required'=>'required']) !!}
                                                        </div>
                                                    </td>
                                                    <td>{!! Form::select('menu_item_id[]', $menu_item_lists, '', ['class'=>'form-control', 'required'=>'required']) !!} </td>
                                                    <td>{!! Form::select('active[]', [''=>'Status', 1=>'Enable', 0=>'Disable'],'', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>
                                                        <select multiple class="form-control selectpicker"
                                                                name="role_id[1][]">
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
                                                <th style="width: 1%;">#</th>
                                                <th style="width: 10%;">Title</th>
                                                <th style="width: 10%;">Url</th>
                                                <th style="width: 9%;">Icon</th>
                                                <th style="width: 15%;">Parent Menu</th>
                                                <th style="width: 10%;">Status</th>
                                                <th style="width: 25%;">Role</th>
                                                <th style="width: 5%;">Order</th>
                                                <th style="width: 5%;">actions</th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="form-actions noborder">
                                        <button class="btn green pull-left add_sub_menu_item"> Add New
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <button type="submit" class="btn blue pull-right">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="hide" id="new_roles">
        <select multiple class="form-control" name="role_id[][]">
            @foreach($roles as $role)
                <option value="{{ $role->role_id }}">{{ $role->display_name }}</option>
            @endforeach
        </select>
    </div>
    <!-- END CONTENT BODY -->
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
    <script src="{{ asset('assets/custom/js/menus/sub-menu-items.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/sub-menu-items"]');
        });
    </script>
@endsection
