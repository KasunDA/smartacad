<?php

namespace App\Models\Admin\Exams;

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
        return $this->belongsTo('App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom', 'subject_classroom_id');
    }
}
