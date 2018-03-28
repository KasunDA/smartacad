<?php

namespace App\Http\Controllers\Admin\Assessments;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Assessments\Domains\Domain;
use App\Models\Admin\Assessments\Domains\DomainAssessment;
use App\Models\Admin\Assessments\Domains\DomainDetail;
use App\Models\Admin\Assessments\Remark;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Users\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DomainsController extends Controller
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
        $classlevels = ClassLevel::pluck('classlevel', 'classlevel_id')
            ->prepend('- Select Class Level -', '');

        return view('admin.assessments.domains.index', compact('academic_years', 'classlevels'));
    }

    /**
     * Search for Class Room Assigned To Logged In Staff
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function classroomAssigned(Request $request)
    {
        $inputs = $request->all();
        $response = array();

        $term = AcademicTerm::findOrFail($inputs['academic_term_id']);
        $response['flag'] = 0;
        $response['term'] = $term->academic_term;

        //Returns All the class rooms for the super admins only
        if(Auth::user()->user_type_id == User::DEVELOPER || Auth::user()->user_type_id == User::SUPER_ADMIN){
            $classrooms = ClassRoom::all();
            //format the record sets as json readable
            if($classrooms->count() > 0){
                foreach($classrooms as $classroom){
                    $res[] = array(
                        "classroom"=>$classroom->classroom,
                        "academic_term"=>$term->academic_term,
                        "hashed_class_id"=>$this->getHashIds()->encode($classroom->classroom_id),
                        "hashed_term_id"=>$this->getHashIds()->encode($term->academic_term_id),
                        "class_master"=>($classroom->classMasters()->count() > 0
                            && isset($classroom->classMasters()
                                ->where('academic_year_id', $term->academic_year_id)->first()->user))
                                ? $classroom->classMasters()
                                    ->where('academic_year_id', $term->academic_year_id)
                                    ->first()
                                    ->user
                                    ->fullNames()
                                : '<span class="label label-danger">nil</span>'
                    );
                }
                $response['flag'] = 1;
                $response['Classrooms'] = isset($res) ? $res : [];
            }
        }else{
            $classMasters = Auth::user()->classMasters()->where('academic_year_id', $term->academic_year_id)->get();
            //format the record sets as json readable
            if($classMasters->count() > 0){
                foreach($classMasters as $classMaster){
                    $res[] = array(
                        "classroom"=>$classMaster->classroom->classroom,
                        "academic_term"=>$term->academic_term,
                        "hashed_class_id"=>$this->getHashIds()->encode($classMaster->classroom_id),
                        "hashed_term_id"=>$this->getHashIds()->encode($term->academic_term_id),
                        "class_master"=>($classMaster->user()->count() > 0)
                            ? $classMaster->user->fullNames()
                            : '<span class="label label-danger">nil</span>'
                    );
                }
                $response['flag'] = 1;
                $response['Classrooms'] = isset($res) ? $res : [];
            }
        }


        echo json_encode($response);
    }

    /**
     * Displays the students in the class room for the given academic term
     * @param String $class_id
     * @param String $term_id
     * @return \Illuminate\View\View
     */
    public function viewStudents($class_id, $term_id)
    {
        $classroom = ClassRoom::findOrFail($this->decode($class_id));
        $term = AcademicTerm::findOrFail($this->decode($term_id));
        $studentClasses = [];

        if($classroom){
            $studentClasses = $classroom->studentClasses()
                ->where('academic_year_id', $term->academic_year_id)
                ->get();
        }

        return view('admin.assessments.domains.view-students', compact('studentClasses', 'classroom', 'term'));
    }

    /**
     * Displays the affective domains to assess the student
     * @param String $stud_id
     * @param String $term_id
     * @return \Illuminate\View\View
     */
    public function assess($stud_id, $term_id)
    {
        $student = Student::findOrFail($this->decode($stud_id));
        $term = AcademicTerm::findOrFail($this->decode($term_id));
        $domains = Domain::orderBy('domain')->get();
        $assessments = DomainAssessment::where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id);

        //Check if exist return it else create a new record
        if($assessments->count() > 0){
            $assessment = $assessments->first();
        }else{
            $assessment = new DomainAssessment();
            $assessment->student_id = $student->student_id;
            $assessment->academic_term_id = $term->academic_term_id;
            $assessment->save();
        }

        return view('admin.assessments.domains.assess', compact('domains', 'student', 'term', 'assessment'));
    }

    /**
     * Save the assessment made
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveAssess(Request $request)
    {
        $inputs = $request->all();
        $domains = Domain::orderBy('domain')->get();
        $i=0;

        foreach ($domains as $domain) {
            $domain_detail = ($inputs['domain_detail_id'][$i] > 0)
                ? DomainDetail::find($inputs['domain_detail_id'][$i])
                : new DomainDetail();
            $domain_detail->domain_assessment_id = $inputs['domain_assessment_id'];
            $domain_detail->domain_id = $domain->domain_id;
            $domain_detail->option = $inputs['optionsRadios' . $domain->domain_id];
            $domain_detail->save();
            $i++;
        }
        $assessment = DomainAssessment::findOrFail($inputs['domain_assessment_id']);
        $term = $assessment->academicTerm()->first();
        $class = $assessment->student()->first()->currentClass($term->academic_year_id);

        if($i > 0) $this->setFlashMessage($i . ' Affective Domains has been successfully Assessed.', 1);

        return redirect('/domains/view-students/'
            .$this->encode($class->classroom_id).'/'.$this->encode($term->academic_term_id));
    }

    /**
     * Displays the students in the class room for the given academic term for Remark Assessment
     * @param String $class_id
     * @param String $term_id
     * @return \Illuminate\View\View
     */
    public function remark($class_id, $term_id)
    {
        $classroom = ClassRoom::findOrFail($this->decode($class_id));
        $term = AcademicTerm::findOrFail($this->decode($term_id));
        $studentClasses = [];
        if($classroom){
            $studentClasses = $classroom->studentClasses()
                ->where('academic_year_id', $term->academic_year_id)
                ->get();
        }

        return view('admin.assessments.domains.remark', compact('studentClasses', 'classroom', 'term'));
    }

    /**
     * Save the students remarks
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveRemark(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for($i = 0; $i < count($inputs['remark_id']); $i++){
            $remark = ($inputs['remark_id'][$i] > 0) ? Remark::find($inputs['remark_id'][$i]) : new Remark();
            $remark->student_id = $inputs['student_id'][$i];
            $remark->academic_term_id = $inputs['academic_term_id'];
            $remark->class_teacher = ($inputs['class_teacher'][$i] == '' || !isset($inputs['class_teacher'][$i])) ? null : $inputs['class_teacher'][$i];
            $remark->principal = ($inputs['principal'][$i] == '' || !isset($inputs['principal'][$i])) ? null : $inputs['principal'][$i];
            $remark->user_id = Auth::user()->user_id;

            if($remark->save()) $count++;
        }
        // Set the flash message
        if($count > 0){
            $this->setFlashMessage($count . ' Students Remark has been successfully inputted.', 1);
        }
        // redirect to the create a new inmate page
        return redirect('/domains');
    }
}
