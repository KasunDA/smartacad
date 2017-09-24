<?php

namespace App\Models\Admin\Attendances;

use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'classroom_id',
        'academic_term_id',
        'user_id',
        'attendance_date'
    ];
    
    protected $dates = ['attendance_date'];

    /**
     * An Attendance belongs to a Class Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classroom(){
        return $this->belongsTo(ClassRoom::class);
    }

    /**
     * An Attendance belongs to an Academic Term
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicTerm(){
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * An Attendance was taken by a Class Tutor (User)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classMaster(){
        return $this->belongsTo(User::class);
    }

    /**
     * An Attendance has many Attendance Details
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function details(){
        return $this->hasMany(AttendanceDetail::class);
    }

}
