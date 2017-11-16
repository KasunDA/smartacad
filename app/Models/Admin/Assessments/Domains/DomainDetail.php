<?php

namespace App\Models\Admin\Assessments\Domains;

use Illuminate\Database\Eloquent\Model;

class DomainDetail extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'domain_details';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'domain_detail_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['domain_assessment_id', 'option', 'domain_id'];

    /**
     * disable the time stamps
     * @var bool
     */
    public $timestamps = false;

    /**
     * A Domain Detail Belongs To A Domain
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain(){
        return $this->belongsTo(Domain::class, 'domain_id');
    }
}
