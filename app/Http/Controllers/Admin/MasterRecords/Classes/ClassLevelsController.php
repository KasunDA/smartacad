<?php

namespace App\Http\Controllers\Admin\MasterRecords\Classes;

use App\Models\Admin\MasterRecords\Classes\ClassGroup;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\School\School;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ClassLevelsController extends Controller
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

        ((int) $this->school->setup === School::CLASS_LEVEL)
            ? $this->setFlashMessage('Warning!!! Kindly Setup the Class Levels records Before Proceeding.', 3)
            : $this->middleware('setup');
    }

    /**
     * Display a listing of the Menus for Master Records.
     * @param Boolean $group_id
     * @return Response
     */
    public function index($group_id = false)
    {
        $classGroup = ($group_id) ? ClassGroup::findOrFail($this->decode($group_id)) : false;
        $classlevels = ($classGroup) ? $classGroup->classLevels()->get() : ClassLevel::all();

        $classgroups = ClassGroup::pluck('classgroup', 'classgroup_id')
            ->prepend('- Select Class Group -', '');

        return view('admin.master-records.classes.class-levels', compact('classlevels', 'classgroups', 'classGroup'));
    }

    /**
     * Insert or Update the class level records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

       for($i = 0; $i < count($inputs['classlevel_id']); $i++){
            $classlevel = ($inputs['classlevel_id'][$i] > 0) ? ClassLevel::find($inputs['classlevel_id'][$i]) : new ClassLevel();
            $classlevel->classlevel = $inputs['classlevel'][$i];
            $classlevel->classgroup_id = $inputs['classgroup_id'][$i];
            if($classlevel->save()){
                $count = $count+1;
            }
        }
        //Update The Setup Process
        if ($this->school->setup == School::CLASS_LEVEL){
            $this->school->setup = School::CLASS_ROOM;
            $this->school->save();
            return redirect('/class-rooms');
        }

        if($count > 0) $this->setFlashMessage($count . ' Academic Year has been successfully updated.', 1);

        return redirect('/class-levels');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function delete($id)
    {
        $classlevel = ClassLevel::findOrFail($id);
        $delete = ($classlevel !== null) ? $classlevel->delete() : null;

        ($delete)
            ? $this->setFlashMessage('  Deleted!!! '.$classlevel->classlevel.' Class Level have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }

    /**
     * Get The Class Levels Given the group id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function classGroups(Request $request)
    {
        $inputs = $request->all();

        return redirect('/class-levels/' . $this->encode($inputs['class_group_id']));
    }
}
