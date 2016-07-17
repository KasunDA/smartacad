<?php

namespace App\Models\Admin\MasterRecords\Subjects;

use Illuminate\Database\Eloquent\Model;

class SubjectAssessmentView extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subjects_assessmentsviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject_id',
        'classroom_id',
        'subject_classroom_id'
    ];

    /**
     * A SchoolSubject Belongs To A School
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function classroom(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Classes\ClassRoom', 'classroom_id');
    }


    /**
     * A SchoolSubject Has Many Subject
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function subject(){
    return $this->belongsTo('App\Models\School\Setups\Subjects\Subject', 'subject_id');
    }

    /**
     * A SchoolSubject Has Many Subject
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function subjectClassRoom(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom', 'subject_classroom_id');
    }

    /**
     * A Subject Tutor Belongs To A User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tutor(){
        return $this->belongsTo('App\Models\Admin\Users\User', 'tutor_id', 'user_id');
    }
}
