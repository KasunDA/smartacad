<?php

namespace App\Models\Admin\Users;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
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
        'user_type'
    ];

    /**
     * Parent User Type ID
    */
    const PARENT = 3;

    /**
     * A User Type has many Users
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function users(){
        return $this->hasMany('App\Models\Admin\Users\User');
    }
}
