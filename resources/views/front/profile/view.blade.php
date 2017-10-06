@extends('front.layout.default')

@section('layout-style')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/pages/css/profile-2.min.css') }}" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'View Profile')

@section('breadcrumb')
    <li>
        <a href="{{ url('/home') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <span>View Profile</span>
    </li>
@stop

@section('page-title')
    <h1>My Profile
        <small></small>
    </h1>
@endsection

@section('content')
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        <div class="profile-sidebar">
            <!-- PORTLET MAIN -->
            <div class="portlet light ">
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
                                                        <table class="table table-stripped table-bordered">
                                                            <tr>
                                                                <td>Title:</td>
                                                                <td>{!! ($user->salutation_id) ? $user->salutation()->first()->salutation : '<span class="label label-danger">nil</span>' !!}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Email:</td>
                                                                <td>{{ $user->email }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Mobile Number 1:</td>
                                                                <td>{{ $user->phone_no }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Mobile Number 2:</td>
                                                                <td>{!! ($user->phone_no2) ? $user->phone_no2 : '<span class="label label-danger">nil</span>' !!}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Gender:</td>
                                                                <td>{!! ($user->gender) ? $user->gender : '<span class="label label-danger">nil</span>' !!}</td>
                                                            </tr>
                                                            @if($user->lga)
                                                                <tr>
                                                                    <td>State Of Origin:</td>
                                                                    <td>{!! ($user->lga()->first()) ? $user->lga()->first()->state()->first()->state .' State' : '<span class="label label-danger">nil</span>' !!}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>L. G. A.:</td>
                                                                    <td>{!! ($user->lga()->first()) ? $user->lga()->first()->lga : '<span class="label label-danger">nil</span>' !!}</td>
                                                                </tr>
                                                            @endif
                                                            <tr>
                                                                <td>Date Of Birth:</td>
                                                                <td>{!! ($user->dob) ? $user->dob->format('jS M, Y') : '<span class="label label-danger">nil</span>' !!}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Age:</td>
                                                                <td>{!! ($user->dob) ? $user->dob->age . ' Years' : '<span class="label label-danger">nil</span>' !!}</td>
                                                            </tr>
                                                            {{--<tr>--}}
                                                            {{--<td>Address:</td>--}}
                                                            {{--<td>{!! ($user->address) ? $user->address : '<span class="label label-danger">nil</span>' !!}</td>--}}
                                                            {{--</tr>--}}
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
            </div>
        </div>
    </div>
@endsection


@section('layout-script')
    <script src="{{ asset('assets/custom/js/users/profile.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/profiles"]');
        });
    </script>
@endsection
