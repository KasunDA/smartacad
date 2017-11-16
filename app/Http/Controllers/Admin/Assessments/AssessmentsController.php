<?php

namespace App\Http\Controllers\Admin\Assessments;

use App\Helpers\LabelHelper;
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
    public function index()
    {
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id')
            ->prepend('- Select Academic Year -', '');
        $classlevels = ClassLevel::pluck('classlevel', 'classlevel_id')
            ->prepend('- Select Class Level -', '');

        return view('admin.assessments.index', compact('academic_years', 'classlevels'));
    }

    /**
     * Search for Subjects Assigned To Logged In Staff
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function subjectAssigned(Request $request)
    {
        $inputs = $request->all();
        $response = array();
        $response['flag'] = 0;
        $user_id = (Auth::user()->user_type_id == User::DEVELOPER) ? null : Auth::user()->user_id;

        //Filter by classlevel if its selected else only by classroom
        if($inputs['classlevel_id'] > 0){
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['academic_term_id'])
                ->whereIn(
                    'classroom_id', 
                    ClassRoom::where('classlevel_id', $inputs['classlevel_id'])
                        ->pluck('classroom_id')
                        ->toArray()
                )
                ->where(function ($query) use ($user_id) {
                    //If its not a developer admin filter by the logged in user else return all records in the class level
                    if($user_id) $query->where('tutor_id', $user_id);
                })
                ->get();
        }else{

            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['academic_term_id'])
                ->where(function ($query) use ($user_id) {
                    //If its not a developer admin filter by the logged in user else return all records in the class room
                    if($user_id) $query->where('tutor_id', $user_id);
                })
                ->get();
        }

        //format the record sets as json readable
        if(isset($class_subjects)){
            foreach($class_subjects as $class_subject){
                $res[] = array(
                    "classroom"=> ($class_subject->classroom()->first()) ? $class_subject->classroom()->first()->classroom : LabelHelper::danger(),
                    "subject"=> ($class_subject->subject()->first()) ? $class_subject->subject()->first()->subject : LabelHelper::danger(),
                    "subject_classroom_id"=>$class_subject->subject_classroom_id,
                    "hashed_id"=>$this->getHashIds()->encode($class_subject->subject_classroom_id),
                    "academic_term"=>$class_subject->academicTerm()->first()->academic_term,
                    "tutor"=>($class_subject->tutor()->first()) ? $class_subject->tutor()->first()->fullNames() : LabelHelper::danger(),
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
    public function subjectDetails($encodeId)
    {
        $subject = SubjectClassRoom::findOrFail($this->decode($encodeId));
        $assessment_setup = AssessmentSetup::where('academic_term_id', $subject->academic_term_id)
            ->where('classgroup_id', $subject->classRoom()->first()->classLevel()->first()->classGroup()->first()->classgroup_id)
            ->first();

        return view('admin.assessments.subject-details', compact('subject', 'assessment_setup'));
    }

    /**
     * Displays the details of the subjects and the number of assessments
     * @param String $setup_id
     * @param String $subject_id
     * @param Boolean $view
     * @return \Illuminate\View\View
     */
    public function inputScores($setup_id, $subject_id, $view = false)
    {
        $setup_detail_id = $this->getHashIds()->decode($setup_id)[0];
        $subject_classroom_id = $this->getHashIds()->decode($subject_id)[0];
        $subject = SubjectClassRoom::findOrFail($subject_classroom_id);
        $setup_detail = AssessmentSetupDetail::findOrFail($setup_detail_id);
        $assessment = Assessment::where('assessment_setup_detail_id', $setup_detail_id)
            ->where('subject_classroom_id', $subject_classroom_id)
            ->first();

        if(!$view){
            if(empty($assessment)){
                //Insert New
                $assessment = new Assessment();
                $assessment->assessment_setup_detail_id = $setup_detail_id;
                $assessment->subject_classroom_id = $subject_classroom_id;

                if ($assessment->save()) {
                    Assessment::populatedAssessmentDetails($assessment->assessment_id);
                }
            }else{
                //Update
                Assessment::populatedAssessmentDetails($assessment->assessment_id);
            }
        }

        return view('admin.assessments.input-scores', compact('assessment', 'subject', 'setup_detail', 'view'));
    }

    /**
     * Displays the details of the subjects and the number of assessments
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveInputScores(Request $request)
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
    public function searchStudents(Request $request)
    {
        $inputs = $request->all();
        $students = (isset($inputs['view_classroom_id']))
            ? StudentClass::where('academic_year_id', $inputs['view_academic_year_id'])
                ->where('classroom_id', $inputs['view_classroom_id'])
                ->get()
            : [];

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
    public function reportDetails($encodeStud, $encodeTerm)
    {
        $student = Student::findOrFail($this->decode($encodeStud));
        $term = AcademicTerm::findOrFail($this->decode($encodeTerm));
        $classroom = $student->currentClass($term->academicYear->academic_year_id);

        $assessments = AssessmentDetailView::orderBy('subject_classroom_id')
            ->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $classroom->classroom_id)
            ->get();

        $subjectClasses = AssessmentDetailView::orderBy('subject_classroom_id')
            ->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $classroom->classroom_id)
            ->distinct()
            ->get(['subject_classroom_id']);

        $setup = AssessmentSetup::where('academic_term_id', $term->academic_term_id)
            ->where('classgroup_id', $classroom->classLevel()->first()->classgroup_id)
            ->first();
        
        $setup_details = ($setup) ? $setup->assessmentSetupDetails()->orderBy('number') : false;

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
    public function printReport($encodeStud, $encodeTerm)
    {
        $student = Student::findOrFail($this->decode($encodeStud));
        $term = AcademicTerm::findOrFail($this->decode($encodeTerm));
        $classroom = $student->currentClass($term->academicYear->academic_year_id);

        $assessments = AssessmentDetailView::orderBy('subject_classroom_id')
            ->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $classroom->classroom_id)
            ->get();
        $subjectClasses = AssessmentDetailView::orderBy('subject_classroom_id')
            ->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $classroom->classroom_id)
            ->distinct()
            ->get(['subject_classroom_id']);

        $setup = AssessmentSetup::where('academic_term_id', $term->academic_term_id)
            ->where('classgroup_id', $classroom->classLevel()->first()->classgroup_id)
            ->first();
        $setup_details = $setup->assessmentSetupDetails()->orderBy('number');

        return view('admin.assessments.print-report', compact('student', 'assessments', 'term', 'classroom', 'setup_details', 'subjectClasses'));
    }

    /**
     * Displays the summary of students assessments ever taken
     * @param String $encodeStud
     * @return \Illuminate\View\View
     */
    public function view($encodeStud)
    {
        $student = Student::findOrFail($this->decode($encodeStud));
        $assessments = AssessmentDetailView::orderBy('assessment_id', 'desc')
            ->where('student_id', $student->student_id)
            ->groupBy(['student_id', 'academic_term'])
            ->get();

        return view('admin.accounts.students.assessment.view', compact('student', 'assessments'));
    }

    /**
     * Displays the details of students assessments based on class and term
     * @param String $encodeStud
     * @param String $encodeTerm
     * @return \Illuminate\View\View
     */
    public function details($encodeStud, $encodeTerm)
    {
        $student = Student::findOrFail($this->decode($encodeStud));
        $term = AcademicTerm::findOrFail($this->decode($encodeTerm));
        $classroom = $student->currentClass($term->academicYear->academic_year_id);

        $assessments = AssessmentDetailView::orderBy('subject_classroom_id')
            ->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $classroom->classroom_id)
            ->get();
        $subjectClasses = AssessmentDetailView::orderBy('subject_classroom_id')
            ->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $classroom->classroom_id)
            ->distinct()
            ->get(['subject_classroom_id']);

        $setup = AssessmentSetup::where('academic_term_id', $term->academic_term_id)
            ->where('classgroup_id', $classroom->classLevel()->first()->classgroup_id)
            ->first();

        $setup_details = ($setup) ? $setup->assessmentSetupDetails()->orderBy('number') : false;

        return view('admin.accounts.students.assessment.details',
            compact('student', 'assessments', 'term', 'classroom', 'setup_details', 'subjectClasses')
        );
    }
}
