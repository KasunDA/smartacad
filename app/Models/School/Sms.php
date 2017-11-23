<?php

namespace App\Models\School;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $connection = 'admin_mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sms';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unit_bought',
        'unit_used',
        'status',
    ];
}
