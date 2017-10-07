<?php

namespace App\Http\Controllers\Front\Assessments;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\AssessmentSetups\AssessmentSetup;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\Assessments\AssessmentDetailView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use stdClass;

class AssessmentsController extends Controller
{
    /**
     * Displays the Students of the sponsor
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $students = Auth::user()->students()->get();

        return view('front.assessments.index', compact('students'));
    }
    
    /**
     * Displays the details of the subjects students scores for a specific academic term
     * @param String $encodeStud
     * @param String $encodeTerm
     * @return \Illuminate\View\View
     */
    public function getPrintReport($encodeStud, $encodeTerm)
    {
        $student = Student::findOrFail($this->decode($encodeStud));
        $term = AcademicTerm::findOrFail($this->decode($encodeTerm));
        $classroom = $student->currentClass($term->academicYear->academic_year_id);
        
        $assessments = AssessmentDetailView::orderBy('subject_classroom_id')
            ->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $classroom->classroom_id)
            ->get();
        
        $subjectClasses = AssessmentDetailView::orderBy('subject_classroom_id')
            ->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $classroom->classroom_id)
            ->distinct()
            ->get(['subject_classroom_id']);
        
        $setup = AssessmentSetup::where('academic_term_id', $term->academic_term_id)
            ->where('classgroup_id', $classroom->classLevel()->first()->classgroup_id)
            ->first();
        $setup_details = $setup->assessmentSetupDetails()->orderBy('number');
        
        return view('admin.assessments.print-report', 
            compact('student', 'assessments', 'term', 'classroom', 'setup_details', 'subjectClasses')
        );
    }

    /**
     * Displays the summary of students assessments ever taken
     * @param String $encodeStud
     * @return \Illuminate\View\View
     */
    public function getView($encodeStud)
    {
        $student = Student::findOrFail($this->decode($encodeStud));
        $assessments = AssessmentDetailView::orderBy('assessment_id', 'desc')
            ->where('student_id', $student->student_id)
            ->groupBy(['student_id', 'academic_term'])
            ->get();

        return view('front.assessments.view', compact('student', 'assessments'));
    }

    /**
     * Displays the details of students assessments based on class and term
     * @param String $encodeStud
     * @param String $encodeTerm
     * @return \Illuminate\View\View
     */
    public function getDetails($encodeStud, $encodeTerm)
    {
        $student = Student::findOrFail($this->decode($encodeStud));
        $term = AcademicTerm::findOrFail($this->decode($encodeTerm));
        $classroom = $student->currentClass($term->academicYear->academic_year_id);

        $assessments = AssessmentDetailView::orderBy('subject_classroom_id')
            ->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $classroom->classroom_id)
            ->get();
        $subjectClasses = AssessmentDetailView::orderBy('subject_classroom_id')
            ->where('student_id', $student->student_id)
            ->where('academic_term_id', $term->academic_term_id)
            ->where('classroom_id', $classroom->classroom_id)
            ->distinct()
            ->get(['subject_classroom_id']);

        $setup = AssessmentSetup::where('academic_term_id', $term->academic_term_id)
            ->where('classgroup_id', $classroom->classLevel()->first()->classgroup_id)
            ->first();

        $setup_details = ($setup) ? $setup->assessmentSetupDetails()->orderBy('number') : false;

        return view('front.assessments.details',
            compact('student', 'assessments', 'term', 'classroom', 'setup_details', 'subjectClasses')
        );
    }

}
