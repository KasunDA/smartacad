<?php

namespace App\Http\Controllers\Admin\Attendances;

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
        $classrooms = $classes = ClassMaster::where('academic_year_id', AcademicYear::activeYear()->academic_year_id)
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
     * @return \Illuminate\View\View
     */
    public function take($classId)
    {
        $classroom = ClassRoom::findOrFail($this->decode($classId));
        $students = $classroom->studentClasses()->where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)->get();

        return view('admin.attendances.take', compact('students', 'classroom'));
    }
}
