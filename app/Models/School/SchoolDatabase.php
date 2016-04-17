<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class SchoolDatabase extends Model
{
    protected $connection = 'admin_mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'school_databases';

    /**
     * The table Menus primary key
     *
     * @var int
     */
    protected $primaryKey = 'school_database_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'host',
        'database',
        'username',
        'password',
        'schools_id',
    ];

    /**
     * A Database belongs to a School
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school(){
        return $this->belongsTo('App\Models\School\School');
    }
}
