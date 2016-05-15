<?php

namespace App\Models\Admin\Subjects;

use Illuminate\Database\Eloquent\Model;

class SubjectTutor extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subject_tutors';

    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'subject_tutor_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tutor_id',
        'subject_classroom_id',
    ];

    /**
     * A Subject Tutor Belongs To A User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tutor(){
        return $this->belongsTo('App\Models\Admin\Users\User');
    }

    /**
     * A Subject Tutor belongs to Subject Class Room
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectClassRoom(){
        return $this->belongsTo('App\Models\Admin\Subjects\SubjectClassRoom', 'subject_classroom_id');
    }
}
