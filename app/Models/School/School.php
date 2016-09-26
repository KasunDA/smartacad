<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $connection = 'admin_mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'schools';

    /**
     * The table Menus primary key
     *
     * @var int
     */
    protected $primaryKey = 'school_id';

    /**
     * Path to the files
     */
    public $logo_path = 'uploads/schools/logos/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'full_name',
        'phone_no',
        'phone_no2',
        'email',
        'motto',
        'website',
        'address',
        'logo',
        'setup',
        'admin_id',
        'status_id',
    ];

    const ACADEMIC_YEAR = 1;
    const ACADEMIC_TERM = 2;
    const CLASS_GROUP = 3;
    const CLASS_LEVEL = 4;
    const CLASS_ROOM = 5;
    const SUBJECT = 6;
    const ASSESSMENT = 7;
    const ASSESSMENT_DETAIL = 8;
    const GRADE = 9;
    const COMPLETED = 10;

    /**
     * get the school information
    */
    public static function mySchool(){
        //Set The School Info. into a variable school
        $school = null;
        if(env('SCHOOL_ID')){
            $school = School::findOrFail(env('SCHOOL_ID'));
        }
        return $school;
    }

    /**
     * School Logo full path
     */
    public function getLogoPath(){
        return ($this->logo) ? DIRECTORY_SEPARATOR . $this->logo_path . $this->logo : false;
    }

    /**
     * A School has one Database
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function database(){
        return $this->hasOne('App\Models\School\SchoolDatabase', 'school_id');
    }

    /**
     * Get the subjects associated with the given school
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subjects()
    {
        return $this->belongsToMany('App\Models\School\Setups\Subjects\Subject', 'schools_subjects', 'school_id', 'subject_id')->withPivot('subject_alias');
    }
}
