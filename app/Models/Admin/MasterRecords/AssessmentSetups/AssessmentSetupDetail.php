<?php

namespace App\Models\Admin\MasterRecords\AssessmentSetups;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AssessmentSetupDetail extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'assessment_setup_details';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'assessment_setup_detail_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number',
        'weight_point',
        'percentage',
        'assessment_setup_id',
        'submission_date',
        'description',
    ];

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;

    /**
     * Dates To Be Treated As Carbon Instance
     * @var array
     */
    protected $dates = ['submission_date'];

    /**
     * An Assessment Setup Detail Belongs To An Assessment Setup
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assessmentSetup(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetup', 'assessment_setup_id');
    }

    /**
     * Format The Date of Birth Before Inserting
     * @param $date
     */
    public function setSubmissionDateAttribute($date)
    {
        $this->attributes['submission_date'] = ($date) ? Carbon::createFromFormat('Y-m-d', $date) : null;
    }
}
