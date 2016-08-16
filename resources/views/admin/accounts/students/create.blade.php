@extends('admin.layout.default')

@section('page-level-css')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('layout-style')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/pages/css/profile-2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection
@section('title', 'Add New Student')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <span>Add Student</span>
    </li>
@stop

@section('content')
    <h3 class="page-title">New Student Record</h3>

    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-user font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Create Student</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    @include('errors.errors')
                        {!! Form::open([
                                'method'=>'POST',
                                'class'=>'form',
                                'role'=>'form'
                            ])
                        !!}
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label">Sponsor Name<span class="text-danger">*</span></label>
                                {!! Form::text('sponsor_name', '', ['placeholder'=>'Sponsor Name', 'class'=>'form-control', 'id'=>'sponsor_name', 'required'=>'required']) !!}
                                {!! Form::hidden('sponsor_id', '', ['class'=>'form-control', 'id'=>'sponsor_id']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">First Name <span class="text-danger">*</span></label>
                                {!! Form::text('first_name','', ['placeholder'=>'First Name', 'class'=>'form-control', 'required'=>'required']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Last Name <span class="text-danger">*</span></label>
                                {!! Form::text('last_name','', ['placeholder'=>'Last Name', 'class'=>'form-control', 'required'=>'required']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Gender <span class="text-danger">*</span></label>
                                {!! Form::select('gender', [''=>'Select Gender', 'Male'=>'Male', 'Female'=>'Female'], '', ['class'=>'form-control selectpicker input-lg', 'required'=>'required']) !!}
                            </div>

                            <div class="form-group">
                                <label class="control-label">Class Level <span class="text-danger">*</span></label>
                                <div>
                                    {!! Form::select('classlevel_id', $classlevels, old('classlevel_id'), ['class'=>'form-control selectpicker', 'id'=>'classlevel_id', 'required'=>'required']) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Current Class Room <span class="text-danger">*</span></label>
                                {!! Form::select('classroom_id', [], '', ['class'=>'form-control', 'id'=>'classroom_id', 'required'=>'required']) !!}
                            </div>
                            <div class="margiv-top-10">
                                <button class="btn green pull-right btn-lg"> Save Record </button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </form>
                </div>
            </div>
            <!-- END SAMPLE FORM PORTLET-->
        </div>
    </div>

@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/accounts/students.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/students/create"]');

            // Auto Complete of Sponsor Name
            autoCompleteField($("#sponsor_name"), $("#sponsor_id"), "/students/sponsors/");
        });
    </script>
@endsection
