<?php

namespace App\Models\School\Banks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes;
    
    protected $connection = 'admin_mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'banks';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'active'
    ];

    const ACTIVE = 1;
    
    /**
     * A Bank has many schools
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function schoolBanks(){
        return $this->hasMany(SchoolBank::class, 'bank_id');
    }

    public function scopeActive($query){
        return $query->where('active', self::ACTIVE);
    }
    
}
