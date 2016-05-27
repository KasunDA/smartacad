<?php

namespace App\Http\Controllers\Admin\MasterRecords\Subjects;

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

class SubjectTutorsController extends Controller
{
    /**
     * Display a listing of the Subjects for Master Records.
     *
     * @return Response
     */
    public function getIndex()
    {
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('Select Academic Year', '');
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('Select Class Level', '');
        return view('admin.master-records.subjects.subject-tutor', compact('academic_years', 'classlevels', 'school_subjects'));
    }

    /**
     * Search For Subjects to be managed
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSearchSubjects(Request $request)
    {
        $inputs = $request->all();
        $response = array();
        $response['flag'] = 0;
        $tutor_id = Auth::user()->user_id;

        if(isset($inputs['manage_classlevel_id']) and $inputs['manage_classlevel_id'] != ''){
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['manage_academic_term_id'])->where('subject_id', $inputs['subject_id'])
                ->whereIn('classroom_id', ClassRoom::where('classlevel_id', $inputs['manage_classlevel_id'])->lists('classroom_id')->toArray())
                ->where('tutor_id', $tutor_id)->get();
        }else{
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['manage_academic_term_id'])->where('tutor_id', $tutor_id)->get();
        }
        if(isset($class_subjects)){
            foreach($class_subjects as $class_subject){
                $res[] = array(
                    "classroom"=>$class_subject->classRoom()->first()->classroom,
                    "subject"=>$class_subject->subject()->first()->subject,
                    "subject_classroom_id"=>$class_subject->subject_classroom_id,
                    "academic_term"=>$class_subject->academicTerm()->first()->academic_term,
                    "tutor"=>($class_subject->tutor()->first()) ? $class_subject->tutor()->first()->fullNames() : '<span class="label label-danger">nil</span>',
                    "status"=>($class_subject->exam_status_id == 2) ? '<span class="label label-danger">Not Setup</span>' : '<span class="label label-success">Already Setup</span>',
                );
            }
            $response['flag'] = 1;
            $response['ClassSubjects'] = isset($res) ? $res : [];
        }
        echo json_encode($response);
    }

    /**
     * Search For Subjects to be managed
     * @param Int $id
     * @param Int $term
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getManageStudent($id, $term)
    {
        $subject = SubjectClassRoom::findOrFail($id);
        $term = AcademicTerm::findOrFail($term);
        $response = array();
        $response['flag'] = 0;
        $students = [];

        if($subject){
            //All the students in the class room for the academic year
            foreach($subject->classRoom()->first()->studentClasses()->where('academic_year_id', $term->academic_year_id)->get() as $student){
                $object = new stdClass();
                $object->student_id = $student->student_id;
                $object->name = $student->student()->first()->fullNames();
                $students[] = $object;
            }
            //All the students that registered the subject in the class room for the academic year
            foreach($subject->studentSubjects()->get() as $student_subject){
                $res2[] = $student_subject->student_id;
            }
            //Sort The Students by name
            usort($students, function($a, $b)
            {
                return strcmp($a->name, $b->name);
            });
            $response['flag'] = 1;
            $response['Students'] = isset($students) ? $students : [];
            $response['Registered'] = isset($res2) ? $res2 : [];
        }
        echo json_encode($response);
    }

    /**
     * Submit For Subjects Managed
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postManageStudents(Request $request)
    {
        $inputs = $request->all();
        $subject = SubjectClassRoom::findOrFail($inputs['subject_classroom_id']);
        $student_ids = implode(',', $inputs['student_id']);
        if($subject->modifyStudentsSubject($student_ids)){
            $this->setFlashMessage(count($inputs['student_id']) . ' Students has been enrolled for '
                . $subject->subject()->first()->subject.' Subject in '.$subject->classRoom()->first()->classroom.' class room.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
//        $student_ids = rtrim($student_ids, ',') . ')';
    }

    /**
     * Search For Subjects Assigned all ready to class room or level
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postViewAssigned(Request $request)
    {
        $inputs = $request->all();
        $response = array();
        $response['flag'] = 0;

        if(isset($inputs['view_classroom_id']) and $inputs['view_classroom_id'] != ''){
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['view_academic_term_id'])
                ->where('classroom_id', $inputs['view_classroom_id'])->get();
        }else{
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['view_academic_term_id'])
                ->whereIn('classroom_id', ClassRoom::where('classlevel_id', $inputs['view_classlevel_id'])->lists('classroom_id')->toArray())->get();
        }
        if(isset($class_subjects)){
            foreach($class_subjects as $class_subject){
                $res[] = array(
                    "classroom"=>$class_subject->classRoom()->first()->classroom,
                    "subject"=>$class_subject->subject()->first()->subject,
                    "subject_classroom_id"=>$class_subject->subject_classroom_id,
                    "academic_term"=>$class_subject->academicTerm()->first()->academic_term,
                    "tutor"=>($class_subject->tutor()->first()) ? $class_subject->tutor()->first()->fullNames() : '<span class="label label-danger">nil</span>',
                    "tutor_id"=>($class_subject->tutor()->first()) ? $class_subject->tutor()->first()->user_id : -1,
                );
            }
            $response['flag'] = 1;
            $response['ClassSubjects'] = isset($res) ? $res : [];
        }
        echo json_encode($response);
    }

}