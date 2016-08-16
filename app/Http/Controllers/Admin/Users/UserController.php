<?php

namespace App\Http\Controllers\Admin\Users;

use App\Models\Admin\RolesAndPermissions\Role;
use App\Models\Admin\Users\User;
use App\Models\Admin\Users\UserType;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
    /**
     * Redirects To The Inmates Default Page
     * @var string
     */
    protected $redirectTo = '/users';

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'first_name.required' => 'First Name is Required!',
            'last_name.required' => 'Last Name is Required!',
            'email.unique' => 'This E-Mail Address Has Already Been Assigned!',
            'phone_no.unique' => 'The Mobile Number Has Already Been Assigned!',
            'gender.required' => 'Gender is Required!',
            'user_type_id.required' => 'User Type is Required!',
        ];
        return Validator::make($data, [
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'email' => 'required|email|max:255|unique:users,email',
            'phone_no' => 'max:15|min:11|unique:users,phone_no',
            'user_type_id' => 'required',
        ], $messages);
    }

    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function getIndex()
    {
        $users = User::orderBy('first_name')->whereIn('user_type_id', UserType::where('type', 2)->get(['user_type_id'])->toArray())->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Display a listing of the Users using Ajax Datatable.
     * @return Response
     */
    public function postAllUsers()
    {

        $iTotalRecords = User::orderBy('first_name')->whereIn('user_type_id', UserType::where('type', 2)->get(['user_type_id'])->toArray())->count();;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $q = @$_REQUEST['sSearch'];

        $users = User::orderBy('first_name')->whereIn('user_type_id', UserType::where('type', 2)->get(['user_type_id'])->toArray())
        ->where(function ($query) use ($q) {
            //Filter by either email, name or phone number
            if (!empty($q))
                $query->orWhere('first_name', 'like', '%'.$q.'%')->orWhere('last_name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')->orWhere('phone_no', 'like', '%'.$q.'%');
        });
        // iTotalDisplayRecords = filtered result count
        $iTotalDisplayRecords = $users->count();


        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        $i = $iDisplayStart;
        $allUsers = $users->skip($iDisplayStart)->take($iDisplayLength)->get();
        foreach ($allUsers as $user){
            $status = ($user->status == 1)
                ? '<button value="'.$user->user_id.'" rel="2" class="btn btn-success btn-rounded btn-condensed btn-xs user_status">Deactivate</button>'
                : '<button value="'.$user->user_id.'" rel="1" class="btn btn-danger btn-rounded btn-condensed btn-xs user_status">Activate</button>';
            $records["data"][] = array(
                ($i++ + 1),
                $user->fullNames(),
                $user->phone_no,
                $user->email,
                $user->userType()->first()->user_type,
                $status,
                '<a target="_blank" href="/users/view/'.$this->getHashIds()->encode($user->user_id).'" class="btn btn-info btn-rounded btn-condensed btn-xs">
                     <span class="fa fa-eye-slash"></span>
                 </a>',
                '<a target="_blank" href="/users/edit/'.$this->getHashIds()->encode($user->user_id).'" class="btn btn-warning btn-rounded btn-condensed btn-xs">
                     <span class="fa fa-edit"></span>
                 </a>',
                '<button class="btn btn-danger btn-rounded btn-xs delete_user" value=".'.$user->user_id.'">
                    <span class="fa fa-trash-o"></span>
                 </button>'
//                '<span class="label label-sm label-'.(key($status)).'">'.(current($status)).'</span>',
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = isset($iTotalDisplayRecords) ? $iTotalDisplayRecords :$iTotalRecords;

        echo json_encode($records);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getCreate()
    {
        $user_types = UserType::where('type', 2)->orderBy('user_type')->get();
        return view('admin.users.create', compact('user_types'));
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
            return redirect('/users/create')->withErrors($this->validator($input))->withInput();
        }

        //Set the verification code to any random 40 characters and password to random 8 characters
        $verification_code = str_random(40);
        $password = str_random(8);
        $input['verification_code'] = $verification_code;
        $input['password'] = $password;
        $temp = '.';

        // Store the User...
        $user = $this->newUser($input);
        //Assign a role to the user
        $role = Role::where('user_type_id', $input['user_type_id'])->first();
        $user->attachRole($role);
        ///////////////////////////////////////////////////////// mail sending using $user object ///////////////////////////////////////////
        // TODO Sending of SMS
        // TODO:: Grab uploaded file sample of attaching files to mail
        //$attach = $request->file('file');
        if($user){
            //Verification Mail Sending
            $content = 'Welcome to Smart School, kindly click on the link below to complete your registration. Thank You';
            $content .= "Here are your credentials <br> Username: <strong>" . $user->email . "</strong>  or <stron>". $user->phone_no." </stron><br>";
            $content .= "Password: <strong>" . $password . "</strong> ";
            $result = Mail::send('emails.new-account', ['user'=>$user, 'content'=>$content], function($message) use($user) {
                $message->from(env('APP_MAIL'), env('APP_NAME'));
                $message->subject("Account Creation");
                $message->to($user->email);
                //Attach file
                //$message->attach($attach);
            });
            if($result) $temp = ' and a mail has been sent to '.$user->email;
        }
        // Set the flash message
        $this->setFlashMessage('Saved!!! '.$user->fullNames().' have successfully been saved'.$temp, 1);
        return redirect('users/create');
    }

    /**
     * Display the password change form
     * @return \Illuminate\View\View
     */
    public function getChange()
    {
        return view('admin.users.change-password');
    }

    /**
     * Change password form via logged in user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postChange(Request $request)
    {
        $inputs = $request->all();
        $user = Auth::user();

        //Validate if the password match the current password
        if (! Hash::check($inputs['password'], $user->password) ) {
            return redirect('/users/change')->withErrors([
                'password' => 'Warning!!! '.$user->first_name.', Your Old Password Credential did not match your current'
            ]);
        }
        if($request->password_confirmation !== $request->new_password){
            return redirect('/users/change')->withErrors([
                'password' => 'Warning!!! '.$user->first_name.', Your New and Confirm Password Credential did not match'
            ]);
        }
//         Store the password...
        $user->fill(['password' => Hash::make($request->new_password)])->save();
        // Set the flash message
        $this->setFlashMessage('Changed!!! '.$user->first_name.' Your password change was successful.', 1);
        // redirect to the create a new inmate page
        return redirect('/users/change/');
    }

    /**
     * Activate or Deactivate a User. Activate : 1, Deactivate : 0
     * @param  int  $user_id
     * @param  int  $status
     * @return Response
     */
    public function getStatus($user_id, $status)
    {
        $user = User::findOrFail($user_id);
        if($user !== null) {
            $user->status = $status;
            //Save The Project
            $user->save();
            ($status === '1')
                ? $this->setFlashMessage(' Activated!!! '.$user->fullNames().' have been activated.', 1)
                : $this->setFlashMessage(' Deactivated!!! '.$user->fullNames().' have been deactivated.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to perform task try again.', 2);
        }
    }

    /**
     * Displays the user profiles details
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getView($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);
        $userView = (empty($decodeId)) ? abort(305) : User::findOrFail($decodeId[0]);
        return view('admin.users.view', compact('userView'));
    }

    /**
     * Displays the user profiles details for editing
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getEdit($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);

        $user = (empty($decodeId)) ? abort(305) : User::findOrFail($decodeId[0]);
        $user_types = UserType::where('type', 2)->lists('user_type', 'user_type_id')->prepend('Select User Type', '');

        return view('admin.users.edit', compact('user','user_types'));
    }

    /**
     * Store the form for creating a new resource.
     * @param  Request $request
     * @return Response
     */
    public function postEdit(Request $request)
    {
        //Keep track of selected tab
        session()->put('active', 'info');

        $inputs = $request->all();
        $user = (empty($inputs['user_id'])) ? abort(403) : User::findOrFail($inputs['user_id']);
        $messages = [
            'status.required' => 'The User Status is Required!',
            'first_name.required' => 'First Name is Required!',
            'last_name.required' => 'Last Name is Required!',
            'email.unique' => 'This E-Mail Address Has Already Been Assigned!',
            'phone_no.unique' => 'The Mobile Number Has Already Been Assigned!',
            'gender.required' => 'Gender is Required!',
        ];
        $validator = Validator::make($inputs, [
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'email' => 'required|email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'phone_no' => 'required|max:15|min:11|unique:users,phone_no,'.$user->user_id.',user_id',
            'status' => 'required',
        ], $messages);
        //Validate Request Inputs
        if ($validator->fails()) {
            $this->setFlashMessage('  Error!!! You have error(s) while filling the form.', 2);
            return redirect('/users/edit/'.$this->getHashIds()->encode($inputs['user_id']))->withErrors($validator)->withInput();
        }
        // Update the user record
        $user->update($inputs);

        if ($user) {
            // Set the flash message
            $this->setFlashMessage('  Updated!!! ' . $user->fullNames() . ' have successfully been updated.', 1);
            // redirect to the create Committee page and enable the take roll call link
            return redirect('/users');
        }
    }

    /**
     * Profile Picture Upload
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAvatar(Request $request)
    {
        $inputs = Input::all();
        if ($request->file('avatar')) {
            $file = $request->file('avatar');
            $filename = $file->getClientOriginalName();
            $img_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            $user = (empty($inputs['user_id'])) ? abort(403) : User::findOrFail($inputs['user_id']);
            $user->avatar = $user->user_id . '_avatar.' . $img_ext;
            Input::file('avatar')->move($user->avatar_path, $user->user_id . '_avatar.' . $img_ext);

            $user->save();
            $this->setFlashMessage($user->fullNames() . '  profile picture has been successfully uploaded.', 1);
            return redirect('/users/view/'.$this->getHashIds()->encode($inputs['user_id']));
        }
    }

    /**
     * Delete a Users Record
     * @param $id
     */
    public function getDelete($id)
    {
        $user = User::findOrFail($id);
        //Delete The Record
        if($user){
            $user->delete();
            $this->setFlashMessage('  Deleted!!! '.$user->fullNames().' User have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * This will be usedto upload profile image of the user
     * @return mixed
     */
    public function postUploadPicture()
    {
        $inputs = Input::all();
        $file = Input::file('file');

        if (!is_null($file)) {

            $filename = $file->getClientOriginalName();
            $img_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            $user = (empty($inputs['user_id'])) ? abort(305) : User::findOrFail($inputs['user_id']);
            $destinationPath = 'uploads/avartars/';

            $user->avatar = $destinationPath . '' . $user->user_id . '_avatar.' . $img_ext;
            Input::file('file')->move($destinationPath, $user->user_id . '_avatar.' . $img_ext);

            $result = $user->save();
            if ($result) {
                return '<div class="cropping-image-wrap"><img src="/'.$user->avatar.'?'.time().'" class="img-thumbnail" id="crop_image"/></div>';;
            } else {
                return '<div class="alert alert-danger">This format of image is not supported</div>';
            }
        } else {
            return '<div class="alert alert-danger">How did you do that?O_o</div>';
        }
    }

    /**
     *This is used to crop the image before upload is done
     * @return mixed
     */
    public function postCropPicture()
    {
        $inputs = Input::all();

        $imgX = $inputs['ic_x'];
        $imgY = $inputs['ic_y'];
        $imgW = $inputs['ic_w'];
        $imgH = $inputs['ic_h'];

        $user = (empty($inputs['user_id'])) ? abort(305) : User::findOrFail($inputs['user_id']);

        $file = File::get($user->avatar);
        $image = Image::make($file);

//        // crop image
        $image =  $image->crop($imgW, $imgH,$imgX,$imgY);
        $result = $image->save($user->avatar,60);

        if($result){
            $file = File::get($user->avatar);
            Flysystem::connection('awss3')->put($user->avatar,$file);
            return $user->avatar . '?'.time();
//            return $user->avatar;
        }
    }
}
