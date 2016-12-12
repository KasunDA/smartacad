@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/jstree/dist/themes/default/style.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Display Subject Grouping')


@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-circle"></i>
    </li>
    <li><span class="active">Subject Grouping</span></li>
@stop


@section('content')
<!-- BEGIN PAGE BASE CONTENT -->
    <h3 class="page-title"> Display Subject Grouping</h3>
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-social-dribbble font-blue-sharp"></i>
                            <span class="caption-subject font-green bold uppercase">Display Subject Grouping</span>
                        </div>
                    </div>
                    <div id="customs_tree" class="tree-demo">
                        <ul>
                            @if(count($customs) > 0)
                                @foreach($customs as $one)
                                    @if(count($one->getImmediateDescendants()) > 0)
                                        <li data-jstree='{  "opened" : true, "type" : "one"}'> {{ $one->name }}
                                            <ul>
                                                <!-- BEGIN LEVEL TWO -->
                                                @foreach($one->getImmediateDescendants() as $two)
                                                    <li data-jstree='{ "type" : "two" }'> {{ $two->subject->subject }}</li>
                                                @endforeach
                                                <!-- END LEVEL TWO -->
                                            </ul>
                                        </li>
                                    @else
                                        <li data-jstree='{ {{ ($one->active != 1) ? '"disabled" : true,' : "" }} "type" : "file" }'> {{ $one->name }}</li>
                                    @endif
                                @endforeach
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
    <script src="{{ asset('assets/custom/js/subjects/custom.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/custom-subjects"]');
            UITree.init();
        });
    </script>
@endsection