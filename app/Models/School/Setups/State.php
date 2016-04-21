<?php

namespace App\Models\School\Setups;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $connection = 'admin_mysql';


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'states';
    /**
     * The table states primary key
     *
     * @var int
     */
    protected $primaryKey = 'state_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['state', 'state_code'];

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;


    /**
     * This will get all the lga of the state using the hasMany relationship
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lgas(){
        return $this->hasMany('App\Models\School\Setups\Lga');
    }
}
