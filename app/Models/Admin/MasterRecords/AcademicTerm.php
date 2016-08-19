<?php

namespace App\Models\Admin\MasterRecords;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AcademicTerm extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'academic_terms';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'academic_term_id';

    /**
     * Dates To Be Treated As Carbon Instance
     * @var array
     */
    protected $dates = ['term_begins', 'term_ends'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'academic_term',
        'status',
        'academic_year_id',
        'term_type_id',
        'term_begins',
        'term_ends',
//        'exam_status_id',
//        'exam_setup_by',
//        'exam_setup_date',
    ];

    /**
     * An Academic Term Belongs To An Academic Year
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function academicYear(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\AcademicYear');
    }

    /**
     * An Academic Term was created by A User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
//    public function examSetupBy(){
//        return $this->belongsTo('App\Models\Admin\Users\User', 'exam_setup_by', 'user_id');
//    }

    /**
     * An Academic Term Has Many Subject Class Room
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function subjectClassRooms(){
        return $this->hasMany('App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom', 'academic_term_id');
    }

    /**
     * An Academic Term Has Many Remarks
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function remarks(){
        return $this->hasMany('App\Models\Admin\Assessments\Remark', 'academic_term_id');
    }

    /**
     * Get The Current Active Academic Term
     */
    public static function activeTerm(){

        return AcademicTerm::where('status', 1)->first();
    }

    /**
     * Clone Subjects assigned Records
     * * @param Int $from
     * * @param Int $to
     */
    public static function cloneSubjectAssigned($from, $to){
        return DB::statement('call sp_cloneSubjectsAssigned(' . $from . ', "' . $to . '")');
    }

    /**
     * get the next academic term
     * @return null
     */
    public function nextAcademicTerm(){
        $year = $this->academicYear()->first();
        
        if($this->term_type_id == 1) {
            //if its first term then get second term
            return AcademicTerm::where('academic_year_id', $year->academic_year_id)->where('term_type_id', 2)->first();
        }elseif($this->term_type_id == 2) {
            //if its second term then get third term
            return AcademicTerm::where('academic_year_id', $year->academic_year_id)->where('term_type_id', 3)->first();
        }elseif($this->term_type_id == 3 && $year->nextAcademicYear()) {
            //if its second term then get third term
            return AcademicTerm::where('academic_year_id', $year->nextAcademicYear()->academic_year_id)->where('term_type_id', 1)->first();
        }
        return null;
    }
}
