<?php

namespace App\Models\Admin\Items;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemType extends Model
{

    use SoftDeletes;

    public $timestamps = false;

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
