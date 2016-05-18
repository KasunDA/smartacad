<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\Users\User;
use App\Models\School\Setups\Salutation;
use App\Models\School\Setups\State;
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
        $staff = (empty($decodeId)) ? abort(305) : User::findOrFail($decodeId[0]);
        return view('admin.accounts.staffs.view', compact('staff'));
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

        $staff = (empty($decodeId)) ? abort(305) : User::findOrFail($decodeId[0]);
        $salutations = Salutation::orderBy('salutation')->lists('salutation', 'salutation_id')->prepend('Select Title', '');
        $states = State::orderBy('state')->lists('state', 'state_id')->prepend('Select State', '');
        $lga = ($staff->lga()->first()) ? $staff->lga()->first() : null;
        $lgas = ($staff->lga_id > 0) ? Lga::where('state_id', $staff->lga()->first()->state_id)->lists('lga', 'lga_id')->prepend('Select L.G.A', '') : null;

        return view('admin.accounts.staffs.edit', compact('staff', 'salutations', 'states', 'lga', 'lgas'));
    }

    /**
     * Update the users profile
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postEdit(Request $request)
    {
        $inputs = $request->all();
        $user = (empty($inputs['user_id'])) ? abort(305) : User::findOrFail($inputs['user_id']);
        $messages = [
            'salutation_id.required' => 'Title is Required!',
            'first_name.required' => 'First Name is Required!',
            'last_name.required' => 'Last Name is Required!',
            'email.unique' => 'This E-Mail Address Has Already Been Assigned!',
            'phone_no.unique' => 'The Mobile Number Has Already Been Assigned!',
            'gender.required' => 'Gender is Required!',
            'dob.required' => 'Date of Birth is Required!',
//            'address.required' => 'Contact Address is Required!',
        ];
        $validator = Validator::make($inputs, [
            'salutation_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'phone_no' => 'required|max:15|min:11|unique:users,phone_no,' . $user->user_id . ',user_id',
            'gender' => 'required',
            'dob' => 'required',
//            'address' => 'required',
        ], $messages);

        if ($validator->fails()) {
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/staffs/edit/' . $this->getHashIds()->encode($inputs['user_id']))->withErrors($validator)->withInput();
        }

        $user->update($inputs);
        // :: TODO //Update Address
        $this->setFlashMessage('Staff ' . $user->fullNames() . ', Information has been successfully updated.', 1);

        return redirect('/staffs');
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
