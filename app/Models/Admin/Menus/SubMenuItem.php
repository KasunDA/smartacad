<?php

namespace App\Models\Admin\Menus;

use Illuminate\Database\Eloquent\Model;

class SubMenuItem extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sub_menu_items';

    /**
     * The table Ranks primary key
     *
     * @var int
     */
    protected $primaryKey = 'sub_menu_item_id';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['sub_menu_item', 'sub_menu_item_url', 'sub_menu_item_icon', 'active','type', 'sequence', 'menu_item_id'];

    /**
     * A Menu Item belongs to a Menu
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menuItem(){
        return $this->belongsTo('App\Models\Admin\Menus\SubMenuItem');
    }

    /**
     * A Sub most Menu item has many sub Menu Items
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subMostMenuItems(){
        return $this->hasMany('App\Models\Admin\Menus\SubMostMenuItem');
    }

    /**
     * Get the roles associated with the given sub menu item
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\RolesAndPermissions\Role', 'roles_sub_menu_items', 'sub_menu_item_id', 'role_id');
    }
}
