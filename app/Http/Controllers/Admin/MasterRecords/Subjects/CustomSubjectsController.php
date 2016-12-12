<?php

namespace App\Http\Controllers\Admin\MasterRecords\Subjects;

use App\Models\Admin\MasterRecords\Classes\ClassGroup;
use App\Models\Admin\MasterRecords\Subjects\CustomSubject;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CustomSubjectsController extends Controller
{
    /**
     * Display a listing of all the Menus for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $customs = CustomSubject::roots()->get();
        return view('admin.master-records.subjects.custom.index', compact('customs'));
    }

    /**
     * Display a listing of the Custom Subject Groupings.
     *
     * @return Response
     */
    public function getGroupings()
    {
        $customs = CustomSubject::roots()->get();
        $groups = ClassGroup::orderBy('classgroup')->get();
        return view('admin.master-records.subjects.custom.group', compact('customs', 'groups'));
    }

    /**
     * Insert or Update the Custom Subject Groupings
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postGroupings(Request $request)
    {
        $count = $this->saveGroupings($request);
        // Set the flash message
        $this->setFlashMessage($count . ' Subject Groupings has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/custom-subjects/groupings');
    }

    /**
     * Display a listing of the Custom Sub Subject Groupings.
     * @return Response
     */
    public function getSubjects()
    {
        $customs = CustomSubject::roots()->get();
        $sub = CustomSubject::whereNotNull('parent_id')->count();
        $parents = CustomSubject::roots()->orderBy('name')->get()->pluck('name', 'custom_subject_id')->prepend('Select Parent', '');
        $subjects =  $this->school_profile->subjects()->orderBy('subject')->get();
//        dd($subjects);
        return view('admin.master-records.subjects.custom.subject', compact('customs', 'parents', 'sub', 'subjects'));
    }

    /**
     * Insert or Update the Sub Subject records also Assigning a Custom Subject Groupings as the parent
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSubjects(Request $request)
    {
        $count = $this->saveGroupings($request, true);
        // Set the flash message
        $this->setFlashMessage($count . ' Custom Subject Groupings has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/custom-subjects/subjects');
    }

    /**
     * Delete a Menu from the list of Categories using a given menu id
     * @param $custom_subject_id
     */
    public function getDelete($custom_subject_id)
    {
        $cus = CustomSubject::findOrFail($custom_subject_id);
        $delete = ($cus !== null) ? $cus->delete() : null;
        if($delete){
            $this->setFlashMessage(' Deleted!!! '.$cus->name.' Subject Group have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Helper Method for saving sub levels of a menu
     * @param mixed $request
     * @param Boolean $isRoot
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    private function saveGroupings($request, $isRoot=false)
    {
        $inputs = $request->all();
        $count = 0;
        for($i = 0; $i < count($inputs['custom_subject_id']); $i++){
            //Create New or Modify Existing Sub Menu
            $menu = ($inputs['custom_subject_id'][$i] > 0) ? CustomSubject::find($inputs['custom_subject_id'][$i]) : new CustomSubject();
            $menu->name = (isset($inputs['name'][$i])) ? $inputs['name'][$i] : null;
            $menu->custom_subject_id = (isset($inputs['custom_subject_id'][$i])) ? $inputs['custom_subject_id'][$i] : null;
            $menu->subject_id = (isset($inputs['subject_id'][$i])) ? $inputs['subject_id'][$i] : null;
            $menu->classgroup_id = (isset($inputs['classgroup_id'][$i])) ? $inputs['classgroup_id'][$i] : null;
            $menu->abbr = (isset($inputs['abbr'][$i])) ? $inputs['abbr'][$i] : null;
            $menu->save();
            $count++;

            if($isRoot){
                $root = CustomSubject::find($inputs['parent_id'][$i]);
                //Attach a Parent(Menu) to it
                $menu->makeChildOf($root);
            }
        }
        return $count;
    }
}
