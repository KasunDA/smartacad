<!-- BEGIN SIDEBAR -->
<div class="page-sidebar-wrapper">
    <!-- BEGIN SIDEBAR -->
    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
    <div class="page-sidebar navbar-collapse collapse">
        <!-- BEGIN SIDEBAR MENU -->
        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
            <li class="sidebar-toggler-wrapper hide">
                <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                <div class="sidebar-toggler"> </div>
                <!-- END SIDEBAR TOGGLER BUTTON -->
            </li>
            <!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
            <li class="sidebar-search-wrapper">
                <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
                <!-- DOC: Apply "sidebar-search-bordered" class the below search form to have bordered search box -->
                <!-- DOC: Apply "sidebar-search-bordered sidebar-search-solid" class the below search form to have bordered & solid search box -->
                <form class="sidebar-search  sidebar-search-bordered" action="#" method="POST">
                    <a href="javascript:;" class="remove">
                        <i class="icon-close"></i>
                    </a>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search...">
                        <span class="input-group-btn">
                            <a href="javascript:;" class="btn submit">
                                <i class="icon-magnifier"></i>
                            </a>
                        </span>
                    </div>
                </form>
                <!-- END RESPONSIVE QUICK SEARCH FORM -->
            </li>
            <li class="nav-item start open">
                <a href="{{ url('/dashboard') }}" class="nav-link">
                    <i class="icon-home"></i>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            {{-- Check if the user is logged in--}}
            @if(Auth::check())
                <?php $show_menu_header = []?>
                {{--Loop Through The Users Roles--}}
                @foreach(Auth::user()->roles()->get() as $role)
                    {{--Loop Through The Menu Headers --}}
                    @if(count($active_headers) > 0)
                        @foreach($active_headers as $menu_header)
                            {{--  Check if the menu header has been displayed--}}
                            @if(!in_array($menu_header->menu_header_id, $show_menu_header))

                                {{--  Check if the logged in user have access to view the menu--}}
                                @if(in_array($menu_header->menu_header_id, $role->menuHeaders()->get()->lists('menu_header_id')->toArray()))
                                    <?php $show_menu_header[] = $menu_header->menu_header_id ?>

                                    {{--  Displays the menus header--}}
                                    <li class="heading">
                                        <h3>{{$menu_header->menu_header}}</h3>
                                    </li>
                                    {{-- Check if the menu header has menus--}}
                                    @if($menu_header->menus()->count() > 0)
                                        <?php $show_menu = []?>
                                        {{--  Loop Through The Menus--}}
                                        @foreach($menu_header->menus()->orderBy('sequence')->get() as $menu)
                                            {{--  Check if the menu has been displayed and its enabled--}}
                                            @if(!in_array($menu->menu_id, $show_menu) and $menu->active === 1)

                                                {{--Check if the logged in user have access to view the menu --}}
                                                @if(in_array($menu->menu_id, $role->menus()->get()->lists('menu_id')->toArray()))
                                                    <?php $show_menu[] = $menu->menu_id?>

                                                    {{--  Displays the menus--}}
                                                    <li class="nav-item">
                                                        <a href="{{ $menu->menu_url }}" class="nav-link nav-toggle">
                                                            <i class="{{ $menu->icon }}"></i>
                                                            <span class="title">{{$menu->menu}}</span>
                                                            @if($menu->menuItems()->count() > 0)
                                                                <span class="arrow "></span>
                                                            @endif
                                                        </a>
                                                        <ul class="sub-menu">
                                                            <li class="nav-item">
                                                                <a href="/users/change">
                                                                    <i class="icon-lock"></i> <span class="title">Change Password</span> </a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a href="/users/change">
                                                                    <i class="icon-lock"></i> <span class="title">Change Password 2</span> </a>
                                                            </li>
                                                        </ul>

                                                    </li>
                                                @endif{{--Check if the logged in user have access to view the menu --}}
                                            @endif{{--  Check if the menu has been displayed and its enabled--}}
                                        @endforeach{{--  Loop Through The Menus--}}
                                    @endif{{-- Check if the menu header has menus--}}
                                @endif{{--  Check if the logged in user have access to view the menu--}}
                            @endif{{--  Check if the menu header has been displayed--}}
                        @endforeach  {{--Loop Through The Menu Headers --}}
                    @endif
                @endforeach {{--Loop Through The Users Roles--}}
            @endif{{-- Check if the user is logged in--}}
            <li class="nav-item">
                <a href="/users/change">
                    <i class="icon-lock"></i> <span class="title">Change Password</span> </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('/auth/logout') }}" class="nav-link">
                    <i class="fa fa-power-off"></i>
                    <span class="title">Log Out</span>
                </a>
            </li>
        </ul>
        <!-- END SIDEBAR MENU -->
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->