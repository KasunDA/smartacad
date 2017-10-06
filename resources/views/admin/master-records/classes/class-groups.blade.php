@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Class Groups')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li><i class="fa fa-chevron-right"></i></li>
    <li>
        <a href="{{ url('/class-groups') }}">Class Groups</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> Class Groups</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-10">
            <div class="portlet light bordered">
                <div class="portlet">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Class Groups</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn btn-sm green add_class_group"> Add New
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
                                <table class="table table-bordered table-striped table-actions" id="class_group_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 40%;">Class Groups</th>
                                        <th style="width: 25%;">C.A (Weight Point)</th>
                                        <th style="width: 25%;">Exam (Weight Point)</th>
                                        <th style="width: 5%;">Actions</th>
                                    </tr>
                                    </thead>
                                    @if(count($class_groups) > 0)
                                        <tbody>
                                        <?php $i = 1; ?>
                                        @foreach($class_groups as $class_group)
                                            <tr>
                                                <td class="text-center">{{$i++}} </td>
                                                <td>
                                                    {!! Form::text('classgroup[]', $class_group->classgroup, ['placeholder'=>'Class Group', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('classgroup_id[]', $class_group->classgroup_id, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::text('ca_weight_point[]', $class_group->ca_weight_point, ['placeholder'=>'C.A Weight Point', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::text('exam_weight_point[]', $class_group->exam_weight_point, ['placeholder'=>'Exam Weight Point', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$class_group->classgroup}}" data-title="Delete Confirmation"
                                                             data-action="/class-groups/delete/{{$class_group->classgroup_id}}" class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
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
                                                {!! Form::text('classgroup[]', '', ['placeholder'=>'Class Group', 'class'=>'form-control', 'required'=>'required']) !!}
                                                {!! Form::hidden('classgroup_id[]', '-1', ['class'=>'form-control']) !!}
                                            </td>
                                            <td>{!! Form::text('ca_weight_point[]', '', ['placeholder'=>'C.A Weight Point', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>{!! Form::text('exam_weight_point[]', '', ['placeholder'=>'Exam Weight Point', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>
                                                <button class="btn btn-danger btn-rounded btn-condensed btn-sm">
                                                    <span class="fa fa-times"></span> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                    <tfoot>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 40%;">Class Groups</th>
                                        <th style="width: 25%;">C.A (Weight Point)</th>
                                        <th style="width: 25%;">Exam (Weight Point)</th>
                                        <th style="width: 5%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                </table>
                                <div class="col-md-12 margin-bottom-10">
                                    <div class="btn-group pull-left">
                                        <button class="btn btn-sm green add_class_group"> Add New
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
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script>
        jQuery(document).ready(function () {

            $('.add_class_group').click(function(e){
                e.preventDefault();
                var clone_row = $('#class_group_table tbody tr:last-child').clone();

                $('#class_group_table tbody').append(clone_row);

                clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
                clone_row.children(':nth-child(2)').children('input').val('');
                clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
                clone_row.children(':nth-child(3)').children('input').val('');
                clone_row.children(':nth-child(4)').children('input').val('');
                clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_class_group"><span class="fa fa-times"></span> Remove</button>');
            });

            $(document.body).on('click','.remove_class_group',function(){
                $(this).parent().parent().remove();
            });

            setTabActive('[href="/class-groups"]');
            setTableData($('#class_group_table')).init();
        });
    </script>
@endsection
