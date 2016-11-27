<?php

namespace App\Http\Controllers\Front\Assessments;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\Exams\Exam;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use App\Models\Admin\PinNumbers\PinNumber;
use App\Models\Admin\PinNumbers\ResultChecker;
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
        return view('front.assessments.exams.index', compact('academic_years'));
    }

    /**
     * Search For Students in a classroom for an academic term
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSearchStudents(Request $request)
    {
        $inputs = $request->all();
        $response = array();
        $response['flag'] = 0;
        $output = [];
        $myStudents = Auth::user()->students()->get(['student_id'])->toArray();

        $students = (isset($inputs['academic_year_id']))
            ? StudentClass::where('academic_year_id', $inputs['academic_year_id'])->whereIn('student_id', $myStudents)->get() : [];

        if(count($students) > 0){
            foreach ($students as $student){
                $object = new stdClass();
                $object->student_id = $student->student_id;
                $object->hashed_stud = $this->getHashIds()->encode($student->student_id);
                $object->hashed_term = $this->getHashIds()->encode($inputs['academic_term_id']);
                $object->student_no = $student->student()->first()->student_no;
                $object->name = $student->student()->first()->fullNames();
                $object->gender = $student->student()->first()->gender;
                $object->classroom = $student->classRoom()->first()->classroom;
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
     * @param String $type
     * @return \Illuminate\View\View
     */
    public function getTerminalResult($encodeStud, $encodeTerm, $type=null)
    {
        $decodeStud = $this->getHashIds()->decode($encodeStud);
        $decodeTerm = $this->getHashIds()->decode($encodeTerm);
        $student = (empty($decodeStud)) ? abort(305) : Student::findOrFail($decodeStud[0]);
        $term = (empty($decodeTerm)) ? abort(305) : AcademicTerm::findOrFail($decodeTerm[0]);
        $classroom = $student->currentClass($term->academicYear->academic_year_id);

        $position = Exam::terminalClassPosition($term->academic_term_id, $classroom->classroom_id, $student->student_id);
        $position = (object) array_shift($position);
//        $subjects = $student->subjectClassRooms()->where('academic_term_id', $term->academic_term_id)->where('classroom_id', $class_id)->get();
        $subjects = SubjectClassRoom::where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)->get();

        if($type) {
            return view('front.assessments.terminal.print', compact('student', 'subjects', 'term', 'position', 'classroom'));
        }else{
            return view('front.assessments.terminal.student', compact('student', 'subjects', 'term', 'position', 'classroom'));
        }
    }

    /**
     * Verify if a result has been checked before or not
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function postVerify(Request $request)
    {
        $inputs = $request->all();

        $decodeStud = $this->getHashIds()->decode($inputs['student_id']);
        $decodeTerm = $this->getHashIds()->decode($inputs['term_id']);
        $student = (empty($decodeStud)) ? abort(305) : Student::findOrFail($decodeStud[0]);
        $term = (empty($decodeTerm)) ? abort(305) : AcademicTerm::findOrFail($decodeTerm[0]);
        $classroom = $student->currentClass($term->academicYear->academic_year_id);

        $check = ResultChecker::where('student_id', $student->student_id)->where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $classroom->classroom_id)->count();

        return response()->json(($check > 0) ? true : false);
    }

    /**
     * Verify if a result has been checked before or not
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function postResultChecker(Request $request)
    {
        $inputs = $request->all();
        $response['flag'] = false;
        $decodeStud = $this->getHashIds()->decode($inputs['student_id']);
        $decodeTerm = $this->getHashIds()->decode($inputs['academic_term_id']);
        $student = (empty($decodeStud)) ? abort(305) : Student::findOrFail($decodeStud[0]);
        $term = (empty($decodeTerm)) ? abort(305) : AcademicTerm::findOrFail($decodeTerm[0]);
        $classroom = $student->currentClass($term->academicYear()->first()->academic_year_id);

        $pin = '';
        $space = (PinNumber::SPACING > 0) ? (PinNumber::NUMBER_OF_DIGITS / PinNumber::SPACING) : 4;
        for($k=0; $k < $space; $k++){
            $pin .= substr($inputs['pin_number'], ($k * $space), $space) . ' ';
        }
        $serial = substr($inputs['serial_number'], 0, 4) . ' ' . substr($inputs['serial_number'], 4, 4);
        $pinNo = PinNumber::where('serial_number', trim($serial))->where('pin_number', trim($pin))->where('status', 1)->first();

        if(count($pinNo) > 0){
            ResultChecker::create([
                'pin_number_id'=>$pinNo->pin_number_id, 'student_id'=>$student->student_id,
                'academic_term_id'=>$term->academic_term_id, 'classroom_id'=>$classroom->classroom_id
            ]);
            $pinNo->status = 0;
            $pinNo->save();
            $response['flag'] = true;
            $response['url'] = $inputs['student_id'] . '/' . $inputs['academic_term_id'];
            $this->setFlashMessage($student->fullNames() . ' Exams Results has been activated for '.$term->academic_term.' Academic Year', 1);
        }
        return response()->json($response);
    }
}
