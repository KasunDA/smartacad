@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Item Billings')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="{{ url('/billings') }}">Billings</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page">Process, Manage Orders(Items) Billings</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet light bordered">
                    <div class="portlet-title tabbable-line">
                        <ul class="nav nav-pills">
                            <li class="{{ (session('billing-tab') == 'terminal') ? 'active' : ((!session()->has('billing-tab')) ? 'active' : '') }}">
                                <a href="#terminal_biiling" data-toggle="tab"><i class="fa fa-gears"></i> Process Terminal Billings</a>
                            </li>
                            <li class="{{ (session('billing-tab') == 'student') ? 'active' : '' }}">
                                <a href="#student_billing" data-toggle="tab"> <i class="fa fa-money"></i> Bill Student / Class Room</a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body form">
                        <div class="tab-content">
                            <div class="tab-pane {{ (session('billing-tab') == 'terminal') ? 'active' : ((!session()->has('billing-tab')) ? 'active' : '') }}" id="terminal_biiling">
                                <div class="alert alert-info"> Initiate the process to <strong>Bill All Active Students</strong> For a specific <strong> Academic Term</strong></div>
                                <div class="alert alert-warning"> <strong>Note: </strong>Only <strong>Active Students</strong> in the selected Academic Term will be billed</div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id' => 'initiate_billings_form'
                                    ])
                                !!}
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                    <div>
                                                        {!! Form::select('academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id,
                                                            ['class'=>'form-control', 'id'=>'academic_year_id', 'required'=>'required'])
                                                        !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Academic Term <span class="text-danger">*</span></label>
                                                    {!! Form::select('academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                                            ->orderBy('term_type_id')
                                                            ->lists('academic_term', 'academic_term_id')
                                                            ->prepend('- Academic Term -', ''),
                                                        AcademicTerm::activeTerm()->academic_term_id,
                                                        ['class'=>'form-control', 'id'=>'academic_term_id', 'required'=>'required'])
                                                    !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions noborder">
                                        <button type="submit" class="btn blue  col-md-2 col-md-offset-4">
                                            <i class="fa fa-send"></i> Proceed
                                        </button>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane {{ (session('billing-tab') == 'student') ? 'active' : '' }}" id="student_billing">
                                <div class="alert alert-info"> Search by <strong>Academic Term</strong> and <strong>Class Room</strong> To View Reports</div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id' => 'search_view_student_form'
                                    ])
                                !!}
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Academic Year <small class="font-red">*</small></label>
                                                    <div>
                                                        {!! Form::select('view_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id,
                                                            ['class'=>'form-control', 'id'=>'view_academic_year_id', 'required'=>'required'])
                                                         !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Academic Term <small class="font-red">*</small></label>
                                                    {!! Form::select('view_academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                                        ->lists('academic_term', 'academic_term_id')
                                                        ->prepend('- Academic Term -', ''),
                                                        AcademicTerm::activeTerm()->academic_term_id,
                                                        ['class'=>'form-control', 'id'=>'view_academic_term_id', 'required'=>'required'])
                                                     !!}
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Class Level <small class="font-red">*</small></label>
                                                    <div>
                                                        {!! Form::select('view_classlevel_id', $classlevels, old('classlevel_id'),
                                                            ['class'=>'form-control', 'id'=>'view_classlevel_id', 'required'=>'required'])
                                                         !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Class Room </label>
                                                    {!! Form::select('view_classroom_id', [], '',
                                                        ['class'=>'form-control', 'id'=>'view_classroom_id'])
                                                     !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions noborder">
                                        <button type="submit" class="btn blue pull-right">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                {!! Form::close() !!}
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="portlet-body">
                                            <div class="row">
                                                <table class="table table-striped table-bordered table-hover table-checkable" id="view_student_datatable">

                                                </table>
                                                <div class="form-actions noborder">
                                                    <button type="button" id="all-marked" value="all" class="btn blue hide">
                                                        <i class="fa fa-money"></i> Bill All Marked
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- modal -->
    <div id="billing_form" class="modal fade bs-modal-lg" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title text-center text-primary" id="modal-title-text">Header Form</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn btn-xs green add_item"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <form method="POST" action="#" class="form" role="form" id="items_billing_form">
                            {!! csrf_field() !!}
                            {!! Form::hidden('ids', '', ['id'=>'ids']) !!}
                            {!! Form::hidden('type_id', '', ['id'=>'type_id']) !!}
                            {!! Form::hidden('term_id', '', ['id'=>'term_id']) !!}
                            <div style="width: auto" data-always-visible="1" data-rail-visible1="1">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped table-actions" id="item_table">
                                        <thead>
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th style="width: 55%;">Item</th>
                                            <th style="width: 30%;">Amount (&#8358;)</th>
                                            <th style="width: 10%;">Actions</th>
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th style="width: 55%;">Item</th>
                                            <th style="width: 30%;">Amount (&#8358;)</th>
                                            <th style="width: 10%;">Actions</th>
                                        </tr>
                                        </tfoot>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td>
                                                    {!! Form::select('item_id[]', $items, old('item_id'),
                                                        ['class'=>'form-control each-item', 'id'=>'all_item_id', 'required'=>'required'])
                                                     !!}
                                                </td>
                                                <td></td>
                                                <td>
                                                    <button class="btn btn-danger btn-xs btn-condensed btn-sm">
                                                        <span class="fa fa-times"></span> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                                <button type="submit" class="btn green">Submit</button>
                                <div class="col-md-12">
                                    <div class="btn-group pull-left">
                                        <button class="btn btn-xs green add_item"> Add New
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal -->

    <!-- END CONTENT BODY -->
    @endsection


    @section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/orders/billings.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {

            $('.add_item').click(function(e){
                e.preventDefault();
                var clone_row = $('#item_table tbody tr:last-child').clone();

                $('#item_table tbody').append(clone_row);

                clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
                clone_row.children(':nth-child(2)').children('select').val('');
                clone_row.children(':nth-child(3)').children().html('');
                clone_row.children(':last-child').html('<button class="btn btn-danger btn-xs btn-condensed btn-xs remove_item"><span class="fa fa-times"></span> Remove</button>');
            });

            $(document.body).on('click','.remove_item',function(){
                $(this).parent().parent().remove();
            });

            setTabActive('[href="/billings"]');
        });
    </script>
@endsection
