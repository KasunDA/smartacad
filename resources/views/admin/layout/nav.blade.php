<!-- BEGIN TOP NAVIGATION MENU -->
<div class="top-menu">
    <ul class="nav navbar-nav pull-right">
        <!-- BEGIN NOTIFICATION DROPDOWN -->
        <li class="dropdown dropdown-user">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                @if(Auth::check())
                    <img alt="" class="img-circle" src="{{ Auth::user()->getAvatarPath() }}" />
                @else
                    <img alt="" class="img-circle" src="{{ asset('/uploads/no-image.jpg') }}" />
                @endif
                <span class="username username-hide-on-mobile">
                    @if(Auth::check()) {{ Auth::user()->simpleName() }} @endif
                </span>
                <i class="fa fa-angle-down"></i>
            </a>
            @if(Auth::check())
                <ul class="dropdown-menu dropdown-menu-default">
                    <li>
                        <a href="{{ url('/profiles') }}">
                            <i class="icon-user"></i> My Profile </a>
                    </li>
                    <li class="divider"> </li>
                    <li>
                        <a href="{{ url('/users/change') }}">
                            <i class="icon-lock"></i> Change Password </a>
                    </li>
                    <li>
                        <a href="{{ url('/auth/logout') }}">
                            <i class="fa fa-power-off"></i> Log Out </a>
                    </li>
                </ul>
            @endif
        </li>
    </ul>
</div>
<!-- END TOP NAVIGATION MENU -->