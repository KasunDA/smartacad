<?php

namespace App\Http\Controllers\Admin\Attendances;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\Attendances\Attendance;
use App\Models\Admin\Attendances\AttendanceDetail;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassMaster;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\RolesAndPermissions\Role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AttendancesController extends Controller
{
    /**
     * Display a Form for billing.
     *
     * @return Response
     */
    public function index()
    {
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('- Academic Year -', '');
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('- Class Level -', '');
        $classrooms = $classes = ClassMaster::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
            ->where(function($query){
                if(!Auth::user()->hasRole([Role::DEVELOPER, Role::SUPER_ADMIN]))
                    $query->where('user_id', Auth::user()->user_id);
            })
            ->get();

        return view('admin.attendances.index', compact('academic_years', 'classlevels', 'classrooms'));
    }

    /**
     * Displays the details of the subjects students scores for a specific academic term
     * @param String $classId
     * @param String attendId
     * @return \Illuminate\View\View
     */
    public function initiate($classId, $attendId=null)
    {
        $classroom = ClassRoom::findOrFail($this->decode($classId));
        $attendances = ($attendId) ? Attendance::findOrFail($this->decode($attendId)) : null;
        
        $studentClasses = $classroom->studentClasses()
            ->where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
            ->get();
        $classMaster = $classroom->classMasters()
            ->where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
            ->first();
        
        return view('admin.attendances.take', compact('studentClasses','classMaster', 'attendances'));
    }

    /**
     * Save records
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function take(Request $request)
    {
        $inputs = $request->all();
        $attendance = isset($inputs['attendance_id']) ? Attendance::findOrFail($this->decode($inputs['attendance_id'])) : false;
        $classroom_id = $this->decode($inputs['classroom_id']);
        $classroom = ClassRoom::findOrFail($classroom_id);
        $exist = Attendance::validateDate($classroom_id, $inputs['attendance_date']);


        if($exist && (!$attendance || ($attendance && $attendance->attendance_date->format('Y-m-d') != $inputs['attendance_date']))){
            $this->setFlashMessage('Attendance for 1 ' . $inputs['attendance_date'] . ' Has been taken, kindly edit for any necessary adjustments ', 2);
            return redirect()->back();
        }

        $attendance = ($attendance) ? $attendance : new Attendance();
        $attendance->classroom_id = $classroom_id;
        $attendance->academic_term_id = AcademicTerm::activeTerm()->academic_term_id;
        $attendance->user_id = Auth::id();
        $attendance->attendance_date = $inputs['attendance_date'];

        if($attendance->save()){
            for( $i=0; $i < count($inputs['students']); $i++ ){
                $details = isset($inputs['details']) ? AttendanceDetail::find($inputs['details'][$i]) : new AttendanceDetail();
                $details->student_id = $inputs['students'][$i];
                $details->status = isset($inputs['status'][$i]);
                $details->reason = $inputs['reason'][$i] ?? null;
                $details->attendance_id = $attendance->id;
                $details->save();
            }
        }

        session()->put('attendance-tab', 'initiate');
        $this->setFlashMessage('Attendance for ' . $classroom->classroom . ' on ' . $inputs['attendance_date'] . ' successfully taken', 1);

        return redirect('/attendances');
    }

    /**
     * Edit attendance records
     * @param String $classId
     * @return \Illuminate\View\View
     */
    public function adjust($classId)
    {
        $classroom = ClassRoom::findOrFail($this->decode($classId));
        $classMaster = $classroom->classMasters()
            ->where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
            ->first();
        $attendances = Attendance::where('classroom_id', $classroom->classroom_id)
            ->where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)
            ->orderBy('attendance_date', 'DESC')
            ->get();

        return view('admin.attendances.adjust', compact('attendances', 'classMaster'));
    }

    /**
     * Display summary search based on classroom
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function classroom(Request $request)
    {
        session()->put('attendance-tab', 'classroom');
        $inputs = $request->all();
        $attendances = [];
        $response['flag'] = 0;

        if(!empty($inputs['classroom_id']) && !empty($inputs['academic_term_id'])){
            $attendances = Attendance::where('classroom_id', $inputs['classroom_id'])
                ->where('academic_term_id', $inputs['academic_term_id'])
                ->where(function($query){
                    if(!Auth::user()->hasRole([Role::DEVELOPER, Role::SUPER_ADMIN]))
                        $query->where('user_id', Auth::user()->user_id);
                })
                ->orderBy('attendance_date', 'DESC')
                ->get();
        }

        if(!empty($attendances)){
            //All the students in the class room for the academic year
            foreach($attendances as $attendance){
                $output[] = [
                    'id' => $this->encode($attendance->id),
                    'tutor' => $attendance->classMaster->fullNames(),
                    'classroom' => $attendance->classroom->classroom,
                    'term' => $attendance->academicTerm->academic_term,
                    'present' => $attendance->details()->where('status', 1)->count(),
                    'absent' => $attendance->details()->where('status', 0)->count(),
                    'date_taken' => $attendance->attendance_date->format('D jS, M Y')
                ];
            }
            //Sort The Students by name
            $response['flag'] = 1;
            $response['Attendance'] = $output ?? [];
        }
        echo json_encode($response);
    }

    /**
     * Get details on attendance based on class room
     * @param String $attendId
     * @return \Illuminate\View\View
     */
    public function classroomDetails($attendId)
    {
        $attendance = Attendance::findOrFail($this->decode($attendId));
        $details = $attendance->details()
            ->with('student')
            ->get()
            ->sortBy('student.first_name');

        return view('admin.attendances.classroom-details', compact('attendance', 'details'));
    }

    /**
     * Display summary search based on student
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function student(Request $request)
    {
        session()->put('attendance-tab', 'student');
        $inputs = $request->all();
        $response['flag'] = 0;
        $term = AcademicTerm::find($inputs['view_academic_term_id']);

        if(!empty($inputs['view_classroom_id']) && !empty($inputs['view_academic_term_id'])){
            $studentClasses = StudentClass::with('student')
                ->where('classroom_id', $inputs['view_classroom_id'])
                ->where('academic_year_id', $term->academic_year_id)
                ->get()
                ->sortBy('student.first_name');

            //All the students in the class room for the academic year
            foreach($studentClasses as $studentClass){
                $output[] = [
                    'studClassId' => $this->encode($studentClass->student_class_id),
                    'termId' => $this->encode($term->academic_term_id),
                    'student' => $studentClass->student->simpleName(),
                    'studentNo' => $studentClass->student->student_no,
                    'classroom' => $studentClass->classroom->classroom,
                    'present' => $studentClass->student
                        ->attendanceDetails()
                        ->present()
                        ->whereIn('attendance_id',
                            Attendance::where('classroom_id', $inputs['view_classroom_id'])
                                ->where('academic_term_id', $term->academic_term_id)
                                ->lists('id')
                                ->toArray()
                        )
                        ->count(),
                    'absent' => $studentClass->student
                        ->attendanceDetails()
                        ->absent()
                        ->whereIn('attendance_id',
                            Attendance::where('classroom_id', $inputs['view_classroom_id'])
                                ->where('academic_term_id', $term->academic_term_id)
                                ->lists('id')
                                ->toArray()
                        )
                        ->count(),
                ];
            }
            $response['flag'] = 1;
            $response['Students'] = $output ?? [];
        }
        echo json_encode($response);
    }

    /**
     * Get details on attendance based on students
     * @param String $studentClassId
     * @param String $termId
     * @return \Illuminate\View\View
     */
    public function studentDetails($studentClassId, $termId)
    {
        $studentClass = StudentClass::findOrFail($this->decode($studentClassId));
        $term = AcademicTerm::find($this->decode($termId));

        $attendances = Attendance::with(['details' => function($query) use($studentClass){
                $query->where('student_id', $studentClass->student_id);
            }])
            ->where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $studentClass->classroom_id)
            ->get()
            ->sortByDesc('attendance_date');
        return view('admin.attendances.student-details', compact('studentClass', 'term', 'attendances'));
    }

    /**
     * Displays the Student attendance header
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function viewStudent($encodeId)
    {
        $student = Student::findOrFail($this->decode($encodeId));
        $attendances = Attendance::whereIn('id', AttendanceDetail::where('student_id', $student->student_id)
            ->lists('attendance_id')->toArray()
        )
            ->groupBy(['academic_term_id'])
            ->orderBy('attendance_date', 'DESC')
            ->get();

        return view('admin.attendances.view', compact('student', 'attendances'));
    }

    /**
     * Displays the Student attendance details
     * @param String $studId
     * @param String $attendId
     * @return \Illuminate\View\View
     */
    public function viewDetails($studId, $attendId)
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

        return view('admin.attendances.details', compact('student', 'attendances'));
    }
}
