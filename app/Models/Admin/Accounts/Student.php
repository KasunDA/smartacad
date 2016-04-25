<?php

namespace App\Models\Admin\Accounts;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /**
     * The table users primary key
     * @var string
     */
    protected $primaryKey = 'student_id';

    /**
     * User Type ID
     */
    const USER_TYPE = 5;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'students';

    /**
     * Dates To Be Treated As Carbon Instance
     * @var array
     */
    protected $dates = ['dob'];

    /**
     * Path to the files
     */
    public $avatar_path = 'uploads/students/';

    /**
     * Format The Date of Birth Before Inserting
     * @param $date
     */
    public function setDobAttribute($date)
    {
        $this->attributes['dob'] = ($date) ? Carbon::createFromFormat('Y-m-d', $date) : null;
    }

}
