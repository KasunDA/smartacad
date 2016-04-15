<!-- BEGIN HEADER -->
<div class="page-header">
    <!-- BEGIN HEADER TOP -->
    <div class="page-header-top">
        <div class="container">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="index.html">
                    <img src="../assets/layouts/layout3/img/logo-default.jpg" alt="logo" class="logo-default">
                </a>
            </div>
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="menu-toggler"></a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <!-- BEGIN INBOX DROPDOWN -->
                    {{--<li class="dropdown dropdown-extended dropdown-inbox dropdown-dark" id="header_inbox_bar">--}}
                        {{--<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">--}}
                            {{--<span class="circle">3</span>--}}
                            {{--<span class="corner"></span>--}}
                        {{--</a>--}}
                        {{--<ul class="dropdown-menu">--}}
                            {{--<li class="external">--}}
                                {{--<h3>You have--}}
                                    {{--<strong>7 New</strong> Messages</h3>--}}
                                {{--<a href="app_inbox.html">view all</a>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">--}}
                                    {{--<li>--}}
                                        {{--<a href="#">--}}
                                                    {{--<span class="photo">--}}
                                                        {{--<img src="../assets/layouts/layout3/img/avatar2.jpg" class="img-circle" alt=""> </span>--}}
                                                    {{--<span class="subject">--}}
                                                        {{--<span class="from"> Lisa Wong </span>--}}
                                                        {{--<span class="time">40 mins </span>--}}
                                                    {{--</span>--}}
                                            {{--<span class="message"> Vivamus sed auctor 40% nibh congue nibh... </span>--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                {{--</ul>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                    <!-- END INBOX DROPDOWN -->
                    <!-- BEGIN USER LOGIN DROPDOWN -->
                    <li class="dropdown dropdown-user dropdown-dark">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <img alt="" class="img-circle" src="../assets/layouts/layout3/img/avatar9.jpg">
                            <span class="username username-hide-mobile">Nick</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li>
                                <a href="page_user_profile_1.html">
                                    <i class="icon-user"></i> My Profile </a>
                            </li>
                            <li>
                                <a href="app_inbox.html">
                                    <i class="icon-envelope-open"></i> My Inbox
                                    <span class="badge badge-danger"> 3 </span>
                                </a>
                            </li>
                            <li>
                                <a href="page_user_login_1.html">
                                    <i class="icon-key"></i> Log Out </a>
                            </li>
                        </ul>
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