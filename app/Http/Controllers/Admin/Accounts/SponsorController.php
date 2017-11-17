<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Helpers\LabelHelper;
use App\Models\Admin\Users\User;
use App\Models\School\Setups\Lga;
use App\Models\School\Setups\Salutation;
use App\Models\School\Setups\State;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SponsorController extends Controller
{
    /**
     * Display a listing of the Users.
     * @return Response
     */
    public function index()
    {
        return view('admin.accounts.sponsors.index');
    }

    /**
     * Display a listing of the Sponsors using Ajax Datatable.
     *
     * @return Response
     */
    public function data()
    {
        $iTotalRecords = User::where('user_type_id', User::SPONSOR)->orderBy('first_name')->count();;
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $q = @$_REQUEST['sSearch'];

        //List of Sponsors
        $sponsors = User::where('user_type_id', User::SPONSOR)
            ->orderBy('first_name')
            ->where(function ($query) use ($q) {
                if (!empty($q)){
                    $query->orWhere('first_name', 'like', '%'.$q.'%')
                        ->orWhere('last_name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('phone_no', 'like', '%'.$q.'%');
                }
            });
        // iTotalDisplayRecords = filtered result count
        $iTotalDisplayRecords = $sponsors->count();
        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        $i = $iDisplayStart;
        $allSponsors = $sponsors->skip($iDisplayStart)->take($iDisplayLength)->get();

        foreach ($allSponsors as $sponsor) {
            $status = ($sponsor->status == 1)
                ? LabelHelper::success('Activated') : LabelHelper::danger('Deactivated');
            
            $records["data"][] = array(
                ($i++ + 1),
                $sponsor->fullNames(),
                $sponsor->phone_no,
                $sponsor->email,
                $sponsor->created_at->format('jS M, Y'),
                $status,
                '<a target="_blank" href="/sponsors/view/'.$this->encode($sponsor->user_id).'" class="btn btn-info btn-rounded btn-condensed btn-xs">
                     <span class="fa fa-eye-slash"></span>
                 </a>',
                '<a target="_blank" href="/sponsors/edit/'.$this->encode($sponsor->user_id).'" class="btn btn-warning btn-rounded btn-condensed btn-xs">
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
     *
     * @param String $encodeId
     *
     * @return \Illuminate\View\View
     */
    public function view($encodeId)
    {
        $sponsor = User::findOrFail($this->decode($encodeId));
        return view('admin.accounts.sponsors.view', compact('sponsor'));
    }

    /**
     * Displays the Staff profiles details for editing
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function edit($encodeId)
    {
        $sponsor = User::findOrFail($this->decode($encodeId));

        $salutations = Salutation::orderBy('salutation')
            ->pluck('salutation', 'salutation_id')
            ->prepend('- Select Title -', '');

        $states = State::orderBy('state')
            ->pluck('state', 'state_id')
            ->prepend('- Select State -', '');

        $lga = ($sponsor->lga()->first()) ? $sponsor->lga()->first() : null;
        $lgas = ($sponsor->lga_id > 0) 
            ? Lga::where('state_id', $sponsor->lga()->first()->state_id)
                ->pluck('lga', 'lga_id')
                ->prepend('- Select L.G.A -', '')
            : null;

        return view('admin.accounts.sponsors.edit', compact('sponsor', 'salutations', 'states', 'lga', 'lgas'));
    }

    /**
     * Update the users profile
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $inputs = $request->all();
        $user = (empty($inputs['user_id'])) ? abort(305) : User::findOrFail($inputs['user_id']);
        $messages = [
            'salutation_id.required' => 'Title is Required!',
            'first_name.required' => 'First Name is Required!',
            'last_name.required' => 'Last Name is Required!',
//            'email.unique' => 'This E-Mail Address Has Already Been Assigned!',
            'phone_no.unique' => 'The Mobile Number Has Already Been Assigned!',
//            'dob.required' => 'Date of Birth is Required!'
        ];
        $validator = Validator::make($inputs, [
            'salutation_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
//            'email' => 'required|email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'phone_no' => 'required|max:15|min:11|unique:users,phone_no,'.$user->user_id.',user_id',
//            'dob' => 'required'
        ], $messages);

        if ($validator->fails()) {
            $this->setFlashMessage('Error!!! You have error(s) while filling the form.', 2);
            return redirect('/sponsors/edit/'.$this->encode($inputs['user_id']))->withErrors($validator)->withInput();
        }

        $user->update($inputs);
        $this->setFlashMessage('Sponsor ' . $user->fullNames() . ', Information has been successfully updated.', 1);

        return redirect('/sponsors');
    }
}
