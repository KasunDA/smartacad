<?php

namespace App\Models\Admin\Exams;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Exam extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exams';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'exam_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject_classroom_id',
        'marked'
    ];

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;

    /**
     * An Assessment Belongs To An Subject Class Room
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectClassroom(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom', 'subject_classroom_id');
    }

    /**
     * An Exam Has Many An Exam Detail
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examDetails(){
        return $this->hasMany('App\Models\Admin\Exams\ExamDetail', 'exam_id');
    }

    /**
     * An Exam was written by Many Students Through Exam Details
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasManyThrough('App\Models\Admin\Accounts\Students\Student', 'App\Models\Admin\Exams\ExamDetail', 'exam_id', 'student_id');
    }

    /**
     * Populate Assessment Details // Ready for inputting Scores
     * @param $term_id
     */
    public static function processExam($term_id){
        return DB::statement('call sp_processExams(' . $term_id . ')');
    }
}
