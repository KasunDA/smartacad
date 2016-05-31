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

@section('title', 'Modify User')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <span>Modify User</span>
    </li>
@stop


@section('content')
    <h3 class="page-title">Modify User</h3>

    <!-- END PAGE HEADER-->
    <div class="profile">
        <div class="tabbable-line tabbable-full-width">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab_1_1" data-toggle="tab"> Modify User Info </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1_1">
                    <div class="row profile-account">
                        <div class="col-md-3">
                            <ul class="ver-inline-menu tabbable margin-bottom-10">
                                <li class="{{ (session('active') == 'info') ? 'active' : '' }} {{ (!session()->has('active')) ? 'active' : '' }}">
                                    <a data-toggle="tab" href="#tab_1-1">
                                        <i class="fa fa-cog"></i> User info </a>
                                    <span class="after"> </span>
                                </li>
                                <li class="{{ (session('active') == 'avatar') ? 'active' : '' }}">
                                    <a data-toggle="tab" href="#tab_2-2">
                                        <i class="fa fa-picture-o"></i> Change Avatar </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            @include('errors.errors')
                            <div class="tab-content">
                                <div id="tab_1-1"
                                     class="tab-pane {{ (session('active') == 'info') ? 'active' : '' }} {{ (!session()->has('active')) ? 'active' : '' }} )">
                                    {!! Form::open([
                                                'method'=>'POST',
                                                'class'=>'form',
                                                'role'=>'form'
                                            ])
                                        !!}
                                    {!! Form::hidden('user_id', $user->user_id) !!}

                                    <div class="form-group">
                                        <label>First name</label>
                                        <div class="input-icon input-icon-lg">
                                            <i class="fa fa-user"></i>
                                            <input type="text" class="form-control input-lg" required name="first_name" placeholder="First name" value="{{ $user->first_name }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Last name</label>
                                        <div class="input-icon input-icon-lg">
                                            <i class="fa fa-user"></i>
                                            <input type="text" class="form-control input-lg" required name="last_name" placeholder="Last name" value="{{ $user->last_name }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Gender</label>
                                        <div>
                                            {!! Form::select('gender', [''=>'Nothing Selected', 'Male'=>'Male', 'Female'=>'Female'], $user->gender, ['class'=>'form-control selectpicker input-lg', 'required'=>'required']) !!}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Email</label>
                                        <div class="input-icon input-icon-lg">
                                            <i class="fa fa-envelope"></i>
                                            <input type="email" class="form-control input-lg" required placeholder="Email" name="email" value="{{ $user->email }}"> </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Mobile</label>
                                        <div class="input-icon input-icon-lg">
                                            <i class="fa fa-phone"></i>
                                            <input type="text" class="form-control input-lg" placeholder="Mobile" name="phone_no" value="{{ $user->phone_no }}"> </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Status</label>
                                        {!! Form::select('status', [''=>'Nothing Selected', '1'=>'Activate', '2'=>'Deactivate'], $user->status, ['class'=>'form-control selectpicker input-lg', 'required'=>'required']) !!}
                                    </div>
                                    <div class="margiv-top-10">
                                        <button class="btn green"> Update Info</button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                                <div id="tab_2-2"
                                     class="tab-pane {{ (session('active') == 'avatar') ? 'active' : '' }}">
                                    <form action="/users/avatar" role="form" method="post" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        {!! Form::hidden('user_id', $user->user_id) !!}
                                        <div class="form-group">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail"
                                                     style="width: 200px; height: 150px;">
                                                    @if($user->avatar)
                                                        <img src="{{ $user->getAvatarPath() }}" class="img-responsive pic-bordered"
                                                             alt="{{ $user->fullNames() }}"/>
                                                    @else
                                                        <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image"
                                                             alt=""/>
                                                    @endif
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail"
                                                     style="max-width: 200px; max-height: 150px;">
                                                </div>
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
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <!-- END PAGE LEVEL SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/users/create"]');
        });
    </script>
@endsection



