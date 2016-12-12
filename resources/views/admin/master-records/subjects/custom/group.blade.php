@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Manage Subject Groupings')

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
        <a href="{{ url('/custom-subjects') }}">Subject Groupings</a>
        <i class="fa fa-circle"></i>
    </li>
    <li><span class="active">Grouping</span></li>
@stop


@section('content')
<!-- BEGIN PAGE BASE CONTENT -->
    <h3 class="page-title"> Manage Subject Grouping</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-tree font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Custom Grouping</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn green add_custom"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-10">
                            {!! Form::open([
                                   'method'=>'POST',
                                   'class'=>'form',
                                   'role'=>'form',
                                ])
                            !!}
                            <div class="table-responsive">
                                <table class="table table-bordered" id="custom_table">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="45%">Subject Group Name</th>
                                            <th width="25%">Group Abbr.</th>
                                            <th width="25%">Class Group</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="45%">Subject Group Name</th>
                                            <th width="25%">Group Abbr.</th>
                                            <th width="25%">Class Group</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @if(count($customs) > 0)
                                            <?php $i = 1; ?>
                                            @foreach($customs as $custom)
                                                <tr>
                                                    <td class="text-center">{{$i}} </td>
                                                    <td>
                                                        {!! Form::text('name[]', $custom->name, ['placeholder'=>'Grouping Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                                        {!! Form::hidden('custom_subject_id[]', $custom->custom_subject_id, ['class'=>'form-control']) !!}
                                                    </td>
                                                    <td>{!! Form::text('abbr[]', $custom->abbr, ['placeholder'=>'Grouping Abbr.', 'class'=>'form-control']) !!}</td>
                                                    <td>
                                                        <select class="form-control" name="classgroup_id[]">
                                                            <option value="">-- Class Group --</option>
                                                            @foreach($groups as $group)
                                                                @if($group->classgroup_id == $custom->classgroup_id)
                                                                    <option selected value="{{ $group->classgroup_id }}">{{ $group->classgroup }}</option>
                                                                @else
                                                                    <option value="{{ $group->classgroup_id }}">{{ $group->classgroup }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-danger btn-rounded btn-condensed btn-xs delete_custom" value="{{$custom->custom_subject_id}}">
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
                                                    {!! Form::text('name[]', '', ['placeholder'=>'Grouping Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('custom_subject_id[]', '-1', ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::text('abbr[]', '', ['placeholder'=>'Grouping Abbr.', 'class'=>'form-control']) !!}</td>
                                                <td>
                                                    <select class="form-control" name="classgroup_id[]">
                                                        <option value="">-- Class Group --</option>
                                                        @foreach($groups as $group)
                                                            <option value="{{ $group->classgroup_id }}">{{ $group->classgroup }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger btn-rounded btn-condensed btn-xs">
                                                        <span class="fa fa-times"></span> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="form-actions noborder">
                                    <button class="btn green pull-left add_custom"> Add New
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
    <script src="{{ asset('assets/custom/js/subjects/custom.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/custom-subjects/groupings"]');

        });
    </script>
@endsection