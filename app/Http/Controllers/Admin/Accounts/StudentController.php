<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Users\User;
use App\Models\School\Setups\Lga;
use App\Models\School\Setups\Salutation;
use App\Models\School\Setups\State;
use App\Models\School\Setups\Status;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{

    /**
     * Get a validator for an incoming registration request.
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'first_name.required' => 'The First Name is Required!',
            'last_name.required' => 'The Last Name is Required!',
            'sponsor_name.required' => 'Student Sponsor is Required!',
            'gender.required' => 'A Gender is Required!',
            'classlevel_id.required' => 'A Class Level is Required!',
            'classroom_id.required' => 'A Class Room is Required!',
        ];
        return Validator::make($data, [
            'sponsor_name' => 'required',
            'first_name' => 'required|max:100|min:2',
            'last_name' => 'required|max:100|min:2',
            'gender' => 'required',
            'classlevel_id' => 'required',
            'classroom_id' => 'required',
        ], $messages);
    }

    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function getIndex()
    {
        $students = Student::orderBy('first_name')->get();
        return view('admin.accounts.students.index', compact('students'));
    }

    /**
     * Displays the Staff profiles details
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getView($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);
        $student = (empty($decodeId)) ? abort(305) : Student::findOrFail($decodeId[0]);
        return view('admin.accounts.students.view', compact('student'));
    }

    /**
     * Displays the Staff profiles details for editing
     * @return \Illuminate\View\View
     */
    public function getCreate()
    {
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('Select Class Level', '');
        return view('admin.accounts.students.create', compact('classlevels'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request  $request
     * @return Response
     */
    public function postCreate(Request $request)
    {
        $input = $request->all();
        //Validate Request Inputs
        if ($this->validator($input)->fails())
        {
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/students/create')->withErrors($this->validator($input))->withInput();
        }

        if($input['sponsor_id'] < 1){
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/students/create')->withErrors(['Choose Student Sponsor From The List of Suggested Sponsors!'])->withInput();;
        }

        // Store the Record...
        $input['created_by'] = Auth::user()->user_id;
        $input['admitted_term_id'] = AcademicTerm::activeTerm()->academic_term_id;
        $student = Student::create($input);
        if($student->save()){
            $class = new StudentClass();
            $class->student_id = $student->student_id;
            $class->classroom_id = $input['classroom_id'];
            $class->academic_year_id = AcademicYear::activeYear()->academic_year_id;
            $class->save();
            $student->student_no = trim('STD'. str_pad($student->student_id, 5, '0', STR_PAD_LEFT));
            $student->save();
            // Set the flash message
            $this->setFlashMessage('Saved!!! '.$student->fullNames().' have successfully been saved', 1);
        }
        // redirect to the create new warder page
        return redirect('/students');
    }

    /**
     * Displays the Staff profiles details for editing
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getEdit($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);

        $student = (empty($decodeId)) ? abort(305) : Student::findOrFail($decodeId[0]);
        $status = Status::lists('status', 'status_id')->prepend('Select Status', '');
        $states = State::orderBy('state')->lists('state', 'state_id');
        $lga = ($student->lga()->first()) ? $student->lga()->first() : null;
        $lgas = ($student->lga_id > 0) ? Lga::where('state_id', $student->lga()->first()->state_id)->lists('lga', 'lga_id')->prepend('Select L.G.A', '') : null;
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('Select Class Level', '');
        $classroom = ($student->classroom_id) ? $student->classRoom()->first() : null;
        $classrooms = ($student->classroom_id > 0) ? ClassRoom::where('classlevel_id', $classroom->classlevel_id)
                ->lists('classroom', 'classroom_id')->prepend('Select Class Room', '') : null;

        return view('admin.accounts.students.edit', compact('student', 'states', 'lga', 'lgas', 'status', 'classlevels', 'classroom', 'classrooms'));
    }

    /**
     * Update the users profile
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postEdit(Request $request)
    {
        $inputs = $request->all();
        $student = (empty($inputs['student_id'])) ? abort(305) : Student::findOrFail($inputs['student_id']);

        if ($this->validator($inputs)->fails())
        {
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/students/edit')->withErrors($this->validator($inputs))->withInput();
        }

        if($inputs['sponsor_id'] < 1){
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/students/edit')->withErrors(['Choose Student Sponsor From The List of Suggested Sponsors!'])->withInput();;
        }

        $student->update($inputs);
        $this->setFlashMessage('Student ' . $student->fullNames() . ', Information has been successfully updated.', 1);

        return redirect('/students');
    }

    /**
     * Get The List of Sponsors
     */
    public function getSponsors()
    {
        $inputs = Input::get('term');
        $sponsors = User::where('user_type_id', Sponsor::USER_TYPE)->where('first_name', 'like', $inputs.'%')->get();
        $response = array();
        if($sponsors->count() > 0){
            foreach($sponsors as $sponsor){
                $response[] = array(
                    "value"=>$sponsor->fullNames(),
                    "id"=>$sponsor->user_id
                );
            }
        }else{
            $response[0]['id'] = -1;
            $response[0]['value'] = 'No Record Found';
        }
        echo json_encode($response);
    }
}
