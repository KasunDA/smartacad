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

        {{-- Check if the user is logged in--}}
        @if(Auth::check())
            <ul class="page-sidebar-menu   " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                <li class="sidebar-toggler-wrapper hide">
                    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                    <div class="sidebar-toggler"> </div>
                    <!-- END SIDEBAR TOGGLER BUTTON -->
                </li>
                <li class="nav-item start">
                    <a href="{{ url('/dashboard') }}" class="nav-link">
                        <i class="fa fa-dashboard"></i>
                        <span class="title">DASHBOARD</span>
                    </a>
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
                @if(count($active_menus) > 0)
                    @foreach($active_menus as $one)
                        {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                        @if(!in_array($one->menu_id, $displayed_menu) and in_array($one->menu_id, $roles_menu_ids))
                            <?php $displayed_menu[] = $one->menu_id ?>

                            {{--  Displays the Menus Level One--}}
                            <li class="nav-item">
                                <a href="{{ $one->url }}" class="nav-link {{ (count($one->getImmediateDescendants()->where('active', 1)) > 0) ? 'nav-toggle' : '' }}">
                                    <i class="{{ $one->icon }}"></i> <span class="title">{{$one->name}}</span>
                                    @if(count($one->getImmediateDescendants()->where('active', 1)) > 0)
                                        <span class="arrow "></span>
                                    @endif
                                </a>

                                {{-- Check if the menu level one has level two --}}
                                @if(count($one->getImmediateDescendants()->where('active', 1)) > 0)
                                    <ul class="sub-menu">
                                        {{--  Loop Through The Menu Level Two --}}
                                        @foreach($one->getImmediateDescendants()->where('active', 1) as $two)
                                            {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                                            @if(!in_array($two->menu_id, $displayed_menu) and in_array($two->menu_id, $roles_menu_ids))
                                                <?php $displayed_menu[] = $two->menu_id?>

                                                {{--  Displays the menus level two --}}
                                                <li class="nav-item">
                                                    <a href="{{ $two->url }}" class="nav-link {{ (count($two->getImmediateDescendants()->where('active', 1)) > 0) ? 'nav-toggle' : '' }}">
                                                        <i class="{{ $two->icon }}"></i> {{$two->name}}
                                                        @if(count($two->getImmediateDescendants()->where('active', 1)) > 0)
                                                            <span class="arrow "></span>
                                                        @endif
                                                    </a>
                                                    {{-- Check if the menu level two has level three --}}
                                                    @if(count($two->getImmediateDescendants()->where('active', 1)) > 0)
                                                        <ul class="sub-menu">
                                                            {{--  Loop Through The Menu Level Three --}}
                                                            @foreach($two->getImmediateDescendants()->where('active', 1) as $three)
                                                                {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                                                                @if(!in_array($three->menu_id, $displayed_menu) and in_array($three->menu_id, $roles_menu_ids))
                                                                    <?php $displayed_menu[] = $three->menu_id?>

                                                                    {{--  Displays the menus level three --}}
                                                                    <li class="nav-item">
                                                                        <a href="{{ $three->url  }}" class="nav-link {{ (count($three->getImmediateDescendants()->where('active', 1)) > 0) ? 'nav-toggle' : '' }}">
                                                                            <i class="{{ $three->icon  }}"></i> {{ $three->name  }}
                                                                            @if(count($three->getImmediateDescendants()->where('active', 1)) > 0)
                                                                                <span class="arrow "></span>
                                                                            @endif
                                                                        </a>
                                                                        {{-- Check if the menu level three has level four --}}
                                                                        @if(count($three->getImmediateDescendants()->where('active', 1)) > 0)
                                                                            <ul class="sub-menu">
                                                                                {{--  Loop Through The Menu Level Four --}}
                                                                                @foreach($three->getImmediateDescendants()->where('active', 1) as $four)
                                                                                    {{--Check if the logged in user have access to view the menu and Check if the menu has been displayed--}}
                                                                                    @if(!in_array($four->menu_id, $displayed_menu) and in_array($four->menu_id, $roles_menu_ids))
                                                                                        <?php $displayed_menu[] = $four->menu_id?>

                                                                                        {{--  Displays the menus level four --}}
                                                                                        <li class="nav-item">
                                                                                            <a href="{{ $four->url  }}" class="nav-link {{ (count($four->getImmediateDescendants()->where('active', 1)) > 0) ? 'nav-toggle' : '' }}">
                                                                                                <i class="{{ $four->icon  }}"></i> {{ ucwords(strtolower($four->name))  }}
                                                                                                @if(count($four->getImmediateDescendants()->where('active', 1)) > 0)
                                                                                                    <span class="arrow "></span>
                                                                                                @endif
                                                                                            </a>
                                                                                            {{-- Check if the menu level four has level five --}}
                                                                                            @if(count($four->getImmediateDescendants()->where('active', 1)) > 0)
                                                                                                <ul class="sub-menu">
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
                <li class="nav-item">
                    <a href="/users/change">
                        <i class="icon-lock"></i> <span class="title">CHANGE PASSWORD</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/auth/logout') }}" class="nav-link">
                        <i class="fa fa-power-off"></i> <span class="title">LOG OUT</span>
                    </a>
                </li>
            </ul>
        @endif{{-- Check if the user is logged in--}}
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->