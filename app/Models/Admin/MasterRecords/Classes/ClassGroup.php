<?php

namespace App\Models\Admin\MasterRecords\Classes;

use App\Models\Admin\Items\ItemQuote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassGroup extends Model
{
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'classgroups';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'classgroup_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'classgroup',
        'ca_weight_point',
        'exam_weight_point',
    ];

    /**
     * A Class Group Has Many Grades
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function grades(){
        return $this->hasMany('App\Models\Admin\MasterRecords\Grade', 'classgroup_id');
    }
    /**
     * A Class Group Has Many Class Level
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function classLevels(){
        return $this->hasMany(ClassLevel::class, 'classgroup_id');
    }

    /**
     * A Class Group Has Many Class Level
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function itemQuotes(){
        return $this->hasMany(ItemQuote::class, 'classgroup_id');
    }

    /**
     * A Class Group Has Many Assessment Setup
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assessmentSetups(){
        return $this->hasMany('App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetup', 'classgroup_id');
    }
}
