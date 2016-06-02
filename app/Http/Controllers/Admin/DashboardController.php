<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Accounts\Staff;
use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        $sponsors_count = User::where('user_type_id', Sponsor::USER_TYPE)->count();
        $staff_count = User::where('user_type_id', Staff::USER_TYPE)->count();
        $students_count = Student::count();
        return view('admin.dashboard', compact('sponsors_count','staff_count', 'students_count'));
    }

    /**
     * Gets The Number of Students Based on their gender
     * @return Response
     */
    public function getStudentsGender()
    {
        $male = Student::where('gender', 'Male')->where('status_id', Student::ACTIVE)->count();
        $female = Student::where('gender', 'Female')->where('status_id', Student::ACTIVE)->count();
        $response[] = ['label'=>'Male', 'color'=>'#CCC', 'data'=>$male, 'value'=>$male];
        $response[] = ['label'=>'Female', 'color'=>'#3CF', 'data'=>$female, 'value'=>$female];

        return response()->json($response);
    }

    /**
     * Gets The Number of Students Based on their gender
     * @return Response
     */
    public function getStudentsClasslevel()
    {
        $classlevels = ClassLevel::all();
        $response = [];
        foreach($classlevels as $classlevel){
            $sum = 0;
            foreach($classlevel->classRooms()->get() as $classroom){
                $sum += $classroom->studentClasses()->where('academic_year_id', AcademicYear::activeYear()->academic_year_id)->count();
            }
            $response[] = array(
                'y'=>$classlevel->classlevel,
                'a'=>$sum
            );
        }
        return response()->json($response);
    }

    /**
     * Send SMS
     */
    private function sendSMSAlert($msg, $no){
        $mobile_no = trim($no);
        $msg_sender = 'Solid Steps';
        if(substr($mobile_no, 0, 1) === '0'){
            $num = '234' . substr($mobile_no, 1);
        }elseif (substr($mobile_no, 0, 3) === '234') {
            $num = $mobile_no;
        }elseif (substr($mobile_no, 0, 1) === '+') {
            $num = substr($mobile_no, 1);
        }else{
            $num = '234' . $mobile_no;
        }
        $user = "ZumaComm";
        $password = "zuma123456";
        $number = (isset($num)) ? $num : $mobile_no;

        $url = 'http://107.20.195.151/mcast_ws/?user='.$user.'&password='.$password.'&from='.$msg_sender.'&to='.$number.'&message='.$msg;
//        $ret = file($url);

        return $url;
    }

    public function getStaff(){

//        $url = "http://107.20.195.151/mcast_ws/?user=$user&password=$password&from=Kheengz&to=2348030734377&message=message_testing";
//        $ret = file($url);
//        $url2 = "http://107.20.195.151/mcast_ws/?user=$user&password=$password&from=Kheengz&to=2348022020075&message=message_testing";
//        $ret2 = file($url2);
//        $staffs = User::where('user_type_id', 1)->get();
        $temp = '';
//        $staffs = User::where('user_type_id', Staff::USER_TYPE)->get();
//        foreach($staffs as $staff){
//            $msg = 'Username: ' . $staff->phone_no .' or ' . $staff->email;
//            $msg .= ' and Password: password then visit this link to login via portal.solidsteps.org' ;
//            $temp .= $this->sendSMS($msg, '08022020075')[0];
//            $temp = $temp . '<br>' . $this->sendSMS($msg, '08030737377')[0];
////            $this->sendSMS($msg, $staff->phone_no);
//
//        }
        $msg = 'Username: 08022020075 or kheengz@gmail.com';
        $msg .= ' and Password: password then visit this link to login via portal.solidsteps.org' ;
        $temp = $this->sendSMSAlert($msg, '08022020075')[0];
        echo $temp;
    }
}
