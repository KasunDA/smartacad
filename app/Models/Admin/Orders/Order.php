<?php

namespace App\Models\Admin\Orders;

use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Order extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'status',
        'number',
        'paid',
        'backend',
        'amount',
        'tax',
        'student_id',
        'sponsor_id',
        'classroom_id',
        'academic_term_id'
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
     * @param Int $term_id
     */
    public static function processBillings($term_id){
        return DB::statement('call sp_processBillings(' . $term_id .')');
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
        return $this->belongsTo(ClassRoom::class);
    }

    /**
     * An Order belongs to an academic term
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicTerm(){
        return $this->belongsTo(AcademicTerm::class);
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
