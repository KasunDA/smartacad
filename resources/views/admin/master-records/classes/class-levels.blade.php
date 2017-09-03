@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Class Levels')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li><i class="fa fa-chevron-right"></i></li>
    <li>
        <a href="{{ url('/class-levels') }}">Class Levels</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> Class Levels</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8 col-xs-12 col-md-offset-2 margin-bottom-10">
            <form method="post" action="/class-levels/class-groups" role="form" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-md-3 control-label">Class Groups</label>

                    <div class="col-md-6">
                        <div class="col-md-9">
                            <select class="form-control selectpicker" name="class_group_id" id="class_group_id">
                                @foreach($classgroups as $key => $value)
                                    @if($classGroup && $classGroup->classgroup_id === $key)
                                        <option selected value="{{$key}}">{{$value}}</option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary" type="submit">Filter</button>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-10">
                        <h3 class="text-center">Class Levels in:
                            <span class="text-primary">{{ ($classGroup) ? $classGroup->classgroup : 'All' }}</span> Class Groups
                        </h3>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-8">
            <div class="portlet light bordered">
                <div class="portlet">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Class Levels</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-level">
                                <button class="btn btn-sm green add_class_level"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            {!! Form::open([
                                'method'=>'POST',
                                'class'=>'form',
                                'role'=>'form'
                            ])
                        !!}
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-actions" id="class_level_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 40%;">Class Level</th>
                                        <th style="width: 40%;">Class Group</th>
                                        <th style="width: 5%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 40%;">Class Level</th>
                                        <th style="width: 40%;">Class Group</th>
                                        <th style="width: 5%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                    @if(count($classgroups) > 1)
                                        @if(count($classlevels) > 0)
                                            <tbody>
                                            <?php $i = 1; ?>
                                            @foreach($classlevels as $class_level)
                                                <tr>
                                                    <td class="text-center">{{$i++}} </td>
                                                    <td>
                                                        {!! Form::text('classlevel[]', $class_level->classlevel, ['placeholder'=>'Class Level', 'class'=>'form-control', 'required'=>'required']) !!}
                                                        {!! Form::hidden('classlevel_id[]', $class_level->classlevel_id, ['class'=>'form-control']) !!}
                                                    </td>
                                                    <td>{!! Form::select('classgroup_id[]', $classgroups, $class_level->classgroup_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>
                                                        <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$class_level->classlevel}}" data-title="Delete Confirmation"
                                                                 data-action="/class-levels/delete/{{$class_level->classlevel_id}}" class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
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
                                                    {!! Form::text('classlevel[]', '', ['placeholder'=>'Class Level', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('classlevel_id[]', '-1', ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::select('classgroup_id[]', $classgroups, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <button class="btn btn-danger btn-rounded btn-condensed btn-sm">
                                                        <span class="fa fa-times"></span> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @else
                                        <tr><td colspan="4" class="text-center"><label class="label label-danger"><strong>A Class Group Record Must Be Inserted Before Inserting Class Level</strong></label></td></tr>
                                    @endif
                                </table>
                                <div class="col-md-12 margin-bottom-10">
                                    <div class="btn-level pull-left">
                                        <button class="btn btn-sm green add_class_level"> Add New
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-actions noborder">
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
    @endsection


    @section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {

            $('.add_class_level').click(function(e){
                e.preventDefault();
                var clone_row = $('#class_level_table tbody tr:last-child').clone();

                $('#class_level_table tbody').append(clone_row);

                clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
                clone_row.children(':nth-child(2)').children('input').val('');
                clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
                clone_row.children(':nth-child(3)').children('select').val('');
                clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_class_level"><span class="fa fa-times"></span> Remove</button>');
            });

            $(document.body).on('click','.remove_class_level',function(){
                $(this).parent().parent().remove();
            });

            setTabActive('[href="/class-levels"]');
            setTableData($('#class_level_table')).init();
        });
    </script>
@endsection
