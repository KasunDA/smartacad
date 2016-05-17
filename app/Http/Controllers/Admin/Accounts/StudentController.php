<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Admin\Accounts\Student;
use App\Models\Admin\Users\User;
use App\Models\School\Setups\Salutation;
use App\Models\School\Setups\State;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class StudentController extends Controller
{

    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function getIndex()
    {
        $students = User::where('user_type_id', Student::USER_TYPE)->get();
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
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getCreate()
    {
        $salutations = Salutation::orderBy('salutation')->lists('salutation', 'salutation_id')->prepend('Select Title', '');
        $states = State::orderBy('state')->lists('state', 'state_id')->prepend('Select State', '');
        return view('admin.accounts.students.create', compact('salutations', 'states'));
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
}
