<?php

namespace App\Models\Banks;

use App\Models\Admin\MasterRecords\Classes\ClassGroup;
use App\Models\School\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolBank extends Model
{
    use SoftDeletes;
    
    protected $connection = 'admin_mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'school_banks';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_name',
        'account_number',
        'active',
        'bank_id',
        'classgroup_id',
        'school_id'
    ];

    /**
     * A School Bank belongs to a School
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school(){
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * A School Bank belongs to a Bank
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank(){
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    /**
     * A School Bank belongs to a Class group
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classGroup(){
        return $this->belongsTo(ClassGroup::class, 'classgroup_id');
    }
    
    
}
