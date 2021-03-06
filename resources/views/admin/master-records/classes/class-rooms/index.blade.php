@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Class Rooms')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li><i class="fa fa-chevron-right"></i></li>
    <li>
        <a href="{{ url('/class-rooms') }}">Class Rooms</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> Class Rooms</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8 col-xs-12 col-md-offset-2 margin-bottom-10">
            <form method="post" action="/class-rooms/levels" role="form" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-md-3 control-label">Class Levels</label>

                    <div class="col-md-6">
                        <div class="col-md-9">
                            <select class="form-control selectpicker" name="classlevel_id" id="classlevel_id">
                                @foreach($classlevels as $key => $value)
                                    @if($classlevel && $classlevel->classlevel_id === $key)
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

                <div class="form-group">
                    <div class="col-md-10">
                        <h3 class="text-center">Class Rooms in:
                            <span class="text-primary">{{ ($classlevel) ? $classlevel->classlevel : 'All' }}</span> Class Level</h3>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-10">
            <div class="portlet light bordered">
                <div class="portlet">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Class Rooms</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-room">
                                <button class="btn btn-sm green add_class_room"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <form method="post" action="/class-rooms" role="form" class="form">
                                {!! csrf_field() !!}
                                <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="class_room_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 35%;">Class Room</th>
                                        <th style="width: 30%;">Class Level</th>
                                        <th style="width: 15%;">Capacity</th>
                                        <th style="width: 5%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 35%;">Class Room</th>
                                        <th style="width: 30%;">Class Level</th>
                                        <th style="width: 15%;">Capacity</th>
                                        <th style="width: 5%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                    <tbody>
                                        @if(count($classlevels) > 1)
                                            @if(count($classrooms) > 0)

                                                <?php $i = 1; ?>
                                                @foreach($classrooms as $class_room)
                                                    <tr>
                                                        <td class="text-center">{{$i++}} </td>
                                                        <td>
                                                            {!! Form::text('classroom[]', $class_room->classroom, ['placeholder'=>'Class Room', 'class'=>'form-control', 'required'=>'required']) !!}
                                                            {!! Form::hidden('classroom_id[]', $class_room->classroom_id, ['class'=>'form-control']) !!}
                                                        </td>
                                                        <td>{!! Form::select('classlevel_id[]', $classlevels, $class_room->classlevel_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                        <td>{!! Form::text('class_size[]', $class_room->class_size, ['placeholder'=>'Class Capacity', 'class'=>'form-control']) !!}</td>
                                                        <td>
                                                            <button  data-name="{{$class_room->classroom}}"
                                                                     data-action="/class-rooms/delete/{{$class_room->classroom_id}}"
                                                                     class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
                                                                    <span class="fa fa-trash-o"></span> Delete
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td>
                                                        {!! Form::text('classroom[]', '', ['placeholder'=>'Class Room', 'class'=>'form-control', 'required'=>'required']) !!}
                                                        {!! Form::hidden('classroom_id[]', '-1', ['class'=>'form-control']) !!}
                                                    </td>
                                                    <td>{!! Form::select('classlevel_id[]', $classlevels, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>{!! Form::text('class_size[]', '', ['placeholder'=>'Class Capacity', 'class'=>'form-control']) !!}</td>
                                                    <td>
                                                        <button class="btn btn-danger btn-rounded btn-condensed btn-sm">
                                                            <span class="fa fa-times"></span> Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @else
                                            <tr><td colspan="5" class="text-center"><label class="label label-danger"><strong>A Class Level Record Must Be Inserted Before Inserting Class Room</strong></label></td></tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="col-md-12 margin-bottom-10">
                                    <div class="btn-room pull-left">
                                        <button class="btn btn-sm green add_class_room"> Add New
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-actions noborder">
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
    <!-- END CONTENT BODY -->
    @endsection


    @section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/custom/js/master-records/classes/class-room.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/class-rooms"]');
            setTableData($('#class_room_table')).init();
        });
    </script>
@endsection
