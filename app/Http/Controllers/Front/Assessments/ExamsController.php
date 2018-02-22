<?php

namespace App\Http\Controllers\Front\Assessments;

use App\Helpers\LabelHelper;
use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\Exams\Exam;
use App\Models\Admin\Exams\ExamDetailView;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Subjects\CustomSubject;
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
    public function index()
    {
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id')
            ->prepend('- Select Academic Year -', '');

        return view('front.assessments.exams.index', compact('academic_years'));
    }

    /**
     * Search For Students in a classroom for an academic term
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function searchStudents(Request $request)
    {
        $inputs = $request->all();
        $response = array();
        $response['flag'] = 0;
        $output = [];
        $myStudents = Auth::user()
            ->students()
            ->get(['student_id'])
            ->toArray();

        $students = (isset($inputs['academic_year_id']))
            ? StudentClass::where('academic_year_id', $inputs['academic_year_id'])
                ->whereIn('student_id', $myStudents)
                ->get()
            : [];

        if (count($students) > 0) {
            foreach ($students as $student) {
                $class = ($student->student()->first()->currentClass($inputs['academic_year_id']))
                    ? $student->student()
                        ->first()
                        ->currentClass($inputs['academic_year_id'])
                    : null;

                $re = ResultChecker::where('student_id', $student->student_id)
                    ->where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)
                    ->where(function ($query) use ($class) {
                        if($class) $query->where('classroom_id', $class->classroom_id);
                    })
                    ->count();

                $object = new stdClass();
                $object->student_id = $student->student_id;
                $object->hashed_stud = $this->encode($student->student_id);
                $object->hashed_term = $this->encode($inputs['academic_term_id']);
                $object->student_no = $student->student()->first()->student_no;
                $object->name = $student->student()->first()->fullNames();
                $object->gender = $student->student()->first()->gender;
                $object->classroom = $class->classroom;
                $object->status = ($re > 0)
                    ? LabelHelper::success('Activated')
                    : LabelHelper::danger('Not Activated');
                $output[] = $object;
            }
            //Sort The Students by name
            usort($output, function($a, $b) {
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
    public function terminalResult($encodeStud, $encodeTerm, $type=null)
    {
        $student = Student::findOrFail($this->decode($encodeStud));
        $term = AcademicTerm::findOrFail($this->decode($encodeTerm));
        $classroom = $student->currentClass($term->academicYear->academic_year_id);

        $position = Exam::terminalClassPosition(
            $term->academic_term_id,
            $classroom->classroom_id,
            $student->student_id
        );
        $position = (object) array_shift($position);

        $exams = ExamDetailView::where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $classroom->classroom_id)
            ->where('student_id', $student->student_id)
            ->where('marked', 1)
            ->get();
        $groups = CustomSubject::roots()
            ->where('classgroup_id', $classroom->classlevel->classgroup_id)
            ->get();

        return ($type)
            ? view('front.assessments.terminal.print',
                compact('student', 'groups', 'exams', 'term', 'position', 'classroom')
            )
            : view('front.assessments.terminal.student',
                compact('student', 'groups', 'exams', 'term', 'position', 'classroom')
            );
    }

    /**
     * Verify if a result has been checked before or not
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function verify(Request $request)
    {
//        $inputs = $request->all();
//        $student = Student::findOrFail($this->decode($inputs['student_id']));
//        $term = AcademicTerm::findOrFail($this->decode($inputs['term_id']));
//        $classroom = $student->currentClass($term->academicYear->academic_year_id);
//
//        $check = ResultChecker::where('student_id', $student->student_id)
//            ->where('academic_term_id', $term->academic_term_id)
//            ->where('classroom_id', $classroom->classroom_id)
//            ->count();

        //return response()->json(($check > 0) ? true : false);
        return response()->json(true);
    }

    /**
     * Verify if a result has been checked before or not
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function resultChecker(Request $request)
    {
        $inputs = $request->all();
        $response['flag'] = false;
        $student = Student::findOrFail($this->decode($inputs['student_id']));
        $term = AcademicTerm::findOrFail($this->decode($inputs['academic_term_id']));
        $classroom = $student->currentClass($term->academicYear()->first()->academic_year_id);
        $inp = $inputs['serial_number'];

        if (count($inp) != 20) {
            $response['msg'] = 'Incomplete Serial Number Entered!!! Carefully check and retry again.';
        }

        $p = substr($inp, 0, 12);
        $s = substr($inp, 12);

        $pin = '';
        $space = (PinNumber::SPACING > 0) ? (PinNumber::NUMBER_OF_DIGITS / PinNumber::SPACING) : 4;

        for ($k=0; $k < $space; $k++) {
            $pin .= substr($p, ($k * $space), $space) . ' ';
        }
        
        $serial = substr($s, 0, 4) . ' ' . substr($s, 4, 4);
        $pinNo = PinNumber::where('serial_number', trim($serial))
            ->where('pin_number', trim($pin))
            ->where('status', 1)
            ->first();

        if (count($pinNo) > 0) {
            ResultChecker::create([
                'pin_number_id'=>$pinNo->pin_number_id,
                'student_id'=>$student->student_id,
                'academic_term_id'=>$term->academic_term_id,
                'classroom_id'=>$classroom->classroom_id
            ]);
            $pinNo->status = 0;
            $pinNo->save();
            
            $response['flag'] = true;
            $response['url'] = $inputs['student_id'] . '/' . $inputs['academic_term_id'];
            $this->setFlashMessage(
                $student->fullNames() . ' Exams Results has been activated for ' 
                    . $term->academic_term.' Academic Year', 1
            );
        } else {
            $response['msg'] = 'Invalid Card Serial Number or Pin Number';
        }
        
        return response()->json($response);
    }
}
