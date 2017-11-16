<?php

namespace App\Models\Admin\Assessments;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\MasterRecords\AcademicTerm;
use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'remarks';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'remark_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['class_teacher', 'principal', 'student_id', 'academic_term_id', 'user_id'];
    
    /**
     * A Remark Belongs To An Academic Term
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicTerm(){
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }

    /**
     * A Remark Belongs To A Student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }
}
