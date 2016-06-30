<?php

namespace App\Http\Controllers\Admin\Assessments;

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
                    "classroom"=>$exam->subjectClassroom()->first()->classRoom()->first()->classroom,
                    "subject"=>$exam->subjectClassroom()->first()->subject()->first()->subject,
                    "exam_id"=>$exam->exam_id,
                    "hashed_id"=>$this->getHashIds()->encode($exam->exam_id),
                    "academic_term"=>$exam->subjectClassroom()->first()->academicTerm()->first()->academic_term,
                    "tutor"=>($exam->subjectClassroom()->first()->tutor()->first()) ? $exam->subjectClassroom()->first()->tutor()->first()->fullNames() : '<span class="label label-danger">nil</span>',
                    "marked"=>($exam->marked == 1) ? '<span class="label label-success">Marked</span>' : '<span class="label label-danger">Not Marked</span>',
                );
            }
            $response['flag'] = 1;
            $response['Exam'] = isset($res) ? $res : [];
        }
        echo json_encode($response);
    }

    /**
     * Displays the details of the subjects and the number of assessments
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
}
