<?php

namespace App\Http\Controllers\Admin\MasterRecords;

use App\Models\School\School;
use App\Models\School\Setups\Subjects\Subject;
use App\Models\School\Setups\Subjects\SubjectGroup;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SchoolSubjectsController extends Controller
{
    /**
     * Display a listing of the Subjects for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $subject_groups = SubjectGroup::orderBy('subject_group')->get();
        $subjects = Subject::lists('subject_id')->toArray();
        return view('admin.master-records.subjects.index', compact('subject_groups', 'subjects'));
    }


    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        $inputs = $request->all();

        if(isset($inputs['subject_id'])){
            $school = School::mySchool();
            (isset($inputs['subject_id'])) ? $school->subjects()->sync($inputs['subject_id']) : $school->subjects()->sync([]);
            // Set the flash message
            $this->setFlashMessage(count($inputs['subject_id']) . ' Subjects has been successfully registered.', 1);
        }else{
            $this->setFlashMessage('No Subject has been selected!!!.', 2);
        }
        // redirect to the create a new inmate page
        return redirect('/school-subjects');
    }

    /**
     * Display a listing of the Subjects for Master Records.
     *
     * @return Response
     */
    public function getView()
    {
        return view('admin.master-records.subjects.view');
    }
}
