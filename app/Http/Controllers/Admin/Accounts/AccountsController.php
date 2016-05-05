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
use Illuminate\Support\Facades\Mail;
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
            'phone_no.required' => 'A Mobile Number is Required!',
            'phone_no.unique' => 'The Mobile Number Has Already Been Assigned!',
            'email.email' => 'A Valid E-Mail Address is Required!',
            'email.unique' => 'This E-Mail Address Has Already Been Assigned!',
        ];
        return Validator::make($data, [
            'salutation_id' => 'required',
            'first_name' => 'required|max:100|min:2',
            'other_name' => 'required|max:100|min:2',
            'phone_no' => 'required|max:15|min:11|unique:users,username',
            'email' => 'email|max:255|unique:users,email',
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
    private function createAccount($data, $user_type){
        $user = new User();
        $role = Role::where('user_type_id', $user_type)->first();
//        $user->username = $data->generateNo();
        $user->username = $data->phone_no;
        $user->email = $data->email;
        $user->password = Hash::make('password');
        $user->verified = 1;
        $user->display_name = $data->fullNames();
        $user->verification_code = $verification_code = str_random(40);
        $user->user_type_id = $user_type;
        $user->save();

        if($user->user_id) {
            $user->attachRole($role);

            //$data->phone_no

            //Verification Mail Sending
//            $content = 'Welcome to Smart Edu Application.
//            SmartEdu is a solution uniquely tailored to meet the needs of Educators, Parents and Students.
//            SmartEdu helps Educators and Parents monitor and improve student academic performance.Thank You';
//            Mail::send('emails.account', ['user' => $user, 'content' => $content], function ($message) use ($user) {
//                $message->from(env('APP_MAIL'), env('APP_NAME'));
//                $message->subject("Account Creation");
//                $message->to($user->email);
//            });
        }


        return $user;
    }
}
