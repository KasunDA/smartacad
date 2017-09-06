<?php

namespace App\Models\Admin\Orders;

use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderInitiate extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'academic_term_id',
    ];

    /**
     * An Order Initiated belongs to an Academic Term
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicTerm(){
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * An Order Initiated belongs to a User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * An Order Initiated has many Order
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function orders(){
        return $this->hasMany(Order::class);
    }
}
