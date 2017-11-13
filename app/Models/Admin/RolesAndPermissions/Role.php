<?php

namespace App\Models\Admin\RolesAndPermissions;

use App\Models\Admin\Menus\Menu;
use App\Models\Admin\Users\User;
use App\Models\Admin\Users\UserType;
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
    const DEVELOPER = 'developer';
    const SUPER_ADMIN = 'super_admin';
    const SPONSOR = 'sponsor';
    const STAFF = 'staff';
    const CLASS_TEACHER = 'class_teacher';

    /**
     * A User belongs to a User Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userType(){
        return $this->belongsTo(UserType::class, 'user_type_id');
    }

    /**
     * Get the menus associated with the given role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menus_roles', 'role_id', 'menu_id');
    }

    /**
     * Get the Menu Items associated with the given role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }
}
