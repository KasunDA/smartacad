<?php

namespace App\Http\Controllers\Admin\Assessments;

use App\Models\Admin\Accounts\Staff;
use App\Models\Admin\Assessments\Assessment;
use App\Models\Admin\Assessments\AssessmentDetail;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetup;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetupDetail;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use App\Models\Admin\Users\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AssessmentsController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('Select Academic Year', '');
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('Select Class Level', '');
        return view('admin.assessments.index', compact('academic_years', 'classlevels'));
    }

    /**
     * Search for Subjects Assigned To Logged In Staff
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSubjectAssigned(Request $request)
    {
        $inputs = $request->all();
        $response = array();
        $response['flag'] = 0;
        $user_id = Auth::user()->user_id;

        if($inputs['classlevel_id'] > 0){
            $class_subjects = SubjectClassRoom::where('tutor_id', $user_id)->where('academic_term_id', $inputs['academic_term_id'])
            ->whereIn('classroom_id', ClassRoom::where('classlevel_id', $inputs['classlevel_id'])->lists('classroom_id')->toArray())->get();
        }else{
            $class_subjects = SubjectClassRoom::where('tutor_id', $user_id)->where('academic_term_id', $inputs['academic_term_id'])->get();
        }
        if(isset($class_subjects)){
            foreach($class_subjects as $class_subject){
                $res[] = array(
                    "classroom"=>$class_subject->classRoom()->first()->classroom,
                    "subject"=>$class_subject->subject()->first()->subject,
                    "subject_classroom_id"=>$class_subject->subject_classroom_id,
                    "hashed_id"=>$this->getHashIds()->encode($class_subject->subject_classroom_id),
                    "academic_term"=>$class_subject->academicTerm()->first()->academic_term,
                    "tutor"=>($class_subject->tutor()->first()) ? $class_subject->tutor()->first()->fullNames() : '<span class="label label-danger">nil</span>',
                );
            }
            $response['flag'] = 1;
            $response['ClassSubjects'] = isset($res) ? $res : [];
        }
        echo json_encode($response);
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
