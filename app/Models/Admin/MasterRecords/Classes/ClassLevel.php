<?php

namespace App\Models\Admin\MasterRecords\Classes;

use App\Models\Admin\Items\ItemQuote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassLevel extends Model
{
    use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'classlevels';
    /**
     * The table permissions primary key
     * @var int
     */
    protected $primaryKey = 'classlevel_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'classlevel',
        'classgroup_id',
    ];

    /**
     * A Class Level Has Many Class Rooms
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function classRooms(){
        return $this->hasMany('App\Models\Admin\MasterRecords\Classes\ClassRoom', 'classlevel_id');
    }

    /**
     * A Class Level Belongs To Class Group
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function classGroup(){
        return $this->belongsTo('App\Models\Admin\MasterRecords\Classes\ClassGroup', 'classgroup_id');
    }

    /**
     * A Class Level Has Many Item Quotes
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function itemQuotes(){
        return $this->hasMany(ItemQuote::class, 'classlevel_id');
    }
}
