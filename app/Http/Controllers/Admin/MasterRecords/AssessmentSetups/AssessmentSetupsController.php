<?php

namespace App\Http\Controllers\Admin\MasterRecords\AssessmentSetups;

use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetup;
use App\Models\Admin\MasterRecords\Classes\ClassGroup;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AssessmentSetupsController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $assessment_setups = AssessmentSetup::all();
        $academic_terms = AcademicTerm::lists('academic_term', 'academic_term_id')->prepend('Select Academic Term', '');
        $classgroups = ClassGroup::lists('classgroup', 'classgroup_id')->prepend('Select Class Group', '');
        return view('admin.master-records.assessment-setups.index', compact('academic_terms', 'classgroups', 'assessment_setups'));
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

        for($i = 0; $i < count($inputs['assessment_setup_id']); $i++){
            $assessment_setup = ($inputs['assessment_setup_id'][$i] > 0) ? AssessmentSetup::find($inputs['assessment_setup_id'][$i]) : new AssessmentSetup();
            $assessment_setup->assessment_no = $inputs['assessment_no'][$i];
            $assessment_setup->classgroup_id = $inputs['classgroup_id'][$i];
            $assessment_setup->academic_term_id = $inputs['academic_term_id'][$i];
            if($assessment_setup->save()){
                $count = $count+1;
            }
        }
        // Set the flash message
        if($count > 0) $this->setFlashMessage($count . ' Assessment Setups has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/assessment-setups');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $assessment_setup = AssessmentSetup::findOrFail($id);
        //Delete The Record
        $delete = ($assessment_setup !== null) ? $assessment_setup->delete() : null;

        if($delete){
            $this->setFlashMessage('  Deleted!!! '.$assessment_setup->academic_term.' Assessment Setup have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function getDetail()
    {
        $assessment_setups = AssessmentSetup::all();
        $academic_terms = AcademicTerm::lists('academic_term', 'academic_term_id')->prepend('Select Academic Term', '');
        $classgroups = ClassGroup::lists('classgroup', 'classgroup_id')->prepend('Select Class Group', '');
        return view('admin.master-records.assessment-setups.index', compact('academic_terms', 'classgroups', 'assessment_setups'));
    }
}
