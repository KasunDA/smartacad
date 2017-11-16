@extends('admin.layout.default')

@section('title', 'Cloning of Existing Records')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="{{ url('/academic-terms/clones') }}">Clone Records</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page">Cloning of Subjects Assigned To Class Room And Tutors In An Academic Term</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div id="error-box"></div>
        {!! Form::open([
                'method'=>'POST',
                'class'=>'form-horizontal',
                'id' => 'clone_subjects_assigned'
            ])
        !!}
            <div class="col-md-5">
                <div class="portlet light bordered">
                    <div class="portlet-body form">
                        <div class="alert alert-info"> Clone Records <strong>From</strong> Academic Term</div>
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-1">
                                    <div class="form-group">
                                        <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                        <div>
                                            {!! Form::select('academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'id'=>'academic_year_id', 'required'=>'required']) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Academic Term <span class="text-danger">*</span></label>
                                        {!! Form::select('academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)->pluck('academic_term', 'academic_term_id')->prepend('Select Academic Term', ''),
                                        AcademicTerm::activeTerm()->academic_term_id, ['class'=>'form-control', 'id'=>'academic_term_id', 'required'=>'required']) !!}
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
                        <div class="alert alert-info"> Clone Records <strong>To</strong> Academic Term</div>
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
                                        <label class="control-label">Academic Term <span class="text-danger">*</span></label>
                                        {!! Form::select('to_academic_term_id', [], '', ['class'=>'form-control', 'id'=>'to_academic_term_id', 'required'=>'required']) !!}
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
    @endsection


    @section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/master-records/academic-term.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/academic-terms/clones"]');

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });
        });
    </script>
@endsection
