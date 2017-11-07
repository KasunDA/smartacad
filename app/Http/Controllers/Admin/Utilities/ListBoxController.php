<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\School\Setups\Lga;

class ListBoxController extends Controller
{
    /**
     *
     * Make sure the user is logged in
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Get the local government areas based on the state id
     * @param int $id
     * @return Response
     */
    public function lga($id)
    {
        $lgas = Lga::where('state_id', $id)->orderBy('lga')->get();

        return view('admin.partials.lga')->with([
            'lgas' => $lgas
        ]);
    }

    /**
     * Get academic term based on the academic year id
     * @param int $id
     * @return Response
     */
    public function academicTerm($id)
    {
        $academic_terms = AcademicTerm::where('academic_year_id', $id)->orderBy('term_type_id')->get();

        return view('admin.partials.academic-term')->with([
            'academic_terms' => $academic_terms
        ]);
    }

    /**
     * Get class rooms based on the class level id
     * @param int $id
     * @return Response
     */
    public function classroom($id)
    {
        $classrooms = ClassRoom::where('classlevel_id', $id)->orderBy('classroom')->get();

        return view('admin.partials.classroom')->with([
            'classrooms' => $classrooms
        ]);
    }
}
