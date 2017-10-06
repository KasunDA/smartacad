@extends('admin.layout.default')

@section('layout-style')
<link href="{{ asset('assets/pages/css/profile-2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Sponsor Profile')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <span>Sponsor Profile</span>
    </li>
@stop


@section('content')
    <h3 class="page-title">Sponsor Profile</h3>

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
                                    @if(!$sponsor->avatar)
                                        <img src="{{ asset('/uploads/no-image.jpg') }}" class="img-responsive pic-bordered" alt="{{ $sponsor->fullNames() }}"/>
                                    @else
                                        <img src="{{ $sponsor->getAvatarPath() }}" class="img-responsive pic-bordered" alt="{{ $sponsor->fullNames() }}"/>
                                    @endif
                                    <a href="{{ url('/sponsors/edit/'.$hashIds->encode($sponsor->user_id)) }}" class="profile-edit"> edit </a>
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
                                    <h1 class="font-green sbold uppercase">{{ $sponsor->fullNames() }}</h1>
                                    <h4>
                                        {{ $sponsor->userType()->first()->user_type }}
                                    </h4>
                                    <ul class="list-inline">
                                        <li>
                                            <i class="fa fa-map-marker"></i> Nigeria
                                        </li>
                                    </ul>

                                    <div class="portlet sale-summary">
                                        <div class="portlet-title">
                                            <div class="caption font-red sbold"> Sponsor Information </div>
                                        </div>
                                        <div class="portlet-body">
                                            <table class="table table-stripped table-bordered">
                                                <tr>
                                                    <td>Email</td>
                                                    <td>{{ $sponsor->email }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Mobile No.</td>
                                                    <td>{{ $sponsor->phone_no }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Mobile No 2.</td>
                                                    <td>{!! ($sponsor->phone_no2) ? $sponsor->phone_no2 : '<span class="label label-danger">nil</span>' !!}</td>
                                                </tr>
                                                <tr>
                                                    <td>Date Of Birth</td>
                                                    <td>{!! ($sponsor->dob) ? $sponsor->dob->format('jS M, Y') : '<span class="label label-danger">nil</span>' !!}</td>
                                                </tr>
                                                <tr>
                                                    <td>Age</td>
                                                    <td>{!! ($sponsor->dob) ? $sponsor->dob->age . ' Years' : '<span class="label label-danger">nil</span>' !!}</td>
                                                </tr>
                                                @if($sponsor->lga)
                                                    <tr>
                                                        <td>State</td>
                                                        <td>{{ $sponsor->lga()->first()->state()->first()->state }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>L.G.A.</td>
                                                        <td>{{ $sponsor->lga()->first()->lga }}</td>
                                                    </tr>
                                                @endif
                                                {{--<tr>--}}
                                                    {{--<td>Address.</td>--}}
                                                    {{--<td>{!! ($sponsor->address) ? $sponsor->address . ' Years' : '<span class="label label-danger">nil</span>' !!}</td>--}}
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
@endsection

@section('layout-script')
    {{--<script src="{{ asset('assets/pages/scripts/profile.min.js') }}" type="text/javascript"></script>--}}
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/sponsors"]');
        });
    </script>
@endsection
