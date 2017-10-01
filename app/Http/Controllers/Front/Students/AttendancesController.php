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
     * Displays the Student attendance header
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
        
        return view('front.attendances.view', compact('student', 'attendances'));
    }

    /**
     * Displays the Student attendance details
     * @param String $studId
     * @param String $attendId
     * @return \Illuminate\View\View
     */
    public function getDetails($studId, $attendId)
    {
        $student = Student::findOrFail($this->decode($studId));
        $attendance = Attendance::findOrFail($this->decode($attendId));

        $attendances = Attendance::with(['details' => function($query) use($student){
                $query->where('student_id', $student->student_id);
            }])
            ->where('academic_term_id', $attendance->academic_term_id)
            ->where('classroom_id', $attendance->classroom_id)
            ->get()
            ->sortByDesc('attendance_date');

        return view('front.attendances.details', compact('student', 'attendances'));
    }
}
