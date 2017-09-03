@extends('admin.layout.default')

@section('layout-style')
@endsection

@section('title', 'Items')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/items') }}">Items</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Items</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Item </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-11 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn btn-sm green add_item"> Add New
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
                                <table class="table table-bordered table-striped table-actions" id="item_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 20%;">Name <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 40%;">Description</th>
                                        <th style="width: 15%;">Item Type <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 15%;">Status <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 20%;">Name <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 40%;">Description</th>
                                        <th style="width: 15%;">Item Type <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 15%;">Status <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                    @if(count($items) > 0)
                                        <tbody>
                                        <?php $i = 1; ?>
                                        @foreach($items as $item)
                                            <tr>
                                                <td class="text-center">{{$i++}} </td>
                                                <td>
                                                    {!! Form::text('name[]', $item->name, ['placeholder'=>'Item Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('id[]', $item->id, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::text('description[]', $item->description, ['placeholder'=>'Description (Optional)', 'class'=>'form-control']) !!}</td>
                                                <td>{!! Form::select('item_type_id[]', $item_types, $item->item_type_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::select('status[]', [''=>'- Status -', 1=>'Active', 0=>'Inactive'], $item->status, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$item->name}}" data-title="Delete Confirmation"
                                                             data-action="/items/delete/{{$item->id}}" class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
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
                                                {!! Form::text('name[]', '', ['placeholder'=>'Item Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                                {!! Form::hidden('id[]', '-1', ['class'=>'form-control']) !!}
                                            </td>
                                            <td>{!! Form::text('description[]', '', ['placeholder'=>'Description (Optional)', 'class'=>'form-control']) !!}</td>
                                            <td>{!! Form::select('item_type_id[]', $item_types, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>{!! Form::select('status[]', [''=>'- Status -', 1=>'Active', 0=>'Inactive'],'', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>
                                                <button class="btn btn-danger btn-xs btn-condensed btn-sm">
                                                    <span class="fa fa-times"></span> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                                <div class="pull-left">
                                    <div class="btn-group">
                                        <button class="btn btn-sm green add_item"> Add New
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
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            setTabActive('[href="/items"]');

            $('.add_item').click(function(e){
                e.preventDefault();
                var clone_row = $('#item_table tbody tr:last-child').clone();

                $('#item_table tbody').append(clone_row);

                clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
                clone_row.children(':nth-child(2)').children('input').val('');
                clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
                clone_row.children(':nth-child(3)').children('input').val('');
                clone_row.children(':nth-child(4)').children('select').val('');
                clone_row.children(':nth-child(5)').children('select').val('');
                clone_row.children(':last-child').html('<button class="btn btn-danger btn-xs btn-condensed btn-sm remove_item"><span class="fa fa-times"></span> Remove</button>');
            });

            $(document.body).on('click','.remove_item',function(){
                $(this).parent().parent().remove();
            });
        });
    </script>
@endsection
