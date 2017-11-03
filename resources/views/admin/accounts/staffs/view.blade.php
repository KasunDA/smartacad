@extends('admin.layout.default')

@section('layout-style')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Staff Profile')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/staffs') }}">Staffs</a>
        <i class="fa fa-users"></i>
    </li>
    <li>
        <span>Staff Profile</span>
    </li>
@stop


@section('content')
    <h3 class="page-title">Staff Profile | Information</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.staff-nav', ['active' => 'view'])
                <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-9">
                    <div class="portlet light ">
                        <div class="portlet-title tabbable-line">
                            <h1 class="font-green sbold uppercase">{{ $staff->fullNames() }}</h1>
                            <ul class="list-inline">
                                <li>
                                    <i class="fa fa-user"></i>
                                    Username: {{ $staff->email }}
                                </li>
                                <li>
                                    <i class="fa fa-check"></i>
                                    Status: {!! ($staff->status == 1) ? LabelHelper::success('Activated') : LabelHelper::danger('Deactivated') !!}
                                </li>
                            </ul>
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Staff Details</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="portlet sale-summary">
                                <div class="portlet-body">
                                    <table class="table table-stripped table-bordered">
                                        <tr>
                                            <td>Email</td>
                                            <td>{{ $staff->email }}</td>
                                        </tr>
                                        <tr>
                                            <td>Mobile No.</td>
                                            <td>{{ $staff->phone_no }}</td>
                                        </tr>
                                        <tr>
                                            <td>Mobile No 2.</td>
                                            <td>{!! ($staff->phone_no2) ? $staff->phone_no2 : LabelHelper::danger()  !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Gender</td>
                                            <td>{!! ($staff->gender) ? $staff->gender : LabelHelper::danger() !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Date Of Birth</td>
                                            <td>{!! ($staff->dob) ? $staff->dob->format('jS M, Y') : LabelHelper::danger() !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Age</td>
                                            <td>{!! ($staff->dob) ? $staff->dob->age . ' Years' : LabelHelper::danger() !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <td>{!! ($staff->status == 1) ? LabelHelper::success('Activated') : LabelHelper::danger('Deactivated') !!}</td>
                                        </tr>
                                        @if($staff->lga)
                                            <tr>
                                                <td>State</td>
                                                <td>{{ $staff->lga()->first()->state()->first()->state }}</td>
                                            </tr>
                                            <tr>
                                                <td>L.G.A.</td>
                                                <td>{{ $staff->lga()->first()->lga }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                        <td>Address.</td>
                                            <td>{!! ($staff->address) ? $staff->address : LabelHelper::danger() !!}</td>
                                        </tr>
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
@endsection


@section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <!-- END PAGE LEVEL PLUGINS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/staffs"]');
        });
    </script>
@endsection
