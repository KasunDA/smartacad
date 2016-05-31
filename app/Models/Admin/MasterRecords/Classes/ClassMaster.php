<?php

namespace App\Models\Admin\MasterRecords\Classes;

use Illuminate\Database\Eloquent\Model;

class ClassMaster extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'class_masters';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'class_master_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'classroom_id',
        'academic_year_id',
        'user_id',
    ];

    /**
     * A Class Master belongs to a Class Room
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classRoom(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Classes\ClassRoom', 'classroom_id');
    }

    /**
     * A Class Master belongs to an academic year
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicYear(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\AcademicYear');
    }

    /**
     * A Class Master belongs to an academic year
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\Models\Admin\Users\User');
    }
}
