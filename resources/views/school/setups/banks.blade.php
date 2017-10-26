@extends('admin.layout.default')

@section('layout-style')
@endsection

@section('title', 'School Banks')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <a href="{{ url('/school-banks') }}">School Banks</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> School Banks</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-marital_status">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">School Banks</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn btn-xs green add_marital_status"> Add New
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
                                <table class="table table-bordered table-striped table-actions" id="bank_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 1%;">#</th>
                                        <th style="width: 25%;">Account Name</th>
                                        <th style="width: 17%;">Account Number</th>
                                        <th style="width: 20%;">Bank</th>
                                        <th style="width: 20%;">Class Group</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 7%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th style="width: 1%;">#</th>
                                        <th style="width: 25%;">Account Name</th>
                                        <th style="width: 17%;">Account Number</th>
                                        <th style="width: 20%;">Bank</th>
                                        <th style="width: 20%;">Class Group</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 7%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                    @if(count($school_banks) > 0)
                                        <tbody>
                                        <?php $i = 1; ?>
                                        @foreach($school_banks as $bank)
                                            <tr>
                                                <td class="text-center">{{$i++}} </td>
                                                <td>
                                                    {!! Form::text('account_name[]', $bank->account_name, ['placeholder'=>'Account Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('id[]', $bank->id, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::text('account_number[]', $bank->account_number, ['placeholder'=>'Account Number', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::select('bank_id[]', $banks, $bank->bank_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::select('classgroup_id[]', $classgroups, $bank->classgroup_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::select('active[]', [''=>'- Status -', 1=>'Active', 0=>'Inactive'], $bank->active, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$bank->bank->name}}" data-title="Delete Confirmation"
                                                         data-message="Are you sure you want to delete <b>{{$bank->bank->name}}</b> with acc. <b>{{$bank->account_name}}: {{$bank->account_number}}</b>?"
                                                         data-action="/school-banks/delete/{{$bank->id}}" class="btn btn-danger btn-xs btn-condensed confirm-delete-btn">
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
                                                {!! Form::text('account_name[]', '', ['placeholder'=>'Account Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                                {!! Form::hidden('id[]', '-1', ['class'=>'form-control']) !!}
                                            </td>
                                            <td>{!! Form::text('account_number[]', '', ['placeholder'=>'Account Number', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>{!! Form::select('bank_id[]', $banks, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>{!! Form::select('classgroup_id[]', $classgroups, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>{!! Form::select('active[]', [''=>'- Status -', 1=>'Active', 0=>'Inactive'], '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>
                                                <button class="btn btn-danger btn-rounded btn-condensed btn-xs">
                                                    <span class="fa fa-times"></span> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                                <div class="col-md-12 margin-bottom-10">
                                    <div class="btn-group pull-left">
                                        <button class="btn btn-xs green add_bank"> Add New
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
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script>
        jQuery(document).ready(function () {

            $('.add_bank').click(function(e){
                e.preventDefault();
                var clone_row = $('#bank_table tbody tr:last-child').clone();

                $('#bank_table tbody').append(clone_row);

                clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
                clone_row.children(':nth-child(2)').children('input').val('');
                clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
                clone_row.children(':nth-child(3)').children('input').val('');
                clone_row.children(':nth-child(4)').children('select').val('');
                clone_row.children(':nth-child(5)').children('select').val('');
                clone_row.children(':nth-child(6)').children('select').val('');
                clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_bank"><span class="fa fa-times"></span> Remove</button>');
            });

            $(document.body).on('click','.remove_bank',function(){
                $(this).parent().parent().remove();
            });

            setTabActive('[href="/school-banks"]');
        });
    </script>
@endsection
