<?php

namespace App\Http\Controllers\Auth;

use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\RolesAndPermissions\Role;
use App\Models\Admin\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);

        $this->middleware('auth', ['except' => [
            'getLogin', 'postLogin', 'getRegister', 'postRegister', 'getVerify', 'getResetPassword', 'postResetPassword'
        ]]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make('password'),
            'verified' => 1,
            'verification_code' => $data['verification_code'],
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $login = $request->input('login');
        //Check login field
        $login_type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_no';
        //Merge login field into the request with either email or phone_no as key
        $request->merge([$login_type => $login]);
        //Validate and set the credentials
        if($login_type == 'email'){
            $this->validate($request, [
                'email' => 'required|email', 'password' => 'required',
            ]);
            $credentials = $request->only('email', 'password');
        }else{
            $this->validate($request, [
                'phone_no' => 'required', 'password' => 'required',
            ]);
            $credentials = $request->only('phone_no', 'password');
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

//        $credentials = $this->getCredentials($request);

        //////////////////////////////////////////////// start: KHEENGZ CUSTOM CODE ////////////////////////////////////////////
        // Allow Only Active Users Where status and verified is 1
        $credentials = array_add($credentials, 'status', 1);
        $credentials = array_add($credentials, 'verified', 1);
        //////////////////////////////////////////////// end: KHEENGZ CUSTOM CODE //////////////////////////////////////////////

        if (Auth::guard($this->getGuard())->attempt($credentials, $request->has('remember'))) {
//            dd(Auth::user());
            if(Auth::user()->user_type_id === Sponsor::USER_TYPE)
                // redirect to the PARENT / STUDENT page
                return redirect('/home');
            return $this->handleUserWasAuthenticated($request, $throttles);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Handle a registration request for the application.
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function postResetPassword(Request $request)
    {
        $inputs = $request->all();

        $validator = Validator::make($inputs, [
            'email' => 'required|email'
        ], ['email.required' => 'A registered E-Mail Address is Required to reset your password!', 'email.email' => 'A Valid E-Mail Address is Required!!']);

        if ($validator->fails()) {
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/auth/login#')->withErrors($validator)->withInput();
        }
        //////////////////////////////////////////////////////////////////////// starts: KHEENGZ CUSTOM CODE////////////////////////////////////////////////////////
        //Set the verification code to any random 40 characters
        $password = strtoupper(str_random(8));
        $result = User::where('email', $inputs['email']);
        $user = ($result !== null) ? $result->first() : null;

        if ($user) {
            //Password Reset Mail Sending
            $content = "Welcome to Smart School application, kindly find below your new credentials to access the application. Thank You \n";
            $content .= "Here is your new password: <strong>" . $password . "</strong> ";
            Mail::send('emails.reset-password', ['user' => $user, 'content' => $content], function ($message) use ($user, $password) {
                $message->from(env('APP_MAIL'), env('APP_NAME'));
                $message->subject("Password Reset");
                $message->to($user->email);

                $user->password = Hash::make($password);
                $user->save();
                $this->setFlashMessage(' Reset Successful!!! Your Password has been reset' . ' kindly login to ' . $user->email . ' to view your new password', 1);
            });
            
        } else {
            $this->setFlashMessage(' Failed!!! Reset was not successful with this email ' . $inputs['email'] . ' kindly enter your registered email or contact your admin', 2);
        }
        return redirect('/auth/login');
        ///////////////////////////////////////////////////////////////////////// ends: KHEENGZ CUSTOM CODE////////////////////////////////////////////////////////
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function postRegister(Request $request)
//    {
//        $inputs = $request->all();
//        $validator = $this->validator($inputs);
//
//        if ($validator->fails()) {
//            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
//
//            $this->throwValidationException($request, $validator);
//        }
//
//        ///////////////////////////////////////////////////////// starts: KHEENGZ CUSTOM CODE//////////////////////////////////////////////////////////////
//        //Set the verification code to any random 40 characters
//        $inputs['verification_code'] = str_random(40);
//        $user = $this->create($inputs);
//
//        //Attach a role to the user
//        $user->roles()->attach(Role::DEFAULT_ROLE);

//        if($user){
//            //Verification Mail Sending
//            $content = 'Welcome to PLEDGE Application.
//            PLEDGE is a new way of communicating with leaders of your country and make actionable decisions based on their political activities and achievements.
//            Thank You';
//            $result = Mail::send('emails.verification', ['user'=>$user, 'content'=>$content], function($message) use($user) {
//                $message->from(env('APP_MAIL'), env('APP_NAME'));
//                $message->subject("Account Verification");
//                $message->to($user->email);
//            });
//            if($result)
//                $this->setFlashMessage(' Registration Saved!!! A mail has been sent to '.$user->email, 1);
//
//        Auth::login($user);
//
//            return redirect($this->redirectPath());
//        }
        /////////////////////////////////////////////////////////// ends: KHEENGZ CUSTOM CODE//////////////////////////////////////////////////////////////////
//        $this->setFlashMessage(' Registration Saved!!! A mail has been sent to '.$user->email, 1);
//        return redirect('/merchants/profile');
//    }

}
