<?php

namespace App\Models\Admin\MasterRecords\Classes;

use App\Models\Admin\Attendances\Attendance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRoom extends Model
{
    use SoftDeletes;
    
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

    /**
     * A Class Room Has Many Subject Class Room
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function subjectClassRooms(){
        return $this->hasMany('App\Models\Admin\Subjects\SubjectClassRoom', 'subject_classroom_id');
    }

    /**
     * A Class Room has many Students
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentClasses(){
        return $this->hasMany('App\Models\Admin\Accounts\Students\StudentClass', 'classroom_id');
    }

    /**
     * A Class Room has many Class Masters
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classMasters(){
        return $this->hasMany('App\Models\Admin\MasterRecords\Classes\ClassMaster', 'classroom_id');
    }

    /**
     * A Class Room has many Attendance
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances(){
        return $this->hasMany(Attendance::class, 'classroom_id');
    }
}
