<?php

namespace App\Http\Controllers\Front\Students;

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
            'gender.required' => 'A Gender is Required!',
            'dob.required' => 'Student Date Of Birth is required!',
        ];
        return Validator::make($data, [
            'first_name' => 'required|max:100|min:2',
            'last_name' => 'required|max:100|min:2',
            'gender' => 'required',
            'dob' => 'required',
        ], $messages);
    }

    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function getIndex()
    {
        $students = Auth::user()->students()->get();

        return view('front.students.index', compact('students'));
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
        return view('front.students.view', compact('student'));
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
        $states = State::orderBy('state')->lists('state', 'state_id')->prepend('- Select State -', '');
        $lga = ($student->lga()->first()) ? $student->lga()->first() : null;
        $lgas = ($student->lga_id > 0) ? Lga::where('state_id', $student->lga()->first()->state_id)->lists('lga', 'lga_id')->prepend('- Select L.G.A -', '') : null;

        return view('front.students.edit', compact('student', 'states', 'lga', 'lgas', 'status'));
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
            return redirect('/wards/edit/'.$this->getHashIds()->encode($inputs['student_id']))->withErrors($this->validator($inputs))->withInput();
        }
        $student->update($inputs);
        $this->setFlashMessage('Student ' . $student->fullNames() . ', Information has been successfully updated.', 1);

        return redirect('/wards');
    }
}
