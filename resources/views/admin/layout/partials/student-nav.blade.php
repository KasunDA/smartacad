<!-- BEGIN PROFILE SIDEBAR -->
<div class="profile-sidebar">
    <!-- PORTLET MAIN -->
    <div class="portlet light profile-sidebar-portlet ">
        <!-- SIDEBAR USERPIC -->
        <div class="profile-userpic">
            <img src="{{ $student->getAvatarPath() }}" class="img-responsive" alt="{{ $student->fullNames() }} Passport"/>
            {{--<img src="../assets/pages/media/profile/profile_user.jpg" class="img-responsive" alt=""> --}}
        </div>
        <!-- END SIDEBAR USERPIC -->
        <!-- SIDEBAR USER TITLE -->
        <div class="profile-usertitle">
            <div class="profile-usertitle-name"> {{ $student->fullNames() }} </div>
            <div class="profile-usertitle-job"> {{ $student->student_no }} </div>
        </div>
        <!-- END SIDEBAR USER TITLE -->
        <!-- SIDEBAR MENU -->
        <div class="profile-usermenu">
            <ul class="nav">
                <li class="{!! ($active == 'view' || !isset($active)) ? 'active' : ''  !!}">
                    <a href="{{ url('/students/view/'.$hashIds->encode($student->student_id)) }}">
                        <i class="icon-info"></i> Information
                    </a>
                </li>
                <li class="{!! ($active == 'edit') ? 'active' : ''  !!}">
                    <a href="{{ url('/students/edit/'.$hashIds->encode($student->student_id)) }}">
                        <i class="fa fa-edit"></i> Edit Record
                    </a>
                </li>
                <li class="{!! ($active == 'assessment') ? 'active' : ''  !!}">
                    <a href="{{ url('/assessments/view/'.$hashIds->encode($student->student_id)) }}">
                        <i class="fa fa-book"></i> Assessments
                    </a>
                </li>
                <li class="{!! ($active == 'exam') ? 'active' : ''  !!}">
                    <a href="{{ url('/exams/view/'.$hashIds->encode($student->student_id)) }}">
                        <i class="fa fa-industry"></i> Exams
                    </a>
                </li>
                <li class="{!! ($active == 'billing') ? 'active' : ''  !!}">
                    <a href="{{ url('/billings/view/'.$hashIds->encode($student->student_id)) }}">
                        <i class="fa fa-money"></i> Billings
                    </a>
                </li>
                <li class="{!! ($active == 'attendance') ? 'active' : ''  !!}">
                    <a href="{{ url('/attendances/view/'.$hashIds->encode($student->student_id)) }}">
                        <i class="fa fa-check"></i> Attendance
                    </a>
                </li>
            </ul>
        </div>
        <!-- END MENU -->
    </div>
    <!-- END PORTLET MAIN -->
</div>
<!-- END BEGIN PROFILE SIDEBAR -->
