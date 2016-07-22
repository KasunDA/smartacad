@extends('admin.layout.default')

@section('title', 'Exam Setup')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="{{ url('/exams/setup') }}">Exam Setup</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-academic_year">Exam Setup</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-9">
            <div class="portlet light bordered">
                <div class="portlet-body form">
                    <div class="alert alert-info"> Setup Exam For an <strong> Academic Term</strong></div>
                    <div id="error-box"></div>
                    {!! Form::open([
                            'method'=>'POST',
                            'class'=>'form-horizontal',
                            'id' => 'setup_exam_form'
                        ])
                    !!}
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-1">
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
                            </div>
                        </div>
                        <div class="form-actions noborder">
                            <button type="submit" class="btn blue pull-right">
                                <i class="fa fa-gears"></i> Setup Exam
                            </button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY -->
    <div id="exam_setup_modal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title"><strong>Exam Setup Confirmation</strong></h4>
                </div>
                <div class="modal-body" style="height: 200px">
                    <h4><strong>Are You Sure You Want Setup The Exam?</strong></h4>
                    <div id="exam-message" class="alert alert-info"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">No Close</button>
                    <button type="button" class="btn green" id="confirm-btn" value="">Yes Setup</button>
                </div>
            </div>
        </div>
    </div>
    @endsection


    @section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/assessments/exam.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/exams/setup"]');

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });
        });
    </script>
@endsection
