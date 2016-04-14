<?php

namespace App\Models\Admin\Menus;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'menus';

    /**
     * The table Menus primary key
     *
     * @var int
     */
    protected $primaryKey = 'menu_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['menu', 'menu_url','icon', 'active', 'sequence','menu_header_id'];

    /**
     * A Menu Item belongs to a Menu
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menuHeader(){
        return $this->belongsTo('App\Models\Admin\Menus\MenuHeader');
    }

    /**
     * A Menu header has many Menus
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function menuItems(){
        return $this->hasMany('App\Models\Admin\Menus\MenuItem');
    }

    /**
     * Get the roles associated with the given menu
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\RolesAndPermissions\Role', 'roles_menus', 'menu_id', 'role_id');
    }
}
