@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Custom Subjects')

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
        <a href="{{ url('/custom-subjects') }}">Subjects Grouping</a>
        <i class="fa fa-circle"></i>
    </li>
    <li><span class="active">Manage Subjects</span></li>
@stop


@section('content')
    <!-- BEGIN PAGE BASE CONTENT -->
    <h3 class="page-title"> Manage Subjects</h3>
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-tree font-green"></i>
                            <span class="caption-subject font-green bold uppercase">Manage Subjects</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-10 margin-bottom-10">
                                <div class="btn-group">
                                    <button class="btn green add_subject"> Add New
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
                                    <table class="table table-bordered" id="subject_table">
                                        <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="30%">Subject Group</th>
                                            <th width="30%">Subject</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="30%">Subject Group</th>
                                            <th width="30%">Subject</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                        </tfoot>
                                        <tbody>
                                            @if(count($customs) > 0)
                                                <?php $i = 1; ?>
                                                @foreach($customs as $custom)
                                                    @foreach($custom->getImmediateDescendants() as $custom_subject)
                                                        <tr>
                                                            <td class="text-center">{{$i}} </td>
                                                            <td>
                                                                {!! Form::select('parent_id[]', $parents, $custom_subject->parent_id, ['class'=>'form-control']) !!}
                                                                {!! Form::hidden('custom_subject_id[]', $custom_subject->custom_subject_id, ['class'=>'form-control']) !!}
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="subject_id[]">
                                                                    <option value="">-- Select Subject --</option>
                                                                    @foreach($subjects as $subject)
                                                                        @if($subject->subject_id == $custom_subject->subject_id)
                                                                            <option selected value="{{ $subject->subject_id }}">{{ $subject->subject }}</option>
                                                                        @else
                                                                            <option value="{{ $subject->subject_id }}">{{ $subject->subject }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-danger btn-rounded btn-condensed btn-xs delete_subject" value="{{$custom_subject->custom_subject_id}}">
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
                                                            {!! Form::select('parent_id[]', $parents, '', ['class'=>'form-control']) !!}
                                                            {!! Form::hidden('custom_subject_id[]', '-1', ['class'=>'form-control']) !!}
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="subject_id[]">
                                                                <option value="">-- Select Subject --</option>
                                                                @foreach($subjects as $subject)
                                                                    <option value="{{ $subject->subject_id }}">{{ $subject->subject }}</option>
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
                                            @endif
                                        </tbody>
                                    </table>
                                    <div class="form-actions noborder">
                                        <button class="btn green pull-left add_subject"> Add New
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
            setTabActive('[href="/custom-subjects/subjects"]');
        });
    </script>
@endsection