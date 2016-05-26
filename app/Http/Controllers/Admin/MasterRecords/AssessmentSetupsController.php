<?php

namespace App\Http\Controllers\Admin\MasterRecords;

use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetup;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetupDetail;
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
        $count = 0;

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
     * @param String $encodeId
     * @return Response
     */
    public function getDetails($encodeId=null)
    {
        $academic_term = ($encodeId === null) ? AcademicTerm::activeTerm() : AcademicTerm::findOrFail($this->getHashIds()->decode($encodeId)[0]);
        $assessment_setups = AssessmentSetup::where('academic_term_id', $academic_term->academic_term_id)->get();
        $academic_terms = AcademicTerm::lists('academic_term', 'academic_term_id')->prepend('Select Academic Term', '');

        return view('admin.master-records.assessment-setups.detail', compact('academic_terms', 'assessment_setups', 'academic_term'));
    }

    /**
     * Get The Assessment Details Given the class term id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postTerms(Request $request)
    {
        $inputs = $request->all();
        return redirect('/assessment-setups/details/' . $this->getHashIds()->encode($inputs['academic_term_id']));
    }

    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postDetails(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for($i = 0; $i < count($inputs['assessment_setup_detail_id']); $i++){
            $assessment_detail = ($inputs['assessment_setup_detail_id'][$i] > 0)
                ? AssessmentSetupDetail::find($inputs['assessment_setup_detail_id'][$i]) : new AssessmentSetupDetail();
            $assessment_detail->number = $inputs['number'][$i];
            $assessment_detail->weight_point = $inputs['weight_point'][$i];
            $assessment_detail->percentage = $inputs['percentage'][$i];
            $assessment_detail->assessment_setup_id = $inputs['assessment_setup_id'][$i];
            $assessment_detail->submission_date = ($inputs['submission_date'][$i]) ? $inputs['submission_date'][$i] : null;
            $assessment_detail->description = ($inputs['description'][$i]) ? $inputs['description'][$i] : null;
            if($assessment_detail->save()){
                $count = $count+1;
            }
        }
        // Set the flash message
        if($count > 0) $this->setFlashMessage($count . ' Assessment Setups Details has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/assessment-setups/details');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function getDetailsDelete($id, $classgroup_id)
    {
        $assessment_detail = AssessmentSetupDetail::findOrFail($id);
        //Delete The Record
        $delete = ($assessment_detail !== null) ? $assessment_detail->delete() : null;

        if($delete){
            $setup = $assessment_detail->assessmentSetup->where('classgroup_id', $classgroup_id)->first();
            $setup->assessment_no = $setup->assessment_no - 1;
            $setup->save();
            $this->setFlashMessage('  Deleted!!! An Assessment Setup Detail have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }
}