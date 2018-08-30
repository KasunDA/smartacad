<?php

namespace App\Http\Controllers\Admin\MasterRecords\Sessions;

use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\School\School;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AcademicYearsController extends Controller
{
    protected $school;
    /**
     * Make sure the user is logged in and The Record has been setup
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->school = School::mySchool();

        if ($this->school->setup == School::ACADEMIC_YEAR) {
            $this->setFlashMessage('Warning!!! Kindly Setup the Academic Years records Before Proceeding.', 3);
        }
    }
    
    /**
     * Display a listing of the Menus for Master Records.
     *
     * @return Response
     */
    public function index()
    {
        $academic_years = AcademicYear::all();

        return view('admin.master-records.sessions.academic-years', compact('academic_years'));
    }


    /**
     * Insert or Update the menu records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = $status = 0;

        // Validate TO Make Sure Only One Status is Set
        for ($j=0; $j<count($inputs['status']); $j++) {
            if($inputs['status'][$j] == '1') $status++;
        }

        if ($status != 1) {
            $this->setFlashMessage('Note!!! An Academic Year (Only One) Must Be Set To Active At Any Point In Time.', 2);
        } else {

            for ($i = 0; $i < count($inputs['academic_year_id']); $i++) {
                $academic_year = ($inputs['academic_year_id'][$i] > 0)
                    ? AcademicYear::find($inputs['academic_year_id'][$i])
                    : new AcademicYear();
                $academic_year->academic_year = $inputs['academic_year'][$i];
                $academic_year->status = $inputs['status'][$i];
                if ($academic_year->save()) {
                    $count = $count+1;
                }
            }
            //Update The Setup Process
            if ($this->school->setup == School::ACADEMIC_YEAR) {
                $this->school->setup = School::ACADEMIC_TERM;
                $this->school->save();
                
                return redirect('/academic-terms');
            }
            
            if($count > 0) $this->setFlashMessage($count . ' Academic Year has been successfully updated.', 1);
        }
        
        return redirect('/academic-years');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function delete($id)
    {
        $academic_year = AcademicYear::findOrFail($id);

        (($academic_year !== null) && $academic_year->delete())
            ? $this->setFlashMessage('  Deleted!!! '.$academic_year->academic_year.' Academic Year have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }
}
