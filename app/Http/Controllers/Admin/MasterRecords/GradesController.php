<?php

namespace App\Http\Controllers\Admin\MasterRecords;

use App\Models\Admin\MasterRecords\Classes\ClassGroup;
use App\Models\Admin\MasterRecords\Grade;
use App\Models\School\School;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class GradesController extends Controller
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

        ((int) $this->school->setup === School::GRADE)
            ? $this->setFlashMessage('Warning!!! Kindly Setup the Grades records Before Proceeding.', 3)
            : $this->middleware('setup');
    }
    /**
     * Display a listing of the Menus for Master Records.
     * @param bool $encodeId
     *
     * @return Response
     */
    public function index($encodeId = false)
    {
        $classgroup = ($encodeId) ? ClassGroup::findOrFail($this->decode($encodeId)) : false;
        $grades = ($classgroup)
            ? Grade::where('classgroup_id', $classgroup->classgroup_id)->get()
            : Grade::all();
        $classgroups = ClassGroup::pluck('classgroup', 'classgroup_id')
            ->prepend('Select Class Group', '');
        
        return view('admin.master-records.grades', compact('classgroups', 'grades', 'classgroup'));
    }

    /**
     * Insert or Update the menu records
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for ($i = 0; $i < count($inputs['grade_id']); $i++) {
            $grade = ($inputs['grade_id'][$i] > 0)
                ? Grade::find($inputs['grade_id'][$i])
                : new Grade();
            
            $grade->grade = $inputs['grade'][$i];
            $grade->grade_abbr = $inputs['grade_abbr'][$i];
            $grade->upper_bound = $inputs['upper_bound'][$i];
            $grade->lower_bound = $inputs['lower_bound'][$i];
            $grade->classgroup_id = $inputs['classgroup_id'][$i];
            
            if ($grade->save()) {
                $count = $count+1;
            }
        }
        
        //Update The Setup Process
        if ($this->school->setup == School::GRADE){
            $this->school->setup = School::COMPLETED;
            $this->school->save();
        
            return redirect('/dashboard');
        }
        if($count > 0) $this->setFlashMessage($count . ' Grades has been successfully updated.', 1);
        
        return redirect('/grades');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function delete($id)
    {
        $grade = Grade::findOrFail($id);

        (($grade !== null) && $grade->delete())
            ? $this->setFlashMessage('  Deleted!!! '.$grade->grade.' Grade have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }

    /**
     * Get The Grades Given the class group id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function classGroups(Request $request)
    {
        $inputs = $request->all();
        
        return redirect('/grades/' . $this->encode($inputs['classgroup_id']));
    }
}
