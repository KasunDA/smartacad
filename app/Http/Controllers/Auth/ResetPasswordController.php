<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

//        $inputs = $request->all();
//
//        //////////////////////////////////////////////////////////////////////// starts: KHEENGZ CUSTOM CODE////////////////////////////////////////////////////////
//        //Set the verification code to any random 40 characters
//        $password = strtoupper(str_random(8));
//        $result = User::where('email', $inputs['email']);
//        $user = ($result !== null) ? $result->first() : null;
//
//        if ($user) {
//            //Password Reset Mail Sending
//            $content = "Welcome to Smart School application, kindly find below your new credentials to access the application. Thank You \n";
//            $content .= "Here is your new password: <strong>" . $password . "</strong> ";
//            Mail::send('emails.reset-password', ['user' => $user, 'content' => $content], function ($message) use ($user, $password) {
//                $message->from(env('APP_MAIL'), env('APP_NAME'));
//                $message->subject("Password Reset");
//                $message->to($user->email);
//
//                $user->password = Hash::make($password);
//                $user->save();
//                $this->setFlashMessage(' Reset Successful!!! Your Password has been reset' . ' kindly login to ' . $user->email . ' to view your new password', 1);
//            });
//
//        } else {
//            $this->setFlashMessage(' Failed!!! Reset was not successful with this email ' . $inputs['email'] . ' kindly enter your registered email or contact your admin', 2);
//        }
//        return redirect('/auth/login');
        
        
        

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($response)
            : $this->sendResetFailedResponse($request, $response);
    }
}
