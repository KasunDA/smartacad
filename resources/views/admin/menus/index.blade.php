@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/jstree/dist/themes/default/style.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Display Menus')


@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-circle"></i>
    </li>
    <li><span class="active">Menus</span></li>
@stop


@section('content')
<!-- BEGIN PAGE BASE CONTENT -->
    <h3 class="page-title"> Display Menus Levels And Roles Assigned</h3>
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-social-dribbble font-blue-sharp"></i>
                            <span class="caption-subject font-green bold uppercase">Display Menus</span>
                        </div>
                    </div>
                    <div id="menus_tree" class="tree-demo">
                        <ul>
                            @if(count($menus) > 0)
                                <!-- BEGIN LEVEL ONE -->
                                @foreach($menus as $one)
                                    @if(count($one->getImmediateDescendants()) > 0)
                                        <li data-jstree='{ {{ ($one->active != 1) ? '"disabled" : true,' : "" }} "opened" : true, "type" : "one"}'> {{ $one->name }}::
                                            @foreach($one->roles()->get(['display_name']) as $role)
                                                <span class="font-green"><i class="fa fa-tag"></i> {{ $role->display_name }} </span>
                                            @endforeach
                                            <ul>
                                                <!-- BEGIN LEVEL TWO -->
                                                @foreach($one->getImmediateDescendants() as $two)
                                                    @if(count($two->getImmediateDescendants()) > 0)
                                                        <li data-jstree='{ {{ ($two->active != 1) ? '"disabled" : true,' : "" }} "opened" : true, "type" : "two" }'> {{ $two->name }}::
                                                            @foreach($two->roles()->get(['display_name']) as $role)
                                                                <span class="font-green"><i class="fa fa-tag"></i> {{ $role->display_name }} </span>
                                                            @endforeach
                                                            <ul>
                                                                <!-- BEGIN LEVEL THREE -->
                                                                @foreach($two->getImmediateDescendants() as $three)
                                                                    @if(count($three->getImmediateDescendants()) > 0)
                                                                        <li data-jstree='{ {{ ($three->active != 1) ? '"disabled" : true,' : "" }} "opened" : true, "type" : "three" }'> {{ $three->name }}::
                                                                            @foreach($three->roles()->get(['display_name']) as $role)
                                                                                <span class="font-green"><i class="fa fa-tag"></i> {{ $role->display_name }} </span>
                                                                            @endforeach
                                                                            <ul>
                                                                                <!-- BEGIN LEVEL FOUR -->
                                                                                @foreach($three->getImmediateDescendants() as $four)
                                                                                    @if(count($four->getImmediateDescendants()) > 0)
                                                                                        <li data-jstree='{ {{ ($four->active != 1) ? '"disabled" : true,' : "" }} "opened" : true, "type" : "four" }'> {{ $four->name }}::
                                                                                            @foreach($four->roles()->get(['display_name']) as $role)
                                                                                                <span class="font-green"><i class="fa fa-tag"></i> {{ $role->display_name }} </span>
                                                                                            @endforeach
                                                                                            <ul>
                                                                                                <!-- BEGIN LEVEL FIVE -->
                                                                                                @foreach($four->getImmediateDescendants() as $five)
                                                                                                    <li data-jstree='{ {{ ($five->active != 1) ? '"disabled" : true,' : "" }} "opened" : true, "type" : "five"  }'> {{ $five->name }}::
                                                                                                        @foreach($five->roles()->get(['display_name']) as $role)
                                                                                                            <span class="font-green"><i class="fa fa-tag"></i> {{ $role->display_name }} </span>
                                                                                                        @endforeach
                                                                                                    </li>
                                                                                                @endforeach
                                                                                                <!-- END LEVEL FIVE -->
                                                                                            </ul>
                                                                                        </li>
                                                                                    @else
                                                                                        <li data-jstree='{ {{ ($four->active != 1) ? '"disabled" : true,' : "" }} "type" : "file" }'> {{ $four->name }}::
                                                                                            @foreach($four->roles()->get(['display_name']) as $role)
                                                                                                <span class="font-green"><i class="fa fa-tag"></i> {{ $role->display_name }} </span>
                                                                                            @endforeach
                                                                                        </li>
                                                                                    @endif
                                                                                @endforeach
                                                                               <!-- END LEVEL FOUR -->
                                                                            </ul>
                                                                        </li>
                                                                    @else
                                                                        <li data-jstree='{ {{ ($three->active != 1) ? '"disabled" : true,' : "" }} "type" : "file" }'> {{ $three->name }}::
                                                                            @foreach($three->roles()->get(['display_name']) as $role)
                                                                                <span class="font-green"><i class="fa fa-tag"></i> {{ $role->display_name }} </span>
                                                                            @endforeach
                                                                        </li>
                                                                    @endif
                                                                @endforeach
                                                                <!-- END LEVEL THREE -->
                                                            </ul>
                                                        </li>
                                                    @else
                                                        <li data-jstree='{ {{ ($two->active != 1) ? '"disabled" : true,' : "" }} "type" : "file" }'> {{ $two->name }}::
                                                            @foreach($two->roles()->get(['display_name']) as $role)
                                                                <span class="font-green"><i class="fa fa-tag"></i> {{ $role->display_name }} </span>
                                                            @endforeach
                                                        </li>
                                                    @endif
                                                @endforeach
                                                <!-- END LEVEL TWO -->
                                            </ul>
                                        </li>
                                    @else
                                        <li data-jstree='{ {{ ($one->active != 1) ? '"disabled" : true,' : "" }} "type" : "file" }'> {{ $one->name }}::
                                            @foreach($one->roles()->get(['display_name']) as $role)
                                                <span class="font-green"><i class="fa fa-tag"></i> {{ $role->display_name }} </span>
                                            @endforeach
                                        </li>
                                    @endif
                                @endforeach
                                <!-- END LEVEL ONE -->
                                @for($i = 0; $i <= $max; $i++)
                                    <li data-jstree='{ "type" : "add" }'>
                                        <a href="/menus/level-{{ $i + 1 }}"> Add New Menu Level {{ $i + 1 }}</a>
                                    </li>
                                @endfor
                            @else
                                <li data-jstree='{ "type" : "add" }'>
                                    <a href="/menus/level-1"> No Menu was created...create some!</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
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
    <script src="{{ asset('assets/global/plugins/jstree/dist/jstree.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/menus/menus.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/menus"]');
            UITree.init();
        });
    </script>
@endsection