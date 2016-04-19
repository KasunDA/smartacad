<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Admin\RolesAndPermissions\Role;
use App\Models\Admin\Users\UserType;
use App\Models\School\Setups\Salutation;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class AccountsController extends Controller
{
    /**
     * Redirects To The Inmates Default Page
     * @var string
     */
    protected $redirectTo = '/accounts/create';


    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getCreate()
    {
        $salutations = Salutation::all();
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

        //Set the verification code to any random 40 characters and password to random 8 characters
        $verification_code = str_random(40);
        $password = str_random(8);
        $input['verification_code'] = $verification_code;
        $input['password'] = $password;
        $temp = '.';

        // Store the User...
        $user = $this->newUser($input);

        $role = Role::where('user_type_id', $input['user_type_id'])->first();
        $user->attachRole($role);
        ///////////////////////////////////////////////////////// mail sending using $user object ///////////////////////////////////////////
//        if($user){
//            //Assign a role to the user
//            //Verification Mail Sending
//            $content = 'Welcome to printivo, kindly click on the verify link below to complete your registration. Thank You';
//            $content .= "Here are your credentials <br> Username: <strong>" . $user->email . "</strong> <br>";
//            $content .= "Password: <strong>" . $password . "</strong> ";
//            $result = Mail::send('emails.verify', ['user'=>$user, 'content'=>$content], function($message) use($user) {
//                $message->from(env('APP_MAIL'), env('APP_NAME'));
//                $message->subject("Account Verification");
//                $message->to($user->email);
//            });
//            if($result) $temp = ' and a mail has been sent to '.$user->email;
//        }
        // Set the flash message
        $this->setFlashMessage('Saved!!! '.$user->email.' have successfully been saved'.$temp, 1);
        // redirect to the create new warder page
        return redirect('accounts/create');
    }
}
