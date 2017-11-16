<?php

namespace App\Models\Admin\Accounts\Students;

use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use Illuminate\Database\Eloquent\Model;

class StudentSubject extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'student_subjects';

    /**
     * The table users primary key
     * @var string
     */
    protected $primaryKey = 'subject_classroom_id';


    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'student_id',
        'subject_classroom_id',
    ];

    /**
     * A Student Subject belongs to a Subject Class Room
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectClassRoom(){
        return $this->belongsTo(SubjectClassRoom::class, 'subject_classroom_id');
    }

    /**
     * A Student Subject Belongs To a Student
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }
}
