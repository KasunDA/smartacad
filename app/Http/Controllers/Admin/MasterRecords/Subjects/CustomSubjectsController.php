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
    public function index()
    {
        $customs = CustomSubject::roots()->get();

        return view('admin.master-records.subjects.custom.index', compact('customs'));
    }

    /**
     * Display a listing of the Custom Subject Groupings.
     *
     * @return Response
     */
    public function groupings()
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
    public function save(Request $request)
    {
        $count = $this->_saveGroupings($request);
        $this->setFlashMessage($count . ' Subject Groupings has been successfully updated.', 1);

        return redirect('/custom-subjects/groupings');
    }

    /**
     * Display a listing of the Custom Sub Subject Groupings.
     * @return Response
     */
    public function subjects()
    {
        $customs = CustomSubject::roots()->get();
        $sub = CustomSubject::whereNotNull('parent_id')->count();
        $parents = CustomSubject::roots()
            ->orderBy('name')
            ->get()
            ->pluck('name', 'custom_subject_id')
            ->prepend('- Select Parent -', '');
        $subjects =  $this->school_profile->subjects()
            ->orderBy('subject')
            ->get();

        return view('admin.master-records.subjects.custom.subject',
            compact('customs', 'parents', 'sub', 'subjects')
        );
    }

    /**
     * Insert or Update the Sub Subject records also Assigning a Custom Subject Groupings as the parent
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveSubjects(Request $request)
    {
        $count = $this->_saveGroupings($request, true);
        $this->setFlashMessage($count . ' Custom Subject Groupings has been successfully updated.', 1);

        return redirect('/custom-subjects/subjects');
    }

    /**
     * Delete a Menu from the list of Categories using a given menu id
     * @param $custom_subject_id
     */
    public function delete($custom_subject_id)
    {
        $cus = CustomSubject::findOrFail($custom_subject_id);
        
        (($cus !== null) && $cus->delete())
            ? $this->setFlashMessage(' Deleted!!! '.$cus->name.' Subject Group have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }

    /**
     * Helper Method for saving sub levels of a menu
     * @param mixed $request
     * @param Boolean $isRoot
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    private function _saveGroupings($request, $isRoot=false)
    {
        $inputs = $request->all();
        $count = 0;
        for($i = 0; $i < count($inputs['custom_subject_id']); $i++){
            //Create New or Modify Existing Sub Menu
            $group = ($inputs['custom_subject_id'][$i] > 0) 
                ? CustomSubject::find($inputs['custom_subject_id'][$i]) 
                : new CustomSubject();
            $group->name = (isset($inputs['name'][$i])) ? $inputs['name'][$i] : null;
            $group->custom_subject_id = (isset($inputs['custom_subject_id'][$i])) ? $inputs['custom_subject_id'][$i] : null;
            $group->subject_id = (isset($inputs['subject_id'][$i])) ? $inputs['subject_id'][$i] : null;
            $group->classgroup_id = (isset($inputs['classgroup_id'][$i])) ? $inputs['classgroup_id'][$i] : null;
            $group->abbr = (isset($inputs['abbr'][$i])) ? $inputs['abbr'][$i] : null;
            $group->save();
            $count++;

            if($isRoot){
                $root = CustomSubject::find($inputs['parent_id'][$i]);
                $group->makeChildOf($root);
            }
        }
        
        return $count;
    }
}
