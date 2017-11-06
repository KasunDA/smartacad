<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Mail\PasswordReset;
use App\Models\Admin\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

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
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $inputs = $request->all();

        //Set the password code to any random 8 characters
        $password = strtoupper(str_random(8));
        $result = User::where('email', $inputs['email']);
        $user = ($result !== null) ? $result->first() : null;

        if ($user) {
            //Password Reset Mail Sending
            $content = "<h4>Here is your new password: <i>" . $password . "</i></h4> ";
            Mail::to($user)->send(new PasswordReset($user, $content));

            $newPassword = str_replace("$2y$", "$2a$", bcrypt($password));
            $user->fill(['password' => $newPassword])->save();
            $this->setFlashMessage(' Reset Successful!!! Your Password has been reset' . ' kindly login to ' . $user->email . ' to view your new password', 1);

        } else {
            $this->setFlashMessage(' Failed!!! Reset was not successful with this email ' . $inputs['email'] . ' kindly enter your registered email or contact your admin', 2);
        }
        return redirect('/login');
    }
}
