<?php

namespace App\Models\Admin\Assessments;

use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use Illuminate\Database\Eloquent\Model;

class AssessmentDetailView extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'assessment_detailsviews';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'assessment_detail_id';

    /**
     * An Assessment Detail Belongs To A Student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectClassroom(){
        return $this->belongsTo(SubjectClassRoom::class, 'subject_classroom_id');
    }
}
