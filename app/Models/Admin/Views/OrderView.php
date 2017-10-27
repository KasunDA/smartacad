<?php

namespace App\Models\Admin\Views;

use App\Helpers\LabelHelper;
use App\Models\Admin\Orders\Order;
use App\Models\Admin\Orders\OrderInitiate;
use App\Models\Admin\Orders\OrderItem;
use App\Models\Admin\Orders\OrderLog;
use App\Models\Admin\Users\User;
use Illuminate\Database\Eloquent\Model;

class OrderView extends Model
{

    protected $table = 'ordersviews';

    /**
     * Paid Orders From OrderView
     *
     * @param $query
     */
    public function scopePaid($query){
        return $query->where('paid', 1);
    }

    /**
     * Not Paid Orders From OrderView
     *
     * @param $query
     */
    public function scopeNotPaid($query){
        return $query->where('paid', '<>', 1);
    }

    /**
     * Active Students in Orders From OrderView
     *
     * @param $query
     */
    public function scopeActiveStudent($query){
        return $query->where('student_status', 1);
    }

    /**
     * An Order View belongs to a Sponsor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }
    
    /**
     * An Order belongs to a Sponsor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sponsor(){
        return $this->belongsTo(User::class, 'sponsor_id', 'user_id');
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

    public function getStatusLabel()
    {
        if($this->paid == Order::PAID)
            return LabelHelper::success(Order::paid());
        else if($this->paid == Order::NOT_PAID)
            return LabelHelper::danger(Order::notPaid());

        return LabelHelper::warning(Order::cancelled());
    }
}
