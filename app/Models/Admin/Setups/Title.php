<?php

namespace App\Models\Admin\Setups;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $connection = 'admin_mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'titles';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'title_id';

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
        'title',
        'title_abbr'
    ];

    /**
     * A User Type has many Users
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function users(){
        return $this->hasMany('App\Models\Admin\Users\User');
    }
}
