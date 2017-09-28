<?php

namespace App\Http\Controllers\Admin\Attendances;

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

        if(($exist && !$attendance) || ($attendance && $exist && $attendance->attendance_date != $inputs['attendance_date'] )){
            $this->setFlashMessage('Attendance for ' . $inputs['attendance_date'] . ' Has been taken, kindly edit for any necessary adjustments ', 2);
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
     * Display summary search
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function summary(Request $request)
    {
        session()->put('attendance-tab', 'summary');
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
     * Get details on attendance
     * @param String $attendId
     * @return \Illuminate\View\View
     */
    public function details($attendId)
    {
        $attendance = Attendance::findOrFail($this->decode($attendId));

        return view('admin.attendances.details', compact('attendance'));
    }
}
