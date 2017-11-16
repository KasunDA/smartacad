<?php

namespace App\Http\Controllers\Admin\Utilities;

use App\Helpers\LabelHelper;
use App\Http\Controllers\Controller;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Users\User;
use App\Models\Admin\Users\UserType;
use Illuminate\Http\Request;
use stdClass;

class MessageController extends Controller
{
    /**
     * Get the default page of messaging
     * @return Response
     */
    public function index()
    {
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id')
            ->prepend('- Select Academic Year -', '');
        $classlevels = ClassLevel::pluck('classlevel', 'classlevel_id')
            ->prepend('- Select Class Level -', '');

        return view('admin.messages.index', compact('academic_years', 'classlevels'));
    }

    /**
     * Search For Students in a class room or class level for an academic year
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function students(Request $request)
    {
        $inputs = $request->all();
        $class = (isset($inputs['classroom_id']) && $inputs['classroom_id'] != '') ? true : false;

        $students = StudentClass::where('academic_year_id', $inputs['academic_year_id'])
            ->where(function ($query) use ($class, $inputs) {
                //If a class is selected else return all the class level students
                ($class)
                    ? $query->where('classroom_id', $inputs['classroom_id'])
                    : $query->whereIn('classroom_id',
                        ClassRoom::where('classlevel_id', $inputs['classlevel_id'])
                            ->pluck('classroom_id')
                            ->toArray()
                    );
            })->get();

        $studentsClass = $response = [];
        $response['flag'] = 0;

        if ($students->count() > 0) {
            //All the students in the class room for the academic year
            foreach ($students as $student) {
                if ($student->student()->first()->status_id == 1) {
                    $object = new stdClass();
                    $object->student_id = $this->encode($student->student_id);
                    $object->name = $student->student()->first()->fullNames();
                    $object->student_no = $student->student()->first()->student_no;
                    $object->gender = $student->student()->first()->gender;
                    $object->classroom = $student->classRoom()->first()->classroom;
                    $object->sponsor = ($student->student()->first()->sponsor()->first())
                        ? $student->student()->first()->sponsor()->first()->fullNames()
                        : '<span class="label label-danger">nil</span>';
                    $object->phone_no = ($student->student()->first()->sponsor()->first())
                        ? $student->student()->first()->sponsor()->first()->phone_no : '';
                    $object->sponsor_id = ($student->student()->first()->sponsor()->first())
                        ? $this->encode($student->student()->first()->sponsor()->first()->user_id) : -1;
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
    public function staffs()
    {
        $iTotalRecords = User::whereIn('user_type_id',
                UserType::where('type', 2)
                    ->get(['user_type_id'])
                    ->toArray()
            )
            ->where('user_type_id', '<>', User::SPONSOR)
            ->where('status', 1)
            ->count();
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);
        $q = @$_REQUEST['sSearch'];

        //List of Sponsors
        $staffs = User::where('status', 1)
            ->where('user_type_id', '<>', User::SPONSOR)
            ->whereIn('user_type_id',
                UserType::where('type', 2)
                    ->get(['user_type_id'])
                    ->toArray()
            )
            ->where(
                function ($query) use ($q) {
                    if (!empty($q)) {
                        $query->orWhere('first_name', 'like', '%' . $q . '%')
                            ->orWhere('last_name', 'like', '%' . $q . '%')
                            ->orWhere('email', 'like', '%' . $q . '%')
                            ->orWhere('phone_no', 'like', '%' . $q . '%');
                    }
                }
            )
            ->orderBy('first_name');

        $iTotalDisplayRecords = $staffs->count();
        $records["data"] = $records = [];

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
                ($staff->gender) ? $staff->gender : LabelHelper::danger(),
                '<a target="_blank" href="/staffs/view/' . $this->encode($staff->user_id)
                        . '" class="btn btn-info btn-rounded btn-condensed btn-xs">
                     <span class="fa fa-eye-slash"></span>
                 </a>',
                '<button value="' . $staff->phone_no
                        . '" class="btn btn-warning btn-rounded btn-condensed btn-xs send-message">
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
    public function messageSelected(Request $request)
    {
        $inputs = $request->all();
        $response = $nos = [];
        $response['flag'] = 0;
        $count = count($inputs['phone_no']);

        if (isset($inputs['phone_no']) && $count > 0) {
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
    public function send(Request $request)
    {
        $inputs = $request->all();
        $nos = explode(',', $inputs['phone_no']);
        $count = 0;

        if (isset($inputs['message']) && $inputs['message'] != '') {
            for ($i = 0; $i < count($nos); $i++) {
                $res = $this->sendSMS($inputs['message'], $nos[$i]);
                if ($res) $count++;
            }
            $this->setFlashMessage($count . ' individual message has been sent', 1);
        }else{
            $this->setFlashMessage(
                'The message Content was omitted...kindly fill it in, before sending the message', 2
            );
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

        if ($type == '#sponsor') {
            //TODO exclude not active students
            $nos = User::where('user_type_id', User::SPONSOR)
                ->where('status', 1)
                ->distinct()
                ->pluck('phone_no')
                ->toArray();
        } elseif ($type == '#staff') {
            $nos = User::whereIn('user_type_id',
                    UserType::where('type', 2)
                        ->get(['user_type_id'])
                        ->toArray()
                )
                ->where('user_type_id', '<>', User::SPONSOR)
                ->where('status', 1)
                ->distinct()
                ->pluck('phone_no')
                ->toArray();
        }

        for ($i = 0; $i < count($nos); $i++) {
            $res = $this->sendSMS($inputs['message'], $nos[$i]);
            if ($res) $count++;
        }
        
        return redirect('/messages');
    }
}
