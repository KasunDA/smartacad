<?php

namespace App\Models\Admin\Accounts\Students;

use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
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
        return $this->belongsTo(ClassRoom::class, 'classroom_id');
    }

    /**
     * A Student Class belongs to an academic year
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicYear(){
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * A Student Class Belongs To a Student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }
}
