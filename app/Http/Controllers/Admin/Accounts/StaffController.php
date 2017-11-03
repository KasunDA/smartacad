<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Helpers\LabelHelper;
use App\Models\Admin\Accounts\Sponsor;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\Subjects\SubjectAssessmentView;
use App\Models\Admin\MasterRecords\Subjects\SubjectClassRoom;
use App\Models\Admin\Users\User;
use App\Models\Admin\Users\UserType;
use App\Models\School\Setups\Lga;
use App\Models\School\Setups\Salutation;
use App\Models\School\Setups\State;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{

    /**
     * A list of colors for representing charts
     */
    public function __construct()
    {
        $this->colors = [
            '#FF0F00', '#FF6600', '#FF9E01', '#FCD202', '#F8FF01', '#B0DE09', '#04D215', '#0D8ECF', '#0D52D1', '#2A0CD0', '#8A0CCF',
            '#CD0D74', '#754DEB', '#DDDDDD', '#CCCCCC', '#999999', '#333333', '#000000',
            '#FF0F00', '#FF6600', '#FF9E01', '#FCD202', '#F8FF01', '#B0DE09', '#04D215', '#0D8ECF', '#0D52D1', '#2A0CD0', '#8A0CCF',
            '#CD0D74', '#754DEB', '#DDDDDD', '#CCCCCC', '#999999', '#333333', '#000000',
        ];

        parent::__construct();
    }
    
    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function getIndex()
    {
        return view('admin.accounts.staffs.index');
    }

    /**
     * Display a listing of the Staffs using Ajax Datatable.
     * @return Response
     */
    public function postAllStaffs()
    {
        $iTotalRecords = User::whereIn('user_type_id', UserType::where('type', 2)->get(['user_type_id'])->toArray())
            ->where('user_type_id', '<>', Sponsor::USER_TYPE)->count();;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $q = @$_REQUEST['sSearch'];

        //List of Sponsors
        $staffs = User::whereIn('user_type_id', UserType::where('type', 2)->get(['user_type_id'])->toArray())
            ->where('user_type_id', '<>', Sponsor::USER_TYPE)->orderBy('first_name')->where(function ($query) use ($q) {
            if (!empty($q))
                $query->orWhere('first_name', 'like', '%'.$q.'%')->orWhere('last_name', 'like', '%'.$q.'%')
                    ->orWhere('email', 'like', '%'.$q.'%')->orWhere('phone_no', 'like', '%'.$q.'%');
        });
        // iTotalDisplayRecords = filtered result count
        $iTotalDisplayRecords = $staffs->count();
        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        $i = $iDisplayStart;
        $allStaffs = $staffs->skip($iDisplayStart)->take($iDisplayLength)->get();
        foreach ($allStaffs as $staff){
            $status = ($staff->status == 1)
                ? LabelHelper::success('Activated') : LabelHelper::danger('Deactivated');

            $records["data"][] = array(
                ($i++ + 1),
                $staff->fullNames(),
                $staff->phone_no,
                $staff->email,
                ($staff->gender) ? $staff->gender : LabelHelper::danger(),
                '<a target="_blank" href="/staffs/dashboard/'.$this->getHashIds()->encode($staff->user_id).'" class="btn btn-default btn-rounded btn-condensed btn-xs">
                     <span class="fa fa-eye"></span> View
                 </a>',
                $status,
                '<a href="/staffs/view/'.$this->getHashIds()->encode($staff->user_id).'" class="btn btn-info btn-rounded btn-condensed btn-xs">
                     <span class="fa fa-eye-slash"></span> Details
                 </a>',
                '<a href="/staffs/edit/'.$this->getHashIds()->encode($staff->user_id).'" class="btn btn-warning btn-rounded btn-condensed btn-xs">
                     <span class="fa fa-edit"></span>
                 </a>'
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
        $staff = User::findOrFail($this->decode($encodeId));
        return view('admin.accounts.staffs.view', compact('staff'));
    }

    /**
     * Displays the Staff profiles details for editing
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function getEdit($encodeId)
    {
        $decodeId = $this->getHashIds()->decode($encodeId);

        $staff = (empty($decodeId)) ? abort(305) : User::findOrFail($decodeId[0]);
        $salutations = Salutation::orderBy('salutation')->lists('salutation', 'salutation_id')->prepend('Select Title', '');
        $states = State::orderBy('state')->lists('state', 'state_id')->prepend('Select State', '');
        $lga = ($staff->lga()->first()) ? $staff->lga()->first() : null;
        $lgas = ($staff->lga_id > 0) ? Lga::where('state_id', $staff->lga()->first()->state_id)->lists('lga', 'lga_id')->prepend('Select L.G.A', '') : null;

        return view('admin.accounts.staffs.edit', compact('staff', 'salutations', 'states', 'lga', 'lgas'));
    }

    /**
     * Update the users profile
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postEdit(Request $request)
    {
        $inputs = $request->all();
        $user = (empty($inputs['user_id'])) ? abort(305) : User::findOrFail($inputs['user_id']);
        $messages = [
            'salutation_id.required' => 'Title is Required!',
            'first_name.required' => 'First Name is Required!',
            'last_name.required' => 'Last Name is Required!',
            'email.unique' => 'This E-Mail Address Has Already Been Assigned!',
            'phone_no.unique' => 'The Mobile Number Has Already Been Assigned!',
            'gender.required' => 'Gender is Required!',
            'dob.required' => 'Date of Birth is Required!',
//            'address.required' => 'Contact Address is Required!',
        ];
        $validator = Validator::make($inputs, [
            'salutation_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'phone_no' => 'required|max:15|min:11|unique:users,phone_no,'.$user->user_id.',user_id',
            'gender' => 'required',
            'dob' => 'required',
//            'address' => 'required',
        ], $messages);

        if ($validator->fails()) {
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/staffs/edit/'.$this->getHashIds()->encode($inputs['user_id']))->withErrors($validator)->withInput();
        }

        $user->update($inputs);
        $this->setFlashMessage('Staff ' . $user->fullNames() . ', Information has been successfully updated.', 1);

        return redirect('/staffs');
    }

    public function dashboard($encodeId)
    {
        $staff = User::findOrFail($this->decode($encodeId));

        return view('admin.accounts.staffs.dashboard', compact('staff'));
    }

    public function marked($encodeId)
    {
        $staff = User::findOrFail($this->decode($encodeId));
        $marked = SubjectAssessmentView::where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)
            ->where('tutor_id', $staff->user_id)
            ->where('marked', 1)
            ->groupBy('subject', 'subject_id')
            ->get();

        return view('admin.accounts.staffs.marked', compact('marked', 'staff'));
    }

    public function unmarked($encodeId)
    {
        $staff = User::findOrFail($this->decode($encodeId));
        $unmarked = SubjectAssessmentView::where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)
            ->where('tutor_id', $staff->user_id)
            ->where(function ($query) {
                $query->whereNull('assessment_id')->orWhere('marked', '<>', 1);
            })
            ->get();

        return view('admin.accounts.staffs.unmarked', compact('unmarked', 'staff'));
    }

    public function subject($encodeId)
    {
        $staff = User::findOrFail($this->decode($encodeId));
        $subjects = $staff->subjectClassRooms()
            ->groupBy(['academic_term_id','subject_id'])
            ->get();

        return view('admin.accounts.staffs.subject', compact('subjects', 'staff'));
    }

    public function subjectDetails($subjectId)
    {
        $subjectClassrooms = SubjectClassRoom::findOrFail($this->decode($subjectId));
        $staff = User::findOrFail($subjectClassrooms->tutor_id);
        $subjects = $staff->subjectClassRooms()
            ->where('subject_id', $subjectClassrooms->subject_id)
            ->where('academic_term_id', $subjectClassrooms->academic_term_id)
//            ->orderBy('subject')
            ->get();

        return view('admin.accounts.staffs.subject-details', compact('subjects', 'staff'));
    }
}
