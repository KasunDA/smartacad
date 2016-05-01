<?php

namespace App\Models\School\Setups\Subjects;

use Illuminate\Database\Eloquent\Model;

class SchoolSubject extends Model
{
    protected $connection = 'admin_mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'school_subjects';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'school_subject_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'school_subject',
        'subject_group_id',
        'school_subject_abbr'
    ];

    /**
     * A School Subject Belongs To A Subject Group
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function subjectGroup(){
        return $this->belongsTo('App\Models\School\Setups\Subjects\SubjectGroup');
    }
}
