<?php

namespace App\Models\Admin\PinNumbers;

use Illuminate\Database\Eloquent\Model;

class PinNumber extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pin_numbers';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'pin_number_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pin_number', 'serial_number', 'status'
    ];
    
    const NUMBER_OF_DIGITS = 12;
    
    const SPACING = 3;

    /**
     * A Random Number has many ResultChecker
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function resultCheckers(){
        return $this->hasMany(ResultChecker::class, 'pin_number_id');
    }
}
