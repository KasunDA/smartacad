<?php

namespace App\Models\Admin\PinNumbers;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pins';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'pin_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pin', 'serial'
    ];
    
    public $timestamps = false;
}
