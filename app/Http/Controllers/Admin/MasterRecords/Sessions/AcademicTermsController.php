<?php

namespace App\Http\Controllers\Admin\MasterRecords\Sessions;

use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use App\Models\School\School;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AcademicTermsController extends Controller
{
    protected $school;
    /**
     *
     * Make sure the user is logged in and The Record has been setup
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->school = School::mySchool();
        
        ($this->school->setup == School::ACADEMIC_TERM)
            ? $this->setFlashMessage('Warning!!! Kindly Setup the Academic Terms records Before Proceeding.', 3)
            : $this->middleware('setup');
    }
    
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @param Boolean $year_id
     * @return Response
     */
    public function index($year_id=false)
    {
        $academic_year = ($year_id) ? AcademicYear::findOrFail($this->decode($year_id)) : AcademicYear::activeYear();
        $academic_terms = $academic_year->academicTerms()
            ->orderBy('term_type_id')
            ->get();
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id')
            ->prepend('- Academic Year -', '');
        
        return view('admin.master-records.sessions.academic-terms', 
            compact('academic_terms', 'academic_years', 'academic_year')
        );
    }

    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = $status =0;
        $active_academic_term = false;

        // Validate TO Make Sure Only One Status is Set
        for ($j=0; $j<count($inputs['status']); $j++) {
            if ($inputs['status'][$j] == '1') $status++;
        }

        if ($status != 1 && empty(AcademicTerm::activeTerm())) {
            $this->setFlashMessage('Note!!! An Academic Term (Only One) Must Be Set To Active At Any Point In Time.', 2);
        } else {
            for ($i = 0; $i < count($inputs['academic_term_id']); $i++) {
                $academic_term = ($inputs['academic_term_id'][$i] > 0) 
                    ? AcademicTerm::find($inputs['academic_term_id'][$i]) :
                    new AcademicTerm();
                $academic_term->academic_term = $inputs['academic_term'][$i];
                $academic_term->status = $inputs['status'][$i];
                $academic_term->academic_year_id = $inputs['academic_year_id'][$i];
                $academic_term->term_type_id = $inputs['term_type_id'][$i];
                $academic_term->term_begins = $inputs['term_begins'][$i];
                $academic_term->term_ends = $inputs['term_ends'][$i];
                if ($academic_term->save()) {
                    $count = $count+1;

                    if ($academic_term->status == 1) $active_academic_term = $academic_term;
                }
            }
            //Update The Setup Process
            if ($this->school->setup == School::ACADEMIC_TERM) {
                $this->school->setup = School::CLASS_GROUP;
                $this->school->save();
                
                return redirect('/class-groups');
            }

            //update status
            if ($active_academic_term) {
                DB::table('academic_terms')
                    ->where('academic_term_id', '<>', $active_academic_term->academic_term_id)
                    ->update(['status' => 2]);

                DB::table('academic_years')
                    ->where('academic_year_id', $active_academic_term->academic_year_id)
                    ->update(['status' => 1]);

                DB::table('academic_years')
                    ->where('academic_year_id', '<>', $active_academic_term->academic_year_id)
                    ->update(['status' => 2]);
            }

            if ($count > 0) $this->setFlashMessage($count . ' Academic Term has been successfully updated.', 1);
        }
        
//        return redirect('/academic-terms');
        return redirect()->back();
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function delete($id)
    {
        $academic_term = AcademicTerm::findOrFail($id);
        $delete = (!empty($academic_term)) ? $academic_term->delete() : false;

        ($delete)
            ? $this->setFlashMessage('  Deleted!!! '.$academic_term->academic_term.' Academic Term have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }

    /**
     * Cloning an Academic term records form an existing term.
     * @return Response
     */
    public function clone()
    {
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id')
            ->prepend('- Academic Year -', '');
        
        return view('admin.master-records.sessions.clones', compact('academic_years'));
    }

    /**
     * Validate if the subject assigned has been clone
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function validateClone(Request $request)
    {
        $inputs = $request->all();
        $from_term = AcademicTerm::findOrFail($inputs['academic_term_id']);
        $to_term = AcademicTerm::findOrFail($inputs['to_academic_term_id']);
        $from = SubjectClassRoom::where('academic_term_id', $from_term->academic_term_id)->count();
        $to = SubjectClassRoom::where('academic_term_id', $to_term->academic_term_id)->count();
        $response = [];

        if ($from > 0 and $to == 0) {
            $response['flag'] = 1;
            $response['from'] = $from_term;
            $response['to'] = $to_term;
        } elseif ($from == 0 and $to == 0) {
            $response['flag'] = 2;
            $output = ' <h4>Whoops!!! You cannot clone from <strong>'.$from_term->academic_term.
                ' Academic Term</strong> to <strong> '.$to_term->academic_term.'</strong><br> Because it has no record to clone from</h4>';
        } else {
            $response['flag'] = 3;
            $output = ' <h4>Subjects Has Been Assigned To Class Room And Tutors Already for '.$to_term->academic_term.
                '. <br>Kindly Navigate To <strong> Setups > Master Records > Subjects > Assign To Class</strong> For Modifications</h4>';
        }

        $response['output'] = isset($output) ? $output : [];
        
        return response()->json($response);
    }

    /**
     * Clone the subject assigned from an academic term to an academic term 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function cloning(Request $request)
    {
        $inputs = $request->all();
        $from_term = AcademicTerm::findOrFail($inputs['from_academic_term_id']);
        $to_term = AcademicTerm::findOrFail($inputs['to_academic_term_id']);

        if ($from_term && $to_term) {
            $result = AcademicTerm::cloneSubjectAssigned($from_term->academic_term_id, $to_term->academic_term_id);
            
            ($result)
                ? $this->setFlashMessage(' Cloned!!! ' . $from_term->academic_term .
                    ' Academic Term Subject Assigned To Class Rooms and Tutors has been Cloned to ' . $to_term->academic_term , 1)
                : $this->setFlashMessage('Error!!! Unable to clone record kindly retry.', 2);
        }
    }

    /**
     * Get The Academic Terms Given the year id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function academicYears(Request $request)
    {
        $inputs = $request->all();

        return redirect('/academic-terms/' . $this->encode($inputs['academic_year_id']));
    }
}
