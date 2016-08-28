<?php

namespace App\Models\Admin\Menus;

use Illuminate\Database\Eloquent\Model;

class MenuHeader extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'menu_headers';

    /**
     * The table Menu Header primary key
     *
     * @var int
     */
    protected $primaryKey = 'menu_header_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['menu_header', 'active', 'icon', 'type','sequence'];

    /**
     * A Menu header has many Menus
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function menus(){
        return $this->hasMany('App\Models\Admin\Menus\Menu');
    }

    /**
     * Get the roles associated with the given menu
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\RolesAndPermissions\Role', 'roles_menu_headers', 'menu_header_id', 'role_id');
    }
}
