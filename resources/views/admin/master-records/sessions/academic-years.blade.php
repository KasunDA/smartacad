@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Academic Years')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li><i class="fa fa-chevron-right"></i></li>
    <li>
        <a href="{{ url('/academic-years') }}">Academic Years</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-academic_year"> Academic Years</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8">
            <div class="portlet light bordered">
                <div class="portlet-academic_year">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Academic Years</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn green btn-sm add_academic_year"> Add New
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
                                <table class="table table-bordered table-striped table-actions" id="academic_year_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 50%;">Academic Years</th>
                                        <th style="width: 25%;">Year Status</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 50%;">Academic Years</th>
                                        <th style="width: 25%;">Year Status</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                    @if(count($academic_years) > 0)
                                        <tbody>
                                        <?php $i = 1; ?>
                                        @foreach($academic_years as $academic_year)
                                            <tr>
                                                <td class="text-center">{{$i++}} </td>
                                                <td>
                                                    {!! Form::text('academic_year[]', $academic_year->academic_year, ['placeholder'=>'Academic Year', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('academic_year_id[]', $academic_year->academic_year_id, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::select('status[]', [''=>'- Year Status -', 1=>'Active', 2=>'Inactive'], $academic_year->status, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$academic_year->academic_year}}" data-title="Delete Confirmation"
                                                             data-message="Are you sure you want to delete <b>{{$academic_year->academic_year}}?</b>"
                                                             data-action="/academic-years/delete/{{$academic_year->academic_year_id}}" class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
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
                                                {!! Form::text('academic_year[]', '', ['placeholder'=>'Academic Year', 'class'=>'form-control', 'required'=>'required']) !!}
                                                {!! Form::hidden('academic_year_id[]', '-1', ['class'=>'form-control']) !!}
                                            </td>
                                            <td>{!! Form::select('status[]', [''=>'- Year Status -', 1=>'Active', 2=>'Inactive'],'', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>
                                                <button class="btn btn-danger btn-rounded btn-condensed btn-xs">
                                                    <span class="fa fa-times"></span> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                                <div class="col-md-12 margin-bottom-10 pull-left">
                                    <div class="btn-group">
                                        <button class="btn green btn-sm add_academic_year"> Add New
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
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {

            $('.add_academic_year').click(function(e){
                e.preventDefault();
                var clone_row = $('#academic_year_table tbody tr:last-child').clone();

                $('#academic_year_table tbody').append(clone_row);

                clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
                clone_row.children(':nth-child(2)').children('input').val('');
                clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
                clone_row.children(':nth-child(3)').children('select').val('');
                clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_academic_year"><span class="fa fa-times"></span> Remove</button>');
            });

            $(document.body).on('click','.remove_academic_year',function(){
                $(this).parent().parent().remove();
            });



            setTabActive('[href="/academic-years"]');
            setTableData($('#academic_year_table')).init();
        });
    </script>
@endsection
