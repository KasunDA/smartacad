<?php

namespace App\Models\Admin\Orders;

use App\Models\Admin\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartPayment extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'amount',
        'order_id',
        'user_id'
    ];

    const PART_PAYMENT = 1;
    const FULL_PAYMENT = 0;
    const PAYMENT_TYPES = [self::FULL_PAYMENT => 'Full Payment', self::PART_PAYMENT => 'Part Payment'];
    
    /**
     * An Order Item belongs to a Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(){
        return $this->belongsTo(Order::class);
    }

    /**
     * An Order Item belongs to an Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class);
    }
}
