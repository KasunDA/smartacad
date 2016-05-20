<?php

namespace App\Models\Admin\Assessments;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'assessments';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'assessment_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject_classroom_id',
        'assessment_setup_detail_id',
        'marked'
    ];

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;

    /**
     * An Assessment Setup Belongs To An Academic Terms
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assessmentSetupDetail(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetupDetail', 'assessment_setup_detail_id');
    }

    /**
     * An Assessment Belongs To An Subject Class Room
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectClassroom(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Classes\ClassRoom', 'classroom_id');
    }

    /**
     * An Assessment Has Many An Assessment Detail
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assessmentDetails(){
        return $this->hasMany('App\Models\Admin\MasterRecords\AssessmentDetail', 'assessment_id');
    }
}
