<!-- BEGIN PROFILE SIDEBAR -->
<div class="profile-sidebar">
    <!-- PORTLET MAIN -->
    <div class="portlet light profile-sidebar-portlet ">
        <!-- SIDEBAR USERPIC -->
        <div class="profile-userpic">
            <img src="{{ $user->getAvatarPath() }}" class="img-responsive" alt="{{ $user->simpleName() }}"/>
            {{--<img src="../assets/pages/media/profile/profile_user.jpg" class="img-responsive" alt=""> --}}
        </div>
        <!-- END SIDEBAR USERPIC -->
        <!-- SIDEBAR USER TITLE -->
        <div class="profile-usertitle">
            <div class="profile-usertitle-name"> {{ $user->fullNames() }} </div>
            <div class="profile-usertitle-job"> {{ $user->userType->user_type }} </div>
        </div>
        <!-- END SIDEBAR USER TITLE -->
        <!-- SIDEBAR MENU -->
        <div class="profile-usermenu">
            <ul class="nav">
                <li class="{!! ($active == 'view' || !isset($active)) ? 'active' : ''  !!}">
                    <a href="{{ url('/profiles') }}">
                        <i class="icon-info"></i> Information
                    </a>
                </li>
                <li class="{!! ($active == 'edit') ? 'active' : ''  !!}">
                    <a href="{{ url('/profiles/edit') }}">
                        <i class="fa fa-edit"></i> Edit Record
                    </a>
                </li>
                <li class="{!! ($active == 'dashboard') ? 'active' : ''  !!}">
                    <a href="{{ url('/profiles/dashboard') }}">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </a>
                </li>
                <li class="{!! ($active == 'subject') ? 'active' : ''  !!}">
                    <a href="{{ url('/profiles/subject') }}">
                        <i class="fa fa-book"></i> Subjects
                    </a>
                </li>
                <li class="{!! ($active == 'classroom') ? 'active' : ''  !!}">
                    <a href="{{ url('/profiles/classroom') }}">
                        <i class="fa fa-industry"></i> Class Rooms
                    </a>
                </li>
                {{--<li class="{!! ($active == 'marked') ? 'active' : ''  !!}">--}}
                    {{--<a href="{{ url('/staffs/marked/'.$hashIds->encode($user->user_id)) }}">--}}
                        {{--<i class="fa fa-check"></i> Subjects (Marked)--}}
                    {{--</a>--}}
                {{--</li>--}}
                {{--<li class="{!! ($active == 'unmarked') ? 'active' : ''  !!}">--}}
                    {{--<a href="{{ url('/staffs/unmarked/'.$hashIds->encode($user->user_id)) }}">--}}
                        {{--<i class="fa fa-times"></i> Subjects (Unmarked)--}}
                    {{--</a>--}}
                {{--</li>--}}
            </ul>
        </div>
        <!-- END MENU -->
    </div>
    <!-- END PORTLET MAIN -->
</div>
<!-- END BEGIN PROFILE SIDEBAR -->
