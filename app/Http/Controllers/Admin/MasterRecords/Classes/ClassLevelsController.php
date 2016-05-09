<?php

namespace App\Http\Controllers\Admin\MasterRecords\Classes;

use App\Models\Admin\MasterRecords\Classes\ClassGroup;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ClassLevelsController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $classlevels = ClassLevel::all();
        $classgroups = ClassGroup::lists('classgroup', 'classgroup_id')->prepend('Select Class Group', '');
        return view('admin.master-records.classes.class-levels', compact('classlevels', 'classgroups'));
    }


    /**
     * Insert or Update the class level records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
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
        // Set the flash message
        if($count > 0)
            $this->setFlashMessage($count . ' Academic Year has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/class-levels');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $classlevel = ClassLevel::findOrFail($id);
        //Delete The Record
        $delete = ($classlevel !== null) ? $classlevel->delete() : null;

        if($delete){
            $this->setFlashMessage('  Deleted!!! '.$classlevel->classlevel.' Class Level have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }
}
