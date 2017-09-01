<?php

namespace App\Models\Admin\Items;

use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use Illuminate\Database\Eloquent\Model;

class ItemQuote extends Model
{
    protected $fillable = [
        'price',
        'item_id',
        'classlevel_id',
    ];

    /**
     * An Item Quote belongs to an Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(){
        return $this->belongsTo(Item::class);
    }

    /**
     * An Item Quote belongs to a class level
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classLevel(){
        return $this->belongsTo(ClassLevel::class);
    }
}
