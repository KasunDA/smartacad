<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Http\Controllers\Controller;
use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Accounts\Staff;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Users\User;
use App\Models\Admin\Users\UserType;
use Illuminate\Http\Request;
use Knp\Snappy\Pdf;
use stdClass;

class MessageController extends Controller
{
    /**
     * Get the default page of messaging
     * @return Response
     */
    public function getIndex()
    {
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('Select Academic Year', '');
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('Select Class Level', '');
        return view('admin.messages.index', compact('academic_years', 'classlevels'));
    }

    /**
     * Search For Students in a class room or class level for an academic year
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postListStudents(Request $request)
    {
        $inputs = $request->all();
        $class = (isset($inputs['classroom_id']) and $inputs['classroom_id'] != '') ? true : false;

        $students = StudentClass::where('academic_year_id', $inputs['academic_year_id'])
            ->where(function ($query) use ($class, $inputs) {
                //If a class is selected else return all the class level students
                ($class) ? $query->where('classroom_id', $inputs['classroom_id'])
                    : $query->whereIn('classroom_id', ClassRoom::where('classlevel_id', $inputs['classlevel_id'])->lists('classroom_id')->toArray());
            })->get();

        $response = array();
        $response['flag'] = 0;
        $studentsClass = [];

        if ($students->count() > 0) {
            //All the students in the class room for the academic year
            foreach ($students as $student) {
                if ($student->student()->first()->status_id == 1) {
                    $object = new stdClass();
                    $object->student_id = $this->getHashIds()->encode($student->student_id);
                    $object->name = $student->student()->first()->fullNames();
                    $object->student_no = $student->student()->first()->student_no;
                    $object->gender = $student->student()->first()->gender;
                    $object->classroom = $student->classRoom()->first()->classroom;
                    $object->sponsor = ($student->student()->first()->sponsor()->first())
                        ? $student->student()->first()->sponsor()->first()->fullNames() : '<span class="label label-danger">nil</span>';
                    $object->phone_no = ($student->student()->first()->sponsor()->first())
                        ? $student->student()->first()->sponsor()->first()->phone_no : '';
                    $object->sponsor_id = ($student->student()->first()->sponsor()->first())
                        ? $this->getHashIds()->encode($student->student()->first()->sponsor()->first()->user_id) : -1;
                    $studentsClass[] = $object;
                }
            }
            //Sort The Students by name
            usort($studentsClass, function ($a, $b) {
                return strcmp($a->name, $b->name);
            });

            $response['flag'] = 1;
            $response['Students'] = isset($studentsClass) ? $studentsClass : [];
        }
        echo json_encode($response);
    }

    /**
     * Display a listing of the Staffs using Ajax Datatable.
     * @return Response
     */
    public function postAllStaffs()
    {
        $iTotalRecords = User::whereIn('user_type_id', UserType::where('type', 2)->get(['user_type_id'])->toArray())
            ->where('user_type_id', '<>', Sponsor::USER_TYPE)->where('status', 1)->count();
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $q = @$_REQUEST['sSearch'];

        //List of Sponsors
        $staffs = User::whereIn('user_type_id', UserType::where('type', 2)->get(['user_type_id'])->toArray())
            ->where('user_type_id', '<>', Sponsor::USER_TYPE)->where('status', 1)->orderBy('first_name')->where(function ($query) use ($q) {
            if (!empty($q))
                $query->orWhere('first_name', 'like', '%' . $q . '%')->orWhere('last_name', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%')->orWhere('phone_no', 'like', '%' . $q . '%');
        });
        // iTotalDisplayRecords = filtered result count
        $iTotalDisplayRecords = $staffs->count();
        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        $i = $iDisplayStart;
        $allStaffs = $staffs->skip($iDisplayStart)->take($iDisplayLength)->get();
        foreach ($allStaffs as $staff) {
            $records["data"][] = array(
                '<input type="checkbox" name="phone_no[]" value="' . $staff->phone_no . '">',
                ($i++ + 1),
                $staff->fullNames(),
                $staff->phone_no,
                $staff->email,
                ($staff->gender) ? $staff->gender : '<span class="label label-danger">nil</span>',
                '<a target="_blank" href="/staffs/view/' . $this->getHashIds()->encode($staff->user_id) . '" class="btn btn-info btn-rounded btn-condensed btn-xs">
                     <span class="fa fa-eye-slash"></span>
                 </a>',
                '<button value="' . $staff->phone_no . '" class="btn btn-warning btn-rounded btn-condensed btn-xs send-message">
                     <span class="fa fa-envelope"></span>
                 </button>'
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = isset($iTotalDisplayRecords) ? $iTotalDisplayRecords : $iTotalRecords;

        echo json_encode($records);
    }

    /**
     * Let the lists of staffs / sponsors phone number to send messages to
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postMessageSelected(Request $request)
    {
        $inputs = $request->all();
        $response = array();
        $response['flag'] = 0;
        $nos = [];
        $count = count($inputs['phone_no']);

        if (isset($inputs['phone_no']) and $count > 0) {
            $nos = array_unique($inputs['phone_no']);
            $response['flag'] = 1;
        }
        $response['phone_no'] = $nos;
        $response['count'] = $count;

        echo json_encode($response);
    }

    /**
     * send messages to the phone numbers provided
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSend(Request $request)
    {
        $inputs = $request->all();
        $nos = explode(',', $inputs['phone_no']);
        $count = 0;

        if (isset($inputs['message']) and $inputs['message'] != '') {
            for ($i = 0; $i < count($nos); $i++) {
                $res = $this->sendSMS($inputs['message'], $nos[$i]);
//                if ($res == 200)
                $count++;
            }
            $this->setFlashMessage($count . ' individual message has been sent', 1);
//            if ($count > 0)
        }else{
            $this->setFlashMessage('The message Content was omitted...kindly fill it in, before sending the message', 2);
        }

        return redirect('/messages');
    }

    /**
     * send messages to all the active staffs / Sponsors
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSendAll(Request $request)
    {
        $inputs = $request->all();
        $type = $inputs['message_type'];
        $nos = [];
        $count = 0;

        if($type == '#sponsor'){
            $nos = User::where('user_type_id', Sponsor::USER_TYPE)->where('status', 1)->distinct()->lists('phone_no')->toArray();
        }elseif($type == '#staff'){
            $nos = User::whereIn('user_type_id', UserType::where('type', 2)->get(['user_type_id'])->toArray())
                ->where('user_type_id', '<>', Sponsor::USER_TYPE)->where('status', 1)->distinct()->lists('phone_no')->toArray();
        }

        //TODO :: uncomment
        for ($i = 0; $i < count($nos); $i++) {
//            $res = $this->sendSMS($inputs['message'], $nos[$i]);
//            if ($res == 200) $count++;
        }
        return redirect('/messages');
    }

    public function getPdf(){
//        $html = $this->get_web_page('http://smartschool.ekaruztech.com/exams/print-student-terminal-result/A4NRpXnla3J2GBK/4RQDK0nKQZEgdp2');
        $html = file_get_contents('http://localhost:8000/messages/print');
        dd($html);
        // or you can do it in two steps
//        $snappy = new Pdf();
//        $snappy->setBinary('/usr/local/bin/wkhtmltopdf');
        // Display the resulting pdf in the browser
        // by setting the Content-type header to pdf
        $snappy = new Pdf('/usr/local/bin/wkhtmltopdf');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="bill4.pdf"');
//        header('Pragma: public');
        echo $snappy->getOutputFromHtml($html['content']);
//        $snappy->generateFromHtml($html['content'], '/Users/User/Downloads/bill-123.pdf');
//        echo $snappy->getOutputFromHtml('<h1>Bill Show Me the money</h1><p>You owe me money, dude.</p>');
//        echo $snappy->getOutput('https://www.google.com');
    }

    function get_web_page( $url )
    {
        $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

        $options = array(

            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           =>false,        //set to GET
            CURLOPT_USERAGENT      => $user_agent, //set user agent
            CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
            CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }

    public function getPrint(){
        $dd = '<!DOCTYPE html>
<html lang="en">
<head>
    <head>
        <title>Student Terminal Result Sheet</title>
        <link href="'.$this->school_profile->getLogoPath().'" rel="shortcut icon">
        <style type="text/css">
            .table th,
            .table td {
            padding: 6px;
                line-height: 13px;
                text-align: left;
                vertical-align: top;
                border-top: 1px solid #dddddd;
            }
            #apDiv1 {
                position:absolute;
                left:0px;
                top:55px;
                width:750px;
                z-index:1;
                text-align: center;
            }
    #apDiv2 {
position:absolute;
left:779px;
top:10px;
width:315px;
z-index:2;
}
#apDi1 {
position:absolute;
left:0px;
                top:13px;
                width:650px;
                z-index:3;
            }
            .style1 {font-size: x-small;}
        </style>
    </head>
<body style="padding-top: 15px; background-color: white" bgcolor="white">
    <div class="container-fluid">
        <div class="you">
            <div align="center" style="width:100%;">
                <div align="center"><img style="width: 80px; height: 80px;;" src="'.$this->school_profile->getLogoPath().'" alt="School Logo"/></div>

                <div style="color:#666; font-size: 36px; font-weight: bolder;">
                    '.strtoupper($this->school_profile->full_name).'
                </div>
                <div style="font-size: 14px; font-weight: bold;">'.$this->school_profile->address.'</div>
                <h5>'.$this->school_profile->motto.'</h5>
                <h6>'.$this->school_profile->website.'</h6>
            </div>
        </div>
    </div>
</body>
</html>';
        return $dd;
    }
}
