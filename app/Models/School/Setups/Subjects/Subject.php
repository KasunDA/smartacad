<?php

namespace App\Models\School\Setups\Subjects;

use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use App\Models\School\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;
    
    protected $connection = 'admin_mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subjects';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'subject_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'subject_group_id',
        'subject_abbr'
    ];

    /**
     * A School Subject Belongs To A Subject Group
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function subjectGroup(){
        return $this->belongsTo(SubjectGroup::class, 'subject_group_id');
    }

    /**
     * Get the schools associated with the given subject
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function schools()
    {
        return $this->belongsToMany(School::class, 'schools_subjects', 'subject_id', 'school_id')
            ->withPivot('subject_alias');
    }

    /**
     * A Subject Has Many Subject Class Room
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function subjectClassRooms(){
        return $this->hasMany(SubjectClassRoom::class, 'subject_id');
    }
}
