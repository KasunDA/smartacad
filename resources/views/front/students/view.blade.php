@extends('front.layout.default')

@section('layout-style')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/pages/css/profile-2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Student Profile')

@section('breadcrumb')
    <li>
        <a href="{{ url('/home') }}">Home</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <span>Student Profile</span>
    </li>
@stop


@section('page-title')
    <h1> Student Profile </h1>
@endsection

@section('content')
    <div class="col-md-12">
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
                                        @if(!$student->avatar)
                                            <img src="{{ asset('/uploads/no-image.jpg') }}" class="img-responsive pic-bordered" alt="{{ $student->fullNames() }}"/>
                                        @else
                                            <img src="{{ $student->getAvatarPath() }}" class="img-responsive pic-bordered" alt="{{ $student->fullNames() }}"/>
                                        @endif
                                        <a href="{{ url('/wards/edit/'.$hashIds->encode($student->student_id)) }}" class="profile-edit"> edit </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-8 profile-info">
                                        <h1 class="font-green sbold uppercase">{{ $student->fullNames() }}</h1>
                                        <ul class="list-inline">
                                            <li>
                                                <i class="fa fa-user"></i> Student No: {{ $student->student_no }}
                                            </li>
                                            <li>
                                                <i class="fa fa-user-plus"></i> Sponsor:
                                                @if(($student->sponsor_id))
                                                    <a target="_blank" href="{{ url('/profiles') }}" class="btn btn-info btn-link btn-lg">
                                                        {{$student->sponsor()->first()->fullNames()}}
                                                    </a>
                                                @else
                                                    <span class="label label-danger">nil</span>
                                                @endif
                                            </li>
                                        </ul>

                                        <div class="portlet sale-summary">
                                            <div class="portlet-title">
                                                <div class="caption font-red sbold"> Student Information </div>
                                            </div>
                                            <div class="portlet-body">
                                                <table class="table table-stripped table-bordered">
                                                    <tr>
                                                        <td>First Name</td>
                                                        <td>{{ $student->first_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Last Name</td>
                                                        <td>{{ $student->last_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Middle Name</td>
                                                        <td>{!! ($student->middle_name) ? $student->middle_name : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Current Class</td>
                                                        <td>{!! ($student->currentClass(AcademicYear::activeYear()->academic_year_id))
                                                            ? $student->currentClass(AcademicYear::activeYear()->academic_year_id)->classroom
                                                            : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Gender</td>
                                                        <td>{!! ($student->gender) ? $student->gender : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Date Of Birth</td>
                                                        <td>{!! ($student->dob) ? $student->dob->format('jS M, Y') : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Age</td>
                                                        <td>{!! ($student->dob) ? $student->dob->age . ' Years' : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Status</td>
                                                        <td>
                                                            @if($student->status_id)
                                                                <label class="label label-{{$student->status()->first()->label}}">{{ $student->status()->first()->status }}</label>
                                                            @else
                                                                <label class="label label-danger">nil</label>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @if($student->lga)
                                                        <tr>
                                                            <td>State</td>
                                                            <td>{{ $student->lga()->first()->state()->first()->state }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>L.G.A.</td>
                                                            <td>{{ $student->lga()->first()->lga }}</td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td>Address.</td>
                                                        <td>{!! ($student->address) ? $student->address : '<span class="label label-danger">nil</span>' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Term Admitted</td>
                                                        <td>{!! ($student->admitted_term_id) ? $student->termAdmitted()->first()->academic_term : '<span class="label label-danger">nil</span>' !!}</td>
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
    </div>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/wards"]');
        });
    </script>
@endsection
