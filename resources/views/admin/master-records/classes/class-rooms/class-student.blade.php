@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .multi-select-subjects{
            width: 560px;
            /*min-height: 500px;*/
        }
    </style>
@endsection

@section('title', 'Manage Students Classroom')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li><i class="fa fa-chevron-right"></i></li>
    <li>
        <a href="{{ url('/class-rooms/assign-students') }}">Manage Classroom Students</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page">Manage Students in a Class Room</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet light bordered">
                    <div class="portlet-title tabbable-line">
                        <ul class="nav nav-pills">
                            <li class="{{ (session('active') == 'search' || (!session()->has('active'))) ? 'active' : '' }}">
                                <a href="#search4student" data-toggle="tab"><i class="fa fa-search"></i> Find /  <i class="fa fa-eye"></i> View Student in Class Room </a>
                            </li>
                            <li>
                                <a href="#assign2student" data-toggle="tab"><i class="fa fa-plus-square"></i> Add / <i class="fa fa-minus-square"></i> Remove Student in Class Room </a>
                            </li>
                            <li class="{{ (session('active') == 'cloning') ? 'active' : '' }}">
                                <a href="#cloneStudent" data-toggle="tab"><i class="fa fa-clone"></i> Clone Students </a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body form">
                        <div class="tab-content">
                            <div class="tab-pane {{ (session('active') == 'search' || (!session()->has('active'))) ? 'active' : '' }}" id="search4student">
                                <div class="alert alert-info"> Search by <strong>Academic Year</strong> and  <strong>Class Level</strong> To View Students</div>
                                <div id="msg_box1"></div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id'=>'search_student_form'
                                    ])
                                !!}
                                <div class="form-body">
                                    <div class="form-group">
                                        <div class="col-md-4 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="control-label">Class Level <span class="text-danger">*</span></label>
                                                <div>
                                                    {!! Form::select('view_classlevel_id', $classlevels, old('classlevel_id'), ['class'=>'form-control', 'id'=>'view_classlevel_id', 'required'=>'required']) !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Class Room </label>
                                                {!! Form::select('view_classroom_id', [], '', ['class'=>'form-control', 'id'=>'view_classroom_id']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                <div>
                                                    {!! Form::select('view_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'required'=>'required']) !!}
                                                </div>
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
                                                <table class="table table-striped table-bordered table-hover" id="view_students_datatable">

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="assign2student">
                                <div class="alert alert-info"> Search by <strong>Academic Year</strong> and <strong>Class Room</strong></div>
                                <div id="msg_box"></div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal ',
                                        'id' => 'assign_student_form'
                                    ])
                                !!}
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                    <div>
                                                        {!! Form::select('student_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id,
                                                        ['class'=>'form-control', 'id'=>'student_academic_year_id', 'required'=>'required']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Class Level <span class="text-danger">*</span></label>
                                                    <div>
                                                        {!! Form::select('student_classlevel_id', $classlevels, old('classlevel_id'), ['class'=>'form-control', 'id'=>'student_classlevel_id', 'required'=>'required']) !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Class Room <span class="text-danger">*</span></label>
                                                    {!! Form::select('student_classroom_id', [], '', ['class'=>'form-control', 'id'=>'student_classroom_id', 'required'=>'required']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions noborder">
                                        <input type="hidden" value="" name="hidden_classroom_id" id="hidden_classroom_id">
                                        <input type="hidden" value="" name="hidden_academic_year_id" id="hidden_academic_year_id">
                                        <button type="submit" class="btn blue pull-right">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                {!! Form::close() !!}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet-body">
                                            <div class="row">
                                                <table class="table table-striped table-bordered table-hover" id="assign_student_table">
                                                    <tr>
                                                        <td>
                                                            <table  class="table table-bordered table-hover table-striped display" id="available_students" >

                                                            </table>
                                                        </td>
                                                        <td>
                                                            <table class="table table-bordered table-hover table-striped display"  id="assigned_students">

                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane {{ (session('active') == 'cloning') ? 'active' : '' }}" id="cloneStudent">
                                <h4 class="page">Cloning of Students Assigned To Class Room In An Academic Year</h4>
                                <!-- END PAGE HEADER-->
                                <div class="row">
                                    <div id="error-box"></div>
                                    {!! Form::open([
                                            'method'=>'POST',
                                            'class'=>'form-horizontal',
                                            'id' => 'clone_students_assigned'
                                        ])
                                    !!}
                                    <div class="col-md-5">
                                        <div class="portlet light bordered">
                                            <div class="portlet-body form">
                                                <div class="alert alert-info"> Clone Students Class Records <strong>From</strong> Academic Year</div>
                                                <div class="form-body">
                                                    <div class="form-group">
                                                        <div class="col-md-8 col-md-offset-1">
                                                            <div class="form-group">
                                                                <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                                <div>
                                                                    {!! Form::select('from_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id,
                                                                    ['class'=>'form-control', 'id'=>'from_academic_year_id', 'required'=>'required']) !!}
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label">Class Level <span class="text-danger">*</span></label>
                                                                <div>
                                                                    {!! Form::select('from_classlevel_id', $classlevels, old('from_classlevel_id'), ['class'=>'form-control', 'id'=>'from_classlevel_id', 'required'=>'required']) !!}
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label">Class Room <span class="text-danger">*</span></label>
                                                                {!! Form::select('from_classroom_id', [], '', ['class'=>'form-control', 'id'=>'from_classroom_id', 'required'=>'required']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 margin-top-40">
                                        <div class="row  margin-top-20">
                                            <div class="form-group pull-left">
                                                <span class="fa fa-long-arrow-right fa-4x"></span>
                                            </div>
                                        </div>
                                        <div class="form-group  margin-top-20">
                                            <div class="col-md-8 col-md-offset-2">
                                                <span class="fa fa-clone fa-4x"></span>
                                            </div>
                                        </div>
                                        <div class="row  margin-top-20">
                                            <div class="form-group pull-right">
                                                <span class="fa fa-long-arrow-right fa-4x"></span>
                                            </div>
                                        </div>
                                        <div class="form-actions noborder  margin-top-20">
                                            <button type="submit" class="btn blue pull-right">
                                                <i class="fa fa-clone"></i> Clone Record
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="portlet light bordered">
                                            <div class="portlet-body form">
                                                <div class="alert alert-info"> Clone Students Class Records <strong>To</strong> Academic Year</div>
                                                <div class="form-body">
                                                    <div class="form-group">
                                                        <div class="col-md-8 col-md-offset-1">
                                                            <div class="form-group">
                                                                <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                                <div>
                                                                    {!! Form::select('to_academic_year_id', $academic_years,  '', ['class'=>'form-control', 'id'=>'to_academic_year_id', 'required'=>'required']) !!}
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label">Class Level <span class="text-danger">*</span></label>
                                                                <div>
                                                                    {!! Form::select('to_classlevel_id', $classlevels, old('from_classlevel_id'), ['class'=>'form-control', 'id'=>'to_classlevel_id', 'required'=>'required']) !!}
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label">Class Room <span class="text-danger">*</span></label>
                                                                {!! Form::select('to_classroom_id', [], '', ['class'=>'form-control', 'id'=>'to_classroom_id', 'required'=>'required']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                                <!-- END CONTENT BODY -->
                                <div id="clone_record_modal" class="modal fade" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                <h4 class="modal-title"><strong>Clone Record Confirmation</strong></h4>
                                            </div>
                                            <div class="modal-body" style="height: 200px">
                                                <h4><strong>Are You Sure You Want To CLone This Records? <span class="text-danger">Note: its not reversible</span></strong></h4>
                                                <div id="clone-message" class="alert alert-info"></div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" data-dismiss="modal" class="btn dark btn-outline">No Close</button>
                                                <button type="button" class="btn green" id="confirm-btn" value="">Yes Setup</button>
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
    <script src="{{ asset('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-tabdrop/js/bootstrap-tabdrop.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/jquery.mockjax.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js') }}" type="text/javascript" ></script>
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/master-records/classes/class-student.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/class-students"]');

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });
        });
    </script>
@endsection
