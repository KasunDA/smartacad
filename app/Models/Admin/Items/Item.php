<?php

namespace App\Models\Admin\Items;

use App\Models\Admin\Orders\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status',
        'item_type_id'
    ];

    /**
     * An Item belongs to an Item Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function itemType(){
        return $this->belongsTo(ItemType::class, 'item_type_id');
    }

    /**
     * An Item has many Item Quotes
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function itemQuotes(){
        return $this->hasMany(ItemQuote::class, 'item_id');
    }

    /**
     * An Item has many Item Variables
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function itemVariables(){
        return $this->hasMany(ItemVariable::class, 'item_id');
    }

    /**
     * An Item has many Order Items
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function orderItems(){
        return $this->hasMany(OrderItem::class, 'item_id');
    }
}
