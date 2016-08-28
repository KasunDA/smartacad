<!-- BEGIN HEADER -->
<div class="page-header">
    <!-- BEGIN HEADER TOP -->
    <div class="page-header-top">
        <div class="container">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="/">
                    <img src="{{ asset('assets/layouts/layout/img/logo.png') }}" alt="logo" class="logo-default" /> </a>
                <div class="menu-toggler sidebar-toggler"> </div>
            </div>
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="menu-toggler"></a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown dropdown-user dropdown-dark">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                           data-close-others="true">
                            @if(Auth::check() and Auth::user()->getAvatarPath())
                                <img alt="" class="img-circle" src="{{ Auth::user()->getAvatarPath() }}"/>
                            @else
                                <img alt="" class="img-circle" src="{{ asset('/uploads/no-image.jpg') }}"/>
                            @endif
                            <span class="username username-hide-mobile">@if(Auth::check()) {{ Auth::user()->fullNames() }} @endif</span>
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
                    <!-- END USER LOGIN DROPDOWN -->
                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
    </div>
    <!-- END HEADER TOP -->
    @include('front.layout.partials.nav')
</div>
<!-- END HEADER -->