<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Helpers\LabelHelper;
use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Users\User;
use App\Models\School\Setups\Lga;
use App\Models\School\Setups\Salutation;
use App\Models\School\Setups\State;
use App\Models\School\Setups\Status;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{

    /**
     * Get a validator for an incoming registration request.
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'first_name.required' => 'The First Name is Required!',
            'last_name.required' => 'The Last Name is Required!',
            'sponsor_name.required' => 'Student Sponsor is Required!',
            'gender.required' => 'A Gender is Required!',
            'classlevel_id.required' => 'A Class Level is Required!',
            'classroom_id.required' => 'A Class Room is Required!',
        ];
        return Validator::make($data, [
            'sponsor_name' => 'required',
            'first_name' => 'required|max:100|min:2',
            'last_name' => 'required|max:100|min:2',
            'gender' => 'required',
            'classlevel_id' => 'required',
            'classroom_id' => 'required',
        ], $messages);
    }

    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function getIndex()
    {
        $classrooms = ClassRoom::orderBy('classroom')
            ->lists('classroom', 'classroom_id')
            ->prepend('- Class Room -', '');
        $status = Status::orderBy('status')
            ->lists('status', 'status_id')
            ->prepend('- Status -', '');

        return view('admin.accounts.students.index', compact('classrooms', 'status'));
    }

    /**
     * Display a listing of the Students using Ajax Datatable.
     * @return Response
     */
    public function postAllStudents()
    {
        $iTotalRecords = Student::orderBy('first_name')->count();;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);
        
        $q = @$_REQUEST['sSearch'];
        $gender = @$_REQUEST['search']['gender'];
        $status_id = @$_REQUEST['search']['status_id'];
        $classroom_id = @$_REQUEST['search']['classroom_id'];

        //Filter by sponsor name
        $sponsors = (!empty($q))
             ? User::where('user_type_id', Sponsor::USER_TYPE)
                ->where(function ($query) use ($q) {
                    $query->orWhere('first_name', 'like', '%'.$q.'%')
                        ->orWhere('last_name', 'like', '%'.$q.'%');
                })
                ->lists('user_id')
                ->toArray()
            : [];

        //List of Students
        $students = Student::orderBy('first_name')
            ->where(function ($query) use ($q, $gender, $status_id, $classroom_id, $sponsors) {
                //Filter by name
                if (!empty($q)) {
                    $query->orWhere('first_name', 'like', '%'.$q.'%')
                        ->orWhere('last_name', 'like', '%'.$q.'%')
                        ->orWhere('student_no', 'like', '%'.$q.'%');

                    if(count($sponsors) > 0) $query->orWhereIn('sponsor_id', $sponsors);
                }

                //Filter by gender
                if (!empty($gender)) $query->where('gender', $gender);
                //Filter by status
                if (!empty($status_id)) $query->where('status_id', $status_id);
                //Filter by class room
                if (!empty($classroom_id)) $query->where('classroom_id', $classroom_id);
            });

        // iTotalDisplayRecords = filtered result count
        $iTotalDisplayRecords = $students->count();
        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $i = $iDisplayStart;
        $allStudents = $students->skip($iDisplayStart)
            ->take($iDisplayLength)
            ->get();

        foreach ($allStudents as $student){
            $status = (isset($student->status_id))
                ? '<label class="label label-sm label-'.$student->status()->first()->label.'">'.$student->status()->first()->status.'</label>'
                : LabelHelper::danger();

            $sponsor = ($student->sponsor_id)
                ? '<a target="_blank" href="/sponsors/view/'.$this->encode($student->sponsor()->first()->user_id).'" class="btn btn-info btn-link btn-sm">
                    <span class="fa fa-eye-slash"></span> '.$student->sponsor()->first()->simpleName().'</a>'
                : LabelHelper::danger();

            $classroom = ($student->currentClass(AcademicYear::activeYear()->academic_year_id))
                ? $student->currentClass(AcademicYear::activeYear()->academic_year_id)->classroom
                : LabelHelper::danger();

            $records["data"][] = array(
                ($i++ + 1),
                '<a target="_blank" href="/students/view/'.$this->encode($student->student_id).'" class="btn btn-primary btn-link">'.$student->simpleName().'</a>',
                $student->student_no,
                $sponsor,
                $classroom,
                ($student->gender) ? $student->gender : LabelHelper::danger(),
                $status,
                '<a target="_blank" href="/students/view/'.$this->encode($student->student_id).'" class="btn btn-info btn-rounded btn-condensed btn-xs">
                     <span class="fa fa-eye-slash"></span>
                 </a>',
                '<a target="_blank" href="/students/edit/'.$this->encode($student->student_id).'" class="btn btn-warning btn-rounded btn-condensed btn-xs">
                     <span class="fa fa-edit"></span>
                 </a>',
                '<button class="btn btn-danger btn-rounded btn-xs delete_student" value="'.$student->student_id.'">
                    <span class="fa fa-trash-o"></span>
                 </button>'
            );
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = isset($iTotalDisplayRecords) ? $iTotalDisplayRecords :$iTotalRecords;

        echo json_encode($records);
    }

    /**
     * Displays the Staff profiles details
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getView($encodeId)
    {
        $student = Student::findOrFail($this->decode($encodeId));
        return view('admin.accounts.students.view', compact('student'));
    }

    /**
     * Displays the Staff profiles details for editing
     * @return \Illuminate\View\View
     */
    public function getCreate()
    {
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')
            ->prepend('- Class Level -', '');

        return view('admin.accounts.students.create', compact('classlevels'));
    }

    /**
     * Store a newly created resource in storage.
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

            return redirect('/students/create')
                ->withErrors($this->validator($input))
                ->withInput();
        }

        if($input['sponsor_id'] < 1){
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);

            return redirect('/students/create')
                ->withErrors(['Choose Student Sponsor From The List of Suggested Sponsors!'])
                ->withInput();
        }

        //Validate if the student already exist in the system
        $check = Student::where('sponsor_id', $input['sponsor_id'])
            ->where('first_name', trim($input['first_name']))
            ->where('last_name', trim($input['last_name']))
            ->count();

        if($check > 0){
            $this->setFlashMessage('Warning!!! You have error(s) while filling the form.', 2);

            return redirect('/students/create')
                ->withErrors(['message'=>'The Student '.$input['first_name'].' ' .$input['last_name'].' Already Exist.'])
                ->withInput();
        }else {

            // Store the Record...
            $input['created_by'] = Auth::user()->user_id;
            $input['admitted_term_id'] = AcademicTerm::activeTerm()->academic_term_id;
            $student = Student::create($input);
            if ($student->save()) {
                $class = new StudentClass();
                $class->student_id = $student->student_id;
                $class->classroom_id = $input['classroom_id'];
                $class->academic_year_id = AcademicYear::activeYear()->academic_year_id;
                $class->save();
                $student->student_no = trim('STD' . str_pad($student->student_id, 5, '0', STR_PAD_LEFT));
                $student->save();
                // Set the flash message
                $this->setFlashMessage('Saved!!! ' . $student->fullNames() . ' have successfully been saved', 1);
            }

            return redirect('/students');
        }
    }

    /**
     * Displays the Staff profiles details for editing
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getEdit($encodeId)
    {
        $student = Student::findOrFail($this->decode($encodeId));
        $status = Status::lists('status', 'status_id')
            ->prepend('- Select Status -', '');

        $states = State::orderBy('state')
            ->lists('state', 'state_id')
            ->prepend('- Select State -', '');
        $lga = ($student->lga()->first()) ? $student->lga()->first() : null;
        $lgas = ($student->lga_id > 0)
            ? Lga::where('state_id', $student->lga()->first()->state_id)
                ->lists('lga', 'lga_id')
                ->prepend('- Select L.G.A -', '')
            : null;

        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')
            ->prepend('- Select Class Level -', '');
        $classroom = ($student->classroom_id) ? $student->classRoom()->first() : null;
        $classrooms = ($student->classroom_id > 0)
            ? ClassRoom::where('classlevel_id', $classroom->classlevel_id)
                ->lists('classroom', 'classroom_id')
                ->prepend('- Select Class Room -', '')
            : null;

        return view('admin.accounts.students.edit',
            compact('student', 'states', 'lga', 'lgas', 'status', 'classlevels', 'classroom', 'classrooms')
        );
    }

    /**
     * Update the users profile
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postEdit(Request $request)
    {
        //Keep track of selected tab
        session()->put('active', 'info');

        $inputs = $request->all();
        $student = (empty($inputs['student_id'])) ? abort(305) : Student::findOrFail($inputs['student_id']);

        if ($this->validator($inputs)->fails())
        {
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            
            return redirect('/students/edit/'.$this->encode($inputs['student_id']))
                ->withErrors($this->validator($inputs))
                ->withInput();
        }

        if($inputs['sponsor_id'] < 1){
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            
            return redirect('/students/edit/'.$this->encode($inputs['student_id']))
                ->withErrors(['Choose Student Sponsor From The List of Suggested Sponsors!'])
                ->withInput();;
        }

        $student->update($inputs);
        $this->setFlashMessage('Student ' . $student->fullNames() . ', Information has been successfully updated.', 1);

        return redirect('/students/view/'.$this->encode($student->student_id));
    }

    /**
     * Delete a Student Record
     * @param $id
     */
    public function getDelete($id)
    {
        $student = Student::findOrFail($id);
        //Delete The Record
        if($student){
            $student->delete();
            $this->setFlashMessage('  Deleted!!! Student '.$student->fullNames().' have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Get The List of Sponsors
     */
    public function getSponsors()
    {
        $response = array();
        $inputs = Input::get('term');
        $sponsors = User::where('user_type_id', Sponsor::USER_TYPE)
            ->where('first_name', 'like', $inputs.'%')
            ->get();
        
        if($sponsors->count() > 0){
            foreach($sponsors as $sponsor){
                $response[] = array(
                    "value"=>$sponsor->fullNames(),
                    "id"=>$sponsor->user_id
                );
            }
        }else{
            $response[0]['id'] = -1;
            $response[0]['value'] = 'No Record Found';
        }
        
        echo json_encode($response);
    }

    /**
     * Profile Picture Upload
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAvatar(Request $request)
    {
        $inputs = Input::all();
        //Keep track of selected tab
        session()->put('active', 'avatar');
        
        if ($request->file('avatar')) {

            $file = $request->file('avatar');
            if($file->getClientSize() > 200000) {
                $this->setFlashMessage('Passport file size exceeds the required 200KB.', 2);
                return redirect()->back();
            }
            
            $filename = $file->getClientOriginalName();
            $img_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            $student = (empty($inputs['student_id'])) ? abort(403) : Student::findOrFail($inputs['student_id']);
            $student->avatar = time() . '_avatar.' . $img_ext;
            Input::file('avatar')->move($student->avatar_path, $student->avatar);

            $student->save();
            $this->setFlashMessage($student->fullNames() . '  passport has been successfully uploaded.', 1);
            
            return redirect('/students/view/'.$this->encode($inputs['student_id']));
        }
    }
}
