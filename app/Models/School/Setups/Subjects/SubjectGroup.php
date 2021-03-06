<?php

namespace App\Models\School\Setups\Subjects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectGroup extends Model
{
    use SoftDeletes;
    
    protected $connection = 'admin_mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subject_groups';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'subject_group_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject_group',
    ];

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;

    /**
     * A Subject Group has many Subjects
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function subjects(){
        return $this->hasMany(Subject::class, 'subject_group_id');
    }
}
