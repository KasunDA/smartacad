<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use App\Models\School\Setups\Lga;
use App\Models\School\Setups\Salutation;
use App\Models\School\Setups\State;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
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
    public function index()
    {
        $user = Auth::user();

        return view($this->view . 'profile.view', compact('user'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function edit()
    {
        $user = Auth::user();
        session()->put('active', 'info');

        if ($user) {
            $salutations = Salutation::orderBy('salutation')
                ->pluck('salutation', 'salutation_id')
                ->prepend('- Select Title -', '');
            $states = State::orderBy('state')
                ->pluck('state', 'state_id')
                ->prepend('- Select State -', '');
            $lga = ($user->lga()->first()) ? $user->lga()->first() : null;
           
            $lgas = ($user->lga_id > 0) 
                ? Lga::where('state_id', $user->lga()->first()->state_id)
                    ->pluck('lga', 'lga_id')
                    ->prepend('- Select L. G. A -', '') 
                : null;
        } else {
            session()->put('active', 'avatar');
        }

        return view($this->view . 'profile.edit', compact('user', 'salutations', 'states', 'lga', 'lgas'));
    }

    /**
     * Update the users profile
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
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
        $this->setFlashMessage($user->fullNames() . ', Your Profile has been successfully updated.', 1);

        return redirect('/profiles');
    }
    

    /**
     * Change password form via logged in user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $inputs = $request->all();
        $user = Auth::user();

        //Keep track of selected tab
        session()->put('active', 'password');

        //Validate if the password match the current password
        if (!Hash::check($inputs['password'], $user->password)) {
            return redirect('/profiles/edit')->withErrors([
                'password' => 'Warning!!! ' . $user->fullNames()
                    . ', Your Old Password Credential did not match your current'
            ]);
        }
        if ($request->password_confirmation !== $request->new_password) {
            return redirect('/profiles/edit')->withErrors([
                'password' => 'Warning!!! ' . $user->fullNames() 
                    . ', Your New and Confirm Password Credential did not match'
            ]);
        }
        
        $user->fill(['password' => Hash::make($request->new_password)])->save();
        $this->setFlashMessage('Changed!!! ' . $user->fullNames() . ' Your password change was successful.', 1);
        
        return redirect('/profiles/');
    }

    /**
     * Profile Picture Upload
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
    */
    public function uploadAvatar(Request $request)
    {
        if ($request->file('avatar')) {
            session()->put('active', 'avatar');
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

    public function dashboard()
    {
        $user = Auth::user();

        return view('admin.profile.dashboard', compact('user'));
    }

    public function subject()
    {
        $user = Auth::user();
        $conditions = "userId=".$user->user_id.'&url=/profiles/subject-details/';

        return view('admin.profile.subject', compact('user', 'conditions'));
    }

    public function subjectDetails($subjectId)
    {
        $subjectClassrooms = SubjectClassRoom::findOrFail($this->decode($subjectId));
        $user = Auth::user();
        $subjects = $user->subjectClassRooms()
            ->where('subject_id', $subjectClassrooms->subject_id)
            ->where('academic_term_id', $subjectClassrooms->academic_term_id)
            ->get();

        return view('admin.profile.subject-details', compact('subjects', 'user'));
    }

    public function classroom()
    {
        $user = Auth::user();
        $classrooms = $user->classMasters()->get();

        return view('admin.profile.classroom', compact('classrooms', 'user'));
    }
}