@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .multi-select-subjects{
            width: 560px;
            /*min-height: 500px;*/
        }
    </style>
@endsection

@section('title', 'Manage Subjects Assignments')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <span class="icon chevron-right"></span>
    </li>
    <li>
        <a href="{{ url('/subject-tutors') }}">Manage Subjects Assignments</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page">Manage Subjects Assigned To You as a Tutor</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet light bordered">
                    <div class="portlet-title tabbable-line">
                        <ul class="nav nav-pills">
                            <li class="active">
                                <a href="#manage_subject" data-toggle="tab"><i class="fa fa-edit"></i> Manage Students</a>
                            </li>
                            {{--<li>--}}
                                {{--<a href="#view_subject" data-toggle="tab"><i class="fa fa-ticket"></i> View Scores</a>--}}
                            {{--</li>--}}
                        </ul>
                    </div>
                    <div class="portlet-body form">
                        <div class="tab-content">
                            <div class="tab-pane active" id="manage_subject">
                                <div class="alert alert-info"> Search by <strong>Academic Term</strong> and / or <strong>Class Level</strong> To Manage Subjects</div>
                                <div id="error-box"></div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id' => 'search_manage_subject_form'
                                    ])
                                !!}
                                <div class="form-body">
                                    <div class="form-group">
                                        <div class="col-md-5 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                <div>
                                                    {!! Form::select('manage_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'id'=>'manage_academic_year_id', 'required'=>'required']) !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Academic Term <span class="text-danger">*</span></label>
                                                {!! Form::select('manage_academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)->lists('academic_term', 'academic_term_id')->prepend('Select Academic Term', ''),
                                                AcademicTerm::activeTerm()->academic_term_id, ['class'=>'form-control', 'id'=>'manage_academic_term_id', 'required'=>'required']) !!}
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Class Level </label>
                                                <div>
                                                    {!! Form::select('manage_classlevel_id', $classlevels, old('classlevel_id'), ['class'=>'form-control']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-md-offset-1 form-actions noborder">
                                    <button type="submit" class="btn blue pull-right">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                </div>
                                {!! Form::close() !!}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet-body">
                                            <div class="row">
                                                <table class="table table-striped table-bordered table-hover" id="manage_subject_datatable">

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--<div class="tab-pane" id="view_subject">--}}
                                {{--<div class="alert alert-info"> Search by <strong>Academic Term</strong> and <strong>Class Room</strong> To View Subjects</div>--}}
                                {{--{!! Form::open([--}}
                                        {{--'method'=>'POST',--}}
                                        {{--'class'=>'form-horizontal',--}}
                                        {{--'id' => 'search_view_subject_form'--}}
                                    {{--])--}}
                                {{--!!}--}}
                                {{--<div class="form-body">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<div class="col-md-4 col-md-offset-1">--}}
                                            {{--<div class="form-group">--}}
                                                {{--<label class="control-label">Academic Year <span class="text-danger">*</span></label>--}}
                                                {{--<div>--}}
                                                    {{--{!! Form::select('view_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'id'=>'view_academic_year_id', 'required'=>'required']) !!}--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                            {{--<div class="form-group">--}}
                                                {{--<label class="control-label">Academic Term <span class="text-danger">*</span></label>--}}
                                                {{--{!! Form::select('view_academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)->lists('academic_term', 'academic_term_id')->prepend('Select Academic Term', ''),--}}
                                                {{--AcademicTerm::activeTerm()->academic_term_id, ['class'=>'form-control', 'id'=>'view_academic_term_id', 'required'=>'required']) !!}--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        {{--<div class="col-md-4 col-md-offset-1">--}}
                                            {{--<div class="form-group">--}}
                                                {{--<label class="control-label">Class Level <span class="text-danger">*</span></label>--}}
                                                {{--<div>--}}
                                                    {{--{!! Form::select('view_classlevel_id', $classlevels, old('classlevel_id'), ['class'=>'form-control', 'id'=>'view_classlevel_id', 'required'=>'required']) !!}--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                            {{--<div class="form-group">--}}
                                                {{--<label class="control-label">Class Room </label>--}}
                                                {{--{!! Form::select('view_classroom_id', [], '', ['class'=>'form-control', 'id'=>'view_classroom_id']) !!}--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="form-actions noborder">--}}
                                    {{--<button type="submit" class="btn blue pull-right">--}}
                                        {{--<i class="fa fa-search"></i> Search--}}
                                    {{--</button>--}}
                                {{--</div>--}}
                                {{--{!! Form::close() !!}--}}
                                {{--<div class="row">--}}
                                    {{--<div class="col-md-12">--}}
                                        {{--<div class="portlet-body">--}}
                                            {{--<div class="row">--}}
                                                {{--<table class="table table-striped table-bordered table-hover" id="view_subject_datatable">--}}

                                                {{--</table>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- modal -->
    <div id="manage_student_modal" class="modal fade bs-modal-lg" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h5 class="modal-title text-center text-primary" id="manage-title-text"></h5>
                </div>
                <form method="POST" action="#" class="form" role="form" id="manage_student_form">
                    {!! csrf_field() !!}
                    {!! Form::hidden('subject_classroom_id', '', ['id'=>'subject_classroom_id']) !!}
                    <div class="modal-body">
                        <div class="scroller" style="height:300px;" data-always-visible="1" data-rail-visible1="1">
                            <div class="row">
                                <div class="form-body">
                                    <div class="form-group last">
                                        <div class="col-md-10">
                                            <select multiple="multiple" class="multi-select" id="manage_student_multi_select" name="student_id[]">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                        <button type="submit" class="btn green">Save changes</button>
                    </div>
                </form>
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
    <script src="{{ asset('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-tabdrop/js/bootstrap-tabdrop.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/jquery.mockjax.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js') }}" type="text/javascript" ></script>
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/subjects/subject-tutor.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/subject-tutors"]');
        });
    </script>
@endsection
