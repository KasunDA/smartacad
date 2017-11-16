<?php

namespace App\Models\Admin\Exams;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;

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
        return $this->belongsTo(SubjectClassRoom::class, 'subject_classroom_id');
    }

    /**
     * An Exam Has Many An Exam Detail
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examDetails(){
        return $this->hasMany(ExamDetail::class, 'exam_id');
    }

    /**
     * An Exam was written by Many Students Through Exam Details
     * @return \Illuminate\Database\Eloquent\Relations\hasManyThrough
     */
    public function students()
    {
        return $this->hasManyThrough(Student::class, ExamDetail::class, 'exam_id', 'student_id');
    }

    /**
     * Setup Exams To Populate Assessment Details // Ready for inputting Scores
     * @param $term_id
     * @param $tutor_id
     */
    public static function processExams($term_id, $tutor_id='null'){
        return DB::statement('call sp_processExams(' . $term_id . ', '.$tutor_id.')');
    }

    /**
     * Compute and Populate Assessment CA into Exams
     * @param $term_id
     * @param $tutor_id
     */
    public static function processAssessmentCA($term_id, $tutor_id){
        return DB::statement('call sp_processAssessmentCA(' . $term_id . ', '.$tutor_id.')');
    }

    /**
     * Setup My Exams To Populate Assessment Details // Ready for inputting Scores
     * @param $term_id
     * @param $tutor_id
     */
//    public static function processMyExams($term_id, $tutor_id){
//        return DB::statement('call sp_processMyExams(' . $term_id . ', ' . $tutor_id . ')');
//    }

    /**
     * Calculate the terminal class positions
     * @param $term_id
     * @param $classroom_id
     * @param $student_id
     */
    public static function terminalClassPosition($term_id, $classroom_id, $student_id=0){
        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        return DB::select('call sp_terminalClassPosition(?,?,?)', array($term_id, $classroom_id, $student_id));
    }
}
