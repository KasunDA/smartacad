<?php

namespace App\Http\Controllers\School\Setups;

use App\Models\Admin\MasterRecords\Classes\ClassGroup;
use App\Models\School\Banks\Bank;
use App\Models\School\Banks\SchoolBank;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BanksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        $school_banks = SchoolBank::where('school_id', $this->school_profile->school_id)->get();
        $banks = Bank::orderBy('name')
            ->active()
            ->pluck('name', 'id')
            ->prepend('- Select Bank -', '');
        $classgroups = ClassGroup::orderBy('classgroup')
            ->pluck('classgroup', 'classgroup_id')
            ->prepend('- Select Class Group -', '');
        return view('school.setups.banks', compact('school_banks', 'banks', 'classgroups'));
    }

    /**
     * Insert or Update the user type records
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        $inputs = $request->all();
        $count = 0;

        for($i = 0; $i < count($inputs['id']); $i++){
            $bank = ($inputs['id'][$i] > 0) ? SchoolBank::find($inputs['id'][$i]) : new SchoolBank();
            $bank->account_name = $inputs['account_name'][$i];
            $bank->account_number = $inputs['account_number'][$i];
            $bank->bank_id = $inputs['bank_id'][$i];
            $bank->classgroup_id = $inputs['classgroup_id'][$i];
            $bank->active = $inputs['active'][$i];
            $bank->school_id = $this->school_profile->school_id;
            $count = ($bank->save()) ? $count+1 : '';
        }
        // Set the flash message
        if($count > 0)
            $this->setFlashMessage($count . ' School Bank has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/school-banks');
    }

    /**
     * Delete a User type from the list of user Types using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $school_bank = SchoolBank::findOrFail($id);
        //Delete The Warder Record
        $delete = ($school_bank !== null) ? $school_bank->delete() : null;

        ($delete)
            ? $this->setFlashMessage('  Deleted!!! '.$marital_statuses->marital_status.' Salutation have been deleted.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
    }
}
