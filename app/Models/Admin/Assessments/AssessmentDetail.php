<?php

namespace App\Models\Admin\Assessments;

use Illuminate\Database\Eloquent\Model;

class AssessmentDetail extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'assessment_details';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'assessment_detail_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'score',
        'assessment_id'
    ];

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;

    /**
     * An Assessment Detail Belongs To A Student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(){
        return $this->belongsTo('App\Models\Admin\Accounts\Students\Student', 'student_id');
    }

    /**
     * An Assessment Detail Belongs To An Assessment
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assessment(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Assessment', 'assessment_id');
    }
}
