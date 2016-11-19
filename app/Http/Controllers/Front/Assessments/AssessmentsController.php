<?php

namespace App\Http\Controllers\Front\Assessments;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetup;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\Views\AssessmentDetailView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use stdClass;

class AssessmentsController extends Controller
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
        return view('front.assessments.index', compact('academic_years', 'classlevels'));
    }

    /**
     * Displays the details of the subjects students scores for a specific academic term
     * @param String $encodeStud
     * @param String $encodeTerm
     * @return \Illuminate\View\View
     */
    public function getReportDetails($encodeStud, $encodeTerm)
    {
        $decodeStud = $this->getHashIds()->decode($encodeStud);
        $decodeTerm = $this->getHashIds()->decode($encodeTerm);
        $student = (empty($decodeStud)) ? abort(305) : Student::findOrFail($decodeStud[0]);
        $term = (empty($decodeTerm)) ? abort(305) : AcademicTerm::findOrFail($decodeTerm[0]);
        $classroom = $student->currentClass($term->academicYear->academic_year_id);
        $assessments = AssessmentDetailView::orderBy('subject_classroom_id')->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)->get();
        $subjectClasses = AssessmentDetailView::orderBy('subject_classroom_id')->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)->distinct()->get(['subject_classroom_id']);
        $setup = AssessmentSetup::where('academic_term_id', $term->academic_term_id)->where('classgroup_id', $classroom->classLevel()->first()->classgroup_id)->first();
        $setup_details = $setup->assessmentSetupDetails()->orderBy('number');
//        $filtered = array_filter($assessments, function($key){
//            return in_array($key, ['subject_classroom_id', 'number']);
//        }, ARRAY_FILTER_USE_KEY);

        return view('front.assessments.details', compact('student', 'assessments', 'term', 'classroom', 'setup_details', 'subjectClasses'));
    }

    /**
     * Displays the details of the subjects students scores for a specific academic term
     * @param String $encodeStud
     * @param String $encodeTerm
     * @return \Illuminate\View\View
     */
    public function getPrintReport($encodeStud, $encodeTerm)
    {
        $decodeStud = $this->getHashIds()->decode($encodeStud);
        $decodeTerm = $this->getHashIds()->decode($encodeTerm);
        $student = (empty($decodeStud)) ? abort(305) : Student::findOrFail($decodeStud[0]);
        $term = (empty($decodeTerm)) ? abort(305) : AcademicTerm::findOrFail($decodeTerm[0]);
        $classroom = $student->currentClass($term->academicYear->academic_year_id);
        $assessments = AssessmentDetailView::orderBy('subject_classroom_id')->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)->get();
        $subjectClasses = AssessmentDetailView::orderBy('subject_classroom_id')->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)->where('classroom_id', $classroom->classroom_id)->distinct()->get(['subject_classroom_id']);
        $setup = AssessmentSetup::where('academic_term_id', $term->academic_term_id)->where('classgroup_id', $classroom->classLevel()->first()->classgroup_id)->first();
        $setup_details = $setup->assessmentSetupDetails()->orderBy('number');
        return view('admin.assessments.print-report', compact('student', 'assessments', 'term', 'classroom', 'setup_details', 'subjectClasses'));
    }
}
