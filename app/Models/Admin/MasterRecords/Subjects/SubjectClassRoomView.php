<?php

namespace App\Models\Admin\MasterRecords\Subjects;

use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Users\User;
use App\Models\School\Setups\Subjects\Subject;
use Illuminate\Database\Eloquent\Model;

class SubjectClassRoomView extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subjects_classroomviews';

    /**
     * A SchoolSubject Belongs To A School
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function classroom(){
        return $this->belongsTo(ClassRoom::class, 'classroom_id');
    }


    /**
     * A SchoolSubject Has Many Subject
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function subject(){
        return $this->belongsTo(Subject::class, 'subject_id');
    }


    /**
     * A Subject Tutor Belongs To A User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tutor(){
        return $this->belongsTo(User::class, 'tutor_id', 'user_id');
    }
}
