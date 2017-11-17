<?php

namespace App\Models\Admin\Users;

use App\Models\Admin\RolesAndPermissions\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserType extends Model
{
    use SoftDeletes;

    protected $connection = 'admin_mysql';
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_types';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'user_type_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_type','type'
    ];

    /**
     * A User Type has many Users
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function users(){
        return $this->hasMany(User::class, 'user_type_id');
    }

    /**
     * A User Type has many Users
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function roles(){
        return $this->belongsTo(Role::class, 'user_type_id');
    }
}
