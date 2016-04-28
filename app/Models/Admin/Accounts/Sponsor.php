<?php

namespace App\Models\Admin\Accounts;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    /**
     * The table users primary key
     * @var string
     */
    protected $primaryKey = 'sponsor_id';

    /**
     * User Type ID
    */
    const USER_TYPE = 3;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sponsors';

    /**
     * Dates To Be Treated As Carbon Instance
     * @var array
     */
    protected $dates = ['dob'];

    /**
     * Path to the files
     */
    public $avatar_path = 'uploads/sponsors/';

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
        'sponsor_no', 'email', 'first_name', 'other_name',
        'phone_no', 'phone_no2', 'dob', 'address', 'lga_id', 'salutation_id', 'created_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'sponsor_no', 'created_by',
    ];

    /**
     * Generate Sponsor No
     * @return String
     */
    public function generateNo(){
        return trim('SPN'. str_pad($this->sponsor_id, 5, '0', STR_PAD_LEFT));
    }

    /**
     * Concatenate the first, last and the other names to get full names
     * @return mixed|string
     */
    public function fullNames()
    {
        return ucwords(strtolower($this->first_name . ' ' . $this->other_name));
    }

    /**
     * A Staff belongs to a User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\Models\Admin\Users\User', 'phone_no', 'username');
    }

    /**
     * A Staff was created by a User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(){
        return $this->belongsTo('App\Models\Admin\Users\User', 'created_by', 'user_id');
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
}
