<?php

namespace App\Http\Controllers\Admin\Subjects;

use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Subjects\SubjectClassRoom;
use App\Models\School\School;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SubjectClassRoomsController extends Controller
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
        return view('admin.subjects.index', compact('academic_years', 'classlevels'));
    }


    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSearchAssigned(Request $request)
    {
        $inputs = $request->all();
        $type = $inputs['type_id'];

        $response = array();
        $school_subjects = School::mySchool()->subjects()->orderBy('subject')->get(['schools_subjects.subject_id', 'subject', 'schools_subjects.subject_alias']);

        if($type == 1){
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['class_academic_term_id'])
                ->where('classroom_id', $inputs['class_classroom_id'])->lists('subject_id')->toArray();
            $response['ClassID'] = $inputs['class_classroom_id'];
            $response['TermID'] = $inputs['class_academic_term_id'];

        }elseif($type == 2){
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['level_academic_term_id'])
                ->whereIn('classroom_id', ClassRoom::where('classlevel_id', $inputs['level_classlevel_id'])->lists('classroom_id')->toArray())
                ->lists('subject_id')->toArray();
            $response['LevelID'] = $inputs['level_classlevel_id'];
            $response['TermID'] = $inputs['level_academic_term_id'];
        }

        $response['ClassSubjects'] = (isset($class_subjects)) ? $class_subjects : [];
        $response['SchoolSubjects'] = $school_subjects;
        $response['flag'] = 1;
        $response['Type'] = $type;
        echo json_encode($response);
    }


    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postAssignSubjects(Request $request)
    {
        $inputs = $request->all();
        $temp = '';

        if(isset($inputs['subject_id'])) {
            $sub = implode(',', $inputs['subject_id']);
            if(isset($inputs['assign_classroom_id']) and $inputs['assign_classroom_id'] != ''){
                $temp = ClassRoom::find($inputs['assign_classroom_id'])->classroom;
                $result = SubjectClassRoom::assignSubject2Class($inputs['assign_classroom_id'], $inputs['assign_academic_term_id'], $sub);
            }elseif(isset($inputs['assign_classlevel_id']) and $inputs['assign_classlevel_id'] != ''){
                $temp = ClassLevel::find($inputs['assign_classlevel_id'])->classlevel;
                $result = SubjectClassRoom::assignSubject2Level($inputs['assign_classlevel_id'], $inputs['assign_academic_term_id'], $sub);
            }

            (isset($result))
                ? $this->setFlashMessage(count($inputs['subject_id']) . ' Subjects has been successfully assigned to ' . $temp, 1)
                : $this->setFlashMessage(' Error...Kindly Try Again', 2);
        } else {
            $this->setFlashMessage(' Warning... No Subject was selected to be assign for' . $temp, 2);
        }
        echo json_encode($inputs);
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
                    "academic_term"=>$class_subject->academicTerm()->first()->academic_term,
                );
            }
            $response['flag'] = 1;
            $response['ClassSubjects'] = isset($res) ? $res : [];
        }
        echo json_encode($response);
    }
}
