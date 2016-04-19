<?php

namespace App\Models\School\Setups;

use Illuminate\Database\Eloquent\Model;

class MaritalStatus extends Model
{
    protected $connection = 'admin_mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'marital_statuses';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'marital_status_id';

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'marital_status',
        'marital_status_abbr'
    ];

    /**
     * A User Type has many Users
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function users(){
        return $this->hasMany('App\Models\Admin\Users\User');
    }
}
