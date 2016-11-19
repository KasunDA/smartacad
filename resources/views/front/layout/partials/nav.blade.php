<!-- BEGIN HEADER MENU -->
<div class="page-header-menu">
    <div class="container">
        <!-- BEGIN HEADER SEARCH BOX -->
        {{--<form class="search-form" action="page_general_search.html" method="GET">--}}
            {{--<div class="input-group">--}}
                {{--<input type="text" class="form-control" placeholder="Search" name="query">--}}
                            {{--<span class="input-group-btn">--}}
                                {{--<a href="javascript:;" class="btn submit">--}}
                                    {{--<i class="icon-magnifier"></i>--}}
                                {{--</a>--}}
                            {{--</span>--}}
            {{--</div>--}}
        {{--</form>--}}
        <!-- END HEADER SEARCH BOX -->
        <!-- BEGIN MEGA MENU -->
        <!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
        <!-- DOC: Remove data-hover="dropdown" and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
        {{--<div class="hor-menu  ">--}}
            {{--<ul class="nav navbar-nav">--}}
                {{--<li class="menu-dropdown classic-menu-dropdown ">--}}
                    {{--<a href="javascript:;"> Dashboard</a>--}}
                {{--</li>--}}
                {{--<li class="menu-dropdown classic-menu-dropdown ">--}}
                    {{--<a href="javascript:;">--}}
                        {{--<i class="icon-briefcase"></i> Pages--}}
                        {{--<span class="arrow"></span>--}}
                    {{--</a>--}}
                    {{--<ul class="dropdown-menu pull-left">--}}
                        {{--<li class="dropdown-submenu ">--}}
                            {{--<a href="javascript:;" class="nav-link nav-toggle ">--}}
                                {{--<i class="icon-basket"></i> eCommerce--}}
                                {{--<span class="arrow"></span>--}}
                            {{--</a>--}}
                            {{--<ul class="dropdown-menu">--}}
                                {{--<li class=" ">--}}
                                    {{--<a href="ecommerce_index.html" class="nav-link ">--}}
                                        {{--<i class="icon-home"></i> Dashboard </a>--}}
                                {{--</li>--}}
                            {{--</ul>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                {{--</li>--}}
            {{--</ul>--}}
        {{--</div>--}}

        <div class="hor-menu">
            <ul class="nav navbar-nav page-sidebar-menu">
                <li class="menu-dropdown classic-menu-dropdown active">
                    <a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>  DASHBOARD</a>
                </li>
                <li class="menu-dropdown classic-menu-dropdown">
                    <a href="{{ url('/wards') }}"><i class="fa fa-users"></i>  WARD(S)</a>
                </li>

                @if(Auth::check())
                    <?php $show_menu_header = []?>
                    {{--Loop Through The Users Roles--}}
                    @foreach(Auth::user()->roles()->get() as $role)
                        {{--Loop Through The Menu Headers --}}
                        @if(count($active_home_menu) > 0)
                            @foreach($active_home_menu as $menu_header)
                                {{--  Check if the menu header has been displayed--}}
                                @if(!in_array($menu_header->menu_header_id, $show_menu_header))

                                    {{--  Check if the logged in user have access to view the menu--}}
                                    @if(in_array($menu_header->menu_header_id, $role->menuHeaders()->where('type', 2)->get()->lists('menu_header_id')->toArray()))
                                        <?php $show_menu_header[] = $menu_header->menu_header_id ?>
                                        <li class="menu-dropdown classic-menu-dropdown ">
                                            <a href="javascript:;">
                                                <i class="{{ $menu_header->icon }}"></i> {{ $menu_header->menu_header }}
                                            </a>
                                            {{-- Check if the menu header has menus--}}
                                            @if($menu_header->menus()->where('type', 2)->count() > 0)
                                                <ul class="dropdown-menu pull-left">
                                                    <?php $show_menu = []?>
                                                    {{--  Loop Through The Menus--}}
                                                    @foreach($menu_header->menus()->where('type', 2)->orderBy('sequence')->get() as $menu)
                                                        {{--  Check if the menu has been displayed and its enabled--}}
                                                        @if(!in_array($menu->menu_id, $show_menu) and $menu->active === 1)

                                                            {{--Check if the logged in user have access to view the menu --}}
                                                            @if(in_array($menu->menu_id, $role->menus()->where('type', 2)->get()->lists('menu_id')->toArray()))
                                                                <?php $show_menu[] = $menu->menu_id?>

                                                                <li class="dropdown-submenu classic-menu-dropdown">
                                                                    <a href="{{ $menu->menu_url }}" class="nav-link">
                                                                        <i class="{{ $menu->icon }}"></i> {{$menu->menu}}
                                                                    </a>

                                                                    {{-- Check if the menu has menu items--}}
                                                                    @if($menu->menuItems()->where('type', 2)->count() > 0)
                                                                        <ul class="dropdown-menu pull-left">
                                                                            <?php $show_menu_item = []?>
                                                                            {{--  Loop Through The Menu Items--}}
                                                                            @foreach($menu->menuItems()->where('type', 2)->orderBy('sequence')->get() as $menu_item)
                                                                                {{--  Check if the menu item has been displayed and its enabled--}}
                                                                                @if(!in_array($menu_item->menu_item_id, $show_menu_item) and $menu_item->active == 1)

                                                                                    {{--Check if the logged in user have access to view the menu item --}}
                                                                                    @if(in_array($menu_item->menu_item_id, $role->menuItems()->where('type', 2)->get()->lists('menu_item_id')->toArray()))
                                                                                        <?php $show_menu_item[] = $menu_item->menu_item_id?>
                                                                                        <li>
                                                                                            <a href="{{ $menu_item->menu_item_url }}" class="nav-link">
                                                                                                <i class="{{ $menu_item->menu_item_icon }}"></i> {{$menu_item->menu_item}}
                                                                                            </a>
                                                                                        </li>
                                                                                    @endif{{--Check if the logged in user have access to view the menu item --}}
                                                                                @endif{{--  Check if the menu item has been displayed and its enabled--}}
                                                                            @endforeach{{--  Loop Through The Menu Items--}}
                                                                        </ul>
                                                                    @endif {{-- Check if the menu has menu items--}}
                                                                </li>
                                                            @endif{{--Check if the logged in user have access to view the menu --}}
                                                        @endif{{--  Check if the menu has been displayed and its enabled--}}
                                                    @endforeach{{--  Loop Through The Menus--}}
                                                </ul>
                                            @endif{{-- Check if the menu header has menus--}}
                                        </li>
                                    @endif{{--  Check if the logged in user have access to view the menu--}}
                                @endif{{--  Check if the menu header has been displayed--}}
                            @endforeach  {{--Loop Through The Menu Headers --}}
                        @endif
                    @endforeach {{--Loop Through The Users Roles--}}
                @endif{{-- Check if the user is logged in--}}
            </ul>
        </div>
        <!-- END MEGA MENU -->
    </div>
</div>
<!-- END HEADER MENU -->