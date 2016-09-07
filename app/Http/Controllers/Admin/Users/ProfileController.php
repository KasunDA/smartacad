<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\Admin\Accounts\Sponsor;
use App\Models\School\Setups\Lga;
use App\Models\School\Setups\Salutation;
use App\Models\School\Setups\State;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Validator;


class ProfileController extends Controller
{
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'first_name.required' => 'The First Name is Required!',
            'last_name.required' => 'The Last Name is Required!',
            'gender.required' => 'Gender is Required!',
            'dob.required' => 'Date of Birth is Required!',
            'phone_no.required' => 'Phone Number is Required!',
        ];
        return Validator::make($data, [
            'first_name' => 'required|max:100|min:2',
            'last_name' => 'required|max:100|min:2',
            'gender' => 'required',
            'dob' => 'required',
            'phone_no' => 'required|min:10',
        ], $messages);
    }

    /**
     * Displays the user profiles details
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $user = Auth::user();

        return view($this->view . 'profile.view', compact('user'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getEdit()
    {
        $user = Auth::user();
        if($user){
            $salutations = Salutation::orderBy('salutation')->lists('salutation', 'salutation_id')->prepend('Select Title', '');
            $states = State::orderBy('state')->lists('state', 'state_id')->prepend('Select State', '');
            $lga = ($user->lga()->first()) ? $user->lga()->first() : null;
            $lgas = ($user->lga_id > 0) ? Lga::where('state_id', $user->lga()->first()->state_id)->lists('lga', 'lga_id')->prepend('Select L. G. A', '') : null;
        }else{
            session()->put('active', 'avatar');
        }

        return view($this->view . 'profile.edit', compact('user', 'salutations', 'states', 'lga', 'lgas'));
    }

    /**
     * Update the users profile
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postEdit(Request $request)
    {
        $inputs = $request->all();
        $user = Auth::user();
        $messages = [
            'salutation_id.required' => 'Title is Required!',
            'first_name.required' => 'First Name is Required!',
            'last_name.required' => 'Last Name is Required!',
            'email.unique' => 'This E-Mail Address Has Already Been Used!',
            'phone_no.unique' => 'The Mobile Number Has Already Been Used!',
            'gender.required' => 'Gender is Required!',
            'dob.required' => 'Date of Birth is Required!',
//            'address.required' => 'Contact Address is Required!',
        ];
        $validator = Validator::make($inputs, [
            'salutation_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'phone_no' => 'required|max:15|min:11|unique:users,phone_no,'.$user->user_id.',user_id',
            'gender' => 'required',
            'dob' => 'required',
//            'address' => 'required',
        ], $messages);

        if ($validator->fails()) {
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/profiles/edit')->withErrors($validator)->withInput();
        }

        $user->update($inputs);
        // :: TODO //Update Address
        $this->setFlashMessage($user->fullNames() . ', Your Profile has been successfully updated.', 1);

        return redirect('/profiles');
    }

    /**
     * Change password of logged in user via profile modal
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    /*public function postChange(Request $request)
    {
        $user = Auth::user();
        $output = array();
        $output['status'] = 0;

        //Validate if the password match the current password
        if (! Hash::check($request->password, $user->password) ) {
            $output['msg'] = 'Warning!!! '.$user->first_name.', Your Old Password Credential did not match your current';
            //validate if the new and confirm password match
        }elseif($request->password_confirmation !== $request->new_password){
            $output['msg'] = 'Warning!!! '.$user->first_name.', Your New and Confirm Password Credential did not match';
            //Store the password...
        }else{
            $user->fill(['password' => Hash::make($request->new_password)])->save();
            $output['status'] = 1;
            $output['msg'] = 'Changed!!! '.$user->username.' Your password change was successful.';
        }
        return Response::json($output);
    }*/

    /**
     * Change password form via logged in user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postChangePassword(Request $request)
    {
        $inputs = $request->all();
        $user = Auth::user();

        //Keep track of selected tab
        session()->put('active', 'password');

        //Validate if the password match the current password
        if (!Hash::check($inputs['password'], $user->password)) {
            return redirect('/profiles/edit')->withErrors([
                'password' => 'Warning!!! ' . $user->fullNames() . ', Your Old Password Credential did not match your current'
            ]);
        }
        if ($request->password_confirmation !== $request->new_password) {
            return redirect('/profiles/edit')->withErrors([
                'password' => 'Warning!!! ' . $user->fullNames() . ', Your New and Confirm Password Credential did not match'
            ]);
        }
//         Store the password...
        $user->fill(['password' => Hash::make($request->new_password)])->save();
        // Set the flash message
        $this->setFlashMessage('Changed!!! ' . $user->fullNames() . ' Your password change was successful.', 1);
        //Keep track of selected tab
        session()->put('active', 'info');
        return redirect('/profiles/');
    }

    /**
     * Profile Picture Upload
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
    */
    public function postAvatar(Request $request)
    {
        if ($request->file('avatar')) {
            $file = $request->file('avatar');
            $filename = $file->getClientOriginalName();
            $img_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            $user = Auth::user();
            $user->avatar = $user->user_id . '_avatar.' . $img_ext;
            Input::file('avatar')->move($user->avatar_path, $user->user_id . '_avatar.' . $img_ext);

            $user->save();
            $this->setFlashMessage(' Your profile picture has been successfully uploaded.', 1);
            return redirect('/profiles');
        }
    }
}