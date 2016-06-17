<?php

namespace App\Http\Controllers\Admin\Assessments;

use App\Models\Admin\Assessments\Assessment;
use App\Models\Admin\Assessments\AssessmentDetail;
use App\Models\Admin\Exams\Exam;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetup;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetupDetail;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ExamsController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     * @return Response
     */
//    public function getIndex()
//    {
//        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('Select Academic Year', '');
//        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('Select Class Level', '');
//        return view('admin.assessments.exams.setup', compact('academic_years', 'classlevels', 'tutors'));
//    }

    /**
     * Display a listing of the Menus for Master Records.
     * @return Response
     */
    public function getSetup()
    {
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('Select Academic Year', '');
        return view('admin.assessments.exams.setup', compact('academic_years', 'classlevels', 'tutors'));
    }

    /**
     * Validate if the exam has been setup
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postValidateSetup(Request $request)
    {
        $inputs = $request->all();
        $term = AcademicTerm::findOrFail($inputs['academic_term_id']);
        $response = [];

        if ($term->exam_status_id == 1) {
            $output = ' Exam for ' . $term->academic_term . ' Has Already Been Setup By ' . $term->examSetupBy()->first()->fullNames();
            $output .= ' on: ' . $term->exam_setup_date->format('D jS, M Y') . '. <strong>Do You Want To Continue anyway?</strong>';
            $response['flag'] = 1;
        }else{
            $output = '<h4>Make sure all assessments has been inputted before setting up an exam . <strong>Do You Want To Continue anyway?</strong></h4>';
            $response['flag'] = 2;
        }

        $response['output'] = $output;
        $response['term'] = $inputs['academic_term_id'];
        return response()->json($response);
    }

    /**
     * Process Exam Set Up
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSetup(Request $request)
    {
        $inputs = $request->all();
        $term = AcademicTerm::findOrFail($inputs['academic_term_id']);
        $term->exam_status_id = 1;
        $term->exam_setup_by = Auth::user()->user_id;
        $term->exam_setup_date = date('Y-m-d');
        if($term->save()){
            //Update
//            Exam::processExam($term->academic_term_id);
            $this->setFlashMessage('Exams for ' . $term->academic_term . ' Academic Term has been successfully setup.', 1);
        }

        return response()->json($term);
    }


    /**
     * Displays the details of the subjects and the number of assessments
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getSubjectDetails($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);
        $subject = (empty($decodeId)) ? abort(305) : SubjectClassRoom::findOrFail($decodeId[0]);
        $assessment_setup = AssessmentSetup::where('academic_term_id', $subject->academic_term_id)
            ->where('classgroup_id', $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->classgroup_id)->first();

        return view('admin.assessments.subject-details', compact('subject', 'assessment_setup'));
    }

    /**
     * Displays the details of the subjects and the number of assessments
     * @param String $setup_id
     * @param String $subject_id
     * @return \Illuminate\View\View
     */
    public function getInputScores($setup_id, $subject_id)
    {
        $setup_detail_id = $this->getHashIds()->decode($setup_id)[0];
        $subject_classroom_id = $this->getHashIds()->decode($subject_id)[0];
        $subject = SubjectClassRoom::findOrFail($subject_classroom_id);
        $setup_detail = AssessmentSetupDetail::findOrFail($setup_detail_id);
        $assessment = Assessment::where('assessment_setup_detail_id', $setup_detail_id)->where('subject_classroom_id', $subject_classroom_id)->first();

        if(empty($assessment)){
            //Insert New
            $assessment = new Assessment();
            $assessment->assessment_setup_detail_id = $setup_detail_id;
            $assessment->subject_classroom_id = $subject_classroom_id;
            if($assessment->save()){
                Assessment::populatedAssessmentDetails($assessment->assessment_id);
            }
        }else{
            //Update
            Assessment::populatedAssessmentDetails($assessment->assessment_id);
        }

        return view('admin.assessments.input-scores', compact('assessment', 'subject', 'setup_detail'));
    }

    /**
     * Displays the details of the subjects and the number of assessments
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postInputScores(Request $request)
    {
        $inputs = $request->all();
        $count = 0;
        $assessment = Assessment::findOrFail($inputs['assessment_id']);

        for($i = 0; $i < count($inputs['assessment_detail_id']); $i++){
            $detail = AssessmentDetail::find($inputs['assessment_detail_id'][$i]);
            $detail->score = $inputs['score'][$i];
            if($detail->save()){
                $count = $count+1;
            }
        }
        // Set the flash message
        if($count > 0){
            $assessment->marked = 1;
            $assessment->save();
            $this->setFlashMessage($count . ' Students Scores has been successfully inputted.', 1);
        }

        // redirect to the create a new inmate page
        return redirect('/assessments/subject-details/'.$this->getHashIds()->encode($assessment->subject_classroom_id));
    }
}
