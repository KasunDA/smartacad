<?php

namespace App\Models\Admin\Assessments\Domains;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'domains';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'domain_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['domain'];

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;

    /**
     * A Domain Has Many Domain Detail
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domainDetails(){
        return $this->hasMany(DomainDetail::class, 'domain_id');
    }
}
