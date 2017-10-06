<?php


/*
|--------------------------------------------------------------------------
| Application Routes HTTP, WEB, Client
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
//https://kheengz@bitbucket.org/ekaruztech/school.git
//$2y$10$4FIskwMg9Wd40XnizVOyfudWzJb56vLyAbXCWon/QOuhuYYWP.woS

Route::group(['middleware' => ['web']], function () {

    Route::get('/', function () {
        if(\Illuminate\Support\Facades\Auth::check()){
            return (\Illuminate\Support\Facades\Auth::user()->user_type_id == \App\Models\Admin\Accounts\Sponsor::USER_TYPE)
                ? redirect('/home') : redirect('/dashboard');
        }else{
            return redirect('/auth/login');
        }
    });
    
    Route::get('/phpinfo', function () {
        return view('/phpinfo');
    });
    
    //Dependent List Box
//    Route::group(array('prefix'=>'list-box'), function(){
//        // Ajax Get Local Governments Based on the state
//        Route::get('/lga/{id}', 'Admin\ListBoxController@lga');
//        Route::get('/academic-term/{id}', 'Admin\ListBoxController@academicTerm');
//        Route::get('/classroom/{id}', 'Admin\ListBoxController@classroom');
//    });

    //Front End
    Route::controllers([
        'home' => 'Front\HomeController',
        'wards' => 'Front\Students\StudentController',
        'wards-exams' => 'Front\Assessments\ExamsController',
        'wards-assessments' => 'Front\Assessments\AssessmentsController',
        'wards-attendances' => 'Front\Students\AttendancesController',
    ]);

    Route::auth();

    //Fees Routes
    Route::group(['prefix'=>'orders/', 'namespace' => 'Admin\Orders'], function () {
        Route::get('/paid/{termId?}', 'OrdersController@paid')->name('paidOrders');
        Route::get('/not-paid/{termId?}', 'OrdersController@notPaid')->name('notPaidOrders');
        Route::get('/all-orders/{termId?}', 'OrdersController@allOrders')->name('allOrders');
        Route::get('/percentage/{termId?}', 'OrdersController@percentage')->name('percentage');
    });

    //Invoice Routes
    Route::group(['prefix'=>'invoices/', 'namespace' => 'Admin\Orders'], function () {
        Route::get('/order/{orderId}', 'InvoicesController@order');
        Route::get('/download/{orderId}', 'InvoicesController@download');
        Route::get('/pdf/{orderId}', 'InvoicesController@pdf');
    });


    //Assessment Routes
    Route::group(['prefix'=>'assessments/', 'namespace' => 'Admin\Assessments'], function () {
        Route::get('/view/{studentId}', 'AssessmentsController@view');
        Route::get('/details/{studentId}/{termId}', 'AssessmentsController@details');
    });

    
    //Attendance Routes
    Route::group(['prefix'=>'attendances/', 'namespace' => 'Admin\Attendances'], function () {
        Route::get('/', 'AttendancesController@index');
        Route::get('/initiate/{classId}/{attendId?}', 'AttendancesController@initiate')->name('initiateAttendance');
        Route::post('/initiate/{classId}/{attendId?}', 'AttendancesController@take');
        Route::get('/adjust/{classId}', 'AttendancesController@adjust')->name('adjustAttendance');
        Route::post('/classroom', 'AttendancesController@classroom');
        Route::get('/classroom-details/{attendId}', 'AttendancesController@classroomDetails');
        Route::post('/student', 'AttendancesController@student');
        Route::get('/student-details/{studentClassId}/{termId}', 'AttendancesController@studentDetails');
        //Student
        Route::get('/view/{studentId}', 'AttendancesController@viewStudent');
        Route::get('/details/{studentId}/{attendId}', 'AttendancesController@viewDetails');
    });
    
    Route::controllers([
        'auth' => 'Auth\AuthController',
        'dashboard' => 'Admin\Utilities\DashboardController',
        //Dependent List Box
        'list-box' => 'Admin\Utilities\ListBoxController',
        //Random Numbers
        'pin-numbers' => 'Admin\Utilities\PinNumberController',

        //Messaging
        'messages' => 'Admin\Utilities\MessageController',
        
        'users' => 'Admin\Users\UserController',
        'user-types' => 'Admin\Users\UserTypeController',

        'roles' => 'Admin\RolesAndPermission\RolesController',
        'permissions' => 'Admin\RolesAndPermission\PermissionsController',
        'profiles' => 'Admin\Users\ProfileController',

        'schools' => 'School\SchoolController',
        
        //Menus Route
        'menus' => 'Admin\MasterRecords\MenuController',

        //setup records
        'salutations' => 'School\Setups\SalutationController',
        'marital-statuses' => 'School\Setups\MaritalStatusController',

        'accounts' => 'Admin\Accounts\AccountsController',
        'sponsors' => 'Admin\Accounts\SponsorController',
        'staffs' => 'Admin\Accounts\StaffController',
        'students' => 'Admin\Accounts\StudentController',

        //Master Record
        'academic-years' => 'Admin\MasterRecords\Sessions\AcademicYearsController',
        'academic-terms' => 'Admin\MasterRecords\Sessions\AcademicTermsController',
        'grades' => 'Admin\MasterRecords\GradesController',
        'class-groups' => 'Admin\MasterRecords\Classes\ClassGroupsController',
        'class-levels' => 'Admin\MasterRecords\Classes\ClassLevelsController',
        'class-rooms' => 'Admin\MasterRecords\Classes\ClassRoomsController',
        'class-students' => 'Admin\MasterRecords\Classes\ClassStudentsController',

        //Items Records
        'item-types' => 'Admin\MasterRecords\Items\ItemTypesController',
        'items' => 'Admin\MasterRecords\Items\ItemsController',
        'item-quotes' => 'Admin\MasterRecords\Items\ItemQuotesController',

        //Subjects
        'subject-groups' => 'School\Setups\Subjects\SubjectGroupsController',
        'subjects' => 'School\Setups\Subjects\SubjectsController',
        'subject-classrooms' => 'Admin\MasterRecords\Subjects\SubjectClassRoomsController',
        'subject-tutors' => 'Admin\MasterRecords\Subjects\SubjectTutorsController',
        'school-subjects' => 'Admin\MasterRecords\Subjects\SchoolSubjectsController',
        'custom-subjects' => 'Admin\MasterRecords\Subjects\CustomSubjectsController',

        //Assessment Setup
        'assessment-setups' => 'Admin\MasterRecords\AssessmentSetupsController',
        'assessments' => 'Admin\Assessments\AssessmentsController',
        'domains' => 'Admin\Assessments\DomainsController',
        'exams' => 'Admin\Assessments\ExamsController',

        //Order Routes
        'orders' => 'Admin\Orders\OrdersController',
        'billings' => 'Admin\Orders\BillingsController',
    ]);
});

///////////////////////User/Project APIs///////////////////////////////
Route::group(array('prefix'=>'/api'),function(){
    Route::post('/json', function () {
        return json_encode(['name'=>'KayOh', 'surname'=>'China']);
    });
    Route::post('/sign-in', 'Admin\APIController@login');
    Route::get('/clients', 'Admin\APIController@clients');


    Route::get('/sms/{to?}/{message?}/{msg_sender?}', function ($to, $message, $msg_sender="WUFPBKPortal") {
        $mobile_no = trim($to);
        if(substr($mobile_no, 0, 1) == '0'){
            $no = '234' . substr($mobile_no, 1);
        }elseif (substr($mobile_no, 0, 3) == '234') {
            $no = $mobile_no;
        }elseif (substr($mobile_no, 0, 1) == '+') {
            $no = substr($mobile_no, 1);
        }else{
            $no = '234' . $mobile_no;
        }

//        http://www.mcastmessaging.com/mcast_ws_v2/index.php?user=ZumaComm&password=zuma123456&from=EkaruzTech&to=2348066711147&message=Good+day+Mr.+Ameh+Agaba,+this+is+to+notifiy+you+that+you+have+exhausted+your+units+therefore+a+reminder+that+your+renewal+is+due,+as+we+look+forward+to+continue+serving+you+better.+CTO+EkaruzTech&type=json
//        http://www.mcastmessaging.com/mcast_ws_v2/index.php?user=ZumaComm&password=zuma123456&from=EkaruzTech&to=2348022020075&message=Good+day+Mr.+Ameh+Agaba,+this+is+to+notifiy+you+that+you+have+exhausted+your+units+therefore+a+reminder+that+your+renewal+is+due,+as+we+look+forward+to+continue+serving+you+better.+CTO+EkaruzTech&type=json

        $msg = str_replace("+", ' ', $message);
        $message2 = urlencode($msg);
        $username = "ZumaComm";
        $password = "zuma123456";
        // auth call
//        $url = "http://mcastmessaging.com/mcast_ws_v2/index.php?user=$username&password=$password&from=$msg_sender&to=$no&message=$message2&type=json";
//        $url = "http://mcastmessaging.com/mcast_ws_v2/index.php?user=ZumaComm&password=zuma123456&from=KayOh&to=2348022020075&message=THE+MESSAGE+IS+HERE&type=json";
        //http://smartschool.ekaruztech.com/api/sms/2348022020075/Come+now+make+we+go/

        $sms = \App\Models\Admin\MasterRecords\Sms::where('status', 1)->first();
        $ret = 'Sorry was unable to send your request...Kindly contact your SMS providers';

        $message1 = urlencode('You have less than ' . ($sms->unit_bought - $sms->unit_used) . ' Units left in your account!!! Kindly Recharge.');
        if($sms->unit_used < $sms->unit_bought){
            $url = "http://mcastmessaging.com/mcast_ws_v2/index.php?user=$username&password=$password&from=$msg_sender&to=$no&message=$message2&type=json";
            $ret = file($url);
            $sms->unit_used += 1.7;

            if($sms->unit_used + 4 > $sms->unit_bought){
                //Send to Agaba
                $url2 = "http://mcastmessaging.com/mcast_ws_v2/index.php?user=$username&password=$password&from=BULK_SMS&to=2348066711147&message=$message1&type=json";
                file($url2);
                $sms->unit_used += 1.7;
            }
        }elseif($sms->unit_used > $sms->unit_bought and $sms->unit_used < $sms->unit_bought + 3){
            //Send to KayOh
            $url1 = "http://mcastmessaging.com/mcast_ws_v2/index.php?user=$username&password=$password&from=BULK_SMS&to=2348022020075&message=$message1&type=json";
            $ret = file($url1);
            $sms->unit_used += 1.7;
        }
        $sms->save();
        return $ret;
    });

    Route::get('/balance', function () {
        $sms = \App\Models\Admin\MasterRecords\Sms::where('status', 1)->first();
        if($sms->unit_used < $sms->unit_bought){
            $message1 = 'Hello, Waziri Umaru Federal Polytechnic, Birinin Kebbi, Kebbi State, You have ' 
                . ($sms->unit_bought - $sms->unit_used) . ' Units left in your account!!!';
        }else{
            $message1 = 'Hello, Waziri Umaru Federal Polytechnic, Birinin Kebbi, Kebbi State, You have exhausted your (' 
                . $sms->unit_bought . ') Units bought on ' . $sms->created_at->format('jS M, Y');
        }
        //http://smartschool.ekaruztech.com/api/balance
        
        return $message1;
    });
});