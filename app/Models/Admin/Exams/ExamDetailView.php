<?php

namespace App\Models\Admin\Exams;

use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use App\Models\School\Setups\Subjects\Subject;
use Illuminate\Database\Eloquent\Model;

class ExamDetailView extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exams_detailsviews';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'exam_detail_id';

    /**
     * Marked Exams
     *
     * @param $query
     */
    public function scopeMarked($query){
        return $query->where('marked', 1);
    }

    /**
     * UnMarked Exams
     *
     * @param $query
     */
    public function scopeUnMarked($query){
        return $query->where('marked', '<>', 1);
    }

    /**
     * An exam belongs to a subject
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function subject(){
        return $this->belongsTo(Subject::class, 'subject_id');
    }
//SELECT a.student_id, a.fullname, a.ca, a.exam, a.student_total, a.subject_id, b.subject,
//a.academic_term, a.academic_term_id, a.academic_year_id
//FROM smartacad.exams_detailsviews a, smartschools.subjects b
//WHERE a.subject_id = b.subject_id and a.academic_year_id = 2
//group by student_id, subject_id, academic_term_id, academic_term;

    /**
     * An Assessment Detail Belongs To A Student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectClassroom(){
        return $this->belongsTo(SubjectClassRoom::class, 'subject_classroom_id');
    }
}
