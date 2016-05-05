<?php

namespace App\Models\Admin\MasterRecords;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
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
     * Get The Current Academic Year
     */
    public static function currentYear(){

        return AcademicYear::where('status', 1)->first();
    }
}
