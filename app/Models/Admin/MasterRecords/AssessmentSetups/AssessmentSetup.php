<?php

namespace App\Models\Admin\MasterRecords\AssessmentSetups;

use Illuminate\Database\Eloquent\Model;

class AssessmentSetup extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'assessment_setups';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'assessment_setup_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'assessment_no',
        'classgroup_id',
        'academic_term_id'
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
    public function academicTerm(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\AcademicTerm');
    }

    /**
     * An Assessment Setup Belongs To An Academic Terms
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classGroup(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Classes\ClassGroup', 'classgroup_id');
    }

    /**
     * An Assessment Setup Has Many An Assessment setup Detail
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assessmentSetupDetails(){
        return $this->hasMany('App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetupDetail', 'assessment_setup_id');
    }
}
