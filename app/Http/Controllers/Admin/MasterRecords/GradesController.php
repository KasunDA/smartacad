<?php

namespace App\Http\Controllers\Admin\MasterRecords;

use App\Models\Admin\MasterRecords\Classes\ClassGroup;
use App\Models\Admin\MasterRecords\Grade;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class GradesController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $grades = Grade::all();
        $classgroups = ClassGroup::lists('classgroup', 'classgroup_id')->prepend('Select Class Group', '');
        return view('admin.master-records.grades', compact('classgroups', 'grades'));
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

        for($i = 0; $i < count($inputs['grade_id']); $i++){
            $grade = ($inputs['grade_id'][$i] > 0) ? Grade::find($inputs['grade_id'][$i]) : new Grade();
            $grade->grade = $inputs['grade'][$i];
            $grade->grade_abbr = $inputs['grade_abbr'][$i];
            $grade->upper_bound = $inputs['upper_bound'][$i];
            $grade->lower_bound = $inputs['lower_bound'][$i];
            $grade->classgroup_id = $inputs['classgroup_id'][$i];
            if($grade->save()){
                $count = $count+1;
            }
        }
        // Set the flash message
        if($count > 0) $this->setFlashMessage($count . ' Grades has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/grades');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $grade = Grade::findOrFail($id);
        //Delete The Record
        $delete = ($grade !== null) ? $grade->delete() : null;

        if($delete){
            $this->setFlashMessage('  Deleted!!! '.$grade->grade.' Grade have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }
}
