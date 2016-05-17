<?php

namespace App\Models\School\Setups;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $connection = 'admin_mysql';


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'status';
    /**
     * The table states primary key
     *
     * @var int
     */
    protected $primaryKey = 'status_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status', 'label'];

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;
}
