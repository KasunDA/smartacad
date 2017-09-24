@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Manage Attendances')

@section('breadcrumb')
    <li>
        <i class="fa fa-home"></i>
        <a href="{{ url('/dashboard') }}">Home</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <i class="fa fa-money"></i>
        <a href="{{ url('/attendances') }}">Attendances</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> Take/Adjust Attendance</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet light bordered">
                    <div class="portlet-title tabbable-line">
                        <ul class="nav nav-pills">
                            <li class="active">
                            <li class="{{ (session('attendance-tab') == 'take') ? 'active' : ((!session()->has('attendance-tab')) ? 'active' : '') }}">
                                <a href="#take_attendance_tab" data-toggle="tab"> <i class="fa fa-check"></i> Initiate Attendance</a>
                            </li>
                            <li class="{{ (session('attendance-tab') == 'adjust') ? 'active' : '' }}">
                                <a href="#adjust_attendance_tab" data-toggle="tab"> <i class="fa fa-edit"></i> Adjust Attendance</a>
                            </li>
                            <li class="{{ (session('attendance-tab') == 'summary') ? 'active' : '' }}">
                                <a href="#summary_attendance_tab" data-toggle="tab"> <i class="fa fa-th"></i> Summary</a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body form">
                        <div class="tab-content">
                            <div class="tab-pane {{ (session('attendance-tab') == 'take') ? 'active' : ((!session()->has('attendance-tab')) ? 'active' : '') }}" id="take_attendance_tab">
                                <div class="alert alert-info"> View <strong>Students Orders</strong> For a specific <strong> Academic Term</strong></div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id' => 'view_order_form'
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
                                        <div class="col-md-4 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="control-label">Class Level <small class="font-red">*</small></label>
                                                <div>
                                                    {!! Form::select('classlevel_id', $classlevels, old('classlevel_id'),
                                                        ['class'=>'form-control', 'id'=>'classlevel_id', 'required'=>'required'])
                                                     !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Class Room </label>
                                                {!! Form::select('classroom_id', [], '',
                                                    ['class'=>'form-control', 'id'=>'classroom_id'])
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
                                    <div class="col-md-12">
                                        <div class="portlet-body">
                                            <div class="row">
                                                <table class="table table-striped table-bordered table-hover" id="view_order_datatable">

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane {{ (session('attendance-tab') == 'adjust') ? 'active' : '' }}" id="adjust_attendance_tab">
                                <div class="alert alert-info"> Search by <strong>Academic Term</strong> and <strong>Class Room</strong> To View Orders for Adjustments</div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id' => 'adjust_order_form'
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
                                                <label class="control-label">Class Room <small class="font-red">*</small></label>
                                                {!! Form::select('view_classroom_id', [], '',
                                                    ['class'=>'form-control', 'id'=>'view_classroom_id', 'required'=>'required'])
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
                                                <table class="table table-striped table-bordered table-hover" id="adjust_order_datatable">

                                                </table>
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
    <script src="{{ asset('assets/custom/js/attendances/attendance.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/attendances"]');
        });
    </script>
@endsection
