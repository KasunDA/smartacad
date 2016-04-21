<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Accounts\Staff;
use App\Models\Admin\RolesAndPermissions\Role;
use App\Models\Admin\Users\User;
use App\Models\Admin\Users\UserType;
use App\Models\School\Setups\Salutation;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class AccountsController extends Controller
{
    /**
     * Redirects To The Inmates Default Page
     * @var string
     */
    protected $redirectTo = '/accounts/create';


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
            'other_name.required' => 'The Last Name is Required!',
            'salutation_id.required' => 'Salutation is Required!',
            'user_type_id.required' => 'The User Type is Required!',
            'email.required' => 'An E-Mail Address is Required!',
            'email.email' => 'A Valid E-Mail Address is Required!',
            'email.unique' => 'This E-Mail Address Has Been Taken or Assigned Already!',
        ];
        return Validator::make($data, [
            'salutation_id' => 'required',
            'first_name' => 'required|max:100|min:2',
            'other_name' => 'required|max:100|min:2',
            'email' => 'required|email|max:255|unique:users,email',
            'user_type_id' => 'required',
        ], $messages);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getCreate()
    {
        $salutations = Salutation::orderBy('salutation')->get();
        $user_types = UserType::where('type',2)->orderBy('user_type')->get();
        return view('admin.accounts.create', compact('user_types','salutations'));
    }

    /**
     * Store a newly created resource in storage.
     *
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
            return redirect('/accounts/create')->withErrors($this->validator($input))->withInput();
        }

//        dd($input);

        // Store the Record...
        $input['created_by'] = Auth::user()->user_id;
        if($input['user_type_id'] == Sponsor::USER_TYPE) {
            $sponsor = Sponsor::create($input);
            if($sponsor){
                if($this->createAccount($sponsor, $input['user_type_id'])){
                    $sponsor->sponsor_no = $sponsor->generateNo();
                    $sponsor->save();
                }

            }
        }elseif($input['user_type_id'] == Staff::USER_TYPE) {
            $staff = Staff::create($input);
            if($staff){
                if($this->createAccount($staff, $input['user_type_id'])){
                    $staff->staff_no = $staff->generateNo();
                    $staff->save();
                }
            }
        }

        // Set the flash message
        $this->setFlashMessage('Saved!!! '.$input['first_name'].' have successfully been saved', 1);
        // redirect to the create new warder page
        return redirect('accounts/create');
    }

    /**
     * Create a new user instance after a valid registration.
    */
    private function createAccount($sponsor, $user_type){
        $user = new User();
        $role = Role::where('user_type_id', $user_type)->first();
        $user->username = $sponsor->generateNo();
        $user->email = $sponsor->email;
        $user->password = Hash::make('password');
        $user->verified = 1;
        $user->display_name = $sponsor->fullNames();
        $user->verification_code = $verification_code = str_random(40);
        $user->user_type_id = $user_type;
        $user->save();
        if($user->user_id) $user->attachRole($role);

        return $user;
    }
}
