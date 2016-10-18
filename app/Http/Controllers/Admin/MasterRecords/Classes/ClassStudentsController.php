<?php

namespace App\Http\Controllers\Admin\MasterRecords\Classes;

use App\Models\Admin\Accounts\Staff;
use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassMaster;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Users\User;
use App\Models\School\School;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use stdClass;

class ClassStudentsController extends Controller
{
    /**
     * Display a listing of the Menus for Master Records.
     * @return Response
     */
    public function getIndex()
    {
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('Select Academic Year', '');
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('Select Class Level', '');
        return view('admin.master-records.classes.class-rooms.class-student', compact('academic_years', 'classlevels', 'tutors'));
    }

    /**
     * Search For Students in a class room for an academic year
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSearchStudents(Request $request)
    {
        $inputs = $request->all();
        $students = StudentClass::where('academic_year_id', $inputs['student_academic_year_id'])
            ->where('classroom_id', $inputs['student_classroom_id'])->get();
        $response = array();
        $response['flag1'] = 0;
        $response['flag2'] = 0;
        $studentsClass = [];

        if($students->count() > 0){
            $studentsNoClass = Student::where('status_id', 1)
                ->whereNotIn('student_id', StudentClass::where('academic_year_id', $inputs['student_academic_year_id'])
                ->lists('student_id')->toArray())->get();
            //All the students in the class room for the academic year
            foreach($students as $student){
                if($student->student()->first()->status_id == 1){
                    $object = new stdClass();
                    $object->student_class_id = $student->student_class_id;
                    $object->student_id = $student->student_id;
                    $object->name = $student->student()->first()->fullNames();
                    $object->student_no = $student->student()->first()->student_no;
                    $studentsClass[] = $object;
                }
            }
            session()->put('active', 'search');
            //All the students without any class room for the academic year
            foreach($studentsNoClass as $student_no){
                $res[] = array(
                    "name"=>$student_no->fullNames(),
                    "student_no"=>$student_no->student_no,
                    "student_id"=>$student_no->student_id,
                );
            }
            //Sort The Students by name
            usort($studentsClass, function($a, $b)
            {
                return strcmp($a->name, $b->name);
            });

            $response['flag2'] = 1;
            $response['flag1'] = 1;
            $response['StudentsClass'] = isset($studentsClass) ? $studentsClass : [];
            $response['StudentsNoClass'] = isset($res) ? $res : [];
        }
        echo json_encode($response);
    }

    /**
     * Assign students to a classroom or remove them from a classroom
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postAssign(Request $request)
    {
        $inputs = $request->all();
        $student = Student::find($inputs['student_id']);
        //assign
        if($inputs['student_class_id'] === '-1') {
            $student_class = new StudentClass();
            $student_class->student_id = $inputs['student_id'];
            $student_class->classroom_id = $inputs['class_id'];
            $student_class->academic_year_id = $inputs['year_id'];
            if($student_class->save()) {
                //update the classroom_id in the students table
                $student->classroom_id = $student_class->classroom_id;
                echo $student_class->student_class_id;
            } else echo 0;
        //remove
        }else{
            $student_class = StudentClass::find($inputs['student_class_id']);
            if($student_class->delete()){
                $student->classroom_id = null;
                echo $inputs['student_class_id'];
            }else echo 0;
        }
        $student->save();
    }

    /**
     * Search For Students in a class room for an academic year
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postViewStudents(Request $request)
    {
        $inputs = $request->all();
        if(isset($inputs['view_classroom_id']) and $inputs['view_classroom_id'] != ''){
            $students = StudentClass::where('academic_year_id', $inputs['view_academic_year_id'])
                ->where('classroom_id', $inputs['view_classroom_id'])->get();
        }else{
            $students = StudentClass::where('academic_year_id', $inputs['view_academic_year_id'])
                ->whereIn('classroom_id', ClassRoom::where('classlevel_id', $inputs['view_classlevel_id'])->lists('classroom_id')->toArray())->get();
        }

        $response = array();
        $response['flag'] = 0;
        $studentsClass = [];

        if($students->count() > 0){
            //All the students in the class room for the academic year
            foreach($students as $student){
                if($student->student()->first()->status_id == 1){
                    $object = new stdClass();
                    $object->student_id = $this->getHashIds()->encode($student->student_id);
                    $object->name = $student->student()->first()->fullNames();
                    $object->student_no = $student->student()->first()->student_no;
                    $object->gender = $student->student()->first()->gender;
                    $object->classroom = $student->classRoom()->first()->classroom;
                    $object->sponsor = ($student->student()->first()->sponsor()->first())
                        ? $student->student()->first()->sponsor()->first()->fullNames() : '<span class="label label-danger">nil</span>';
                    $object->sponsor_id = ($student->student()->first()->sponsor()->first())
                        ? $this->getHashIds()->encode($student->student()->first()->sponsor()->first()->user_id) : -1;
                    $studentsClass[] = $object;
                }
            }
            //Sort The Students by name
            usort($studentsClass, function($a, $b)
            {
                return strcmp($a->name, $b->name);
            });

            $response['flag'] = 1;
            $response['Students'] = isset($studentsClass) ? $studentsClass : [];
        }
        echo json_encode($response);
    }

    /**
     * Validate if the subject assigned has been clone
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postValidateClone(Request $request)
    {
        $inputs = $request->all();
        $from_year = AcademicYear::findOrFail($inputs['from_academic_year_id']);
        $to_year = AcademicYear::findOrFail($inputs['to_academic_year_id']);
        $from = StudentClass::where('academic_year_id', $from_year->academic_year_id)->where('classroom_id', $inputs['from_classroom_id'])->count();
        $to = StudentClass::where('academic_year_id', $to_year->academic_year_id)->where('classroom_id', $inputs['to_classroom_id'])->count();
        $response = [];

        if ($from > 0 and $to == 0) {
            //Clone
            $response['flag'] = 1;
            $response['from'] = $from_year;
            $response['to'] = $to_year;
            $response['from_class'] = $inputs['from_classroom_id'];
            $response['to_class'] = $inputs['to_classroom_id'];
        }else if($from == 0 and $to == 0) {
            $output = ' <h4>Whoops!!! You cannot clone from <strong>'.$from_year->academic_year.
                ' Academic Year</strong> to <strong> '.$to_year->academic_year.'</strong><br> Because it has no record to clone from</h4>';
            $response['flag'] = 2;
        }else{
            $output = ' <h4>Students Has Been Assigned To The Class Room Already for '.$to_year->academic_year.
                '. <br>Kindly click on <strong> <i class="fa fa-plus-square"></i>'. 
                'Add / <i class="fa fa-minus-square"></i> Remove Student in Class Room</strong> Tab For Modifications</h4>';
            $response['flag'] = 3;
        }

        $response['output'] = isset($output) ? $output : [];
        return response()->json($response);
    }

    /**
     * Clone the subject assigned from an academic term to an academic term
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postCloning(Request $request)
    {
        $inputs = $request->all();
        $from_year = AcademicYear::findOrFail($inputs['from_year']);
        $to_year = AcademicYear::findOrFail($inputs['to_year']);
        $from = StudentClass::where('academic_year_id', $inputs['from_year'])->where('classroom_id', $inputs['from_class'])->get();

        if ($from->count() > 0) {
            //Cloning
            foreach ($from as $new){
                $class = new StudentClass();
                $class->student_id = $new->student_id;
                $class->classroom_id = $inputs['to_class'];
                $class->academic_year_id = $inputs['to_year'];

                if($class->save()){
                    $student = Student::find($new->student_id);
                    $student->classroom_id = $class->classroom_id;
                    $student->save();
                }
            }
            session()->put('active', 'cloning');
            $this->setFlashMessage(' Cloned!!! ' . $from->count() . ' Students Class Rooms have been cloned from '.$from_year->academic_year.' to ' . $to_year->academic_year, 1);
        }
    }
}