<?php

namespace App\Models\Admin\Orders;

use App\Helpers\CurrencyHelper;
use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Float_;

class Order extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'status',
        'number',
        'paid',
        'backend',
        'amount',
        'total_amount',
        'amount_paid',
        'discount',
        'tax',
        'item_count',
        'student_id',
        'sponsor_id',
        'classroom_id',
        'academic_term_id',
        'order_initiate_id',
        'is_part_payment'
    ];

    const PAID = 'paid';
    const NOT_PAID = 'not-paid';
    const CANCELLED = 'cancelled';

    const STATUSES =  [
        self::PAID => [
            'title' => 'Paid',
            'label' => 'success',
            'paid' => true,
            'cancelled' => false,
        ],
        self::NOT_PAID => [
            'title' => 'Not Paid',
            'label' => 'danger',
            'paid' => false,
            'cancelled' => false,
        ],
        self::CANCELLED => [
            'title' => 'Cancelled',
            'label' => 'warning',
            'paid' => false,
            'cancelled' => true,
        ],
    ];


    public static function paidStatuses()
    {
        return array_filter(self::STATUSES, function($status) {
            return $status['paid'];
        });
    }

    public static function notPaidStatuses()
    {
        return array_filter(self::STATUSES, function($status) {
            return $status['not-paid'];
        });
    }

    public static function cancelledStatuses()
    {
        return array_filter(self::STATUSES, function($status) {
            return $status['cancelled'];
        });
    }

    /**
     *  Initiate Billings for all active students in an academic term
     * @param Int $item_initiate_id
     */
    public static function processBillings($item_initiate_id){
        return DB::statement('call sp_processBillings(' . $item_initiate_id .')');
    }

    /**
     *  Process Item Variables for all active students in an academic term
     * @param String $variableIds
     */
    public static function processItemVariables($variableIds){
        return DB::statement('call sp_processItemVariables("' . $variableIds . '")');
    }

    /**
     * Compute Order Amount from Order Items
     *
     * @param $format
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function amount($format=false){
        return (!empty($this->orderItems()->lists('amount'))) 
            ? ( ($format) 
                ? CurrencyHelper::format($this->orderItems()->lists('amount')->sum()) 
                : $this->orderItems()->lists('amount')->sum()
            ) 
            : 0;
    }

    /**
     * An Order belongs to a Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(){
        return $this->belongsTo(Student::class);
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
     * An Order belongs to a class room
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classRoom(){
        return $this->belongsTo(ClassRoom::class, 'classroom_id');
    }

    /**
     * An Order belongs to an academic term
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicTerm(){
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
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

    /**
     * An Order May have many Part Payments
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function partPayments(){
        return $this->hasMany(PartPayment::class);
    }

    /**
     * Compute Order Amount discounts
     *
     * @return Float_
     */
    public function getDiscountedAmount(){
        return ($this->discount == 0) ? $this->amount : CurrencyHelper::discount($this->total_amount, (int) $this->discount);
    }

    /**
     * ReCompute the Order Amount
     *
     * @param $id
     * @return self
     */
    public static function reComputeAmount($id){
        $order = self::find($id);
        foreach ($order->orderItems as $item){
            $item->amount = $item->getDiscountedAmount();
            $item->save();
        }

        $order->updateAmount();
        return $order;
    }

    /**
     * Update the Order Amount from Order Items also consider discounts
     *
     * @return self
     */
    public function updateAmount(){
        $this->amount = $this->total_amount = $this->orderItems()->lists('amount')->sum();
        $this->amount = $this->getDiscountedAmount();
        $this->item_count = count($this->orderItems);

        $this->save();
    }
}
