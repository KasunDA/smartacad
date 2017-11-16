<?php

namespace App\Models\Admin\Exams;

use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
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
     * An Assessment Detail Belongs To A Student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectClassroom(){
        return $this->belongsTo(SubjectClassRoom::class, 'subject_classroom_id');
    }
}
