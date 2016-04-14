@foreach($menu->menuItems()->get() as $menu_item)
    @if($menu_item->subMenuItems()->count() > 0)
        <li class="nav-item">
            <a href="{{ $menu_item->menu_item_url  }}" class="nav-link nav-toggle">
                <i class="{{ $menu_item->menu_item_icon  }}"></i> {{ $menu_item->menu_item  }}
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                @foreach($menu_item->subMenuItems()->get() as $sub_menu_item)
                    @if($sub_menu_item->subMostMenuItems()->count() > 0)
                        <li class="nav-item">
                            <a href="{{ $sub_menu_item->sub_menu_item_url  }}" class="nav-link nav-toggle">
                                <i class="{{ $sub_menu_item->sub_menu_item_icon  }}"></i> {{ $sub_menu_item->sub_menu_item  }}
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                @foreach($sub_menu_item->subMostMenuItems()->get() as $sub_most_menu_item)
                                    <li class="nav-item">
                                        <a href="{{ $sub_most_menu_item->sub_most_menu_item_url  }}" class="nav-link">
                                            <i class="{{ $sub_most_menu_item->sub_most_menu_item_icon  }}"></i>
                                            {{ $sub_most_menu_item->sub_most_menu_item  }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ $sub_menu_item->sub_menu_item_url  }}" class="nav-link nav-toggle">
                                <i class="{{ $sub_menu_item->sub_menu_item_icon  }}"></i>
                                {{ $sub_menu_item->sub_menu_item  }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </li>
    @else
        <li class="nav-item">
            <a href="{{ $menu_item->menu_item_url  }}" class="nav-link nav-toggle">
                <i class="{{ $menu_item->menu_item_icon  }}"></i> {{ $menu_item->menu_item  }}
            </a>
        </li>
    @endif
@endforeach