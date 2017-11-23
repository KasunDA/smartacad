<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/json', function () {
    return json_encode(['name'=>'KayOh', 'surname'=>'China']);
})->middleware('auth:api');

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

    $sms = \App\Models\School\Sms::where('status', 1)->first();
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
    $sms = \App\Models\School\Sms::where('status', 1)->first();
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
