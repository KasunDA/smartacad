<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Admin\Accounts\Sponsor;
use App\Models\School\Setups\Lga;
use App\Models\School\Setups\Salutation;
use App\Models\School\Setups\State;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SponsorController extends Controller
{
    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function getIndex()
    {
        $sponsors = Sponsor::orderBy('first_name')->get();
        return view('admin.accounts.sponsors.index', compact('sponsors'));
    }

    /**
     * Displays the Sponsor profiles details
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getView($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);
        $sponsor= (empty($decodeId)) ? abort(305) : Sponsor::findOrFail($decodeId[0]);
        $user = $sponsor->user()->first();
        return view('admin.accounts.sponsors.view', compact('user', 'sponsor'));
    }

    /**
     * Displays the staff profiles details for editing
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getEdit($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);

        $sponsor = (empty($decodeId)) ? abort(305) : Sponsor::findOrFail($decodeId[0]);
        $lga = $sponsor->lga()->first();
        $salutations = Salutation::orderBy('salutation')->lists('salutation', 'salutation_id')->put('', 'Nothing Selected');
        $states = State::orderBy('state')->lists('state', 'state_id')->put('', 'Nothing Selected');
        $lgas = ($sponsor->lga_id !== null) ? Lga::where('state_id', $sponsor->lga()->first()->state_id)->lists('lga', 'lga_id')->put('', 'Nothing Selected') : null;

        return view('admin.accounts.sponsors.edit', compact('sponsor', 'salutations', 'states', 'lga', 'lgas'));
    }

    /**
     * Store the form for modifying a resource.
     * @param  Request $request
     * @return Response
     */
    public function postEdit(Request $request)
    {

        $inputs = $request->all();
        $sponsor = (empty($inputs['sponsor_id'])) ? abort(403) : Sponsor::findOrFail($inputs['sponsor_id']);
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
        $user = $sponsor->user()->first();

//        dd($inputs);
        $validator = Validator::make($inputs, [
            'salutation_id' => 'required',
            'first_name' => 'required',
            'other_name' => 'required',
            'email' => 'email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'phone_no' => 'required|max:15|min:11|unique:users,username,'.$user->user_id.',user_id',
            'dob' => 'required',
            'address' => 'required',
        ], $messages);
        //Validate Request Inputs
        if ($validator->fails()) {
            $this->setFlashMessage('  Error!!! You have error(s) while filling the form.', 2);
            return redirect('/sponsors/edit/'.$this->getHashIds()->encode($inputs['sponsor_id']))->withErrors($validator)->withInput();
        }
        //Update The Account Record

        $user->email = $inputs['email'];
        $user->username = $inputs['phone_no'];
        $user->display_name = $inputs['first_name'] . ' ' . $inputs['other_name'];
        $user->save();
        // Update the user record
        $sponsor->update($inputs);

        if ($sponsor) {
            // Set the flash message
            $this->setFlashMessage('  Updated!!! ' . $sponsor->fullNames() . ' have successfully been updated.', 1);
            // redirect to the staff page
            return redirect('/sponsors');
        }
    }
}
