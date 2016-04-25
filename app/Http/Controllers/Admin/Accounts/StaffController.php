<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Admin\Accounts\Staff;
use App\Models\School\Setups\Lga;
use App\Models\School\Setups\Salutation;
use App\Models\School\Setups\State;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function getIndex()
    {
//        $users = User::where('user_type_id',4)->get();
        $staffs = Staff::orderBy('first_name')->get();
        return view('admin.accounts.staffs.index', compact('staffs'));
    }

    /**
     * Displays the Staff profiles details
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getView($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);
        $staff = (empty($decodeId)) ? abort(305) : Staff::findOrFail($decodeId[0]);
        $user = $staff->user()->first();
        return view('admin.accounts.staffs.view', compact('user', 'staff'));
    }

    /**
     * Displays the staff profiles details for editing
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getEdit($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);

        $staff = (empty($decodeId)) ? abort(305) : Staff::findOrFail($decodeId[0]);
        $lga = $staff->lga()->first();
        $salutations = Salutation::orderBy('salutation')->lists('salutation', 'salutation_id')->put('', 'Nothing Selected');
        $states = State::orderBy('state')->lists('state', 'state_id')->put('', 'Nothing Selected');
        $lgas = ($staff->lga_id) ? Lga::where('state_id', $staff->lga()->first()->state_id)->lists('lga', 'lga_id')->put('', 'Nothing Selected') : null;

        return view('admin.accounts.staffs.edit', compact('staff', 'salutations', 'states', 'lga', 'lgas'));
    }

    /**
     * Store the form for modifying a resource.
     * @param  Request $request
     * @return Response
     */
    public function postEdit(Request $request)
    {

        $inputs = $request->all();
        $staff = (empty($inputs['staff_id'])) ? abort(403) : Staff::findOrFail($inputs['staff_id']);
        $messages = [
            'salutation_id.required' => 'The Title is Required!',
            'first_name.required' => 'First Name is Required!',
            'other_name.required' => 'TOther Names is Required!',
            'email.unique' => 'This E-Mail Address Has Already Been Assigned!',
            'phone_no.unique' => 'The Mobile Number Has Already Been Assigned!',
            'phone_no.required' => 'The Mobile Number is Required!',
            'gender.required' => 'Gender is Required!',
            'dob.required' => 'Date of Birth is Required!',
            'address.required' => 'Contact Address is Required!',
        ];
        $user = $staff->user()->first();

        $validator = Validator::make($inputs, [
            'salutation_id' => 'required',
            'first_name' => 'required',
            'other_name' => 'required',
            'email' => 'email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'phone_no' => 'required|max:15|min:11|unique:users,username,'.$user->user_id.',user_id',
            'gender' => 'required',
            'dob' => 'required',
            'address' => 'required',
        ], $messages);
        //Validate Request Inputs
        if ($validator->fails()) {
            $this->setFlashMessage('  Error!!! You have error(s) while filling the form.', 2);
            return redirect('/staffs/edit/'.$this->getHashIds()->encode($inputs['staff_id']))->withErrors($validator)->withInput();
        }
        //Update The Account Record

        $user->email = $inputs['email'];
        $user->username = $inputs['phone_no'];
        $user->display_name = $inputs['first_name'] . ' ' . $inputs['other_name'];
        $user->save();
        // Update the user record
        $staff->update($inputs);

        if ($staff) {
            // Set the flash message
            $this->setFlashMessage('  Updated!!! ' . $staff->fullNames() . ' have successfully been updated.', 1);
            // redirect to the staff page
            return redirect('/staffs');
        }
    }
}
