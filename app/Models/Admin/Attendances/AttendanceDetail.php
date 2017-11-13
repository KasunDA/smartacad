<?php

namespace App\Models\Admin\Attendances;

use App\Models\Admin\Accounts\Students\Student;
use Illuminate\Database\Eloquent\Model;

class AttendanceDetail extends Model
{
    protected $fillable = [
        'student_id',
        'status',
        'reason',
        'attendance_id'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
    
    /**
     * An Attendance Details belongs to a Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * An Attendance Details Belongs to an Attendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attendance(){
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    /**
     * Students Present
     *
     * @param $query
     */
    public function scopePresent($query){
        return $query->where('status', 1);
    }

    /**
     * Students Absent
     *
     * @param $query
     */
    public function scopeAbsent($query){
        return $query->where('status', 0);
    }
}
