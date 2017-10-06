@extends('admin.layout.default')

@section('layout-style')
@endsection

@section('title', 'Marital Status')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <a href="{{ url('/marital-status') }}">Marital Status</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-marital_status"> Marital Status</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8">
            <div class="portlet light bordered">
                <div class="portlet-marital_status">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Marital Status</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn btn-sm green add_marital_status"> Add New
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
                                <table class="table table-bordered table-striped table-actions" id="marital_status_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 50%;">Status</th>
                                        <th style="width: 25%;">Status (Abbr.)</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 50%;">Status</th>
                                        <th style="width: 25%;">Status (Abbr.)</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                    @if(count($marital_statuses) > 0)
                                        <tbody>
                                        <?php $i = 1; ?>
                                        @foreach($marital_statuses as $marital_status)
                                            <tr>
                                                <td class="text-center">{{$i++}} </td>
                                                <td>
                                                    {!! Form::text('marital_status[]', $marital_status->marital_status, ['placeholder'=>'Marital Status', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('marital_status_id[]', $marital_status->marital_status_id, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('marital_status_abbr[]', $marital_status->marital_status_abbr, ['placeholder'=>'Marital Status Abbr.', 'class'=>'form-control', 'required'=>'required']) !!}
                                                </td>
                                                <td>
                                                    <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$marital_status->marital_status}}" data-title="Delete Confirmation"
                                                             data-message="Are you sure you want to delete <b>{{$marital_status->marital_status}}?</b>"
                                                             data-action="/marital-statuses/delete/{{$marital_status->marital_status_id}}" class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
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
                                                {!! Form::text('marital_status[]', '', ['placeholder'=>'Marital Status', 'class'=>'form-control', 'required'=>'required']) !!}
                                                {!! Form::hidden('marital_status_id[]', '-1', ['class'=>'form-control']) !!}
                                            </td>
                                            <td>
                                                {!! Form::text('marital_status_abbr[]', '', ['placeholder'=>'Marital Status Abbr.', 'class'=>'form-control', 'required'=>'required']) !!}
                                            </td>
                                            <td>
                                                <button class="btn btn-danger btn-rounded btn-condensed btn-sm">
                                                    <span class="fa fa-times"></span> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                                <div class="col-md-12 margin-bottom-10">
                                    <div class="btn-group pull-left">
                                        <button class="btn btn-sm green add_marital_status"> Add New
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

            $('.add_marital_status').click(function(e){
                e.preventDefault();
                var clone_row = $('#marital_status_table tbody tr:last-child').clone();

                $('#marital_status_table tbody').append(clone_row);

                clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
                clone_row.children(':nth-child(2)').children('input').val('');
                clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
                clone_row.children(':nth-child(3)').children('input').val('');
                clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_marital_status"><span class="fa fa-times"></span> Remove</button>');
            });

            $(document.body).on('click','.remove_marital_status',function(){
                $(this).parent().parent().remove();
            });

            setTabActive('[href="/marital-statuses"]');
        });
    </script>
@endsection
