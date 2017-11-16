<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassMaster;
use App\Models\Admin\MasterRecords\Subjects\SubjectAssessmentView;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;

use App\Http\Requests;
use App\Models\Admin\Users\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;

class DashboardController extends Controller
{
    private $colors;

    /**
     * A list of colors for representing charts
     */
    public function __construct()
    {
        $this->colors = [
            '#FF0F00', '#FF6600', '#FF9E01', '#FCD202', '#F8FF01', '#B0DE09', '#04D215', '#0D8ECF', '#0D52D1', '#2A0CD0', '#8A0CCF',
            '#CD0D74', '#754DEB', '#DDDDDD', '#CCCCCC', '#999999', '#333333', '#000000',
            '#FF0F00', '#FF6600', '#FF9E01', '#FCD202', '#F8FF01', '#B0DE09', '#04D215', '#0D8ECF', '#0D52D1', '#2A0CD0', '#8A0CCF',
            '#CD0D74', '#754DEB', '#DDDDDD', '#CCCCCC', '#999999', '#333333', '#000000',
        ];

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $term = AcademicTerm::activeTerm();
        if(empty($term)){
            $this->setFlashMessage('Warning!!! Kindly Setup the Academic Years and Academic Terms Before Proceeding.', 3);
            return redirect('/academic-terms');
        }

        $sponsors_count = User::where('user_type_id', User::SPONSOR)->count();
        $staff_count = User::where('user_type_id', User::STAFF)->count();
        $students_count = Student::count();

        if(Auth::user()->user_type_id == User::STAFF){
            $unmarked = SubjectAssessmentView::where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)
                ->where('tutor_id', Auth::user()->user_id)
                ->where(function ($query) {
                    $query->whereNull('assessment_id')->orWhere('marked', '<>', 1);
                })
                ->get();

            $marked = SubjectAssessmentView::where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)
                ->where('tutor_id', Auth::user()->user_id)
                ->where('marked', 1)
                ->groupBy('subject', 'subject_id')
                ->get();

            return view('admin.dashboards.staff', compact('marked', 'unmarked'));

        }else if(Auth::user()->user_type_id == User::SPONSOR){

            return redirect('/home');

        }else{
            $unmarked = DB::table('subjects_assessmentsviews')
                ->select('tutor', 'tutor_id', DB::raw('COUNT(subject_classroom_id) AS subjects'))
                ->where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)
                ->where(function ($query) {
                    $query->whereNull('assessment_id')->orWhere('marked', '<>', 1);
                })
                ->groupBy('tutor', 'tutor_id')
                ->get();

            return view('admin.dashboards.admin', compact('sponsors_count','staff_count', 'students_count', 'unmarked'));
        }
    }

    /**
     * Gets The Number of Students Based on their gender
     * @return Response
     */
    public function studentsGender()
    {
        $male = Student::where('gender', 'Male')->where('status_id', Student::ACTIVE)->count();
        $female = Student::where('gender', 'Female')->where('status_id', Student::ACTIVE)->count();
        $response[] = ['label'=>'Male', 'color'=>'#CCC', 'data'=>$male, 'value'=>$male];
        $response[] = ['label'=>'Female', 'color'=>'#3CF', 'data'=>$female, 'value'=>$female];

        return response()->json($response);
    }

    /**
     * Gets The Students Based on their class level
     * @return Response
     */
    public function studentsClasslevel()
    {
        $classlevels = ClassLevel::all();
        $response = [];
        $color = 0;
        foreach($classlevels as $classlevel){
            $sum = 0;
            foreach($classlevel->classRooms()->get() as $classroom){
                $sum += $classroom->studentClasses()->where('academic_year_id', AcademicYear::activeYear()->academic_year_id)->count();
            }
            $response[] = array(
                'classlevel'=>$classlevel->classlevel,
                'students'=>$sum,
                'color'=>$this->colors[$color++]
            );
        }
        return response()->json($response);
    }

    /**
     * Gets The subjects assigned to a tutor for the current academic term
     * @param $userId
     * @return Response
     */
    public function subjectTutor($userId)
    {
        $subjects = SubjectClassRoom::where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)->where('tutor_id', $userId)->get();
        $response = [];
        $color = 0;
        if($subjects->count() > 0){
            foreach($subjects as $subject){
                $response[] = array(
                    'subject'=>$subject->subject()->first()->subject,
                    'classroom'=>$subject->classRoom()->first()->classroom,
                    'students'=>$subject->studentSubjects()->count(),
                    'color'=> $this->colors[$color++]
                );
            }
        }else{
            $response = 'No Subject Has Been Assigned to you for ' . AcademicTerm::activeTerm()->academic_term . ' Academic Year';
        }
        return response()->json($response);
    }

    /**
     * Gets The class room assigned to a teacher for the current academic year
     * @param $userId
     * @return Json
     */
    public function classTeacher($userId)
    {
        $classes = ClassMaster::where('academic_year_id', AcademicYear::activeYear()->academic_year_id)->where('user_id', $userId)->get();
        $response = [];
        $color = 0;
        if($classes->count() > 0){
            foreach($classes as $class){
                $response[] = array(
                    'classroom'=>$class->classRoom()->first()->classroom,
                    'students'=>$class->classRoom()->first()->studentClasses()
                        ->where('academic_year_id', AcademicYear::activeYear()->academic_year_id)
                        ->where('classroom_id', $class->classRoom()->first()->classroom_id)->count(),
                    'color'=>$this->colors[$color++]
                );
            }
        }else{
            $response = 'No Class Room Has Been Assigned to you as <strong>Class Teacher for ' . AcademicYear::activeYear()->academic_year . '</strong> Academic Year';
        }
        return response()->json($response);
    }

//    public function calculate($input){
//        $pattern = '/(?:\-?\d+(?:\.?\d+)?[\+\-\*\/])+\-?\d+(?:\.?\d+)?/';
//        $depth = 10;
//        $input = str_replace(' ', '', $input);
//        if(strpos($input, '+') != null || strpos($input, '-') != null || strpos($input, '/') != null || strpos($input, '*') != null){
//            //  Remove white spaces and invalid math chars
//            $input = str_replace(',', '.', $input);
//            $input = preg_replace('[^0-9\.\+\-\*\/\(\)]', '', $input);
//
//            //  Calculate each of the parenthesis from the top
//            $i = 0;
//            while(strpos($input, '(') || strpos($input, ')')){
//                $input = preg_replace_callback('/\(([^\(\)]+)\)/', 'self::callback', $input);
//
//                $i++;
//                if($i > $depth){
//                    break;
//                }
//            }
//
//            //  Calculate the result
//            if(preg_match($pattern, $input, $match)){
//                $compute = create_function('', 'return '.$match[0].';');
//
//                return 0 + number_format($compute(), 5);
//            }
//
//            return 0;
//        }
//
//        return $input;
//    }

//    public function getStaff(){
//        $count = 0;
//        $staffs = User::where('user_type_id', User::STAFF)->get();
//        foreach($staffs as $staff){
//            $msg = "Username: $staff->phone_no or $staff->email";
//            $msg .= " and Password: password kindly visit this link portal.solidsteps.org to login";
//            $temp = $this->sendSMS($msg, $staff->phone_no)[0];
//            if($temp) $count++;
//        }
//        $this->sendSMS($count . ' Solid Steps Staffs Initialization', '2348022020075');
//        return response()->json('SMS has been sent to '.$count.' staffs');
//    }
}
