<?php

namespace App\Http\Controllers\School\Setups\Subjects;

use App\Models\School\Setups\Subjects\SubjectGroup;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SubjectGroupsController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function index()
    {
        $subject_groups = SubjectGroup::all();
        
        return view('school.setups.subjects.subject-groups', compact('subject_groups'));
    }


    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for ($i = 0; $i < count($inputs['subject_group_id']); $i++) {
            $subject_group = ($inputs['subject_group_id'][$i] > 0) 
                ? SubjectGroup::find($inputs['subject_group_id'][$i]) 
                : new SubjectGroup();
            $subject_group->subject_group = $inputs['subject_group'][$i];
            
            if ($subject_group->save()) {
                $count = $count+1;
            }
        }
        if($count > 0) $this->setFlashMessage($count . ' Subject Group has been successfully updated.', 1);
        
        return redirect('/subject-groups');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function delete($id)
    {
        $subject_group = SubjectGroup::findOrFail($id);

        ($subject_group->delete())
            ? $this->setFlashMessage('  Deleted!!! ' . $subject_group->subject_group 
                . ' Subject Group have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }
}
