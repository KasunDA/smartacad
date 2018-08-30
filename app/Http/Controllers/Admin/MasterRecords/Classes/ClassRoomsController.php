<?php

namespace App\Http\Controllers\Admin\MasterRecords\Classes;

use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassMaster;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\RolesAndPermissions\Role;
use App\Models\Admin\Users\User;
use App\Models\School\School;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use stdClass;

class ClassRoomsController extends Controller
{
    protected $school;
    /**
     *
     * Make sure the user is logged in and The Record has been setup
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->school = School::mySchool();

        $this->school->setup === School::CLASS_ROOM
            ? $this->setFlashMessage('Warning!!! Kindly Setup the Class Rooms records Before Proceeding.', 3)
            : $this->middleware('setup');
    }
    
    /**
     * Display a listing of the Menus for Master Records.
     * @param String $encodeId
     * @return Response
     */
    public function index($encodeId=null)
    {
        $classlevel = ($encodeId == null) ? null : ClassLevel::findOrFail($this->decode($encodeId));
        $classrooms = ($classlevel == null) ? ClassRoom::all() : $classlevel->classRooms()->get();
        $classlevels = ClassLevel::pluck('classlevel', 'classlevel_id')->prepend('- All Class Level -', '');

        return view('admin.master-records.classes.class-rooms.index',
            compact('classlevels', 'classrooms', 'classlevel')
        );
    }

    /**
     * Insert or Update the class room records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

       for($i = 0; $i < count($inputs['classroom_id']); $i++){
            $classroom = ($inputs['classroom_id'][$i] > 0) ? ClassRoom::find($inputs['classroom_id'][$i]) : new ClassRoom();
            $classroom->classroom = $inputs['classroom'][$i];
            $classroom->class_size = ($inputs['class_size'][$i] != '') ? $inputs['class_size'][$i] : null;
            $classroom->classlevel_id = $inputs['classlevel_id'][$i];
            if($classroom->save()){
                $count = $count+1;
            }
        }
        //Update The Setup Process
        if ($this->school->setup == School::CLASS_ROOM){
            $this->school->setup = School::SUBJECT;
            $this->school->save();
            return redirect('/school-subjects');
        }
        
        if($count > 0) $this->setFlashMessage($count . ' Academic Year has been successfully updated.', 1);
        
        return redirect('/class-rooms');
    }

    /**
     * Delete a Menu from the list of Menus using a given menu id
     * @param $id
     */
    public function delete($id)
    {
        $classroom = ClassRoom::findOrFail($id);
        $delete = ($classroom !== null) ? $classroom->delete() : null;

        ($delete)
            ? $this->setFlashMessage('  Deleted!!! '.$classroom->classroom.' Class Room have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }

    /**
     * Get The Class Rooms Given the class level id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function levels(Request $request)
    {
        $inputs = $request->all();

        return redirect('/class-rooms/' . $this->encode($inputs['classlevel_id']));
    }

    /**
     * Assign Student To CLass Room / Class Room To Form Master.
     *
     * @return Response
     */
    public function classTeachers()
    {
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id')
            ->prepend('- Select Academic Year -', '');
        $classlevels = ClassLevel::pluck('classlevel', 'classlevel_id')
            ->prepend('- Select Class Level -', '');
        $tutors = User::where('user_type_id', User::STAFF)
            ->where('status', 1)
            ->orderBy('first_name')
            ->get();

        return view('admin.master-records.classes.class-rooms.class-teacher',
            compact('academic_years', 'classlevels', 'tutors')
        );
    }

    /**
     * Search For Form Masters in a class level for an academic year
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function teachers(Request $request)
    {
        $inputs = $request->all();
        $classrooms = ClassRoom::orderBy('classroom')
            ->where('classlevel_id', $inputs['classlevel_id'])
            ->get();

        $response = array();
        $response['flag'] = 0;
        $classRooms = [];

        if ($classrooms) {
            //All the Class Rooms in the class level
            foreach($classrooms as $classroom){
                $object = new stdClass();
                $classMaster = $classroom->classMasters()->where('academic_year_id', $inputs['academic_year_id'])->first();
                $object->user_id = ($classMaster and $classMaster->user()->first()) ? $classMaster->user()->first()->user_id : -1;
                $object->name = ($classMaster and $classMaster->user()->first()) ? $classMaster->user()->first()->fullNames() : 'Select Class Teacher';
                $object->class_master_id = ($classMaster) ? $classMaster->class_master_id : -1;
                $object->classroom = $classroom->classroom;
                $object->classroom_id = $classroom->classroom_id;
                $object->students = $classroom->studentClasses()->where('academic_year_id', $inputs['academic_year_id'])->where('classroom_id', $classroom->classroom_id)->count();
                $classRooms[] = $object;
            }

            $response['flag'] = 1;
            $response['ClassRooms'] = isset($classRooms) ? $classRooms : [];
        }
        echo json_encode($response);
    }

    /**
     * Assign Class Master
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function assignClassTeachers(Request $request)
    {
        $inputs = $request->all();
        $user_id = ($inputs['user_id'] > 0)  ? $inputs['user_id'] : false;
        $classMaster = ($inputs['class_master_id'] > 0) ? ClassMaster::find($inputs['class_master_id']) : new ClassMaster();
        $role = Role::where('name', Role::CLASS_TEACHER)->first();

        if (isset($classMaster->user_id)) {
            $userOld = $classMaster->user;
            if ($user_id) {
                $userNew = User::find($inputs['user_id']);
            }
        } elseif ($user_id and !isset($classMaster->user_id)) {
            $userNew = User::find($inputs['user_id']);
        }

        $classMaster->classroom_id = $inputs['classroom_id'];
        $classMaster->academic_year_id = $inputs['year_id'];
        $classMaster->user_id = ($user_id) ? $user_id : null;

        if ($classMaster->save()) {
            if (isset($userNew)) {
                if (!$userNew->hasRole([Role::CLASS_TEACHER])) $userNew->roles()->attach($role->role_id);
            }
            if (isset($userOld)) {
                if ($userOld->hasRole([Role::CLASS_TEACHER]) && $userOld->classMasters()->where('academic_year_id', $inputs['year_id'])->count() == 0)
                    $userOld->roles()->detach($role->role_id);
            }
            echo json_encode($classMaster->class_master_id);
        }
    }
}
