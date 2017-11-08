<?php

namespace App\Http\Controllers\Admin\MasterRecords\Subjects;

use App\Models\Admin\Accounts\Staff;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use App\Models\Admin\Users\User;
use App\Models\School\School;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use stdClass;

class SubjectClassRoomsController extends Controller
{
    /**
     * Display a listing of the Subjects for Master Records.
     *
     * @return Response
     */
    public function index()
    {
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id')->prepend('- Select Academic Year -', '');
        $classlevels = ClassLevel::pluck('classlevel', 'classlevel_id')->prepend('- Select Class Level -', '');
        $tutors = User::where('user_type_id', Staff::USER_TYPE)
            ->where('status', 1)
            ->orderBy('first_name')
            ->get();
        $school_subjects = School::mySchool()
            ->subjects()
            ->orderBy('subject')
            ->get(['schools_subjects.subject_id', 'subject', 'schools_subjects.subject_alias']);

        return view('admin.master-records.subjects.subject-classroom',
            compact('academic_years', 'classlevels', 'tutors', 'school_subjects')
        );
    }

    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function searchAssigned(Request $request)
    {
        $inputs = $request->all();
        $type = $inputs['type_id'];

        $response = [];
        $school_subjects = School::mySchool()
            ->subjects()
            ->orderBy('subject')
            ->get(['schools_subjects.subject_id', 'subject', 'schools_subjects.subject_alias']);

        if ($type == 1) {
            $response['ClassID'] = $inputs['class_classroom_id'];
            $response['TermID'] = $inputs['class_academic_term_id'];
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['class_academic_term_id'])
                ->where('classroom_id', $inputs['class_classroom_id'])
                ->pluck('subject_id')
                ->toArray();

        } elseif($type == 2) {
            $response['LevelID'] = $inputs['level_classlevel_id'];
            $response['TermID'] = $inputs['level_academic_term_id'];
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['level_academic_term_id'])
                ->whereIn('classroom_id',
                    ClassRoom::where('classlevel_id', $inputs['level_classlevel_id'])
                    ->pluck('classroom_id')
                    ->toArray()
                )
                ->pluck('subject_id')
                ->toArray();
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
    public function assignSubjects(Request $request)
    {
        $inputs = $request->all();
        $temp = '';

        if (isset($inputs['subject_id'])) {
            $sub = implode(',', $inputs['subject_id']);

            if (isset($inputs['assign_classroom_id']) && $inputs['assign_classroom_id'] != '') {
                $temp = ClassRoom::find($inputs['assign_classroom_id'])->classroom;
                session()->put('subject-tab', 'classroom');
                $result = SubjectClassRoom::assignSubject2Class($inputs['assign_classroom_id'], $inputs['assign_academic_term_id'], $sub);

            } elseif(isset($inputs['assign_classlevel_id']) && $inputs['assign_classlevel_id'] != '') {
                $temp = ClassLevel::find($inputs['assign_classlevel_id'])->classlevel;
                session()->put('subject-tab', 'classlevel');
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
    public function viewAssigned(Request $request)
    {
        $inputs = $request->all();
        $response = [];
        $response['flag'] = 0;

        if (isset($inputs['view_classroom_id']) && $inputs['view_classroom_id'] != '') {
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['view_academic_term_id'])
                ->where('classroom_id', $inputs['view_classroom_id'])
                ->get();
        } else {
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['view_academic_term_id'])
                ->whereIn('classroom_id',
                    ClassRoom::where('classlevel_id', $inputs['view_classlevel_id'])
                        ->pluck('classroom_id')
                        ->toArray()
                )
                ->get();
        }
        
        if (isset($class_subjects)) {
            foreach ($class_subjects as $class_subject) {
                $res[] = [
                    "classroom"=>$class_subject->classRoom()->first()->classroom,
                    "subject"=>$class_subject->subject()->first()->subject,
                    "subject_classroom_id"=>$class_subject->subject_classroom_id,
                    "academic_term"=>$class_subject->academicTerm()->first()->academic_term,
                    "tutor_id"=>($class_subject->tutor()->first()) ? $class_subject->tutor()->first()->user_id : -1,
                    "tutor"=>($class_subject->tutor()->first()) 
                        ? $class_subject->tutor()->first()->fullNames() 
                        : '<span class="label label-danger">nil</span>'
                ];
            }
            $response['flag'] = 1;
            $response['ClassSubjects'] = isset($res) ? $res : [];
        }
        
        echo json_encode($response);
    }

    /**
     * Assign Tutor to subject class
     * @param $subject_classroom_id
     * @param $tutor_id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function assignTutor($subject_classroom_id, $tutor_id)
    {
        $tutor = SubjectClassRoom::find($subject_classroom_id);
        
        if($tutor){
            $tutor->tutor_id = ($tutor_id > 0) ? $tutor_id : null;
            $tutor->save();
            
            echo json_encode($tutor->subject_classroom_id);
        }
    }

    /**
     * Search For Subjects to be managed
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function searchSubjects(Request $request)
    {
        $inputs = $request->all();
        $response = $ClassSubjects = [];
        $response['flag'] = 0;

        if (isset($inputs['subject_id']) and $inputs['subject_id'] != '') {
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['manage_academic_term_id'])
                ->where('subject_id', $inputs['subject_id'])
                ->whereIn('classroom_id',
                    ClassRoom::where('classlevel_id', $inputs['manage_classlevel_id'])
                    ->pluck('classroom_id')
                    ->toArray()
                )
                ->get();
        } else {
            $class_subjects = SubjectClassRoom::where('academic_term_id', $inputs['manage_academic_term_id'])
                ->whereIn('classroom_id',
                    ClassRoom::where('classlevel_id', $inputs['manage_classlevel_id'])
                        ->pluck('classroom_id')
                        ->toArray())
                ->get();
        }
        
        if (isset($class_subjects)) {
            foreach ($class_subjects as $class_subject) {
                $object = new stdClass();
                $object->classroom = $class_subject->classRoom()->first()->classroom;
                $object->subject = $class_subject->subject()->first()->subject;
                $object->subject_classroom_id = $class_subject->subject_classroom_id;
                $object->academic_term = $class_subject->academicTerm()->first()->academic_term;
                $object->tutor = ($class_subject->tutor()->first()) 
                    ? $class_subject->tutor()->first()->fullNames() 
                    : '<span class="label label-danger">nil</span>';
                $object->status = ($class_subject->exam_status_id == 2)
                    ? '<span class="label label-danger">Not Setup</span>' 
                    : '<span class="label label-success">Already Setup</span>';
                
                $ClassSubjects[] = $object;
            }
            //Sort The Subjects by name
            usort($ClassSubjects, function ($a, $b) {
                return strcmp($a->subject, $b->subject);
            });
            
            $response['flag'] = 1;
            $response['ClassSubjects'] = isset($ClassSubjects) ? $ClassSubjects : [];
        }
        
        echo json_encode($response);
    }

    /**
     * Search For Subjects to be managed
     * @param Int $id
     * @param Int $term
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function manageStudent($id, $term)
    {
        $subject = SubjectClassRoom::findOrFail($id);
        $term = AcademicTerm::findOrFail($term);
        $response = $students = [];
        $response['flag'] = 0;

        if ($subject) {
            //All the students in the class room for the academic year
            foreach ($subject->classRoom()->first()->studentClasses()->where('academic_year_id', $term->academic_year_id)->get() as $student) {
                $object = new stdClass();
                $object->student_id = $student->student_id;
                $object->name = $student->student()->first()->fullNames();
                $students[] = $object;
            }
            //All the students that registered the subject in the class room for the academic year
            foreach ($subject->studentSubjects()->get() as $student_subject) {
                $res2[] = $student_subject->student_id;
            }
            //Sort The Students by name
            usort($students, function ($a, $b) {
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
    public function saveStudents(Request $request)
    {
        $inputs = $request->all();
        $subject = SubjectClassRoom::findOrFail($inputs['subject_classroom_id']);
        $student_ids = implode(',', $inputs['student_id']);
        
        if ($subject->modifyStudentsSubject($student_ids)) {
            $this->setFlashMessage(
                count($inputs['student_id']) . ' Students has been enrolled for '
                . $subject->subject()->first()->subject.' Subject in ' 
                . $subject->classRoom()->first()->classroom.' class room.', 1
            );
            session()->put('subject-tab', 'manage-subject');
        } else {
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Delete a Subject Class Room and all its equivalent relations
     * @param $id
     */
    public function delete($id)
    {
        $subject = SubjectClassRoom::findOrFail($id);

        if (($subject !== null) && $subject->delete()) {
            session()->put('subject-tab', 'manage-subject');
            $this->setFlashMessage('  Deleted!!! '.$subject->subject()->first()->subject.' Subject in '.$subject->classRoom()->first()->classroom.' class room have been deleted.', 1);
        } else {
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }
}
