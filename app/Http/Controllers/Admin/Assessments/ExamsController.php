<?php

namespace App\Http\Controllers\Admin\Assessments;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\Exams\Exam;
use App\Models\Admin\Exams\ExamDetail;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
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
            Exam::processExam($term->academic_term_id);
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
        // TOD:: remove the user_id 6 after testing
        $user_id = Auth::user()->user_id;

        if($inputs['classlevel_id'] > 0){
            $class_subjects = SubjectClassRoom::where('tutor_id', $user_id)->where('academic_term_id', $inputs['academic_term_id'])
                ->whereIn('classroom_id', ClassRoom::where('classlevel_id', $inputs['classlevel_id'])->lists('classroom_id')->toArray())->lists('subject_classroom_id')->toArray();
        }else{
            $class_subjects = SubjectClassRoom::where('tutor_id', $user_id)->where('academic_term_id', $inputs['academic_term_id'])->lists('subject_classroom_id')->toArray();
        }
        if(isset($class_subjects)){
            $exams = Exam::whereIn('subject_classroom_id', $class_subjects)->get();
            foreach($exams as $exam){
                $res[] = array(
                    "ca_wp"=>$exam->subjectClassroom()->first()->classRoom()->first()->classLevel()->first()->classGroup()->first()->ca_weight_point,
                    "exam_wp"=>$exam->subjectClassroom()->first()->classRoom()->first()->classLevel()->first()->classGroup()->first()->exam_weight_point,
                    "classroom"=>$exam->subjectClassroom()->first()->classRoom()->first()->classroom,
                    "subject"=>$exam->subjectClassroom()->first()->subject()->first()->subject,
                    "exam_id"=>$exam->exam_id,
                    "hashed_id"=>$this->getHashIds()->encode($exam->exam_id),
                    "academic_term"=>$exam->subjectClassroom()->first()->academicTerm()->first()->academic_term,
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
            $this->setFlashMessage($count . ' Students Scores has been successfully inputted.', 1);
        }

        // redirect to the create a new inmate page
        return redirect('/exams/input-scores/'.$this->getHashIds()->encode($exam->exam_id));
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
        $class_id = $student->currentClass($term->academicYear->academic_year_id)->classroom_id;

        $position = Exam::terminalClassPosition($term->academic_term_id, $class_id, $student->student_id);
        $position = (object) array_shift($position);
//        $subjects = $student->subjectClassRooms()->where('academic_term_id', $term->academic_term_id)->where('classroom_id', $class_id)->get();
        $subjects = SubjectClassRoom::where('academic_term_id', $term->academic_term_id)->where('classroom_id', $class_id)->get();

        return view('admin.assessments.exams.terminal.student', compact('student', 'subjects', 'term', 'position'));
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
}
