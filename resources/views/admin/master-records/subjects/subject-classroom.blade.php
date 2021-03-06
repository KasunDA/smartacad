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
    <li><i class="fa fa-chevron-right"></i></li>
    <li>
        <a href="{{ url('/subject-classrooms') }}">Manage Subjects Assignments</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-academic_year">Manage Subjects Assignments</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet light bordered">
                    <div class="portlet-title tabbable-line">
                        <ul class="nav nav-pills">
                            <li class="{{ (session('subject-tab') == 'classroom' || (!session()->has('subject-tab'))) ? 'active' : '' }}">
                                <a href="#assign_2classroom" data-toggle="tab"><i class="fa fa-plus"></i> Assign To CLass Room </a>
                            </li>
                            <li class="{{ (session('subject-tab') == 'classlevel') ? 'active' : '' }}">
                                <a href="#assign_2classlevel" data-toggle="tab"><i class="fa fa-plus-square"></i> Assign To CLass Level </a>
                            </li>
                            <li>
                                <a href="#view_subject" data-toggle="tab"><i class="fa fa-ticket"></i> View Subjects / Assign Tutor</a>
                            </li>
                            <li class="{{ (session('subject-tab') == 'manage-subject') ? 'active' : '' }}">
                                <a href="#manage_subject" data-toggle="tab"><i class="fa fa-edit"></i> Manage Subjects / Students</a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body form">
                        <div class="tab-content">
                            <div class="tab-pane {{ (session('subject-tab') == 'classroom' || (!session()->has('subject-tab'))) ? 'active' : '' }}" id="assign_2classroom">
                                <div class="alert alert-info"> Search by <strong>Academic Term</strong> and <strong>Class Room</strong></div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal search_subject_assign_form',
                                        //'id' => 'search_subject_class_form'
                                    ])
                                !!}
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                    <div>
                                                        {!! Form::select('class_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'id'=>'class_academic_year_id', 'required'=>'required']) !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Academic Term <span class="text-danger">*</span></label>
                                                    {!! Form::select('class_academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                                    ->orderBy('term_type_id')->pluck('academic_term', 'academic_term_id')->prepend('Select Academic Term', ''),
                                                    AcademicTerm::activeTerm()->academic_term_id, ['class'=>'form-control', 'id'=>'class_academic_term_id', 'required'=>'required']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Class Level <span class="text-danger">*</span></label>
                                                    <div>
                                                        {!! Form::select('class_classlevel_id', $classlevels, old('classlevel_id'), ['class'=>'form-control', 'id'=>'class_classlevel_id', 'required'=>'required']) !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Class Room <span class="text-danger">*</span></label>
                                                    {!! Form::select('class_classroom_id', [], '', ['class'=>'form-control', 'id'=>'class_classroom_id', 'required'=>'required']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions noborder">
                                        {!! Form::hidden('type_id', 1, ['id'=>'type_id']) !!}
                                        <button type="submit" class="btn blue pull-right">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane {{ (session('subject-tab') == 'classlevel') ? 'active' : '' }}" id="assign_2classlevel">
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-1">
                                        <div class="alert alert-info"> Search by <strong>Academic Term</strong> and <strong>Class Level</strong></div>
                                        {!! Form::open([
                                                'method'=>'POST',
                                                'class'=>'form-horizontal search_subject_assign_form',
                                                //'id' => 'search_subject_level_form'
                                            ])
                                        !!}
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <div class="col-md-6 col-md-offset-3">
                                                        <div class="form-group">
                                                            <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                            <div>
                                                                {!! Form::select('level_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'id'=>'level_academic_year_id', 'required'=>'required']) !!}
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Academic Term <span class="text-danger">*</span></label>
                                                            {!! Form::select('level_academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                                            ->orderBy('term_type_id')->pluck('academic_term', 'academic_term_id')->prepend('Select Academic Term', ''),
                                                            AcademicTerm::activeTerm()->academic_term_id, ['class'=>'form-control', 'id'=>'level_academic_term_id', 'required'=>'required']) !!}
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Class Level <span class="text-danger">*</span></label>
                                                            <div>
                                                                {!! Form::select('level_classlevel_id', $classlevels, old('classlevel_id'), ['class'=>'form-control', 'id'=>'level_classlevel_id', 'required'=>'required']) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions noborder">
                                                {!! Form::hidden('type_id', 2, ['id'=>'type_id']) !!}
                                                <button type="submit" class="btn blue pull-right">
                                                    <i class="fa fa-search"></i> Search
                                                </button>
                                            </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="view_subject">
                                <div class="alert alert-info"> Search by <strong>Academic Term</strong> and <strong>Class Room</strong> To View Subjects</div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id' => 'search_view_subject_form'
                                    ])
                                !!}
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                    <div>
                                                        {!! Form::select('view_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'id'=>'view_academic_year_id', 'required'=>'required']) !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Academic Term <span class="text-danger">*</span></label>
                                                    {!! Form::select('view_academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                                    ->orderBy('term_type_id')->pluck('academic_term', 'academic_term_id')->prepend('Select Academic Term', ''),
                                                    AcademicTerm::activeTerm()->academic_term_id, ['class'=>'form-control', 'id'=>'view_academic_term_id', 'required'=>'required']) !!}
                                                </div>
                                            </div>
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
                                                <table class="table table-striped table-bordered table-hover" id="view_subject_datatable">

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane {{ (session('subject-tab') == 'manage-subject') ? 'active' : '' }}" id="manage_subject">
                                <div class="alert alert-info"> Search by <strong>Academic Term</strong> and <strong>Class Level</strong> To Manage Subjects</div>
                                <div id="error-box"></div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id' => 'search_manage_subject_form'
                                    ])
                                !!}
                                <div class="form-body">
                                    <div class="form-group">
                                        <div class="col-md-4 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                <div>
                                                    {!! Form::select('manage_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'id'=>'manage_academic_year_id', 'required'=>'required']) !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Academic Term <span class="text-danger">*</span></label>
                                                {!! Form::select('manage_academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                                ->orderBy('term_type_id')->pluck('academic_term', 'academic_term_id')->prepend('Select Academic Term', ''),
                                                AcademicTerm::activeTerm()->academic_term_id, ['class'=>'form-control', 'id'=>'manage_academic_term_id', 'required'=>'required']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="control-label">Class Level <span class="text-danger">*</span></label>
                                                <div>
                                                    {!! Form::select('manage_classlevel_id', $classlevels, old('classlevel_id'), ['class'=>'form-control', 'required'=>'required']) !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Subjects </label>
                                                <select class="form-control" name="subject_id">
                                                    <option value="">Select Subject</option>
                                                    @foreach($school_subjects as $subject)
                                                        <option value="{{ $subject->subject_id }}">{{ ($subject->subject_alias != '') ? $subject->subject_alias : $subject->subject }}</option>
                                                    @endforeach
                                                </select>
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
                                                <table class="table table-striped table-bordered table-hover" id="manage_subject_datatable">

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

    <!-- /.modal -->
    <div id="assign_subject_modal" class="modal fade bs-modal-lg" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h5 class="modal-title" id="modal-title-text">Assign Subjects To Class Room</h5>
                </div>
                <form method="POST" action="#" class="form" role="form" id="assign_subject_form">
                    {!! csrf_field() !!}
                    {!! Form::hidden('assign_classroom_id', '', ['id'=>'assign_classroom_id']) !!}
                    {!! Form::hidden('assign_classlevel_id', '', ['id'=>'assign_classlevel_id']) !!}
                    {!! Form::hidden('assign_academic_term_id', '', ['id'=>'assign_academic_term_id']) !!}
                    <div class="modal-body">
                        <div class="scroller" style="height:300px;" data-always-visible="1" data-rail-visible1="1">
                            <div class="row">
                                <div class="form-body">
                                    <div class="form-group last">
                                        <div class="col-md-10">
                                            <select multiple="multiple" class="multi-select" id="subject_multi_select" name="subject_id[]">

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
    <select id="subject-tutors" class="form-control hide">
        <option value="-1">Select Tutor</option>
        @foreach($tutors as $tutor)
            <option value="{{ $tutor->user_id }}">{{ $tutor->fullNames() }}</option>
        @endforeach
    </select>
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
    <script src="{{ asset('assets/custom/js/subjects/subject-classroom.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/subject-classrooms"]');
        });
    </script>
@endsection
