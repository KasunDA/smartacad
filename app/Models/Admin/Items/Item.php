<?php

namespace App\Models\Admin\Items;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

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
        return $this->belongsTo(ItemType::class);
    }

    /**
     * An Item has many Item Quotes
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function itemQuotes(){
        return $this->hasMany(ItemQuote::class);
    }

    /**
     * An Item has many Item Variables
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function itemVariables(){
        return $this->hasMany(ItemVariable::class);
    }
}
