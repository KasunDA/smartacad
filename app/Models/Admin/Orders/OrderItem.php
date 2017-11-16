<?php

namespace App\Models\Admin\Orders;

use App\Helpers\CurrencyHelper;
use App\Models\Admin\Items\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'amount',
        'item_amount',
        'discount',
        'quantity',
        'order_id',
        'item_id'
    ];

    /**
     * An Order Item belongs to a Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * An Order Item belongs to an Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(){
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function getDiscountedAmount(){
        return ($this->discount == 0) 
            ? $this->item_amount
            : CurrencyHelper::discountedAmount($this->item_amount, (int) $this->discount);
    }
}
