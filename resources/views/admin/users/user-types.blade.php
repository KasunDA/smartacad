@extends('admin.layout.default')

@section('layout-style')
@endsection

@section('title', 'User Type')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/user-types') }}">User Types</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-title"> User Types</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">User Types</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn btn-sm green add_user_type"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            {!! Form::open([
                                'method'=>'POST',
                                'class'=>'form',
                                'role'=>'form',
                                'id'=>'menu_form'
                            ])
                        !!}
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-actions" id="menu_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 40%;">User Type</th>
                                        <th style="width: 40%;">Type</th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 40%;">User Type</th>
                                        <th style="width: 40%;">Type</th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                    @if(count($user_types) > 0)
                                        <tbody>
                                        <?php $i = 1; ?>
                                        @foreach($user_types as $user_type)
                                            <tr>
                                                <td class="text-center">{{$i++}} </td>
                                                <td>
                                                    {!! Form::text('user_type[]', $user_type->user_type, ['placeholder'=>'User Type', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('user_type_id[]', $user_type->user_type_id, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>
                                                    {!! Form::text('type[]', $user_type->type, ['placeholder'=>'Type', 'class'=>'form-control', 'required'=>'required']) !!}
                                                </td>
                                                <td>
                                                    <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$user_type->user_type}}" data-title="Delete Confirmation"
                                                             data-action="/user-types/delete/{{$user_type->user_type_id}}" class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
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
                                                {!! Form::text('user_type[]', '', ['placeholder'=>'User Type', 'class'=>'form-control', 'required'=>'required']) !!}
                                                {!! Form::hidden('user_type_id[]', '-1', ['class'=>'form-control']) !!}
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
                                        <button class="btn btn-sm green add_user_type"> Add New
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
    <script>
        jQuery(document).ready(function () {

            $('.add_user_type').click(function(e){
                e.preventDefault();
                var clone_row = $('#menu_table tbody tr:last-child').clone();

                $('#menu_table tbody').append(clone_row);

                clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
                clone_row.children(':nth-child(2)').children('input').val('');
                clone_row.children(':nth-child(3)').children('input').val('');
                clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
                clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_user_type"><span class="fa fa-times"></span> Remove</button>');
            });

            $(document.body).on('click','.remove_user_type',function(){
                $(this).parent().parent().remove();
            });

            setTabActive('[href="/user-types"]');
        });
    </script>
@endsection
