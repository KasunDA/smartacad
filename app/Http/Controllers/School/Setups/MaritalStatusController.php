<?php

namespace App\Http\Controllers\School\Setups;

use App\Models\School\Setups\MaritalStatus;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MaritalStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        $marital_statuses = MaritalStatus::all();
        return view('school.setups.marital-status', compact('marital_statuses'));
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

        for($i = 0; $i < count($inputs['marital_status_id']); $i++){
            $marital_status = ($inputs['marital_status_id'][$i] > 0) ? MaritalStatus::find($inputs['marital_status_id'][$i]) : new MaritalStatus();
            $marital_status->marital_status = $inputs['marital_status'][$i];
            $marital_status->marital_status_abbr = $inputs['marital_status_abbr'][$i];
            $count = ($marital_status->save()) ? $count+1 : '';
        }
        // Set the flash message
        if($count > 0)
            $this->setFlashMessage($count . ' User Type has been successfully updated.', 1);
        // redirect to the create a new inmate page
        return redirect('/marital-statuses');
    }

    /**
     * Delete a User type from the list of user Types using a given menu id
     * @param $id
     */
    public function getDelete($id)
    {
        $marital_statuses = MaritalStatus::findOrFail($id);
        //Delete The Warder Record
        $delete = ($marital_statuses !== null) ? $marital_statuses->delete() : null;

        if($delete){
            //Delete its Equivalent Users Record
            $this->setFlashMessage('  Deleted!!! '.$marital_statuses->marital_status.' Salutation have been deleted.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }
}
