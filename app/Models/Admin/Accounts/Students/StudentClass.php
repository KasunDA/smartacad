<?php

namespace App\Models\Admin\Accounts\Students;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    /**
     * The table users primary key
     * @var string
     */
    protected $primaryKey = 'student_class_id';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'student_classes';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'student_id',
        'classroom_id',
        'academic_year_id',
    ];

    /**
     * A Student Class belongs to a Class Room
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classRoom(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Classes\ClassRoom', 'classroom_id');
    }

    /**
     * A Student Class belongs to an academic year
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicYear(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\AcademicYear');
    }

    /**
     * A Student Class Belongs To a Student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(){
        return $this->belongsTo('App\Models\Admin\Accounts\Students\Student', 'student_id');
    }
}
