<?php

namespace App\Models\School\Setups;

use Illuminate\Database\Eloquent\Model;

class Lga extends Model
{
    protected $connection = 'admin_mysql';


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lgas';
    /**
     * The table lgas primary key
     *
     * @var int
     */
    protected $primaryKey = 'lga_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['lga', 'state_id'];

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;


    /**
     * This will get the state of the lga using the belongTo relationship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state(){
        return $this->belongsTo(State::class, 'state_id');
    }
}
