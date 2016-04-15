<?php

namespace App\Models\Admin\Menus;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'menu_items';

    /**
     * The table Ranks primary key
     *
     * @var int
     */
    protected $primaryKey = 'menu_item_id';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['menu_item', 'menu_item_url', 'menu_item_icon', 'active','type', 'sequence', 'menu_id'];

    /**
     * A Menu Item belongs to a Menu
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu(){
        return $this->belongsTo('App\Models\Admin\Menus\Menu');
    }

    /**
     * A Menu item has many sub Menu Items
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subMenuItems(){
        return $this->hasMany('App\Models\Admin\Menus\SubMenuItem');
    }

    /**
     * Get the roles associated with the given menu item
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\RolesAndPermissions\Role', 'roles_menu_items', 'menu_item_id', 'role_id');
    }
}
