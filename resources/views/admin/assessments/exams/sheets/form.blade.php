@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Examination :: Broad Sheet')

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
                            <li class="active"><a href="#sheet" data-toggle="tab"><i class="fa fa-paw"></i> Broad Sheet</a></li>
                        </ul>
                    </div>
                    <div class="portlet-body form">
                        <div class="tab-content">
                            <div id="error-box"></div>
                            @if(Auth::user()->hasRole(['developer', 'super_admin']))
                                <div class="tab-pane active" id="sheet">
                                    <div class="alert alert-info"> Search by <strong>Academic Year</strong></div>
                                    {!! Form::open([
                                            'method'=>'POST',
                                            'class'=>'form-horizontal',
                                        ])
                                    !!}
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                    <div>
                                                        {!! Form::select('academic_year_id', $academic_years, AcademicYear::activeYear()->academic_year_id,
                                                            ['class'=>'form-control', 'id'=>'academic_year_id', 'required'=>'required'])
                                                         !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Class Level <span class="text-danger">*</span></label>
                                                    <div>
                                                        {!! Form::select('classlevel_id', $classlevels, old('classlevel_id'),
                                                            ['class'=>'form-control', 'id'=>'classlevel_id', 'required'=>'required'])
                                                        !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Class Room <small class="font-red">*</small></label>
                                                    <div>
                                                        {!! Form::select('classroom_id', [], '',
                                                            ['class'=>'form-control', 'id'=>'classroom_id', 'required'=>'required'])
                                                        !!}
                                                    </div>
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
                                        <div class="col-md-10">
                                            <div class="portlet-body">
                                                <div class="row">
                                                    <table class="table table-striped table-bordered table-hover" id="broad_sheet_datatable">

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
    <script src="{{ asset('assets/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-tabdrop/js/bootstrap-tabdrop.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/assessments/exam.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/exams/broad-sheet"]');

            getDependentListBox($('#classlevel_id'), $('#classroom_id'), '/list-box/classroom/');

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });
        });
    </script>
@endsection
