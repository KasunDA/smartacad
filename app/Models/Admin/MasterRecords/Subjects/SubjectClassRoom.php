<?php

namespace App\Models\Admin\MasterRecords\Subjects;

use App\Models\Admin\Accounts\Students\StudentSubject;
use App\Models\Admin\Assessments\Assessment;
use App\Models\Admin\Exams\Exam;
use App\Models\Admin\Exams\ExamDetail;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Users\User;
use App\Models\School\Setups\Subjects\Subject;
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
        return $this->belongsTo(ClassRoom::class, 'classroom_id');
    }

    /**
     * A Subject Class Room belongs to Subjects
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject(){
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * An Subject Class Room To An Academic Term
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicTerm(){
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }

    /**
     * A Subject Tutor Belongs To A User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tutor(){
        return $this->belongsTo(User::class, 'tutor_id', 'user_id');
    }

    /**
     * A Subject Class Room has many student subjects
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentSubjects(){
        return $this->hasMany(StudentSubject::class, 'subject_classroom_id');
    }

    /**
     * A Subject Class Room has many Assessment
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assessments(){
        return $this->hasMany(Assessment::class, 'subject_classroom_id');
    }

    /**
     * A Subject Class Room has many Exams
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exams(){
        return $this->hasMany(Exam::class, 'subject_classroom_id');
    }

    /**
     * A Subject Class Room has many Exams Details Through Exams
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examDetails()
    {
        return $this->hasManyThrough(ExamDetail::class, Exam::class, 'subject_classroom_id', 'exam_id');
    }

    /**
     * Assign Subjects To Class Room
     * * @param Int $class
     * * @param Int $term
     * * @param String $subject
     */
    public static function assignSubject2Class($class, $term, $subject){
        return DB::statement('call sp_subject2Classrooms(' . $class . ', ' . $term . ', "' . $subject . '")');
    }

    /**
     * Assign Subjects To Class Level
     * * @param Int $level
     * * @param Int $term
     * * @param String $subject
     */
    public static function assignSubject2Level($level, $term, $subject){
        return DB::statement('call sp_subject2Classlevels(' . $level . ', ' . $term . ', "' . $subject . '")');

    }

    /**
     * Delete Subjects Assigned To Class Room
     */
//    public function deleteSubjectClassRoom(){
//        return DB::statement('call sp_deleteSubjectClassRoom(' . $this->subject_classroom_id . ')');
//    }

    /**
     * Update Subjects Students Registered Table with the list of students
     * @param String $student_ids
     */
    public function modifyStudentsSubject($student_ids) {
        return DB::statement('call sp_modifyStudentsSubject(' . $this->subject_classroom_id . ', "'. $student_ids . '")');
    }
}
