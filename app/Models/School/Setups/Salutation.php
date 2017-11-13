<?php

namespace App\Models\School\Setups;

use App\Models\Admin\Users\User;
use Illuminate\Database\Eloquent\Model;

class Salutation extends Model
{
    protected $connection = 'admin_mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'salutations';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'salutation_id';

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
        'salutation',
        'salutations_abbr'
    ];

    /**
     * A User Type has many Users
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function users(){
        return $this->hasMany(User::class, 'salutation_id');
    }
}
