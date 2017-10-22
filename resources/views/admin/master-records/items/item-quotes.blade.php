@extends('admin.layout.default')

@section('layout-style')
@endsection

@section('title', 'Item Quotes')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/item-quotes') }}">Item Quotes</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Item Quotes</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8 col-xs-12 col-md-offset-2 margin-bottom-10">
            <form method="post" action="/item-quotes/academic-years" role="form" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-md-3 control-label">Academic Year</label>

                    <div class="col-md-6">
                        <div class="col-md-9">
                            <select class="form-control selectpicker" name="academic_year_id" id="academic_year_id">
                                @foreach($academic_years as $key => $value)
                                    @if($academic_year && $academic_year->academic_year_id === $key)
                                        <option selected value="{{$key}}">{{$value}}</option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Items</label>

                    <div class="col-md-6">
                        <div class="col-md-9">
                            <select class="form-control selectpicker" name="item_id" id="item_id">
                                @foreach($items as $key => $value)
                                    @if(!empty($item) && $item->id === $key)
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
                        <h3 class="text-center">
                            <span class="text-primary">{{ !empty($item) ? $item->name : 'All' }}</span> Item, Quotes in:
                            <span class="text-primary">{{ ($academic_year) ? $academic_year->academic_year : 'All' }}</span> Academic Year
                        </h3>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-11 col-xs-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Item Quote</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-10 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn btn-sm green add_item_quote"> Add New
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
                                <table class="table table-bordered table-striped table-actions" id="item_quote_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 25%;">Item <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 20%;">Amount <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 20%;">Class Group <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 20%;">Academic Year <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 25%;">Item <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 20%;">Amount <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 20%;">Class Group <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 20%;">Academic Year <small class="font-red-thunderbird">*</small></th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                    @if(count($item_quotes) > 0)
                                        <tbody>
                                        <?php $i = 1; ?>
                                        @foreach($item_quotes as $item_quote)
                                            <tr>
                                                <td class="text-center">{{$i++}} </td>
                                                <td>
                                                    {!! Form::select('item_id[]', $items, $item_quote->item_id, ['class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('id[]', $item_quote->id, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::text('amount[]', $item_quote->amount, ['placeholder'=>'Price (0.00)', 'class'=>'form-control']) !!}</td>
                                                <td>{!! Form::select('classgroup_id[]', $classgroups, $item_quote->classgroup_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::select('academic_year_id[]', $academic_years, $item_quote->academic_year_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$item_quote->item->name}}" data-title="Delete Confirmation"
                                                             data-message="Are you sure you want to delete <b>{{$item_quote->item->name}} on quote {{ $item_quote->amount }}?</b>"
                                                             data-action="/item-quotes/delete/{{$item_quote->id}}" class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
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
                                                {!! Form::select('item_id[]', $items, '', ['class'=>'form-control', 'required'=>'required']) !!}
                                                {!! Form::hidden('id[]', '-1', ['class'=>'form-control']) !!}
                                            </td>
                                            <td>{!! Form::number('amount[]', '', ['placeholder'=>'Price (0.00)', 'class'=>'form-control']) !!}</td>
                                            <td>{!! Form::select('classgroup_id[]', $classgroups, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>{!! Form::select('academic_year_id[]', $academic_years, AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
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
                                        <button class="btn btn-sm green add_item_quote"> Add New
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
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script type="text/javascript">
        jQuery(document).ready(function () {
            setTabActive('[href="/item-quotes"]');

            $('.add_item_quote').click(function(e){
                e.preventDefault();
                var clone_row = $('#item_quote_table tbody tr:last-child').clone();

                $('#item_quote_table tbody').append(clone_row);

                clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
                clone_row.children(':nth-child(2)').children('select').val('');
                clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
                clone_row.children(':nth-child(3)').children('input').val('');
                clone_row.children(':nth-child(4)').children('select').val('');
                clone_row.children(':nth-child(5)').children('select').val('');
                clone_row.children(':last-child').html('<button class="btn btn-danger btn-xs btn-condensed btn-sm remove_item_quote"><span class="fa fa-times"></span> Remove</button>');
            });

            $(document.body).on('click','.remove_item_quote',function(){
                $(this).parent().parent().remove();
            });
        });
    </script>
@endsection
