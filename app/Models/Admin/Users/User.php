<?php

namespace App\Models\Admin\Users;

use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Accounts\Staff;
use App\Models\Admin\Accounts\Student;
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
     * User Type ID
     */
    const DEVELOPER = 1;
    const SUPER_ADMIN = 2;

    /**
     * Dates To Be Treated As Carbon Instance
     * @var array
     */
    protected $dates = ['dob'];
    
    /**
     * Path to the files
     */
    public $avatar_path = 'uploads/users/';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'phone_no',
        'phone_no2',
        'dob',
        'avatar',
        'lga_id',
        'salutation_id',
        'user_type_id',
        'verification_code',
        'status',
        'verified'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'username', 'status', 'verified', 'verification_code',
    ];

    /**
     * Format The Date of Birth Before Inserting
     * @param $date
     */
    public function setDobAttribute($date)
    {
        $this->attributes['dob'] = ($date) ? Carbon::createFromFormat('Y-m-d', $date) : null;
    }

    /**
     * User Avatar full avatar path
     */
    public function getAvatarPath(){
        return ($this->avatar) ? DIRECTORY_SEPARATOR . $this->avatar_path . $this->avatar : false;
    }

    /**
     * Concatenate the first, last and the other names to get full names
     * @return mixed|string
     */
    public function fullNames()
    {
        return ucwords(strtolower($this->first_name . ' ' . $this->last_name . ' ' . $this->middle_name));
    }

    /**
     * Concatenate the first, last and the other names to get full names and also include the salutation
     * @return mixed|string
     */
    public function fullNamesNSalutation()
    {
        $salutation = ($this->salutation_id) ? $this->salutation()->first()->salutation_abbr : '';
        return $salutation . ' ' .ucwords(strtolower($this->first_name . ' ' . $this->last_name . ' ' . $this->middle_name));
    }

    /**
     * Concatenate the first and last names to get full names
     * @return mixed|string
     */
    public function simpleName()
    {
        return ucwords(strtolower($this->first_name . ' ' . $this->last_name));
    }

    /**
     * Concatenate the first, last and the other names to get full names and also include the salutation
     * @return mixed|string
     */
    public function simpleNameNSalutation()
    {
        $salutation = ($this->salutation_id) ? $this->salutation()->first()->salutation_abbr : '';
        return $salutation . ' ' .ucwords(strtolower($this->first_name . ' ' . $this->last_name));
    }

    /**
     * A Sponsor belongs to a Salutation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salutation(){
        return $this->belongsTo('App\Models\School\Setups\Salutation');
    }

    /**
     * A Sponsor belongs to a Salutation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lga(){
        return $this->belongsTo('App\Models\School\Setups\Lga');
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
     * A Sponsor (User) has many Students
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students(){
        return $this->hasMany('App\Models\Admin\Accounts\Students\Student', 'sponsor_id');
    }

    /**
     * A Staff (User) has many Class Rooms He is Mastering
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classMasters(){
        return $this->hasMany('App\Models\Admin\MasterRecords\Classes\ClassMaster', 'user_id');
    }

    /**
     * Get the roles associated with the given User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\RolesAndPermissions\Role', 'role_user', 'user_id', 'role_id');
    }

    /**
     * A Tutor input assessments scores Through Subjects assigned in a class room for an academic term
     * @return \Illuminate\Database\Eloquent\Relations\hasManyThrough
     */
    public function assessments()
    {
        return $this->hasManyThrough('App\Models\Admin\Assessments\Assessment', 'App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom', 'tutor_id', 'subject_classroom_id');
    }

    /**
     * A Tutor Teaches many subjects in a classroom
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subjectClassRooms(){
        return $this->hasMany('App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom', 'tutor_id');
    }
}
