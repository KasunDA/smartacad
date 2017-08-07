<?php

namespace App\Http\Controllers\Admin\Assessments;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\Exams\Exam;
use App\Models\Admin\Exams\ExamDetail;
use App\Models\Admin\Exams\ExamDetailView;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\MasterRecords\Subjects\CustomSubject;
use App\Models\Admin\MasterRecords\Subjects\SubjectAssessmentView;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use App\Models\Admin\RolesAndPermissions\Role;
use App\Models\Admin\Users\User;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use stdClass;

class ExamsController extends Controller
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

        //Keep track of selected tab
        session()->put('active', 'setup-exam');

//        $exam_details = ExamDetail::all();
//        foreach($exam_details as $exam){
//            $ca = mt_rand(5, 38);
//            $ex = mt_rand(10, 58);
//            $exam->ca = ($exam->ca > 5) ? $exam->ca : $ca;
//            $exam->exam = ($exam->exam > 10) ? $exam->exam : $ex;
//            $exam->save();
//        }
        return view('admin.assessments.exams.index', compact('academic_years', 'classlevels'));
    }

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
    public function postValidateAllSetup(Request $request)
    {
        $inputs = $request->all();
        $response = [];
        $term = AcademicTerm::findOrFail($inputs['academic_term_id']);
        $view = SubjectAssessmentView::where('academic_term_id', $term->academic_term_id)
                                        ->where(function ($query) {
                                            $query->whereNull('marked')->orWhere('marked', '<>', '1');
                                        })->count();
        if ($view > 0 ) {
            $output = '<h4>Make sure all assessments has been inputted before setting up an exam . <strong>Do You Want To Continue anyway?</strong></h4>';
            $response['flag'] = 2;
        }else{
            $output = ' Exam for ' . $term->academic_term . ' Has Already Been Setup <strong>Do You Want To Continue anyway?</strong>';
            $response['flag'] = 1;
        }

        $response['output'] = isset($output) ? $output : [];
        $response['term'] = $inputs['academic_term_id'];
        return response()->json($response);
    }

    /**
     * Process Exam Set Up
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postAllSetup(Request $request)
    {
        $inputs = $request->all();
        $term = AcademicTerm::findOrFail($inputs['academic_term_id']);
        if($term){
            //Update
            Exam::processExams($term->academic_term_id);
            session()->put('active', 'setup-exam');
            $this->setFlashMessage('Exams for ' . $term->academic_term . ' Academic Term has been successfully setup.', 1);
        }

        return response()->json($term);
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

        if($inputs['classlevel_id'] > 0){
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['academic_term_id'])
                ->whereIn('classroom_id', ClassRoom::where('classlevel_id', $inputs['classlevel_id'])->lists('classroom_id')->toArray())
                ->where(function ($query) use ($user_id) {
                    //If its not a developer admin filter by the logged in user else return all records in the class level
                    if($user_id)
                        $query->where('tutor_id', $user_id);
                })->lists('subject_classroom_id')->toArray();
        }else{
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['academic_term_id'])
                ->where(function ($query) use ($user_id) {
                    //If its not a developer admin filter by the logged in user else return all records in the class room
                    if($user_id)
                        $query->where('tutor_id', $user_id);
                })->lists('subject_classroom_id')->toArray();
        }
        if(isset($class_subjects)){
            //Get all the exams that are ready (assessments that have been marked completely before exams will be enable for score input)
            $exams = Exam::whereIn('subject_classroom_id', $class_subjects)->get();
            //format the record sets as json readable
            foreach($exams as $exam){
                $subClass = ($exam->subjectClassroom()->first()) ? $exam->subjectClassroom()->first() : false;
                $res[] = array(
                    "ca_wp"=> ($subClass)
                        ? $subClass->classRoom()->first()->classLevel()->first()->classGroup()->first()->ca_weight_point
                        : '<span class="label label-danger">nil</span>',
                    "exam_wp"=>($subClass)
                        ? $subClass->classRoom()->first()->classLevel()->first()->classGroup()->first()->exam_weight_point
                        : '<span class="label label-danger">nil</span>',
                    "classroom"=>($subClass) ? $subClass->classRoom()->first()->classroom : '<span class="label label-danger">nil</span>',
                    "subject"=>($subClass && $subClass->subject()->first()) ? $subClass->subject()->first()->subject : '<span class="label label-danger">nil</span>',
                    "exam_id"=>$exam->exam_id,
                    "hashed_id"=>$this->getHashIds()->encode($exam->exam_id),
                    "academic_term"=>($subClass) ? $subClass->academicTerm()->first()->academic_term : '<span class="label label-danger">nil</span>',
//                    "tutor"=>($exam->subjectClassroom()->first()->tutor()->first()) ? $exam->subjectClassroom()->first()->tutor()->first()->fullNames() : '<span class="label label-danger">nil</span>',
                    "marked"=>($exam->marked == 1) ? '<span class="label label-success">Marked</span>' : '<span class="label label-danger">Not Marked</span>',
                );
            }
            $response['flag'] = 1;
            $response['Exam'] = isset($res) ? $res : [];
        }
        echo json_encode($response);
    }

    /**
     * Displays the details of the subjects students and make provision for inputting scores
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getInputScores($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);
        $exam = (empty($decodeId)) ? abort(305) : Exam::findOrFail($decodeId[0]);
        $subject = ($exam) ? $exam->subjectClassroom()->first() : null;

        $now = Carbon::now('Africa/Lagos');
        $diff = $now->diffInDays(AcademicTerm::activeTerm()->term_ends, false);

        if($diff < 0 && !Auth::user()->hasRole([Role::DEVELOPER, Role::SUPER_ADMIN])){
            $msg = 'Exams for '.$subject->academicTerm()->first()->academic_term
                . ' Academic Year is due therefore editing has been disabled... Do contact your Systems Admin for further complains';
            $this->setFlashMessage($msg);

            return redirect('/exams/view-scores/'.$this->getHashIds()->encode($exam->exam_id));
        }


        return view('admin.assessments.exams.input-scores', compact('exam', 'subject'));
    }

    /**
     * Displays the details of the subject students and their exams scores
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getViewScores($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);
        $exam = (empty($decodeId)) ? abort(305) : Exam::findOrFail($decodeId[0]);
        $subject = ($exam) ? $exam->subjectClassroom()->first() : null;

        return view('admin.assessments.exams.view-scores', compact('exam', 'subject'));
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
        $exam = Exam::findOrFail($inputs['exam_id']);

        for($i = 0; $i < count($inputs['exam_detail_id']); $i++){
            $detail = ExamDetail::find($inputs['exam_detail_id'][$i]);
            $detail->exam = $inputs['exam'][$i];
            if($detail->save()){
                $count = $count+1;
            }
        }
        // Set the flash message
        if($count > 0){
            $exam->marked = 1;
            $exam->save();
            session()->put('active', 'input-scores');
            $this->setFlashMessage($count . ' Students Scores has been successfully inputted.', 1);
        }

        // redirect to the create a new inmate page
        return redirect('/exams/view-scores/'.$this->getHashIds()->encode($exam->exam_id));
    }

    /**
     * Search For Students in a classroom for an academic term
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSearchResults(Request $request)
    {
        $inputs = $request->all();

        $students = (isset($inputs['view_classroom_id']))
            ? StudentClass::where('academic_year_id', $inputs['view_academic_year_id'])->where('classroom_id', $inputs['view_classroom_id'])->get() : [];
        $classrooms = (empty($inputs['view_classroom_id'])) ? ClassRoom::where('classlevel_id', $inputs['view_classlevel_id'])->get() : [];
        $term = AcademicTerm::findOrFail($inputs['view_academic_term_id']);

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

        if(count($classrooms) > 0){
            //All the class rooms in the class level for the academic year
            foreach($classrooms as $classroom){
                $res[] = array(
                    "classroom"=>$classroom->classroom,
                    "hashed_class"=>$this->getHashIds()->encode($classroom->classroom_id),
                    "academic_term"=>$term->academic_term,
                    "hashed_term"=>$this->getHashIds()->encode($term->academic_term_id),
                    "student_count"=>$classroom->studentClasses()->where('academic_year_id', $inputs['view_academic_year_id'])->count()
                );
            }
            $response['flag'] = 2;
            $response['Classrooms'] = isset($res) ? $res : [];
        }
        echo json_encode($response);
    }

    /**
     * Displays the details of the subjects students scores for a specific academic term
     * @param String $encodeStud
     * @param String $encodeTerm
     * @return \Illuminate\View\View
     */
    public function getStudentTerminalResult($encodeStud, $encodeTerm)
    {
        $decodeStud = $this->getHashIds()->decode($encodeStud);
        $decodeTerm = $this->getHashIds()->decode($encodeTerm);
        $student = (empty($decodeStud)) ? abort(305) : Student::findOrFail($decodeStud[0]);
        $term = (empty($decodeTerm)) ? abort(305) : AcademicTerm::findOrFail($decodeTerm[0]);
        $classroom = $student->currentClass($term->academicYear->academic_year_id);

        $position = Exam::terminalClassPosition($term->academic_term_id, $classroom->classroom_id, $student->student_id);
        $position = (object) array_shift($position);

        $exams = ExamDetailView::where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)
            ->where('student_id', $student->student_id)->where('marked', 1)->get();
        $groups = CustomSubject::roots()->where('classgroup_id', $classroom->classlevel->classgroup_id)->get();

        return view('admin.assessments.exams.terminal.student', compact('student', 'term', 'position', 'classroom', 'groups', 'exams'));
    }

    /**
     * Displays the assessment details for the class room students scores for a specific academic term
     * @param String $encodeClass
     * @param String $encodeTerm
     * @return \Illuminate\View\View
     */
    public function getClassroomTerminalResult($encodeClass, $encodeTerm)
    {
        $decodeClass = $this->getHashIds()->decode($encodeClass);
        $decodeTerm = $this->getHashIds()->decode($encodeTerm);
        $classroom = (empty($decodeClass)) ? abort(305) : ClassRoom::findOrFail($decodeClass[0]);
        $term = (empty($decodeTerm)) ? abort(305) : AcademicTerm::findOrFail($decodeTerm[0]);

        $results = Exam::terminalClassPosition($term->academic_term_id, $classroom->classroom_id);
        $exam = (empty($results)) ? null : $results[0];
        $results = (object) $results;

        return view('admin.assessments.exams.terminal.classroom', compact('exam', 'classroom', 'term', 'results'));
    }

    /**
     * Validate if my (Individual Staffs) exam has been setup
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postValidateMySetup(Request $request)
    {
        $inputs = $request->all();
        $response = [];
        $term = AcademicTerm::findOrFail($inputs['setup_academic_term_id']);
        $view = SubjectAssessmentView::where('academic_term_id', $term->academic_term_id)->where('tutor_id', Auth::user()->user_id)
                                        ->where(function ($query) {
                                            $query->whereNull('marked')->orWhere('marked', '<>', '1');
                                        })->count();

        if ($view > 0 ) {
            $output = ' <strong> ' . $view  . ' Assessment(s) for ' . $term->academic_term .
                ' are yet to be marked? </strong>Kindly input the C.A assessment before you can setup your exams';
            $response['flag'] = 2;
        }else{
            $response['flag'] = 1;
            $response['term'] = $term;
        }
        $response['output'] = isset($output) ? $output : [];
        return response()->json($response);
    }

    /**
     * Validate if my (Individual Staffs) exam has been setup then compute the C.A
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getComputeCa()
    {
        $response = [];
        $term = AcademicTerm::activeTerm();
        $view = SubjectAssessmentView::where('academic_term_id', $term->academic_term_id)->where('tutor_id', Auth::user()->user_id)
            ->where(function ($query) {
                $query->whereNull('marked')->orWhere('marked', '<>', '1');
            })->count();

        if ($view > 0 ) {
            $output = ' <strong> ' . $view  . ' Assessment(s) for ' . $term->academic_term .
                ' are yet to be marked? </strong>Kindly input the C.A assessment before they can be computed for exams';
            $this->setFlashMessage($output, 2);
        }else{
            //Compute C.A
            Exam::processAssessmentCA($term->academic_term_id, Auth::user()->user_id);
            $this->setFlashMessage('The C.A has been Computed and Updated accordingly...Proceed with Exams', 1);
            $response['term'] = $term;
        }
        return response()->json($response);
    }

    /**
     * Process My (Individual Staffs) Exam Set Up
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postMySetup(Request $request)
    {
        $inputs = $request->all();
        $term = AcademicTerm::findOrFail($inputs['academic_term_id']);
        if($term){
            //Process Only my exams so as to input scores
            Exam::processExams($term->academic_term_id, Auth::user()->user_id);
            session()->put('active', 'setup-exam');
            $this->setFlashMessage('Your Exams for ' . $term->academic_term . ' Academic Year has been successfully setup.', 1);
        }
        return response()->json($term);
    }

    /**
     * Displays a printable details of the subjects students scores for a specific academic term
     * @param String $encodeStud
     * @param String $encodeTerm
     * @return \Illuminate\View\View
     */
    public function getPrintStudentTerminalResult($encodeStud, $encodeTerm)
    {
        $decodeStud = $this->getHashIds()->decode($encodeStud);
        $decodeTerm = $this->getHashIds()->decode($encodeTerm);
        $student = (empty($decodeStud)) ? abort(305) : Student::findOrFail($decodeStud[0]);
        $term = (empty($decodeTerm)) ? abort(305) : AcademicTerm::findOrFail($decodeTerm[0]);
        $classroom = $student->currentClass($term->academicYear->academic_year_id);

        $position = Exam::terminalClassPosition($term->academic_term_id, $classroom->classroom_id, $student->student_id);
        $position = (object) array_shift($position);
        $exams = ExamDetailView::where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)
            ->where('student_id', $student->student_id)->where('marked', 1)->get();
        $groups = CustomSubject::roots()->where('classgroup_id', $classroom->classlevel->classgroup_id)->get();

//        $pdf = App::make('dompdf.wrapper');
//        $pdf->loadHTML('<h1>Test Oh</h1>');
//        $pdf->loadView('admin.assessments.exams.terminal.print2', $student, $term);
//        $pdf->loadView('admin.assessments.exams.terminal.print2', compact('student', 'term'));
//        $pdf->loadView('admin.assessments.exams.terminal.print', compact('student', 'groups', 'exams', 'term', 'position', 'classroom'));
//        return $pdf->download('resultChecker.pdf');
        
        return view('admin.assessments.exams.terminal.print', compact('student', 'groups', 'exams', 'term', 'position', 'classroom'));
    }
}
