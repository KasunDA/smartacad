<?php

namespace App\Http\Controllers\School\Setups\Subjects;

use App\Models\School\Setups\Subjects\Subject;
use App\Models\School\Setups\Subjects\SubjectGroup;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SubjectsController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @param Boolean $group_id
     * @return Response
     */
    public function getIndex($group_id=false)
    {
        $subject_group = ($group_id) ? SubjectGroup::findOrFail($this->decode($group_id)) : false;
        
        $subjects = ($subject_group) ? $subject_group->subjects()->orderBy('subject')->get() : Subject::orderBy('subject')->get();
        $subject_groups = SubjectGroup::orderBy('subject_group')->lists('subject_group', 'subject_group_id')->prepend('- Subject Group -', '');

        return view('school.setups.subjects.subjects', compact('subjects', 'subject_groups', 'subject_group'));
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

        for($i = 0; $i < count($inputs['subject_id']); $i++){
            $subject = ($inputs['subject_id'][$i] > 0) ? Subject::find($inputs['subject_id'][$i]) : new Subject();
            $subject->subject = $inputs['subject'][$i];
            $subject->subject_group_id = $inputs['subject_group_id'][$i];
            $subject->subject_abbr = $inputs['subject_abbr'][$i];
            if($subject->save()){
                $count = $count+1;
            }
        }
        // Set the flash message
        if($count > 0)
            $this->setFlashMessage($count . ' Subject has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/subjects');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $subject = Subject::findOrFail($id);
        //Delete The Record
        $delete = ($subject !== null) ? $subject->delete() : null;

        if($delete){
            $this->setFlashMessage('  Deleted!!! '.$subject->subject.' Subject have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Get The Subjects Given the group id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSubjectGroups(Request $request)
    {
        $inputs = $request->all();

        return redirect('/subjects/index/' . $this->encode($inputs['subject_group_id']));
    }
}
