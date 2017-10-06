@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Student Profile')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/wards') }}">Students</a>
        <i class="fa fa-users"></i>
    </li>
    <li>
        <span>Student Profile</span>
    </li>
@stop


@section('content')
    <h3 class="page-title">Student Profile | Information</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.student-nav', ['active' => 'view'])
                <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-9">
                    <div class="portlet light ">
                        <div class="portlet-title tabbable-line">
                            <h1 class="font-green sbold uppercase">{{ $student->fullNames() }}</h1>
                            <ul class="list-inline">
                                <li>
                                    <i class="fa fa-user"></i> Student No: {{ $student->student_no }}
                                </li>
                                <li>
                                    <i class="fa fa-user-plus"></i> Sponsor:
                                    @if(($student->sponsor_id))
                                        <a target="_blank" href="{{ url('/sponsors/view/'.$hashIds->encode($student->sponsor()->first()->user_id)) }}" class="btn btn-info btn-link btn-lg">
                                            {{$student->sponsor()->first()->fullNames()}}
                                        </a>
                                    @else
                                        <span class="label label-danger">nil</span>
                                    @endif
                                </li>
                            </ul>
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">Student Details</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="portlet sale-summary">
                                <div class="portlet-body">
                                    <table class="table table-stripped table-bordered">
                                        <tr>
                                            <th>First Name</th>
                                            <td>{{ $student->first_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Name</th>
                                            <td>{{ $student->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Middle Name</th>
                                            <td>{!! ($student->middle_name) ? $student->middle_name : LabelHelper::danger()!!}</td>
                                        </tr>
                                        <tr>
                                            <th>Student No</th>
                                            <td>{{ $student->student_no }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sponsor</th>
                                            <td>
                                                @if(($student->sponsor_id))
                                                    <a target="_blank" href="{{ url('/profiles') }}">
                                                        {{$student->sponsor()->first()->fullNames()}}
                                                    </a>
                                                @else
                                                    <span class="label label-danger">nil</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Current Class</th>
                                            <td>{!! ($student->currentClass(AcademicYear::activeYear()->academic_year_id))
                                                            ? $student->currentClass(AcademicYear::activeYear()->academic_year_id)->classroom
                                                            : LabelHelper::danger() !!}</td>
                                        </tr>
                                        <tr>
                                            <th>Gender</th>
                                            <td>{!! ($student->gender) ? $student->gender : LabelHelper::danger() !!}</td>
                                        </tr>
                                        <tr>
                                            <th>Date Of Birth</th>
                                            <td>{!! ($student->dob) ? $student->dob->format('jS M, Y') : LabelHelper::danger() !!}</td>
                                        </tr>
                                        <tr>
                                            <th>Age</th>
                                            <td>{!! ($student->dob) ? $student->dob->age . ' Years' : LabelHelper::danger() !!}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
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
                                                <th>State</th>
                                                <td>{{ $student->lga()->first()->state()->first()->state }}</td>
                                            </tr>
                                            <tr>
                                                <th>L.G.A.</th>
                                                <td>{{ $student->lga()->first()->lga }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>Address.</th>
                                            <td>{!! ($student->address) ? $student->address : LabelHelper::danger() !!}</td>
                                        </tr>
                                        <tr>
                                            <th>Term Admitted</th>
                                            <td>{!! ($student->admitted_term_id) ? $student->termAdmitted()->first()->academic_term : LabelHelper::danger() !!}</td>
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
    <!-- END PAGE HEADER-->
@endsection

@section('page-level-js')
    <script src="{{ asset('assets/pages/scripts/profile.min.js') }}" type="text/javascript"></script>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/custom/js/accounts/students.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/students"]');
        });
    </script>
@endsection
