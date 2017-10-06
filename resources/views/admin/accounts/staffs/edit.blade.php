@extends('admin.layout.default')

@section('page-level-css')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('layout-style')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/pages/css/profile-2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection
@section('title', 'Modify Staff')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <span>Add Account</span>
    </li>
@stop

@section('content')
    <h3 class="page-title"> Modify Staff Record</h3>

    <div class="row">
        <div class="col-md-offset-2 col-md-6">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-user font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Modify Staff</span>
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
                        {!! Form::hidden('user_id', $staff->user_id) !!}
                        <div class="form-body">
                            <div class="form-group">
                                <label>Title <span class="text-danger">*</span></label>
                                <div>
                                    {!! Form::select('salutation_id', $salutations, $staff->salutation_id, ['class'=>'form-control selectpicker', 'required'=>'required']) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">First Name <span class="text-danger">*</span></label>
                                {!! Form::text('first_name', $staff->first_name, ['placeholder'=>'First Name', 'class'=>'form-control', 'required'=>'required']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Last Name <span class="text-danger">*</span></label>
                                {!! Form::text('last_name', $staff->last_name, ['placeholder'=>'Last Name', 'class'=>'form-control', 'required'=>'required']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Middle Name</label>
                                {!! Form::text('middle_name', $staff->middle_name, ['placeholder'=>'Middle Name', 'class'=>'form-control']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Email <span class="text-danger">*</span></label>
                                {!! Form::text('email', $staff->email, ['placeholder'=>'Email', 'class'=>'form-control', 'required'=>'required']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Mobile Number <span class="text-danger">*</span></label>
                                {!! Form::text('phone_no', $staff->phone_no, ['placeholder'=>'Mobile No', 'class'=>'form-control', 'required'=>'required']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Mobile Number 2</label>
                                {!! Form::text('phone_no2', $staff->phone_no2, ['placeholder'=>'Mobile No 2', 'class'=>'form-control']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Gender <span class="text-danger">*</span></label>
                                {!! Form::select('gender', [''=>'Gender', 'Male'=>'Male', 'Female'=>'Female'], $staff->gender, ['class'=>'form-control selectpicker', 'required'=>'required']) !!}
                            </div>
                            <div class="form-group">
                                <label class="control-label">Date Of Birth <span class="text-danger">*</span></label>
                                <input class="form-control date-picker" data-date-format="yyyy-mm-dd" name="dob" type="text" value="{!! ($staff->dob) ?  $staff->dob->format('Y-m-d') : '' !!}" />
                            </div>
                            <div class="form-group">
                                <label class="control-label">State </label>
                                <div>
                                    @if($lga === null)
                                        {!! Form::select('state_id', $states, old('state_id'), ['class'=>'form-control', 'id'=>'state_id']) !!}
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
                            {{--<div class="form-group">--}}
                                {{--<label>Contact Address</label>--}}
                                {{--<textarea class="form-control" rows="3" required placeholder="Contact Address" name="address">{{ $staff->address }}</textarea>--}}
                            {{--</div>--}}
                            <div class="margiv-top-10">
                                <button class="btn green pull-right"> Update Info </button>
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
@endsection

@section('layout-script')
    <script src="{{ asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/accounts/staffs.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/staffs"]');
        });
    </script>
@endsection
