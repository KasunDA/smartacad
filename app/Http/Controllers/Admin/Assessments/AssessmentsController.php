<?php

namespace App\Http\Controllers\Admin\Assessments;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\Assessments\Assessment;
use App\Models\Admin\Assessments\AssessmentDetail;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetup;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetupDetail;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use App\Models\Admin\Users\User;
use App\Models\Admin\Assessments\AssessmentDetailView;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use stdClass;

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
        $user_id = (Auth::user()->user_type_id == User::DEVELOPER) ? null : Auth::user()->user_id;

        //Filter by classlevel if its selected else only by classroom
        if($inputs['classlevel_id'] > 0){
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['academic_term_id'])
                ->whereIn('classroom_id', ClassRoom::where('classlevel_id', $inputs['classlevel_id'])->lists('classroom_id')->toArray())
                ->where(function ($query) use ($user_id) {
                    //If its not a developer admin filter by the logged in user else return all records in the class level
                    if($user_id)
                        $query->where('tutor_id', $user_id);
                })->get();
        }else{
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['academic_term_id'])
                ->where(function ($query) use ($user_id) {
                    //If its not a developer admin filter by the logged in user else return all records in the class room
                    if($user_id)
                        $query->where('tutor_id', $user_id);
                })->get();
        }
        //format the record sets as json readable
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

    /**
     * Search For Students in a classroom for an academic term
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSearchStudents(Request $request)
    {
        $inputs = $request->all();
        $students = (isset($inputs['view_classroom_id']))
            ? StudentClass::where('academic_year_id', $inputs['view_academic_year_id'])->where('classroom_id', $inputs['view_classroom_id'])->get() : [];

        $response = array();
        $response['flag'] = 0;
        $output = [];

        if(count($students) > 0){
            //All the students in the class room for the academic year
            foreach($students as $student){
                $object = new stdClass();
                $object->student_id = $student->student_id;
                $object->hashed_stud = $this->getHashIds()->encode($student->student_id);
                $object->hashed_term = $this->getHashIds()->encode($inputs['view_academic_term_id']);
                $object->student_no = $student->student()->first()->student_no;
                $object->name = $student->student()->first()->fullNames();
                $object->gender = $student->student()->first()->gender;
                $output[] = $object;
            }
            //Sort The Students by name
            usort($output, function($a, $b)
            {
                return strcmp($a->name, $b->name);
            });
            $response['flag'] = 1;
            $response['Students'] = $output;
        }
        echo json_encode($response);
    }

    /**
     * Displays the details of the subjects students scores for a specific academic term
     * @param String $encodeStud
     * @param String $encodeTerm
     * @return \Illuminate\View\View
     */
    public function getReportDetails($encodeStud, $encodeTerm)
    {
        $decodeStud = $this->getHashIds()->decode($encodeStud);
        $decodeTerm = $this->getHashIds()->decode($encodeTerm);
        $student = (empty($decodeStud)) ? abort(305) : Student::findOrFail($decodeStud[0]);
        $term = (empty($decodeTerm)) ? abort(305) : AcademicTerm::findOrFail($decodeTerm[0]);
        $classroom = $student->currentClass($term->academicYear->academic_year_id);
        $assessments = AssessmentDetailView::orderBy('subject_classroom_id')->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)->get();
        $subjectClasses = AssessmentDetailView::orderBy('subject_classroom_id')->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)->distinct()->get(['subject_classroom_id']);
        $setup = AssessmentSetup::where('academic_term_id', $term->academic_term_id)->where('classgroup_id', $classroom->classLevel()->first()->classgroup_id)->first();
        $setup_details = $setup->assessmentSetupDetails()->orderBy('number');

//        $filtered = array_filter($assessments, function($key){
//            return in_array($key, ['subject_classroom_id', 'number']);
//        }, ARRAY_FILTER_USE_KEY);

        return view('admin.assessments.student-details', compact('student', 'assessments', 'term', 'classroom', 'setup_details', 'subjectClasses'));
    }

    /**
     * Displays the details of the subjects students scores for a specific academic term
     * @param String $encodeStud
     * @param String $encodeTerm
     * @return \Illuminate\View\View
     */
    public function getPrintReport($encodeStud, $encodeTerm)
    {
        $decodeStud = $this->getHashIds()->decode($encodeStud);
        $decodeTerm = $this->getHashIds()->decode($encodeTerm);
        $student = (empty($decodeStud)) ? abort(305) : Student::findOrFail($decodeStud[0]);
        $term = (empty($decodeTerm)) ? abort(305) : AcademicTerm::findOrFail($decodeTerm[0]);
        $classroom = $student->currentClass($term->academicYear->academic_year_id);
        $assessments = AssessmentDetailView::orderBy('subject_classroom_id')->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)->get();
        $subjectClasses = AssessmentDetailView::orderBy('subject_classroom_id')->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)->distinct()->get(['subject_classroom_id']);
        $setup = AssessmentSetup::where('academic_term_id', $term->academic_term_id)->where('classgroup_id', $classroom->classLevel()->first()->classgroup_id)->first();
        $setup_details = $setup->assessmentSetupDetails()->orderBy('number');
        return view('admin.assessments.print-report', compact('student', 'assessments', 'term', 'classroom', 'setup_details', 'subjectClasses'));
    }
}
