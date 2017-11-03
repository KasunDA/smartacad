@extends('admin.layout.default')

@section('layout-style')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
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
    <h3 class="page-title">Profile | Information</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.profile-nav', ['active' => 'view'])
        <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-9">
                    <div class="portlet light ">
                        <div class="portlet-title tabbable-line">
                            <h1 class="font-green sbold uppercase">{{ $user->fullNames() }}</h1>
                            <ul class="list-inline">
                                <li>
                                    <i class="fa fa-user"></i>
                                    Username: {{ $user->email }}
                                </li>
                                <li>
                                    <i class="fa fa-check"></i>
                                    Status: {!! ($user->status == 1) ? LabelHelper::success('Activated') : LabelHelper::danger('Deactivated') !!}
                                </li>
                                <br>Role(s):
                                @foreach($user->roles()->get() as $role)
                                    <li><i class="fa fa-tag"></i> {{ $role->display_name }}</li>
                                @endforeach
                            </ul>
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Profile Details</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="portlet sale-summary">
                                <div class="portlet-body">
                                    <table class="table table-stripped table-bordered">
                                        <tr>
                                            <td>Title:</td>
                                            <td>{!! ($user->salutation_id) ? $user->salutation()->first()->salutation : '<span class="label label-danger">nil</span>' !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td>Mobile No.</td>
                                            <td>{{ $user->phone_no }}</td>
                                        </tr>
                                        <tr>
                                            <td>Mobile No 2.</td>
                                            <td>{!! ($user->phone_no2) ? $user->phone_no2 : LabelHelper::danger()  !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Gender</td>
                                            <td>{!! ($user->gender) ? $user->gender : LabelHelper::danger() !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Date Of Birth</td>
                                            <td>{!! ($user->dob) ? $user->dob->format('jS M, Y') : LabelHelper::danger() !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Age</td>
                                            <td>{!! ($user->dob) ? $user->dob->age . ' Years' : LabelHelper::danger() !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <td>{!! ($user->status == 1) ? LabelHelper::success('Activated') : LabelHelper::danger('Deactivated') !!}</td>
                                        </tr>
                                        @if($user->lga)
                                            <tr>
                                                <td>State</td>
                                                <td>{{ $user->lga()->first()->state()->first()->state }}</td>
                                            </tr>
                                            <tr>
                                                <td>L.G.A.</td>
                                                <td>{{ $user->lga()->first()->lga }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
    <!-- END PAGE HEADER-->
@endsection


@section('layout-script')
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/profiles"]');
        });
    </script>
@endsection
