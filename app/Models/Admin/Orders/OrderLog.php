<?php

namespace App\Models\Admin\Orders;

use App\Models\Admin\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderLog extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'comment',
        'user_id',
        'order_id',
    ];

    /**
     * An Order Log belongs to an Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(){
        return $this->belongsTo(Order::class);
    }

    /**
     * An Order Log belongs to a User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class);
    }
}
