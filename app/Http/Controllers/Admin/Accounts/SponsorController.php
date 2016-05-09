<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Users\User;
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
        $sponsors = User::where('user_type_id', Sponsor::USER_TYPE)->get();
        return view('admin.accounts.sponsors.index', compact('sponsors'));
    }

    /**
     * Displays the Staff profiles details
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getView($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);
        $sponsor = (empty($decodeId)) ? abort(305) : User::findOrFail($decodeId[0]);
        return view('admin.accounts.sponsors.view', compact('sponsor'));
    }

    /**
     * Displays the Staff profiles details for editing
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getEdit($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);

        $sponsor = (empty($decodeId)) ? abort(305) : User::findOrFail($decodeId[0]);
        $salutations = Salutation::orderBy('salutation')->lists('salutation', 'salutation_id')->prepend('Select Title', '');
        $states = State::orderBy('state')->lists('state', 'state_id')->prepend('Select State', '');
        $lga = ($sponsor->lga()->first()) ? $sponsor->lga()->first() : null;
        $lgas = ($sponsor->lga_id > 0) ? Lga::where('state_id', $sponsor->lga()->first()->state_id)->lists('lga', 'lga_id')->prepend('Select L.G.A', '') : null;

        return view('admin.accounts.sponsors.edit', compact('sponsor', 'salutations', 'states', 'lga', 'lgas'));
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
//            'email.unique' => 'This E-Mail Address Has Already Been Assigned!',
            'phone_no.unique' => 'The Mobile Number Has Already Been Assigned!',
            'dob.required' => 'Date of Birth is Required!',
//            'address.required' => 'Contact Address is Required!',
        ];
        $validator = Validator::make($inputs, [
            'salutation_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
//            'email' => 'required|email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'phone_no' => 'required|max:15|min:11|unique:users,phone_no,'.$user->user_id.',user_id',
            'dob' => 'required',
//            'address' => 'required',
        ], $messages);

        if ($validator->fails()) {
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/sponsors/edit/'.$this->getHashIds()->encode($inputs['user_id']))->withErrors($validator)->withInput();
        }

        $user->update($inputs);
        // :: TODO //Update Address
        $this->setFlashMessage('Sponsor ' . $user->fullNames() . ', Information has been successfully updated.', 1);

        return redirect('/sponsors');
    }
}
