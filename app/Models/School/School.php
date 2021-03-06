<?php

namespace App\Models\School;

use App\Models\School\Banks\SchoolBank;
use App\Models\School\Setups\Subjects\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;
    
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
        return ($this->logo) ? DIRECTORY_SEPARATOR . $this->logo_path . $this->logo : 'assets/pages/img/logo.png';
    }

    /**
     * A School has one Database
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function database(){
        return $this->hasOne(SchoolDatabase::class, 'school_id');
    }

    /**
     * Get the subjects associated with the given school
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'schools_subjects', 'school_id', 'subject_id')
            ->withPivot('subject_alias');
    }

    /**
     * A School has many Schools Bank
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schoolBanks(){
        return $this->hasMany(SchoolBank::class, 'school_id');
    }
}
