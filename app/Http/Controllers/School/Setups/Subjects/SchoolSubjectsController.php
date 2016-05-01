<?php

namespace App\Http\Controllers\School\Setups\Subjects;

use App\Models\School\Setups\Subjects\SchoolSubject;
use App\Models\School\Setups\Subjects\SubjectGroup;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SchoolSubjectsController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $school_subjects = SchoolSubject::all();
        $subject_groups = SubjectGroup::lists('subject_group', 'subject_group_id')->prepend('Subject Group', '');

        return view('school.setups.subjects.school-subjects', compact('school_subjects', 'subject_groups'));
    }


    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for($i = 0; $i < count($inputs['school_subject_id']); $i++){
            $subject = ($inputs['school_subject_id'][$i] > 0) ? SchoolSubject::find($inputs['school_subject_id'][$i]) : new SchoolSubject();
            $subject->school_subject = $inputs['school_subject'][$i];
            $subject->subject_group_id = $inputs['subject_group_id'][$i];
            $subject->school_subject_abbr = $inputs['school_subject_abbr'][$i];
            if($subject->save()){
                $count = $count+1;
            }
        }
        // Set the flash message
        if($count > 0)
            $this->setFlashMessage($count . ' Subject has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/school-subjects');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $subject = SchoolSubject::findOrFail($id);
        //Delete The Record
        $delete = ($subject !== null) ? $subject->delete() : null;

        if($delete){
            $this->setFlashMessage('  Deleted!!! '.$subject->school_subject.' Subject have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }
}
