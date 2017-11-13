<?php

namespace App\Models\Admin\Assessments;

use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetupDetail;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        return $this->belongsTo(AssessmentSetupDetail::class, 'assessment_setup_detail_id');
    }

    /**
     * An Assessment Belongs To An Subject Class Room
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectClassroom(){
        return $this->belongsTo(SubjectClassRoom::class, 'subject_classroom_id');
    }

    /**
     * An Assessment Has Many An Assessment Detail
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assessmentDetails(){
        return $this->hasMany(AssessmentDetail::class, 'assessment_id');
    }

    /**
     * Populate Assessment Details // Ready for inputting Scores
     * @param $assessment_id
     */
    public static function populatedAssessmentDetails($assessment_id){
        return DB::statement('call sp_populateAssessmentDetail(' . $assessment_id . ')');
    }

    /**
     * Format Numbers as Position
     * @param $position
     * @return String
     */
    public static function formatPosition($position=0){
        $lastDigit = substr($position, -1, 1);
        $position = intval($position);
        if($lastDigit == 1 && ($position < 10 || $position > 19)) {
            $fomatedPosition = $position . 'st';
        }elseif($lastDigit == 2 && ($position < 10 || $position > 19)) {
            $fomatedPosition = $position . 'nd';
        }elseif($lastDigit == 3 && ($position < 10 || $position > 19)) {
            $fomatedPosition = $position . 'rd';
        }else{
            $fomatedPosition = $position . 'th';
        }
        return $fomatedPosition;
    }
}
