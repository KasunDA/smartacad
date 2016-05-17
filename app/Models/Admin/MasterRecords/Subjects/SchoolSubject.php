<?php

namespace App\Models\Admin\MasterRecords\Subjects;

use Illuminate\Database\Eloquent\Model;

class SchoolSubject extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'schools_subjects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject_id',
        'school_id',
        'subject_alias'
    ];

    /**
     * A SchoolSubject Belongs To A School
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function school(){
        return $this->belongsTo('App\Models\School\School', 'school_id');
    }


    /**
     * A SchoolSubject Has Many Subject
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function subjects(){
    return $this->hasMany('App\Models\School\Setups\Subjects\Subject', 'subject_id');
    }
}
