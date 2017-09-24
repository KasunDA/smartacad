<?php

namespace App\Http\Controllers\Admin\Attendances;

use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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

        return view('admin.attendances.index', compact('academic_years', 'classlevels'));
    }
}
