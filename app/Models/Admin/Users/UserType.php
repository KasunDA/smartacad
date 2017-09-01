<?php

namespace App\Models\Admin\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserType extends Model
{

    use SoftDeletes;
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
        return $this->hasMany('App\Models\Admin\Users\User');
    }

    /**
     * A User Type has many Users
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function roles(){
        return $this->belongsTo('App\Models\Admin\RolesAndPermissions\Role', 'user_type_id');
    }
}
