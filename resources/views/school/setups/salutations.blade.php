@extends('admin.layout.default')

@section('layout-style')
@endsection

@section('title', 'Salutation')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <a href="{{ url('/salutations') }}">Salutations</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-salutation"> Salutations</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8">
            <div class="portlet light bordered">
                <div class="portlet-salutation">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Salutations</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn btn-sm green add_salutation"> Add New
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
                                <table class="table table-bordered table-striped table-actions" id="salutation_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 50%;">Salutation</th>
                                        <th style="width: 25%;">Salutation Abbr.</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                    </thead>
                                    @if(count($salutations) > 0)
                                        <tbody>
                                        <?php $i = 1; ?>
                                        @foreach($salutations as $salutation)
                                            <tr>
                                                <td class="text-center">{{$i++}} </td>
                                                <td>
                                                    {!! Form::text('salutation[]', $salutation->salutation, ['placeholder'=>'Salutation', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('salutation_id[]', $salutation->salutation_id, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('salutation_abbr[]', $salutation->salutation_abbr, ['placeholder'=>'Salutation Abbr.', 'class'=>'form-control', 'required'=>'required']) !!}
                                                </td>
                                                <td>
                                                    <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$salutation->salutation}}" data-title="Delete Confirmation"
                                                             data-message="Are you sure you want to delete <b>{{$salutation->salutation}}?</b>"
                                                             data-action="/salutations/delete/{{$salutation->salutation_id}}" class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
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
                                                {!! Form::text('salutation[]', '', ['placeholder'=>'Salutation', 'class'=>'form-control', 'required'=>'required']) !!}
                                                {!! Form::hidden('salutation_id[]', '-1', ['class'=>'form-control']) !!}
                                            </td>
                                            <td>
                                                {!! Form::text('salutation_abbr[]', '', ['placeholder'=>'Salutation Abbr.', 'class'=>'form-control', 'required'=>'required']) !!}
                                            </td>
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
                                        <th style="width: 50%;">Salutation</th>
                                        <th style="width: 25%;">Salutation Abbr.</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                </table>
                                <div class="btn-group pull-left">
                                    <button class="btn btn-sm green add_salutation"> Add New
                                        <i class="fa fa-plus"></i>
                                    </button>
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
    <script>
        jQuery(document).ready(function () {

            $('.add_salutation').click(function(e){
                e.preventDefault();
                var clone_row = $('#salutation_table tbody tr:last-child').clone();

                $('#salutation_table tbody').append(clone_row);

                clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
                clone_row.children(':nth-child(2)').children('input').val('');
                clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
                clone_row.children(':nth-child(3)').children('input').val('');
                clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_salutation"><span class="fa fa-times"></span> Remove</button>');
            });

            $(document.body).on('click','.remove_salutation',function(){
                $(this).parent().parent().remove();
            });

            setTabActive('[href="/salutations"]');
        });
    </script>
@endsection
