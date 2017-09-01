<?php

namespace App\Models\Admin\Items;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{

    protected $timestamps = false;

    protected $fillable = [
        'item_type'
    ];

    /**
     * An Item Type has many Items
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function items(){
        return $this->hasMany(Item::class);
    }
}
