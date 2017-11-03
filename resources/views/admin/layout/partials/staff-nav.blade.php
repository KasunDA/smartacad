<!-- BEGIN PROFILE SIDEBAR -->
<div class="profile-sidebar">
    <!-- PORTLET MAIN -->
    <div class="portlet light profile-sidebar-portlet ">
        <!-- SIDEBAR USERPIC -->
        <div class="profile-userpic">
            <img src="{{ $staff->getAvatarPath() }}" class="img-responsive" alt="{{ $staff->simpleName() }}"/>
            {{--<img src="../assets/pages/media/profile/profile_user.jpg" class="img-responsive" alt=""> --}}
        </div>
        <!-- END SIDEBAR USERPIC -->
        <!-- SIDEBAR USER TITLE -->
        <div class="profile-usertitle">
            <div class="profile-usertitle-name"> {{ $staff->fullNames() }} </div>
            <div class="profile-usertitle-job"> User Type: {{ $staff->userType->user_type }} </div>
        </div>
        <!-- END SIDEBAR USER TITLE -->
        <!-- SIDEBAR MENU -->
        <div class="profile-usermenu">
            <ul class="nav">
                <li class="{!! ($active == 'view' || !isset($active)) ? 'active' : ''  !!}">
                    <a href="{{ url('/staffs/view/'.$hashIds->encode($staff->user_id)) }}">
                        <i class="icon-info"></i> Information </a>
                    </a>
                </li>
                <li class="{!! ($active == 'edit') ? 'active' : ''  !!}">
                    <a href="{{ url('/staffs/edit/'.$hashIds->encode($staff->user_id)) }}">
                        <i class="fa fa-edit"></i> Edit Record </a>
                    </a>
                </li>
                <li class="{!! ($active == 'dashboard') ? 'active' : ''  !!}">
                    <a href="{{ url('/assessments/view/'.$hashIds->encode($staff->user_id)) }}">
                        <i class="fa fa-dashboard"></i> Dashboard</a>
                    </a>
                </li>
                <li class="{!! ($active == 'classrooms') ? 'active' : ''  !!}">
                    <a href="{{ url('/exams/view/'.$hashIds->encode($staff->user_id)) }}">
                        <i class="fa fa-industry"></i> Class Rooms </a>
                    </a>
                </li>
                <li class="{!! ($active == 'subjects') ? 'active' : ''  !!}">
                    <a href="{{ url('/billings/view/'.$hashIds->encode($staff->user_id)) }}">
                        <i class="fa fa-book"></i> Subjects </a>
                    </a>
                </li>
            </ul>
        </div>
        <!-- END MENU -->
    </div>
    <!-- END PORTLET MAIN -->
</div>
<!-- END BEGIN PROFILE SIDEBAR -->
