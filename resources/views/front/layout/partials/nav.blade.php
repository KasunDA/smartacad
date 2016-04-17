<!-- BEGIN HEADER MENU -->
<div class="page-header-menu">
    <div class="container">
        <!-- BEGIN HEADER SEARCH BOX -->
        <form class="search-form" action="page_general_search.html" method="GET">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search" name="query">
                            <span class="input-group-btn">
                                <a href="javascript:;" class="btn submit">
                                    <i class="icon-magnifier"></i>
                                </a>
                            </span>
            </div>
        </form>
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

        <div class="hor-menu  ">
            <ul class="nav navbar-nav">
                <li class="menu-dropdown classic-menu-dropdown ">
                    <a href="{{ url('/') }}"><i class="fa fa-home"></i>  Home</a>
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
                                    @if(in_array($menu_header->menu_header_id, $role->menuHeaders()->get()->lists('menu_header_id')->toArray()))
                                        <?php $show_menu_header[] = $menu_header->menu_header_id ?>
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
                                                        <li class="menu-dropdown classic-menu-dropdown ">
                                                            <a href="{{ $menu->menu_url }}" >
                                                                <i class="{{ $menu->icon }}"></i> {{$menu->menu}}
                                                                @if($menu->menuItems()->count() > 0)
                                                                    <span class="arrow "></span>
                                                                @endif
                                                            </a>
                                                            {{-- Check if the menu has menu items--}}
                                                            @if($menu->menuItems()->count() > 0)
                                                                <?php $show_menu_item = []?>
                                                                    <ul class="dropdown-menu pull-left">
                                                                    {{--  Loop Through The Menu Items--}}
                                                                    @foreach($menu->menuItems()->orderBy('sequence')->get() as $menu_item)
                                                                        {{--  Check if the menu item has been displayed and its enabled--}}
                                                                        @if(!in_array($menu_item->menu_item_id, $show_menu_item) and $menu_item->active === 1)

                                                                            {{--Check if the logged in user have access to view the menu item --}}
                                                                            @if(in_array($menu_item->menu_item_id, $role->menuItems()->get()->lists('menu_item_id')->toArray()))
                                                                                <?php $show_menu_item[] = $menu_item->menu_item_id?>

                                                                                    <li class="dropdown-submenu ">
                                                                                    {{-- Check if the menu item has sub menu items--}}
                                                                                    @if($menu_item->subMenuItems()->count() > 0)
                                                                                        <?php $show_sub_menu_item = []?>
                                                                                        <a href="{{ $menu_item->menu_item_url  }}" class="nav-link nav-toggle ">
                                                                                            <i class="{{ $menu_item->menu_item_icon  }}"></i> {{ $menu_item->menu_item  }}
                                                                                            <span class="arrow"></span>
                                                                                        </a>
                                                                                        {{--  Displays the menu items with the its sub menu items--}}
                                                                                            <ul class="dropdown-menu">

                                                                                            {{--  Loop Through The Sub Menu Items--}}
                                                                                            @foreach($menu_item->subMenuItems()->orderBy('sequence')->get() as $sub_menu_item)
                                                                                                {{--  Check if the sub menu item has been displayed and its enabled--}}
                                                                                                @if(!in_array($sub_menu_item->sub_menu_item_id, $show_sub_menu_item) and $sub_menu_item->active === 1)

                                                                                                    {{--Check if the logged in user have access to view the sub menu item --}}
                                                                                                    @if(in_array($sub_menu_item->sub_menu_item_id, $role->subMenuItems()->get()->lists('sub_menu_item_id')->toArray()))
                                                                                                        <?php $show_sub_menu_item[] = $sub_menu_item->sub_menu_item_id?>

                                                                                                        {{-- Check if the sub menu item has sub most menu items--}}
                                                                                                            <li class="dropdown-submenu ">
                                                                                                            @if($sub_menu_item->subMostMenuItems()->count() > 0)
                                                                                                                <?php $show_sub_most_menu_item = []?>

                                                                                                                {{--  Displays the sub menu items with the its sub most menu items--}}
                                                                                                                <a href="{{ $sub_menu_item->sub_menu_item_url  }}" class="nav-link nav-toggle">
                                                                                                                    <i class="{{ $sub_menu_item->sub_menu_item_icon  }}"></i> {{ $sub_menu_item->sub_menu_item  }}
                                                                                                                    <span class="arrow"></span>
                                                                                                                </a>
                                                                                                                    <ul class="dropdown-menu">

                                                                                                                    {{--  Loop Through The Sub Most Menu Items--}}
                                                                                                                    @foreach($sub_menu_item->subMostMenuItems()->orderBy('sequence')->get() as $sub_most_menu_item)
                                                                                                                        {{--  Check if the sub most menu item has been displayed and its enabled--}}
                                                                                                                        @if(!in_array($sub_most_menu_item->sub_most_menu_item_id, $show_sub_most_menu_item) and $sub_most_menu_item->active === 1)

                                                                                                                            {{--Check if the logged in user have access to view the sub most menu item --}}
                                                                                                                            @if(in_array($sub_most_menu_item->sub_most_menu_item_id, $role->subMostMenuItems()->get()->lists('sub_most_menu_item_id')->toArray()))
                                                                                                                                <?php $show_sub_most_menu_item[] = $sub_most_menu_item->sub_most_menu_item_id?>

                                                                                                                                <li class="">
                                                                                                                                    <a href="{{ $sub_most_menu_item->sub_most_menu_item_url  }}" class="nav-link">
                                                                                                                                        <i class="{{ $sub_most_menu_item->sub_most_menu_item_icon  }}"></i>
                                                                                                                                        {{ $sub_most_menu_item->sub_most_menu_item  }}
                                                                                                                                    </a>
                                                                                                                                </li>
                                                                                                                            @endif{{--Check if the logged in user have access to view the sub most menu item --}}
                                                                                                                        @endif{{--  Check if the sub most menu item has been displayed and its enabled--}}
                                                                                                                    @endforeach{{--  Loop Through The Sub Most Menu Items--}}
                                                                                                                </ul>

                                                                                                            @else
                                                                                                                {{--  Displays the sub menu items only--}}
                                                                                                                <a href="{{ $sub_menu_item->sub_menu_item_url  }}" class="nav-link">
                                                                                                                    <i class="{{ $sub_menu_item->sub_menu_item_icon  }}"></i> {{ $sub_menu_item->sub_menu_item  }}
                                                                                                                </a>
                                                                                                            @endif{{-- Check if the sub menu item has sub most menu items--}}
                                                                                                        </li>
                                                                                                    @endif{{--Check if the logged in user have access to view the sub menu item --}}
                                                                                                @endif{{--  Check if the sub menu item has been displayed and its enabled--}}
                                                                                            @endforeach{{--  Loop Through The Sub Menu Items--}}
                                                                                        </ul>
                                                                                    @else
                                                                                        {{--  Displays the menu items only--}}
                                                                                        <a href="{{ $menu_item->menu_item_url  }}">
                                                                                            <i class="{{ $menu_item->menu_item_icon  }}"></i> {{ $menu_item->menu_item  }}
                                                                                        </a>
                                                                                    @endif{{-- Check if the menu item has sub menu items--}}
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
                                        @endif{{-- Check if the menu header has menus--}}
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