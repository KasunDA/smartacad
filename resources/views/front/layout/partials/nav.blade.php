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

        @if(Auth::check())
            <div class="hor-menu">
                <ul class="nav navbar-nav page-sidebar-menu">
                    <li class="menu-dropdown classic-menu-dropdown active">
                        <a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i>  HOME</a>
                    </li>
                    <li class="menu-dropdown classic-menu-dropdown">
                        <a href="{{ url('/wards') }}"><i class="fa fa-users"></i>  STUDENT(S)</a>
                    </li>
                    <?php
                        $roles_menu_ids = [];
                        $displayed_menu = [];
                        //Loop Through The Users Roles
                        foreach(Auth::user()->roles()->get() as $role){
                            $roles_menu_ids = array_merge($roles_menu_ids, $role->menus()->get()->pluck('menu_id')->toArray());
                        }
                        $roles_menu_ids = array_unique($roles_menu_ids);
                    ?>
                    {{--Loop Through The Menu Level One --}}
                    @if(count($active_home_menu) > 0)
                        @foreach($active_home_menu as $one)
                            {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                            @if(!in_array($one->menu_id, $displayed_menu) and in_array($one->menu_id, $roles_menu_ids))
                                <?php $displayed_menu[] = $one->menu_id ?>

                                {{--  Displays the Menus Level One--}}
                                <li class="menu-dropdown classic-menu-dropdown ">
                                    <a href="{{ $one->url }}" class="nav-link {{ (count($one->getImmediateDescendants()->where('active', 1)) > 0) ? 'nav-toggle' : '' }}">
                                        <i class="{{ $one->icon }}"></i> <span class="title">{{$one->name}}</span>
                                        @if(count($one->getImmediateDescendants()->where('active', 1)) > 0)
                                            <span class="arrow "></span>
                                        @endif
                                    </a>

                                    {{-- Check if the menu level one has level two --}}
                                    @if(count($one->getImmediateDescendants()->where('active', 1)) > 0)
                                        <ul class="dropdown-menu pull-left">
                                            {{--  Loop Through The Menu Level Two --}}
                                            @foreach($one->getImmediateDescendants()->where('active', 1) as $two)
                                                {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                                                @if(!in_array($two->menu_id, $displayed_menu) and in_array($two->menu_id, $roles_menu_ids))
                                                    <?php $displayed_menu[] = $two->menu_id?>

                                                    {{--  Displays the menus level two --}}
                                                    <li class="dropdown-submenu classic-menu-dropdown">
                                                        <a href="{{ $two->url }}" class="nav-link {{ (count($two->getImmediateDescendants()->where('active', 1)) > 0) ? 'nav-toggle' : '' }}">
                                                            <i class="{{ $two->icon }}"></i> {{$two->name}}
                                                            @if(count($two->getImmediateDescendants()->where('active', 1)) > 0)
                                                                <span class="arrow "></span>
                                                            @endif
                                                        </a>
                                                        {{-- Check if the menu level two has level three --}}
                                                        @if(count($two->getImmediateDescendants()->where('active', 1)) > 0)
                                                            <ul class="dropdown-menu pull-left">
                                                                {{--  Loop Through The Menu Level Three --}}
                                                                @foreach($two->getImmediateDescendants()->where('active', 1) as $three)
                                                                    {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                                                                    @if(!in_array($three->menu_id, $displayed_menu) and in_array($three->menu_id, $roles_menu_ids))
                                                                        <?php $displayed_menu[] = $three->menu_id?>

                                                                        {{--  Displays the menus level three --}}
                                                                        <li class="dropdown-submenu classic-menu-dropdown">
                                                                            <a href="{{ $three->url  }}" class="nav-link {{ (count($three->getImmediateDescendants()->where('active', 1)) > 0) ? 'nav-toggle' : '' }}">
                                                                                <i class="{{ $three->icon  }}"></i> {{ $three->name  }}
                                                                                @if(count($three->getImmediateDescendants()->where('active', 1)) > 0)
                                                                                    <span class="arrow "></span>
                                                                                @endif
                                                                            </a>
                                                                            {{-- Check if the menu level three has level four --}}
                                                                            @if(count($three->getImmediateDescendants()->where('active', 1)) > 0)
                                                                                <ul class="dropdown-menu pull-left">
                                                                                    {{--  Loop Through The Menu Level Four --}}
                                                                                    @foreach($three->getImmediateDescendants()->where('active', 1) as $four)
                                                                                        {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                                                                                        @if(!in_array($four->menu_id, $displayed_menu) and in_array($four->menu_id, $roles_menu_ids))
                                                                                            <?php $displayed_menu[] = $four->menu_id?>

                                                                                            {{--  Displays the menus level four --}}
                                                                                            <li class="dropdown-submenu classic-menu-dropdown">
                                                                                                <a href="{{ $four->url  }}" class="nav-link {{ (count($four->getImmediateDescendants()->where('active', 1)) > 0) ? 'nav-toggle' : '' }}">
                                                                                                    <i class="{{ $four->icon  }}"></i> {{ ucwords(strtolower($four->name))  }}
                                                                                                    @if(count($four->getImmediateDescendants()->where('active', 1)) > 0)
                                                                                                        <span class="arrow "></span>
                                                                                                    @endif
                                                                                                </a>
                                                                                                {{-- Check if the menu level four has level five --}}
                                                                                                @if(count($four->getImmediateDescendants()->where('active', 1)) > 0)
                                                                                                    <ul class="dropdown-menu pull-left">
                                                                                                        {{--  Loop Through The Menu Level Five --}}
                                                                                                        @foreach($four->getImmediateDescendants()->where('active', 1) as $five)
                                                                                                            {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                                                                                                            @if(!in_array($five->menu_id, $displayed_menu) and in_array($five->menu_id, $roles_menu_ids))
                                                                                                                <?php $displayed_menu[] = $five->menu_id?>

                                                                                                                {{--  Displays the menus level Five --}}
                                                                                                                <li class="nav-item">
                                                                                                                    <a href="{{ $five->url  }}" class="nav-link">
                                                                                                                        <i class="{{ $five->icon  }}"></i> {{ ucwords(strtolower($five->name))  }}
                                                                                                                    </a>
                                                                                                                </li>{{--  Displays the menus level Five --}}
                                                                                                            @endif {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                                                                                                        @endforeach {{--  Loop Through The Menu Level Five --}}
                                                                                                    </ul>
                                                                                                @endif {{-- Check if the menu level four has level five --}}
                                                                                            </li>{{--  Displays the menus level four --}}
                                                                                        @endif {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                                                                                    @endforeach {{--  Loop Through The Menu Level Four --}}
                                                                                </ul>
                                                                            @endif {{-- Check if the menu level three has level four --}}
                                                                        </li> {{--  Displays the menus level three --}}
                                                                    @endif {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                                                                @endforeach {{--  Loop Through The Menu Level Three --}}
                                                            </ul>
                                                        @endif {{-- Check if the menu level two has level three --}}
                                                    </li>   {{--  Displays the menus level two --}}
                                                @endif {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                                            @endforeach {{--  Loop Through The Menu Level Two --}}
                                        </ul>
                                    @endif {{-- Check if the menu level one has level two --}}
                                </li>{{--  Displays the Menus Level One--}}
                            @endif {{--  Check if the menu level one has been displayed--}}
                        @endforeach  {{--Loop Through The Menu Level One --}}
                    @endif
                </ul>
            </div>
        @endif
        <!-- END MEGA MENU -->
    </div>
</div>
<!-- END HEADER MENU -->