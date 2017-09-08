<?php

namespace App\Models\Admin\Views;

use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Orders\OrderInitiate;
use App\Models\Admin\Orders\OrderItem;
use App\Models\Admin\Orders\OrderLog;
use Illuminate\Database\Eloquent\Model;

class ItemView extends Model
{

    protected $table = 'students_itemsviews';

    /**
     * Paid Orders From ItemView
     *
     * @param $query
     */
    public function scopePaid($query){
        return $query->where('paid', 1);
    }

    /**
     * Not Paid Orders From ItemView
     *
     * @param $query
     */
    public function scopeNotPaid($query){
        return $query->where('paid', '<>', 1);
    }

    /**
     * Active Students in Orders From ItemView
     *
     * @param $query
     */
    public function scopeActiveStudent($query){
        return $query->where('student_status', 1);
    }

    /**
     * Non Deleted Orders From ItemView
     *
     * @param $query
     */
    public function scopeNotDeleted($query){
        return $query->where('deleted_at', null);
    }

    /**
     * An Order belongs to a Sponsor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sponsor(){
        return $this->belongsTo(Sponsor::class);
    }

    /**
     * An Order belongs to an Order Initiate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orderInitiate(){
        return $this->belongsTo(OrderInitiate::class);
    }

    /**
     * An Order has many Order Items
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }

    /**
     * An Order has many Order Logs
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function orderLogs(){
        return $this->hasMany(OrderLog::class);
    }
}
