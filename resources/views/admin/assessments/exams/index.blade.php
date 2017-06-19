@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Examination')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="{{ url('/exams') }}">Examination</a>
        <i class="fa fa-circle"></i>
    </li>
@stop

@section('content')
    <h3 class="page">Exams Score</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet light bordered">
                    <div class="portlet-title tabbable-line">
                        <ul class="nav nav-pills">
                            <li class="{{ (session('active') == 'setup-exam') ? 'active' : ((!session()->has('active')) ? 'active' : '') }}">
                                <a href="#my_exams_setup" data-toggle="tab"><i class="fa fa-gears"></i> Setup My Exams </a>
                            </li>
                            <li class="{{ (session('active') == 'input-scores') ? 'active' : '' }}">
                                <a href="#exams_input_score" data-toggle="tab"><i class="fa fa-pencil-square-o"></i> Input Scores </a>
                            </li>
                            <li>
                                <a href="#" id="compute_ca" class="btn btn-link"><i class="fa fa-barcode"></i> Auto Compute C. A.</a>
                            </li>
                            @if(Auth::user()->hasRole(['admin', 'class_teacher', 'developer', 'super_admin']))
                                <li>
                                    <a href="#terminal" data-toggle="tab"><i class="fa fa-book"></i> Terminal Result</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="portlet-body form">
                        <div class="tab-content">
                            <div id="error-box"></div>
                            <div class="tab-pane {{ (session('active') == 'setup-exam') ? 'active' : ((!session()->has('active')) ? 'active' : '') }}" id="my_exams_setup">
                                <div class="alert alert-info"> Setup Exam For an <strong> Academic Term</strong></div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id' => 'my_exam_setup_form'
                                    ])
                                !!}
                                <div class="form-body">
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                <div>
                                                    {!! Form::select('setup_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'id'=>'setup_academic_year_id', 'required'=>'required']) !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Academic Term <span class="text-danger">*</span></label>
                                                {!! Form::select('setup_academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                                ->orderBy('term_type_id')->lists('academic_term', 'academic_term_id')->prepend('Select Academic Term', ''),
                                                AcademicTerm::activeTerm()->academic_term_id, ['class'=>'form-control', 'id'=>'setup_academic_term_id', 'required'=>'required']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions noborder">
                                    <button type="submit" class="btn blue pull-right">
                                        <i class="fa fa-gears"></i> Setup Exam
                                    </button>
                                </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane {{ (session('active') == 'input-scores') ? 'active' : '' }}" id="exams_input_score">
                                <div class="alert alert-info"> Search for <strong>Subjects Assigned</strong> For The <strong> Academic Term</strong></div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id' => 'search_subject_staff'
                                    ])
                                !!}
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                    <div>
                                                        {!! Form::select('academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'id'=>'academic_year_id', 'required'=>'required']) !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Academic Term <span class="text-danger">*</span></label>
                                                    {!! Form::select('academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                                    ->orderBy('term_type_id')->lists('academic_term', 'academic_term_id')->prepend('Select Academic Term', ''),
                                                    AcademicTerm::activeTerm()->academic_term_id, ['class'=>'form-control', 'id'=>'academic_term_id', 'required'=>'required']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Class Level </label>
                                                    <div>
                                                        {!! Form::select('classlevel_id', $classlevels, old('classlevel_id'), ['class'=>'form-control', 'id'=>'classlevel_id']) !!}
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
                                                <table class="table table-striped table-bordered table-hover" id="subject_assigned_datatable">

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(Auth::user()->hasRole(['admin', 'class_teacher', 'developer', 'super_admin']))
                                <div class="tab-pane" id="terminal">
                                    <div class="alert alert-info"> Search by <strong>Academic Term</strong> and <strong>Class Room</strong> To View Subjects</div>
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
                                                        <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                        <div>
                                                            {!! Form::select('view_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'id'=>'view_academic_year_id', 'required'=>'required']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Academic Term <span class="text-danger">*</span></label>
                                                        {!! Form::select('view_academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                                        ->orderBy('term_type_id')->lists('academic_term', 'academic_term_id')->prepend('Select Academic Term', ''),
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
                                        <div class="col-md-10">
                                            <div class="portlet-body">
                                                <div class="row">
                                                    <table class="table table-striped table-bordered table-hover" id="view_student_datatable">

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
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
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/assessments/exam.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/exams"]');

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });
        });
    </script>
@endsection
