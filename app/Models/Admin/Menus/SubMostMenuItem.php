<?php

namespace App\Models\Admin\Menus;

use Illuminate\Database\Eloquent\Model;

class SubMostMenuItem extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sub_most_menu_items';

    /**
     * The table Ranks primary key
     *
     * @var int
     */
    protected $primaryKey = 'sub_most_menu_item_id';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['sub_most_menu_item', 'sub_most_menu_item_url', 'sub_most_menu_item_icon', 'active', 'type','sequence', 'sub_menu_item_id'];

    /**
     * A Menu Item belongs to a Menu
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subMenuItem(){
        return $this->belongsTo('App\Models\Admin\Menus\SubMenuItem');
    }

    /**
     * Get the roles associated with the given sub menu item
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\RolesAndPermissions\Role', 'roles_sub_most_menu_items', 'sub_most_menu_item_id', 'role_id');
    }
}
