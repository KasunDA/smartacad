<?php

namespace App\Models\Admin\RolesAndPermissions;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    protected $connection = 'admin_mysql';
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'permission_id';

    /**
     * A Permission belongs to 1 or many Roles
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }

}
