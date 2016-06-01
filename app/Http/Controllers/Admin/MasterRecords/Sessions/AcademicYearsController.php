<?php

namespace App\Http\Controllers\Admin\MasterRecords\Sessions;

use App\Models\Admin\MasterRecords\AcademicYear;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AcademicYearsController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $academic_years = AcademicYear::all();
        return view('admin.master-records.academic-years', compact('academic_years'));
    }


    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        $inputs = $request->all();
        $count = $status = 0;

        // Validate TO Make Sure Only One Status is Set
        for($j=0; $j<count($inputs['status']); $j++)
            if($inputs['status'][$j] === '1') $status++;

        if($status > 1 || $status < 1) {
            $this->setFlashMessage('Note!!! An Academic Year (Only One) Must Be Set To Active At Any Point In Time.', 2);
        }else{

            for($i = 0; $i < count($inputs['academic_year_id']); $i++){
                $academic_year = ($inputs['academic_year_id'][$i] > 0) ? AcademicYear::find($inputs['academic_year_id'][$i]) : new AcademicYear();
                $academic_year->academic_year = $inputs['academic_year'][$i];
                $academic_year->status = $inputs['status'][$i];
                if($academic_year->save()){
                    $count = $count+1;
                }
            }
            // Set the flash message
            if($count > 0)
                $this->setFlashMessage($count . ' Academic Year has been successfully updated.', 1);
        }
        // redirect to the create a new inmate page
        return redirect('/academic-years');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $academic_year = AcademicYear::findOrFail($id);
        //Delete The Record
        $delete = ($academic_year !== null) ? $academic_year->delete() : null;

        if($delete){
            $this->setFlashMessage('  Deleted!!! '.$academic_year->academic_year.' Academic Year have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }
}
