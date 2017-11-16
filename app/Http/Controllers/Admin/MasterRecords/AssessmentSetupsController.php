<?php

namespace App\Http\Controllers\Admin\MasterRecords;

use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetup;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetupDetail;
use App\Models\Admin\MasterRecords\Classes\ClassGroup;
use App\Models\School\School;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AssessmentSetupsController extends Controller
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

        if ($this->school->setup == School::ASSESSMENT) {
            $this->setFlashMessage('Warning!!! Kindly Setup the Assessments records Before Proceeding.', 3);
        } elseif ($this->school->setup == School::ASSESSMENT_DETAIL) {
            $this->setFlashMessage('Warning!!! Kindly Setup the Assessment Details records Before Proceeding.', 3);
        } else {
            $this->middleware('setup');
        }
    }
    
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @param Boolean $year_id
     * @return Response
     */
    public function index($year_id=false)
    {
        $academic_year = ($year_id) ? AcademicYear::findOrFail($this->decode($year_id)) : AcademicYear::activeYear();
        $assessment_setups = AssessmentSetup::whereIn(
            'academic_term_id', $academic_year->academicTerms()
            ->pluck('academic_term_id')
            ->toArray()
        )->get();

        $classgroups = ClassGroup::pluck('classgroup', 'classgroup_id')
            ->prepend('- Class Group -', '');
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id')
            ->prepend('- Academic Year -', '');

        $academic_terms = $academic_year->academicTerms()
            ->orderBy('term_type_id')
            ->pluck('academic_term', 'academic_term_id')
            ->prepend('- Academic Term -', '');

        return view('admin.master-records.assessment-setups.index',
            compact('academic_terms', 'academic_years', 'academic_year', 'classgroups', 'assessment_setups')
        );
    }

    /**
     * Get The Assessments Setups Given the academic year id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function academicYears(Request $request)
    {
        $inputs = $request->all();
        
        return redirect('/assessment-setups/' . $this->encode($inputs['academic_year_id']));
    }


    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for ($i = 0; $i < count($inputs['assessment_setup_id']); $i++) {
            $assessment_setup = ($inputs['assessment_setup_id'][$i] > 0)
                ? AssessmentSetup::find($inputs['assessment_setup_id'][$i])
                : new AssessmentSetup();
            $assessment_setup->assessment_no = $inputs['assessment_no'][$i];
            $assessment_setup->classgroup_id = $inputs['classgroup_id'][$i];
            $assessment_setup->academic_term_id = $inputs['academic_term_id'][$i];

            if ($assessment_setup->save()) {
                $count = $count+1;
            }
        }
        //Update The Setup Process
        if ($this->school->setup == School::ASSESSMENT){
            $this->school->setup = School::ASSESSMENT_DETAIL;
            $this->school->save();

            return redirect('/assessment-setups/details');
        }

        if($count > 0) $this->setFlashMessage($count . ' Assessment Setups has been successfully updated.', 1);

        return redirect('/assessment-setups');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function delete($id)
    {
        $assessment_setup = AssessmentSetup::findOrFail($id);

        ($assessment_setup !== null && $assessment_setup->delete())
            ? $this->setFlashMessage('  Deleted!!! '.$assessment_setup->academic_term.' Assessment Setup have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }

    /**
     * Display a listing of the Menus for Master Records.
     * @param Boolean $term
     * @param Boolean $year
     *
     * @return Response
     */
    public function details($term=false, $year=false)
    {
        $academic_term = ($term)
            ? AcademicTerm::findOrFail($this->decode($term))
            : AcademicTerm::activeTerm();
        $academic_year = ($year)
            ? AcademicYear::findOrFail($this->decode($year))
            : AcademicYear::activeYear();
        
        $assessment_setups = $academic_term->assessmentSetups()->get();
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id')
            ->prepend('- Academic Year -', '');

        return view('admin.master-records.assessment-setups.detail',
            compact('academic_years', 'academic_terms', 'assessment_setups', 'academic_term', 'academic_year')
        );
    }

    /**
     * Get The Assessment Details Given the class term id
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function terms(Request $request)
    {
        $inputs = $request->all();
        $year = $this->encode($inputs['academic_year_id']);
        $term = $this->encode($inputs['academic_term_id']);

        return redirect('/assessment-setups/details/' . $term . '/' . $year);
    }

    /**
     * Insert or Update the menu records
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveDetails(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for ($i = 0; $i < count($inputs['assessment_setup_detail_id']); $i++) {
            $assessment_detail = ($inputs['assessment_setup_detail_id'][$i] > 0)
                ? AssessmentSetupDetail::find($inputs['assessment_setup_detail_id'][$i])
                : new AssessmentSetupDetail();

            $assessment_detail->number = $inputs['number'][$i];
            $assessment_detail->weight_point = $inputs['weight_point'][$i];
            $assessment_detail->percentage = $inputs['percentage'][$i];
            $assessment_detail->assessment_setup_id = $inputs['assessment_setup_id'][$i];
            $assessment_detail->submission_date = ($inputs['submission_date'][$i]) ? $inputs['submission_date'][$i] : null;
            $assessment_detail->description = ($inputs['description'][$i]) ? $inputs['description'][$i] : null;

            if ($assessment_detail->save()) {
                $count = $count+1;
            }
        }

        //Update The Setup Process
        if ($this->school->setup == School::ASSESSMENT_DETAIL){
            $this->school->setup = School::GRADE;
            $this->school->save();

            return redirect('/grades');
        }
        if($count > 0) $this->setFlashMessage($count . ' Assessment Setups Details has been successfully updated.', 1);

        return redirect()->back();
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function deleteDetails($id, $group_id)
    {
        $assessment_detail = AssessmentSetupDetail::findOrFail($id);
        $delete = ($assessment_detail !== null) ? $assessment_detail->delete() : null;

        if ($delete) {
            $setup = $assessment_detail->assessmentSetup->where('classgroup_id', $group_id)->first();
            $setup->assessment_no = $setup->assessment_no - 1;
            $setup->save();
            $this->setFlashMessage('  Deleted!!! An Assessment Setup Detail have been deleted.', 1);
        } else {
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }
}
