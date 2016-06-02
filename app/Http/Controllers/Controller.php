<?php

namespace App\Http\Controllers;

use App\Models\Admin\Users\User;
use App\Models\School\School;
use Hashids\Hashids;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public $school_profile;

    /**
     *
     * Make sure the user is logged in and Has Permission
     */
    public function __construct()
    {
        $this->middleware('auth');
        if( Schema::connection('admin_mysql')->hasTable('schools') ){
            $this->school_profile = School::findOrFail(env('SCHOOL_ID'));
        }

        //Check if the user has permission to perform such action
//        $this->checkPermission();
    }

    /**
     * Set The HashIds Secret Key, Length and Possible Characters Combinations
     * @return Hashids
     */
    public function getHashIds()
    {
        return new Hashids(env('APP_KEY'), 15, env('APP_CHAR'));
    }

    /**
     * @param  string  $msg
     * @param int $type
     * @return void
     */
    public function setFlashMessage($msg, $type)
    {
        $class1 = 'alert-info';
        $class2 = 'fa fa-info fa-2x';

        if($type == 1){
            $class1 = 'alert-success';
            $class2 = 'fa fa-thumbs-o-up fa-2x';
        }elseif($type == 2){
            $class1 = 'alert-danger';
            $class2 = 'fa fa-thumbs-o-down fa-2x';
        }

        $output =   '<div class="alert '.$class1.'" id="flash_message" role="alert">
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <i class="'.$class2.'"></i> <strong>' . $msg . '</strong>'.
            '</div>';
        \Session::flash('flash_message', $output);
    }

    /**
     * Check if the user has permission to perform such action
     * @return Response
     */
    protected function checkPermission(){
        if(Auth::check()) {
            $action = Route::currentRouteAction();
            $permission = substr($action, strripos($action, '\\') + 1);
            $method = explode('@', $permission)[1];
            if (substr($method, 0, 4) !== 'post' && !Auth::user()->can($permission)) {
                //        dd(Auth::user()->can($permission));
                abort(403);
            }
        }
    }

    /**
     * Create a new user instance after a valid registration.
     * @param  array  $data
     * @return User
     */
    protected function newUser(array $data)
    {
        return User::create([
            'email' => $data['email'],
            'verified' => 1,
//            'password' => Hash::make($data['password']),
            'password' => Hash::make('password'),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_no' => $data['phone_no'],
            'user_type_id' => $data['user_type_id'],
            'verification_code' => $data['verification_code']
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'first_name.required' => 'The First Name is Required!',
            'last_name.required' => 'The Last Name is Required!',
            'user_type_id.required' => 'The User Type is Required!',
            'phone_no.required' => 'The Mobile Number is Required!',
            'email.required' => 'A Valid E-Mail Address is Required!',
            'email.unique' => 'This E-Mail Address Has Been Taken or Assigned Already!',
        ];
        return Validator::make($data, [
            'first_name' => 'required|max:100|min:2',
            'last_name' => 'required|max:100|min:2',
            'email' => 'required|email|max:255|unique:users,email',
            'phone_no' => 'required',
            'user_type_id' => 'required',
        ], $messages);
    }

    /**
     * Send SMS
    */
    public function sendSMS($msg, $no){
        $mobile_no = trim($no);
        $msg_sender = 'SolidSteps';
        if(substr($mobile_no, 0, 1) === '0'){
            $no = '234' . substr($mobile_no, 1);
        }elseif (substr($mobile_no, 0, 3) === '234') {
            $no = $mobile_no;
        }elseif (substr($mobile_no, 0, 1) === '+') {
            $no = substr($mobile_no, 1);
        }else{
            $no = '234' . $mobile_no;
        }
        $message = str_replace("+", ' ', $msg);
        $message2 = urlencode($message);
        $username = "ZumaComm";
        $password = "zuma123456";

        $url = "http://107.20.195.151/mcast_ws/?user=$username&password=$password&from=$msg_sender&to=$no&message=$message2";
        $ret = file($url);

        return $ret;
    }
}
