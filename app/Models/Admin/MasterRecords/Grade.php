<?php

namespace App\Models\Admin\MasterRecords;

use App\Models\Admin\MasterRecords\Classes\ClassGroup;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'grades';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'grade_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'grade',
        'grade_abbr',
        'upper_bound',
        'lower_bound',
        'classgroup_id',
    ];

    /**
     * A Grade Belongs To a CLass Group
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function classGroup(){
        return $this->belongsTo(ClassGroup::class, 'classgroup_id');
    }
}
