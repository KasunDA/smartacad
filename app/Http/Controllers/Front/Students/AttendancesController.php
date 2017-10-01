<?php

namespace App\Http\Controllers\Front\Students;

use App\Models\Admin\Accounts\Students\Student;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Admin\Attendances\Attendance;
use App\Models\Admin\Attendances\AttendanceDetail;

class AttendancesController extends Controller
{
    
    /**
     * Displays the Staff profiles details
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getView($encodeId)
    {
        $student = Student::findOrFail($this->decode($encodeId));
        $attendances = Attendance::whereIn('id', AttendanceDetail::where('student_id', $student->student_id)
                ->lists('attendance_id')->toArray()
            )
            ->groupBy(['academic_term_id'])
            ->orderBy('attendance_date', 'DESC')
            ->get();
        
        return view('front.students.attendance', compact('student', 'attendances'));
    }
}
