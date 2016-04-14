<?php

namespace App\Models\Admin\RolesAndPermissions;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    /**
     * The table roles primary key
     * @var int
     */
    protected $primaryKey = 'role_id';

    /**
     * Set the default role to 11 i.e OWNER
     */
    const DEFAULT_ROLE = 1;

    /**
     * A User belongs to a User Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userType(){
        return $this->belongsTo('App\Models\Admin\Users\UserType');
    }

    /**
     * Get the menu headers associated with the given role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menuHeaders()
    {
        return $this->belongsToMany('App\Models\Admin\Menus\MenuHeader', 'roles_menu_headers', 'role_id', 'menu_header_id');
    }

    /**
     * Get the menus associated with the given role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menus()
    {
        return $this->belongsToMany('App\Models\Admin\Menus\Menu', 'roles_menus', 'role_id', 'menu_id');
    }

    /**
     * Get the Menu Items associated with the given role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menuItems()
    {
        return $this->belongsToMany('App\Models\Admin\Menus\MenuItem', 'roles_menu_items', 'role_id', 'menu_item_id');
    }

    /**
     * Get the Menu Items associated with the given role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subMenuItems()
    {
        return $this->belongsToMany('App\Models\Admin\Menus\SubMenuItem', 'roles_sub_menu_items', 'role_id', 'sub_menu_item_id');
    }

    /**
     * Get the Menu Items associated with the given role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subMostMenuItems()
    {
        return $this->belongsToMany('App\Models\Admin\Menus\SubMostMenuItem', 'roles_sub_most_menu_items', 'role_id', 'sub_most_menu_item_id');
    }

    /**
     * Get the Menu Items associated with the given role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\Admin\Users\User', 'role_user', 'role_id', 'user_id');
    }
}
