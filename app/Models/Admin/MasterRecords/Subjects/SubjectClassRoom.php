<?php

namespace App\Models\Admin\MasterRecords\Subjects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SubjectClassRoom extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subject_classrooms';

    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'subject_classroom_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject_id',
        'classroom_id',
        'academic_term_id',
        'exam_status_id',
        'tutor_id',
    ];

    /**
     * A Subject Class Room Belongs To A Class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classRoom(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Classes\ClassRoom', 'classroom_id');
    }

    /**
     * A Subject Class Room belongs to Subjects
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject(){
        return $this->belongsTo('App\Models\School\Setups\Subjects\Subject', 'subject_id');
    }

    /**
     * An Subject Class Room To An Academic Term
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicTerm(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\AcademicTerm', 'academic_term_id');
    }

    /**
     * A Subject Tutor Belongs To A User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tutor(){
        return $this->belongsTo('App\Models\Admin\Users\User', 'tutor_id');
    }

    /**
     * Assign Subjects To Class Room
     */
    public static function assignSubject2Class($class, $term, $subject){
        return DB::statement('call sp_assignSubject2Classrooms(' . $class . ', ' . $term . ', "' . $subject . '")');
    }

    /**
     * Assign Subjects To Class Level
     */
    public static function assignSubject2Level($level, $term, $subject){
        return DB::statement('call sp_assignSubject2Classlevels(' . $level . ', ' . $term . ', "' . $subject . '")');
    }
}
