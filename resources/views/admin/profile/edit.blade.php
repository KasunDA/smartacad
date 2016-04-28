@extends('admin.layout.default')

@section('page-level-css')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}"
      rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet"
      type="text/css"/>
@endsection

@section('layout-style')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet"
      type="text/css"/>

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/pages/css/profile-2.min.css') }}" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Edit Profile')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <span>Edit Profile</span>
    </li>
@stop


@section('content')
    <h3 class="page-title">Edit Profile</h3>

    <!-- END PAGE HEADER-->
    <div class="profile">
        <div class="tabbable-line tabbable-full-width">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab_1_1" data-toggle="tab"> Edit Profile </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1_1">
                    <div class="row profile-account">
                        <div class="col-md-3">
                            <ul class="ver-inline-menu tabbable margin-bottom-10">
                                @if($staff)
                                <li class="{{ (session('active') == 'info') ? 'active' : '' }} {{ (!session()->has('active')) ? 'active' : '' }}">
                                    <a data-toggle="tab" href="#info">
                                        <i class="fa fa-cog"></i> Personal info </a>
                                    <span class="after"> </span>
                                </li>
                                @endif
                                <li class="{{ (session('active') == 'avatar') ? 'active' : '' }}">
                                    <a data-toggle="tab" href="#avatar">
                                        <i class="fa fa-picture-o"></i> Change Avatar </a>
                                </li>
                                <li class="{{ (session('active') == 'password') ? 'active' : '' }}">
                                    <a data-toggle="tab" href="#password">
                                        <i class="fa fa-lock"></i> Change Password </a>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-6">
                            @include('errors.errors')
                            <div class="tab-content">
                                @if($staff)
                                    <div id="info"
                                         class="tab-pane {{ (session('active') == 'info') ? 'active' : '' }} {{ (!session()->has('active')) ? 'active' : '' }} )">
                                        {!! Form::open([
                                                'method'=>'POST',
                                                'class'=>'form',
                                                'role'=>'form'
                                            ])
                                        !!}
                                        {{--<input type="hidden" name="staff_id" value="{{ $staff->staff_id }}">--}}
                                        <div class="form-group">
                                            <label class="control-label">Title</label>
                                            @if($staff->salutation_id === null)
                                                {!! Form::select('salutation_id', $salutations, old('salutation_id'), ['class'=>'form-control input-lg selectpicker']) !!}
                                            @else
                                                {!! Form::select('salutation_id', $salutations, $staff->salutation_id, ['class'=>'form-control input-lg selectpicker']) !!}
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">First Name</label>
                                            {!! Form::text('first_name', $staff->first_name, ['placeholder'=>'First Name', 'class'=>'form-control', 'required'=>'required']) !!}
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Other Names</label>
                                            {!! Form::text('other_name', $staff->other_name, ['placeholder'=>'Other Names', 'class'=>'form-control', 'required'=>'required']) !!}
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Email</label>
                                            {!! Form::text('email', $staff->email, ['placeholder'=>'Email', 'class'=>'form-control', 'required'=>'required', 'disabled'=>true]) !!}
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Mobile Number</label>
                                            {!! Form::text('phone_no', $staff->phone_no, ['placeholder'=>'Mobile No', 'class'=>'form-control', 'required'=>'required']) !!}
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Mobile Number 2</label>
                                            {!! Form::text('phone_no2', $staff->phone_no2, ['placeholder'=>'Mobile No 2', 'class'=>'form-control']) !!}
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Gender</label>
                                            {!! Form::select('gender', [''=>'Gender', 'Male'=>'Male', 'Female'=>'Female'], $staff->gender, ['class'=>'form-control selectpicker', 'required'=>'required']) !!}
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Date Of Birth </label>
                                            <input class="form-control date-picker" data-date-format="yyyy-mm-dd"
                                                   name="dob" type="text"
                                                   value="{!! ($staff->dob) ?  $staff->dob->format('Y-m-d') : '' !!}"/>
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
                                        <div class="form-group">
                                            <label>Contact Address</label>
                                            <textarea class="form-control input-lg" rows="3" required
                                                      placeholder="Contact Address"
                                                      name="address">{{ $staff->address }}</textarea>
                                        </div>
                                        <div class="margiv-top-10">
                                            <button class="btn green"> Update Info</button>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                @endif
                                <div id="avatar" class="tab-pane {{ (session('active') == 'avatar') ? 'active' : '' }}">
                                    <form action="/profiles/avatar" role="form" method="post"
                                          enctype="multipart/form-data">
                                        <div class="form-group">
                                            {{ csrf_field() }}
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail"
                                                     style="width: 200px; height: 150px;">
                                                    @if($user)
                                                        <img src="{{ $user->getAvatarPath() }}"
                                                             class="img-responsive pic-bordered"
                                                             alt="{{ $user->fullNames() }}"/>
                                                    @else
                                                        <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image"
                                                             alt=""/>
                                                    @endif
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail"
                                                     style="max-width: 200px; max-height: 150px;"></div>
                                                <div>
                                                    <span class="btn default btn-file">
                                                    <span class="fileinput-new"> Select image </span>
                                                    <span class="fileinput-exists"> Change </span>
                                                    <input type="file" name="avatar"></span>
                                                    <a href="javascript:;" class="btn default fileinput-exists"
                                                       data-dismiss="fileinput"> Remove </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="margin-top-10">
                                            <button type="submit" class="btn green"> Submit</button>
                                        </div>
                                    </form>
                                </div>
                                <div id="password"
                                     class="tab-pane {{ (session('active') == 'password') ? 'active' : '' }}">
                                    <form method="POST" action="/profiles/change-password" accept-charset="UTF-8"
                                          role="form">
                                        {!! csrf_field() !!}
                                        <input type="hidden" value="{{ $hashIds->encode($user->user_id) }}" name="id">

                                        <div class="form-group">
                                            <label class="control-label">Current Password</label>
                                            <input name="password" type="password" class="form-control" required/></div>
                                        <div class="form-group">
                                            <label class="control-label">New Password</label>
                                            <input name="new_password" type="password" class="form-control" required/>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Re-type New Password</label>
                                            <input name="password_confirmation" type="password" class="form-control"
                                                   required/></div>
                                        <div class="margin-top-10">
                                            <button class="btn green"> Update Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!--end col-md-9-->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}"
            type="text/javascript"></script>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/pages/scripts/components-date-time-pickers.min.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/users/profile.js') }}" type="text/javascript"></script>
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <!-- END PAGE LEVEL SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/profiles/edit"]');
        });
    </script>
@endsection



