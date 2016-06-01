<?php

namespace App\Http\Controllers\Admin\MasterRecords\Sessions;

use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AcademicTermsController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $academic_terms = AcademicTerm::all();
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('Academic Year', '');
        return view('admin.master-records.academic-terms', compact('academic_terms', 'academic_years'));
    }


    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        $inputs = $request->all();
        $count = $status =0;

        // Validate TO Make Sure Only One Status is Set
        for($j=0; $j<count($inputs['status']); $j++)
            if($inputs['status'][$j] === '1') $status++;

        if($status > 1 || $status < 1) {
            $this->setFlashMessage('Note!!! An Academic Term (Only One) Must Be Set To Active At Any Point In Time.', 2);
        }else{
            for($i = 0; $i < count($inputs['academic_term_id']); $i++){
                $academic_term = ($inputs['academic_term_id'][$i] > 0) ? AcademicTerm::find($inputs['academic_term_id'][$i]) : new AcademicTerm();
                $academic_term->academic_term = $inputs['academic_term'][$i];
                $academic_term->status = $inputs['status'][$i];
                $academic_term->academic_year_id = $inputs['academic_year_id'][$i];
                $academic_term->term_type_id = $inputs['term_type_id'][$i];
                $academic_term->term_begins = $inputs['term_begins'][$i];
                $academic_term->term_ends = $inputs['term_ends'][$i];
                if($academic_term->save()){
                    $count = $count+1;
                }
            }
            // Set the flash message
            if($count > 0) $this->setFlashMessage($count . ' Academic Term has been successfully updated.', 1);
        }
        // redirect to the create a new inmate page
        return redirect('/academic-terms');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $academic_term = AcademicTerm::findOrFail($id);
        //Delete The Record
        $delete = ($academic_term !== null) ? $academic_term->delete() : null;

        if($delete){
            $this->setFlashMessage('  Deleted!!! '.$academic_term->academic_term.' Academic Term have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }
}
