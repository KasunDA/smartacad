@extends('admin.layout.default')

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

@section('title', 'User Profile')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <span>User Profile</span>
    </li>
@stop


@section('content')
    <h3 class="page-title">User profile</h3>

    <!-- END PAGE HEADER-->
    <div class="profile">
        <div class="tabbable-line tabbable-full-width">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab_1_1" data-toggle="tab"> Overview </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1_1">
                    <div class="row">
                        <div class="col-md-3">
                            <ul class="list-unstyled profile-nav">
                                <li>
                                    @if(!$userView->avatar)
                                        <img src="{{ asset('/uploads/no-image.jpg') }}"
                                             class="img-responsive pic-bordered" alt="{{ $userView->fullNames() }}"/>
                                    @else
                                        <img src="{{ $userView->getAvatarPath() }}" class="img-responsive pic-bordered"
                                             alt="{{ $userView->fullNames() }}"/>
                                    @endif
                                    <a href="{{ url('/users/edit/'.$hashIds->encode($userView->user_id)) }}"
                                       class="profile-edit"> edit </a>
                                </li>
                                {{--<li>--}}
                                    {{--<a href="javascript:;"> Messages--}}
                                        {{--<span> 3 </span>--}}
                                    {{--</a>--}}
                                {{--</li>--}}
                            </ul>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-8 profile-info">
                                    <h1 class="font-green sbold uppercase">{{ $userView->fullNames() }}</h1>
                                    <h4>
                                        User Type: {{ $userView->userType()->first()->user_type }}
                                    </h4>
                                    <ul class="list-inline">
                                        <li>
                                            <i class="fa fa-map-marker"></i> Nigeria
                                        </li>
                                        <li>
                                            <i class="fa fa-envelope"></i> {{ $userView->email }}
                                        </li>
                                        <br>Role(s):
                                        @foreach($userView->roles()->get() as $role)
                                            <li><i class="fa fa-tag"></i> {{ $role->display_name }}</li>
                                        @endforeach
                                    </ul>
                                        <div class="portlet sale-summary">
                                            <div class="portlet-title">
                                                <div class="caption font-red sbold"> User Information
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <table class="table table-stripped table-bordered">
                                                    <tr>
                                                        <td>Email</td>
                                                        <td>{{ $userView->email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mobile No.</td>
                                                        <td>{{ $userView->phone_no }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mobile No 2.</td>
                                                        <td>{!! ($userView->phone_no2) ? $userView->phone_no2 : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Gender</td>
                                                        <td>{!! ($userView->gender) ? $userView->gender : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Date Of Birth</td>
                                                        <td>{!! ($userView->dob) ? $userView->dob->format('jS M, Y') : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Age</td>
                                                        <td>{!! ($userView->dob) ? $userView->dob->age . ' Years' : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    @if($userView->lga)
                                                        <tr>
                                                            <td>State</td>
                                                            <td>{{ $userView->lga()->first()->state()->first()->state }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>L.G.A.</td>
                                                            <td>{{ $userView->lga()->first()->lga }}</td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td>Address:</td>
                                                        <td>{!! ($userView->address) ? $userView->address : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
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
@endsection

@section('layout-script')
    <script src="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}"
            type="text/javascript"></script>
    <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/gmaps/gmaps.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/users"]');
        });
    </script>
@endsection
