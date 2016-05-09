<?php

namespace App\Models\Admin\MasterRecords\Classes;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'classrooms';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'classroom_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'classroom',
        'class_size',
        'class_status',
        'classlevel_id',
    ];

    /**
     * A Class Room Belongs To Class Level
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function classLevel(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Classes\ClassLevel', 'classlevel_id');
    }
}
