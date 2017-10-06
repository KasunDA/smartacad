@extends('front.layout.default')

@section('page-level-css')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('layout-style')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection
@section('title', 'Update Student')

@section('breadcrumb')
    <li>
        <a href="{{ url('/home') }}">Home</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/wards') }}">Students</a>
        <i class="fa fa-users"></i>
    </li>
    <li>
        <span>Editing</span>
    </li>
@stop

@section('page-title')
    <h1> Student Profile | Edit</h1>
@endsection

@section('content')
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('front.layout.partials.student-nav', ['active' => 'edit'])
                <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-8">
                    <div class="portlet light ">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Update Student Form</span>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <div class="form-body">
                                @include('errors.errors')
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form',
                                        'role'=>'form'
                                    ])
                                !!}
                                    {!! Form::hidden('student_id', $student->student_id) !!}
                                    <div class="form-group">
                                        <label class="control-label">First Name <span class="text-danger">*</span></label>
                                        {!! Form::text('first_name', $student->first_name, ['placeholder'=>'First Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Last Name <span class="text-danger">*</span></label>
                                        {!! Form::text('last_name', $student->last_name, ['placeholder'=>'Last Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Middle Name</label>
                                        {!! Form::text('middle_name', $student->middle_name, ['placeholder'=>'Middle Name', 'class'=>'form-control']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Gender <span class="text-danger">*</span></label>
                                        {!! Form::select('gender', [''=>'Gender', 'Male'=>'Male', 'Female'=>'Female'], $student->gender, ['class'=>'form-control selectpicker', 'required'=>'required']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Date Of Birth <span class="text-danger">*</span></label>
                                        <input class="form-control date-picker" data-date-format="yyyy-mm-dd" name="dob" type="text" value="{!! ($student->dob) ?  $student->dob->format('Y-m-d') : '' !!}" />
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">State </label>
                                        <div>
                                            @if($lga === null)
                                                {!! Form::select('state_id', $states, '', ['class'=>'form-control', 'id'=>'state_id']) !!}
                                            @else
                                                {!! Form::select('state_id', $states, $lga->state_id, ['class'=>'form-control', 'id'=>'state_id']) !!}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">L.G.A </label>
                                        <div>
                                            @if($lga == null)
                                                {!! Form::select('lga_id', [''=>'Nothing Selected'], '', ['class'=>'form-control', 'id'=>'lga_id']) !!}
                                            @else
                                                {!! Form::select('lga_id', $lgas, $lga->lga_id, ['class'=>'form-control', 'id'=>'lga_id']) !!}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Contact Address</label>
                                        <textarea class="form-control" rows="3" required placeholder="Contact Address" name="address">{{ $student->address }}</textarea>
                                    </div>
                                    <div class="margin-top-10">
                                        <button class="btn green pull-right"> Update Info </button>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/jquery.sparkline.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/profile.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/accounts/students.js') }}" type="text/javascript"></script>

    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/wards"]');
        });
    </script>
@endsection
