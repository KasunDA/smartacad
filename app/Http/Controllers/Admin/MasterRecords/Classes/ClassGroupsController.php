<?php

namespace App\Http\Controllers\Admin\MasterRecords\Classes;

use App\Models\Admin\MasterRecords\Classes\ClassGroup;
use App\Models\School\School;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ClassGroupsController extends Controller
{
    protected $school;
    /**
     *
     * Make sure the user is logged in and The Record has been setup
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->school = School::mySchool();

        $this->school->setup === School::CLASS_GROUP
            ? $this->setFlashMessage('Warning!!! Kindly Setup the Class Groups records Before Proceeding.', 3)
            : $this->middleware('setup');
    }
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function index()
    {
        $class_groups = ClassGroup::all();
        
        return view('admin.master-records.classes.class-groups', compact('class_groups'));
    }


    /**
     * Insert or Update the class group records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

       for($i = 0; $i < count($inputs['classgroup_id']); $i++){
            $classgroup = ($inputs['classgroup_id'][$i] > 0) ? ClassGroup::find($inputs['classgroup_id'][$i]) : new ClassGroup();
            $classgroup->classgroup = $inputs['classgroup'][$i];
            $classgroup->ca_weight_point = $inputs['ca_weight_point'][$i];
            $classgroup->exam_weight_point = $inputs['exam_weight_point'][$i];
            if($classgroup->save()){
                $count = $count+1;
            }
        }
        //Update The Setup Process
        if ($this->school->setup == School::CLASS_GROUP){
            $this->school->setup = School::CLASS_LEVEL;
            $this->school->save();
            return redirect('/class-levels');
        }
        
        if($count > 0) $this->setFlashMessage($count . ' Academic Year has been successfully updated.', 1);
        
        return redirect('/class-groups');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function delete($id)
    {
        $class_group = ClassGroup::findOrFail($id);
        //Delete The Record
        $delete = ($class_group !== null) ? $class_group->delete() : null;

        ($delete)
            ? $this->setFlashMessage('  Deleted!!! '.$class_group->classgroup.' Class Group have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }
}
