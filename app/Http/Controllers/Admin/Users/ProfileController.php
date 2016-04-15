<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
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
     *
     * Make sure the user is logged in
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getEdit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
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

        if ($this->validator($inputs)->fails()) {
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/profiles/edit')->withErrors($this->validator($inputs))->withInput();
        }

        $user->update($inputs);
        $this->setFlashMessage(' Your profile has been successfully updated.', 1);

        return redirect('/profiles');
    }

    /**
     * Displays the user profiles details
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $user = Auth::user();
        return view('admin.profile.view', compact('user'));
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

    public function postAvatar(Request $request)
    {
        if ($request->file('avatar')) {
            $file = $request->file('avatar');
            $name = $file->getClientOriginalName();
            $key = 'avatar/' . $name;
            Storage::put($key, file_get_contents($file));
        }
    }
}