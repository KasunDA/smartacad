<?php

namespace App\Models\Admin\Users;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use EntrustUserTrait;
    /**
     * The table users primary key
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Dates To Be Treated As Carbon Instance
     * @var array
     */
    protected $dates = ['dob'];

    /**
     * Format The Date of Birth Before Inserting
     * @param $date
     */
    public function setDobAttribute($date)
    {
        $this->attributes['dob'] = ($date) ? Carbon::createFromFormat('Y-m-d', $date) : null;
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'first_name', 'last_name', 'user_type_id', 'verified',
        'status', 'gender', 'phone_no', 'dob', 'avatar', 'password', 'verification_code'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'status', 'verified', 'verification_code',
    ];

    /**
     * Concatenate the first, last and the other names to get full names
     * @return mixed|string
     */
    public function fullNames()
    {
        return ucwords(strtolower($this->first_name . ' ' . $this->last_name));
    }

    /**
     * A User belongs to a User Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userType(){
        return $this->belongsTo('App\Models\Admin\Users\UserType');
    }

    /**
     * Get the roles associated with the given User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\RolesAndPermissions\Role', 'role_user', 'user_id', 'role_id');
    }

}
