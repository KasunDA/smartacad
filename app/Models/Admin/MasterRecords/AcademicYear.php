<?php

namespace App\Models\Admin\MasterRecords;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'academic_years';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'academic_year_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'academic_year',
    ];

    /**
     * An Academic Year Has Many Academic Terms
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function academicTerms(){
        return $this->hasMany('App\Models\Admin\MasterRecords\AcademicTerm');
    }

    /**
     * Get The Current Active Academic Year
     */
    public static function activeYear(){

        return AcademicYear::where('status', 1)->first();
    }

    /**
     * Get the next academic year
     * @return null
     */
    public function nextAcademicYear(){
        if(isset($this->academic_year_id)) {
            $year = substr($this->academic_year, -4);
            $results = AcademicYear::all();
            foreach($results as $result){
                $next_year = substr($result->academic_year, -4);
                if((intval($year) + 1) === intval($next_year)){
                    return $result;
                    break;
                }
            }
        }
        return null;
    }
}
