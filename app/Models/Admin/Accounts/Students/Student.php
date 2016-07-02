<?php

namespace App\Models\Admin\Accounts\Students;

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
     * Student Status
     */
    const ACTIVE = 1;

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
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'student_no',
        'gender',
        'dob',
        'avatar',
        'sponsor_id',
        'classroom_id',
        'lga_id',
        'admitted_term_id',
        'status_id',
        'address',
        'created_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'admitted_term_id', 'status_id', 'created_by', 'student_no'
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
     * Concatenate the first and last names to get full names
     * @return mixed|string
     */
    public function simpleName()
    {
        return ucwords(strtolower($this->first_name . ' ' . $this->last_name));
    }

    /**
     * Get The Student's Current Class Room
     * @param $year
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentClass($year){
        $class = StudentClass::where('academic_year_id', $year)->where('student_id', $this->student_id)->first();
        return ($class) ? $class->classRoom()->first() : null;
    }

    /**
     * A Student belongs to a Class Room
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classRoom(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Classes\ClassRoom', 'classroom_id');
    }

    /**
     * A Student belongs to a status
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(){
        return $this->belongsTo('App\Models\School\Setups\Status');
    }

    /**
     * A Student belongs to Sponsor
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sponsor(){
        return $this->belongsTo('App\Models\Admin\Users\User', 'sponsor_id');
    }

    /**
     * A Student belongs to an admitted term
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function termAdmitted(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\AcademicTerm', 'admitted_term_id');
    }

    /**
     * A Student was created by a User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(){
        return $this->belongsTo('App\Models\Admin\Users\User', 'created_by');
    }

    /**
     * A Student belongs to a LGA
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lga(){
        return $this->belongsTo('App\Models\School\Setups\Lga');
    }

    /**
     * A Student has many classes
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentClass(){
        return $this->hasMany('App\Models\Admin\Accounts\Students\StudentClass');
    }

    /**
     * A Student registers many subjects
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentSubjects(){
        return $this->hasMany('App\Models\Admin\MasterRecords\Accounts\Students\StudentSubject');
    }

    /**
     * A Student registers many subjects in a class room for an academic term
     * @return \Illuminate\Database\Eloquent\Relations\hasManyThrough
     */
    public function subjectClassRooms(){
        return $this->hasManyThrough('App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom', 'App\Models\Admin\MasterRecords\Accounts\Students\StudentSubject', 'student_id', 'subject_classroom_id');
    }

    /**
     * A Student Has Many Assessment Detail
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assessmentDetails(){
        return $this->hasMany('App\Models\Admin\MasterRecords\AssessmentDetail', 'student_id');
    }

    /**
     * A Student Has Many Exam Detail
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examDetails(){
        return $this->hasMany('App\Models\Admin\Exams\ExamDetail', 'student_id');
    }

}
