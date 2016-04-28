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

@section('title', 'View Profile')

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
        <span>View Profile</span>
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
                                    @if(!$user->avatar)
                                        <img src="{{ asset('/uploads/no-image.jpg') }}"
                                             class="img-responsive pic-bordered" alt="{{ $user->fullNames() }}"/>
                                    @else
                                        <img src="{{ $user->getAvatarPath() }}" class="img-responsive pic-bordered"
                                             alt="{{ $user->fullNames() }}"/>
                                    @endif
                                    <a href="{{ url('/profiles/edit/') }}" class="profile-edit"> edit </a>
                                </li>
                                <li>
                                    <a href="javascript:;"> Messages
                                        <span> 3 </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-8 profile-info">
                                    <h1 class="font-green sbold uppercase">{{ $user->fullNames() }}</h1>
                                    <h4>
                                        {{ $user->userType()->first()->user_type }}
                                    </h4>
                                    <ul class="list-inline">
                                        <li>
                                            <i class="fa fa-map-marker"></i> Nigeria
                                        </li>
                                        <li>
                                            <i class="fa fa-envelope"></i> {{ $user->email }}
                                        </li>
                                    </ul>

                                    <div class="portlet sale-summary">
                                        <div class="portlet-title">
                                            <div class="caption font-red sbold"> User Information</div>
                                        </div>
                                        <div class="portlet-body">
                                            @if($staff)
                                                <table class="table table-stripped table-bordered">
                                                    <tr>
                                                        <td>Title:</td>
                                                        <td>{{ $staff->salutation()->first()->salutation }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Email:</td>
                                                        <td>{{ $staff->email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mobile Number 1:</td>
                                                        <td>{{ $staff->phone_no }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mobile Number 2:</td>
                                                        <td>{!! ($staff->phone_no2) ? $staff->phone_no2 : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Gender:</td>
                                                        <td>{!! ($staff->gender) ? $staff->gender : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>State Of Origin:</td>
                                                        <td>{!! ($staff->lga()->first()) ? $staff->lga()->first()->state()->first()->state .' State' : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>L. G. A.:</td>
                                                        <td>{!! ($staff->lga()->first()) ? $staff->lga()->first()->lga : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Date Of Birth:</td>
                                                        <td>{!! ($staff->dob) ? $staff->dob->format('jS M, Y') : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Age:</td>
                                                        <td>{!! ($staff->dob) ? $staff->dob->age . ' Years' : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Address:</td>
                                                        <td>{!! ($staff->address) ? $staff->address : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                </table>
                                            @endif
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
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/profiles"]');
        });
    </script>
@endsection
